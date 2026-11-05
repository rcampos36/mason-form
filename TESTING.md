# Testing

Audit run on 4 November 2026 against the local Lando site. The address is now https://maison-form.lndo.site. A result below was executed in that pass. Anything not listed was not claimed.

## Pages

HTTP checks and rendered headings:

| URL | Result |
| --- | --- |
| `/` | 200, h1 “Work with a clear point of view” |
| `/work/` | 200, “Selected and recent projects” |
| `/studio/` | 200, “MAISON & FORM” |
| `/services/` | 200, “Three practices” |
| `/journal/` | 200, “Notes from the studio” |
| `/journal/page/2/` | 200, first listed article “Material before mood”; featured block absent |
| `/contact/` | 200, “Write to the studio” |
| `/project/northline/` | 200, “Northline” |
| `/first-note/` | 200, “A note on pace” |
| `/?s=studio` | 200, results include Studio and Northline |
| `/?s=zzznomatch` | 200, no-results copy |
| `/this-page-does-not-exist/` | 404, h1 “Not Found” |

## Behavior that was executed

- Primary and footer menus render. Changing the Journal menu item title to “Journal audit” appeared on the homepage; the title was restored to “Journal”.
- Changing Site Settings company description to “Settings audit check.” appeared on the homepage; it was restored to “A design studio in New York, working across identity, space, and digital.”
- `/work/?project_category=identity` returned only Northline. An unknown category slug returns the unfiltered list.
- Journal page 2 was opened in the browser (Enter on the “2” link) and the listing changed.
- The contact form was posted with the page’s `_wpcf7` nonce and returned “Thank you for your message. It has been sent.”
- Mobile menu at 375px: opened with the toggle, dialog was modal and full width, then closed and `aria-expanded` returned to false.
- Homepage hero image is `loading="eager"` and `fetchpriority="high"`. Other content images on the homepage are `loading="lazy"`. Northline and the journal article use eager for the featured image and lazy for the rest.
- Contact Form 7 CSS and JavaScript are on `/contact/` and absent on `/`.
- `wp-block-library` is absent on the homepage and the contact page. No published post, page, or project contains block markup.
- Editor user: can edit pages, posts, uploads, and menus; cannot manage options, install plugins, or update core. Administrators can. `DISALLOW_FILE_EDIT` blocks theme file editing for everyone. In this development environment `DISALLOW_FILE_MODS` is false.
- `composer audit` and `npm audit` reported no advisories.
- Production build: Vite 8.3.5, `public/build/manifest.json`, `assets/app-BEekkO5z.css` (24.22 kB), `assets/app-CLgoDh5Z.js` (1.69 kB). `public/hot` was left in place, so the live Lando site still loads the dev server.

## Responsive checks

`scrollWidth` was compared with `innerWidth` in the browser. Overflow means `scrollWidth` was more than 1px larger than `innerWidth`.

No overflow:

- Home at 375, 768, 1024, 1440, and 1920. At 375 and 768 the desktop nav is `display: none` and the menu button is `display: flex`. At 1024, 1440, and 1920 the button is hidden and the desktop nav is `display: flex`.
- 375: Work, Studio, Services, Journal, Journal page 2, Contact, Northline, search (`/?s=studio`), 404.
- 1920: Work, Studio, Services, Journal, Contact, Northline, `/first-note/`.

`/first-note/` was also measured at the browser panel width of 411px with no overflow. A later attempt to force 375px on that page did not change `innerWidth`, so 375px was not confirmed for the article. Search and the 404 page were not remeasured at 1920.

## Not executed

- Adding, removing, or reordering flexible-content sections in wp-admin. Free ACF does not provide that UI. Stored sections, repeater rows, and services do render.
- A visual check that the skip link and form focus ring appear when the browser document itself has keyboard focus. The CSS rules are in the built stylesheet.
- A production host with `public/hot` removed. The manifest was built and inspected; the live site was still on the Vite dev server.

## Repeat

```bash
lando wp acorn view:clear
npm run build --prefix web/app/themes/agency-starter
composer audit
npm audit --prefix web/app/themes/agency-starter
```

Query counts were measured through Apache with `SAVEQUERIES` and a temporary mu-plugin that was deleted afterward: home 59, work 43, journal 52, contact 35, Northline 41, first-note 47. None of those queries were slower than 50 ms.
