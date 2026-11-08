# MAISON & FORM

WordPress site for MAISON & FORM, built with [Roots Bedrock](https://roots.io/bedrock/) and the **Agency Starter** theme ([Roots Sage](https://roots.io/sage/) 11, Blade, Vite, Tailwind CSS 4).

The public pages are Home, Work, Studio, Services, Journal, and Contact, plus individual projects, journal posts, search, and the 404 template. Page sections are stored as Advanced Custom Fields data and rendered by Blade. The free ACF plugin is installed. Existing section text, images, and buttons are edited in the Page content panel. Adding, removing, and reordering sections requires a licensed copy of ACF PRO.

## Documentation

- [docs/local-development.md](docs/local-development.md) — Lando, database, permalinks, and the first-time setup
- [CONTENT-EDITING.md](CONTENT-EDITING.md) — how to edit pages, projects, the journal, menus, and site settings
- [docs/sections.md](docs/sections.md) — section types the theme can render
- [DEPLOYMENT.md](DEPLOYMENT.md) — production build and what must be done by hand
- [TESTING.md](TESTING.md) — what was executed in the development audit and how to repeat it

## Local URLs

- Site: https://maison-form.lndo.site
- Admin: https://maison-form.lndo.site/wp/wp-admin/

## Common commands

Run these from the project root. `lando` commands need the Lando environment running.

```bash
lando start
lando wp cache flush
lando wp acorn view:clear
npm run dev --prefix web/app/themes/agency-starter
npm run build --prefix web/app/themes/agency-starter
composer install
scripts/deploy.sh
scripts/deploy.sh content
```

`npm run dev` writes `web/app/themes/agency-starter/public/hot`. While that file exists, the site loads the Vite dev server. Remove it, or stop the dev server, before treating the site as a production build. The production manifest lives at `web/app/themes/agency-starter/public/build/manifest.json`.
