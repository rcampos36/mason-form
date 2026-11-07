# Page sections

Page sections are an Advanced Custom Fields PRO Flexible Content field named **Page sections**. Editors add, duplicate, remove, and drag sections on any page except Site Settings. Repeaters hold cards, quotes, steps, logos, questions, and profiles. Buttons are Link fields. Images are attachment IDs.

While the free plugin is active, **Page content** edits the values already stored for those sections. It does not add, remove, or reorder sections. A blank row at the end of a list adds one item. Gallery images can be replaced, cleared, or added one at a time. Installing ACF PRO replaces that panel with Flexible Content and leaves the same meta in place.

The field group is defined in `web/app/themes/agency-starter/app/fields/Sections.php`. Each layout has a Blade partial in `web/app/themes/agency-starter/resources/views/components/sections/`. Backgrounds, alignment, columns, heights, and aspect ratios are fixed choices. The theme maps those choices to complete Tailwind classes in `app/Design.php`.

## Add or edit a section

1. Install ACF PRO with the licensed Composer package. From an ACF account, open Licenses → Install using Composer and download `auth.json`. Place it at the project root. It is gitignored. The username is the license key and the password is the site URL.
2. From the project root:

```bash
composer require wpengine/advanced-custom-fields-pro
```

3. Deactivate the free Advanced Custom Fields plugin so only PRO is active. The same field group is already registered.
4. In WordPress, open Pages → the page → **Page sections**.
5. Choose **Add section**, pick a layout, and fill in the fields. Instructions under each field say what it controls.
6. Use the row controls to duplicate, remove, or drag a section. Update the page.

Conditional fields stay hidden until they apply. For example, the project picker appears when Portfolio is set to Selected projects, and the number field appears when it is set to Latest projects. Contact details and social links are edited once under **Site Settings**, then shown by the contact section. Project titles, excerpts, and featured images stay on the project. The same is true for services and team records when a section is set to use those records.

Gallery captions are the caption on the image in the Media Library. The lightbox is an option on the gallery section.

## Layouts

| Section | What the editor fills in |
| --- | --- |
| Hero | Eyebrow, heading, description, image, photograph caption, optional video, two buttons, alignment, background, minimum height |
| Introduction | Eyebrow, heading, rich text, link, statement, disciplines, figures, optional image, layout. On the homepage this is the studio band under the title. Figure numbers count up from zero when the band scrolls into view. Disciplines scroll from right to left. |
| Image and text | Heading, description, image, button, image position, background |
| Feature grid | Heading, description, repeatable cards, column count |
| Portfolio grid | Eyebrow, heading, description, selected or latest projects, count, layout, view-all link. On the homepage the eyebrow is the small label, the heading is the large sentence, and each project is a picture with a caption bar, then location, year, area, and type. Each project rises into place when it scrolls into view. |
| Services grid | Eyebrow, heading, description, custom items or service records, column count. On the homepage the eyebrow is the small label, the heading is the large sentence, and each service is a numbered stage. The excerpt is the line under the stage name. Deliverables, duration, and team are filled on the service. The service text opens under the row. |
| Testimonials | Layout, eyebrow, heading, description, repeatable quotes with project, name, job title, company, and photo. Scrolling cards hold the page and move the quotes left until they sit together. |
| Statistics | Optional heading, repeatable number, label, and description |
| Team grid | Heading, custom profiles or team records |
| Process steps | Heading, description, repeatable steps numbered in order |
| Image gallery | Images, media captions, grid, optional lightbox |
| Logo grid | Heading, repeatable logos with optional links |
| FAQ | Heading, repeatable questions and answers in an accordion |
| Call to action | Heading, description, background, two buttons |
| Latest journal posts | Eyebrow, heading, description, chosen articles or a count, category, layout. The scrolling stack pins the page while each article slides up over the last |
| Contact information | Heading, description, site contact details, social links, optional Contact Form 7 form, optional map |
| Full-width media | Image or video, caption, aspect ratio |
| Spacer | Spacing size and an optional line |

## Demonstration

`scripts/seed-section-library.php` publishes a page at `/section-library/` with one of each layout filled in, plus sample projects, services, team records, and journal posts.

```bash
lando wp eval-file scripts/seed-section-library.php
```

Running it again updates that page.
