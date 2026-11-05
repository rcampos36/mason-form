# Deployment

Production installs WordPress, plugins, and the theme through Composer, then serves the Vite production build. Do not ship the Vite dev server.

## Before the first production deploy

1. Set `WP_ENV` to `production` in the server environment. `config/environments/development.php` is the only environment file that turns on `WP_DEBUG_DISPLAY`, `SAVEQUERIES`, and `SCRIPT_DEBUG`, and the only one that sets `DISALLOW_FILE_MODS` to false. Any other `WP_ENV` leaves debug display off and blocks plugin and theme installs from wp-admin.
2. Generate unique WordPress salts. `.env.example` ships placeholder salts and local database defaults. Do not copy those values to production.
3. Keep `.env` and `auth.json` off the git remote. Both are gitignored.
4. Install a licensed copy of ACF PRO if editors need to add, remove, or reorder page sections or edit galleries and repeaters in wp-admin. The free plugin is what Composer installs today (`wp-plugin/advanced-custom-fields`). PRO is installed with Composer using `auth.json` against `https://connect.advancedcustomfields.com`. Stored section content already renders without PRO. Do not paste a license key into the theme or into git.
5. Run `composer install --no-dev` on the server (or in the build artifact). Dev tools such as Pest and Pint stay on local machines.
6. In `web/app/themes/agency-starter`, run `npm ci` and `npm run build`. Confirm `public/build/manifest.json` exists.
7. Delete `web/app/themes/agency-starter/public/hot` if it was copied from a machine that had `npm run dev` running. That file points WordPress at `http://127.0.0.1:5173`.
8. Set the document root to `web/` (Bedrock). Permalinks are `/%postname%/`. The admin is under `/wp/wp-admin/`.
9. Flush permalinks once after deploy: `wp rewrite flush`.

## Caching

No page-cache plugin is installed. On production, add one of these:

- A full-page cache in front of PHP (the host’s cache, or a plugin that respects Bedrock’s `web/` root). Bypass it for logged-in users and for `POST` requests so the contact form and comments stay live.
- A persistent object cache (Redis) if the host provides it. The theme’s per-project `get_field` calls are bounded (the portfolio query is capped at 24) and no query slower than 50 ms was recorded in development, so an object cache is useful under traffic and is not required to fix a measured slow query.

Do not enable a page cache in the Lando development environment. `WP_DEBUG` and `SAVEQUERIES` are on there on purpose.

## What stays off in production

- `public/hot` and the Vite dev server
- `WP_DEBUG_DISPLAY`
- Theme and plugin file editing (`DISALLOW_FILE_EDIT` is true in `config/application.php` for every environment)
- Installing plugins from wp-admin (`DISALLOW_FILE_MODS` stays true outside development). Add plugins with Composer and redeploy.

## Contact form

Contact Form 7 sends to `[_site_admin_email]`. Set the site admin email to a real inbox before launch, and confirm the host can send mail. The theme only loads Contact Form 7 assets on the Contact page and the section library.
