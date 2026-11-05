#!/usr/bin/env bash
# Install WordPress, activate Agency Starter, and create the starter pages.
# Credentials come from the gitignored .env file.

set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "${ROOT}"

if [[ ! -f .env ]]; then
  echo "Missing .env. Copy .env.example and generate salts before continuing." >&2
  exit 1
fi

set -a
# shellcheck disable=SC1091
source .env
set +a

: "${WP_HOME:?WP_HOME is required}"
: "${WP_ADMIN_USER:?WP_ADMIN_USER is required}"
: "${WP_ADMIN_PASSWORD:?WP_ADMIN_PASSWORD is required}"
: "${WP_ADMIN_EMAIL:?WP_ADMIN_EMAIL is required}"

if [[ -z "${LANDO_INFO:-}" ]] && command -v lando >/dev/null 2>&1; then
  wp() {
    lando wp "$@"
  }
fi

if wp core is-installed >/dev/null 2>&1; then
  echo "WordPress is already installed."
else
  wp core install \
    --url="${WP_HOME}" \
    --title="MAISON & FORM" \
    --admin_user="${WP_ADMIN_USER}" \
    --admin_password="${WP_ADMIN_PASSWORD}" \
    --admin_email="${WP_ADMIN_EMAIL}" \
    --skip-email
fi

wp theme activate agency-starter
wp rewrite structure '/%postname%/' --hard

if [[ ! -f web/.htaccess ]]; then
  cat > web/.htaccess <<'HTACCESS'
# BEGIN WordPress
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
RewriteBase /
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.php [L]
</IfModule>
# END WordPress
HTACCESS
fi
wp option update timezone_string 'America/New_York'
wp option update blogname 'MAISON & FORM'
wp option update blogdescription 'Agency starter'

create_page() {
  local title="$1"
  local slug="$2"
  local existing

  existing="$(wp post list --post_type=page --name="${slug}" --field=ID --format=ids | awk 'NR==1 { print $1 }')"
  if [[ -n "${existing}" ]]; then
    printf '%s\n' "${existing}"
    return
  fi

  wp post create \
    --post_type=page \
    --post_status=publish \
    --post_title="${title}" \
    --post_name="${slug}" \
    --porcelain
}

home_id="$(create_page "Home" "home")"
work_id="$(create_page "Work" "work")"
studio_id="$(create_page "Studio" "studio")"
services_id="$(create_page "Services" "services")"
journal_id="$(create_page "Journal" "journal")"
contact_id="$(create_page "Contact" "contact")"

wp option update show_on_front page
wp option update page_on_front "${home_id}"

if ! wp menu list --fields=name --format=csv | grep -qx 'Primary'; then
  wp menu create "Primary" >/dev/null
fi

item_count="$(wp menu item list Primary --format=count)"
if [[ "${item_count}" == "0" ]]; then
  for page_id in "${home_id}" "${work_id}" "${studio_id}" "${services_id}" "${journal_id}" "${contact_id}"; do
    wp menu item add-post Primary "${page_id}" >/dev/null
  done
fi

wp menu location assign Primary primary_navigation
wp rewrite flush --hard

echo "WordPress is installed."
echo "Home: ${WP_HOME}"
echo "Admin: ${WP_HOME}/wp/wp-admin/"
echo "Admin user: ${WP_ADMIN_USER}"
