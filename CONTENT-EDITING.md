# Editing the MAISON & FORM website

Sign in at the site’s `/wp/wp-login.php` address. Editors use the shortcuts on **Dashboard → Edit the website**. Administrators also see plugins, themes, and WordPress settings. Editors do not.

Changes appear on the site after you click **Update** or **Publish**. If a page looks unchanged, reload it once.

## Pages

Under **Pages**, open Home, Work, Studio, Services, Journal, or Contact.

Each of those pages is built from **Page sections**. A section is one block on the page, such as Hero, Introduction, or Call to action. The section name is the label on its panel.

The Journal screen under Pages is the introduction at the top of the article list. The articles themselves are under **Posts**.

## Change homepage text

1. Open **Pages → Home**.
2. In **Page sections**, open the **Hero** section.
3. Change **Heading** or **Description**.
4. Click **Update**.

Other homepage text lives in the section with that heading. Change the field, then click **Update**.

Page sections use ACF PRO. Until that plugin is active, the Hero fields are not shown on the edit screen, and a developer changes section text. The title of the page itself can still be edited at the top of the screen.

## Update images

1. Open the page, project, or article that shows the image.
2. For a picture inside a section, use that section’s image field. For the main picture on a project or article, use **Featured image**.
3. Choose an image from the Media library, or upload one. A preview appears after you select it.
4. Click **Update**.

Recommended sizes:

- Hero background: 2400×1600 pixels
- Section images and galleries: 1600×1000 pixels
- Logos: PNG or WebP, at least 480 pixels wide

Wide photographs should be JPG or WebP. Set the image’s **Alt text** in the Media library so screen readers can name it. Gallery captions use the caption saved on the image.

## Add or remove sections

On a page or project, find **Page sections**. This control is the ACF PRO section editor. If that panel is not on the edit screen, the licensed plugin is not active yet, and a developer needs to install it before sections can be added, removed, or reordered. Text and images that are already on the page stay in place.

When the panel is available:

- **Add section** inserts a block. Choose the type, such as Hero or Testimonials, then fill in its fields.
- Drag a section by its handle to change the order.
- **Duplicate** copies a section.
- **Remove** deletes that section from the page. It does not delete projects, articles, or media.

Click **Update** when the order looks right.

Background, alignment, and column choices are limited to the options in each dropdown.

## Add a portfolio project

1. Open **Projects → Add project**.
2. Enter the title. The title becomes the link visitors see.
3. Write the description in the main content area. A short **Excerpt** is used on the work grid.
4. Set the **Featured image**.
5. Fill in **Client**, **Year**, and, if there is a live site, **Website**.
6. Choose **Project categories** and any related **Services**. Drag the services into the order you want.
7. Click **Publish**.

Extra gallery images and additional page sections use the licensed section editor described above. Until that editor is installed, the featured image, description, client, year, and website are the fields you can change.

The project appears on the Work page. Featuring it inside a section on Home or Services uses the licensed section editor.

## Edit services

1. Open **Services** in the dashboard. These are the service records, not the Services page.
2. Update the title, main text, excerpt, and featured image.
3. Use **Order** on the service to set the fallback order.
4. Click **Update**.

The Services page shows these records. **Order** sets the sequence. Choosing a different set of services inside a page section uses the licensed section editor.

## Publish a journal article

1. Open **Posts → Add post**.
2. Enter the title and the article.
3. Set the **Excerpt**, **Featured image**, and **Categories**.
4. Optionally choose **Related articles**. If you leave that empty, the article shows others from the same category.
5. Click **Publish**.

To feature an article on the journal listing, edit **Pages → Journal** and add it under **Featured articles**. Featured articles stay out of the main grid on the first page.

## Change navigation links

1. Open **Navigation**.
2. Choose **Primary** for the header, or **Footer** for the footer.
3. Add a page from the left, drag items into order, or expand an item to rename it.
4. Click **Save Menu**.

The names and order on the site come from these menus. Renaming an item here does not rename the page.

## Update contact information

1. Open **Site Settings**.
2. Open **General Information**.
3. Edit **Email**, **Phone number**, or **Address** under **Contact details**.
4. Click **Update**.

The contact page and the footer both use these details. Fill in the Footer tab’s contact fields only when the footer should differ from the general details.

## Edit the header and footer

Open **Site Settings** and use the tabs:

- **General Information** — company name, business description, logos, and contact details
- **Header** — header logo, announcement bar, header button, and sticky header
- **Footer** — footer logo, description, footer button, and copyright
- **Social Media** — profile URLs
- **Design Options** — accent color and the header, footer, and background styles

The announcement text and link appear only when the announcement bar is enabled. Leave copyright blank to show the current year and company name.

Header and footer links are edited under **Navigation**, not in these fields. The header and footer buttons are the optional buttons set on the Header and Footer tabs.
