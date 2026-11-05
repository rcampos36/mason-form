# Local development

This project runs WordPress from Bedrock's `web` directory. Lando provides PHP, Apache, and MySQL. Composer installs WordPress core. The Agency Starter theme lives in `web/app/themes/agency-starter` and compiles CSS and JavaScript with Vite and Tailwind CSS.

## Prerequisites

Install these before the first `lando start`:

- Docker Desktop 4.x, running
- Lando 3 stable (`https://docs.lando.dev/install/macos.html`)
- Node.js active LTS (Sage accepts `^20.19.0` or `>=22.12.0`)
- PHP 8.3 or newer and Composer 2, for dependency installs on the host
- Git

Lando's appserver uses PHP 8.5. WordPress is installed inside Lando, so host WP-CLI is optional. `lando wp` is the command this project uses.

## First run

```bash
cp .env.example .env
```

Generate eight unique WordPress salts and put them in `.env`. A local generator is `https://roots.io/salts.html`. Also set `WP_ADMIN_USER`, `WP_ADMIN_PASSWORD`, and `WP_ADMIN_EMAIL`. `.env` is gitignored.

The committed `.env.example` uses Lando's local database defaults (`wordpress` / `wordpress` on host `database`). Those values are for this development recipe only.

Docker Desktop 4.98 keeps its socket at `~/.docker/run/docker.sock`. Lando's network setup still opens `/var/run/docker.sock`. Link them once, which asks for your macOS password:

```bash
sudo ln -sfn ~/.docker/run/docker.sock /var/run/docker.sock
```

Trust Lando's certificate authority once per machine, then start the app:

```bash
lando start
npm install --prefix web/app/themes/agency-starter
npm run build --prefix web/app/themes/agency-starter
./scripts/install-wordpress.sh
```

`npm run build` has to finish before the theme is opened in a browser. Sage stops the request when `public/build/manifest.json` is missing.

## URLs

| Surface | URL |
| --- | --- |
| Site | https://maison-form.lndo.site |
| Dashboard | https://maison-form.lndo.site/wp/wp-admin/ |

`lando info` prints the phpMyAdmin URL and the published database port.

The admin user and password are `WP_ADMIN_USER` and `WP_ADMIN_PASSWORD` in `.env`.

The install script sets:

- theme: Agency Starter (`agency-starter`)
- permalinks: `/%postname%/`
- timezone: `America/New_York`
- static front page: Home
- primary menu: Home, Work, Studio, Services, Journal, Contact
- footer menu: the same pages, editable under Appearance → Menus

Journal is the posts page. Its listing uses `home.blade.php`. Category archives use `archive.blade.php`, and articles use `single.blade.php`. Home uses `front-page.blade.php`. Work, Studio, Services, and Contact use selectable page templates. Projects, services, team members, and project categories are registered by `web/app/mu-plugins/heartwell-content.php`. The contact form is Contact Form 7; the page stores only the form ID.

## Day to day

```bash
lando start
lando stop
lando restart
lando wp plugin list
lando wp theme list
lando mysql
composer install
composer update
npm install --prefix web/app/themes/agency-starter
npm run dev --prefix web/app/themes/agency-starter
npm run build --prefix web/app/themes/agency-starter
```

Run Composer from the repository root for WordPress core, plugins, and the Bedrock stack. Run Composer inside `web/app/themes/agency-starter` for Acorn and other theme PHP dependencies. Sage's `functions.php` loads that theme-local `vendor/autoload.php`.

`npm run dev` starts Vite at `http://127.0.0.1:5173`. Use `npm run build` when you want the version browsers load from the theme's `public/build` directory.

Pretty permalinks need `web/.htaccess`. That file is gitignored. `scripts/install-wordpress.sh` writes the Bedrock rewrite rules when the file is missing. Apache in the Lando appserver has `AllowOverride All` and `mod_rewrite` enabled.

If `lando start` stops with `is not connected to the network lando_bridge_network`, Docker's current error text does not match the string Lando 3.26.9 expects. Create the network, attach the running containers, and start again:

```bash
docker network create lando_bridge_network 2>/dev/null || true
for id in $(docker ps -q); do
  docker network connect lando_bridge_network "$id" || true
done
lando start
```

## Layout

```text
config/                         Bedrock configuration
web/                            document root
web/index.php                   front controller
web/wp-config.php               WordPress bootstrap
web/wp/                         WordPress core (Composer, not committed)
web/app/themes/agency-starter   Agency Starter theme
web/app/plugins                 Composer-installed plugins
web/app/mu-plugins              must-use plugins
scripts/install-wordpress.sh    local site installer
```

Blade templates are in `web/app/themes/agency-starter/resources/views`. `layouts/app.blade.php` is the base layout. `page.blade.php` renders pages. Acorn registers those views through `App\Providers\ThemeServiceProvider`. Tailwind v4 is loaded from `resources/css/app.css` with `@tailwindcss/vite`. The Vite `base` path is `/app/themes/agency-starter/public/build/`.

## Dependencies

Add a WordPress.org plugin from the repository root:

```bash
composer require wp-plugin/plugin-slug
```

Do not add Elementor, Divi, Bootstrap, or another page builder. Tailwind CSS is the styling framework.

Free Advanced Custom Fields is installed with Composer:

```bash
composer require wp-plugin/advanced-custom-fields
```

Page sections use ACF PRO Flexible Content. The field group is in `web/app/themes/agency-starter/app/fields/Sections.php`. Install and editing steps are in `docs/sections.md`. Global details are edited at Dashboard → Site Settings. Projects, services, and team members use their own admin menus. Titles, excerpts, body content, featured images, and menus stay in the normal WordPress fields.

## Database

`.lando.yml` pins MySQL 8.0. From the host, `lando mysql` opens the client. `lando info` prints the published port and credentials. Inside the app container the host name is `database`.

Switching the recipe to MariaDB means changing `config.database` in `.lando.yml`, running `lando rebuild`, and keeping `.env` on the same database name, user, and password.

## Git

`.env`, WordPress core, uploaded media, Composer `vendor` directories, `node_modules`, and the Vite build are ignored. Theme PHP, Blade, CSS, and JavaScript source stay in Git. After a fresh clone, run Composer and `npm run build` before opening the site.
