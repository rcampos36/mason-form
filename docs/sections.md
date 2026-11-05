# Page sections

Page sections are an Advanced Custom Fields PRO Flexible Content field named **Page sections**. Editors add, duplicate, remove, and drag sections on any page except Site Settings. Repeaters hold cards, quotes, steps, logos, questions, and profiles. Buttons are Link fields. Images are attachment IDs.

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
| Hero | Eyebrow, heading, description, background image, optional video, two buttons, alignment, background, minimum height |
| Introduction | Heading, rich text, optional image, layout |
| Image and text | Heading, description, image, button, image position, background |
| Feature grid | Heading, description, repeatable cards, column count |
| Portfolio grid | Heading, description, selected or latest projects, count, layout, view-all button |
| Services grid | Heading, description, custom items or service records, column count |
| Testimonials | Heading, repeatable quotes with name, job title, company, and photo |
| Statistics | Optional heading, repeatable number, label, and description |
| Team grid | Heading, custom profiles or team records |
| Process steps | Heading, description, repeatable steps numbered in order |
| Image gallery | Images, media captions, grid, optional lightbox |
| Logo grid | Heading, repeatable logos with optional links |
| FAQ | Heading, repeatable questions and answers in an accordion |
| Call to action | Heading, description, background, two buttons |
| Latest journal posts | Heading, description, count, category, layout |
| Contact information | Heading, description, site contact details, social links, optional Contact Form 7 form, optional map |
| Full-width media | Image or video, caption, aspect ratio |
| Spacer | Spacing size and an optional line |

## Demonstration

`scripts/seed-section-library.php` publishes a page at `/section-library/` with one of each layout filled in, plus sample projects, services, team records, and journal posts.

```bash
lando wp eval-file scripts/seed-section-library.php
```

Running it again updates that page.
