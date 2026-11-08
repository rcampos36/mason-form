#!/usr/bin/env bash
# Copy this project to the Hostinger site.
#
#   scripts/deploy.sh           theme build, code, and compiled templates
#   scripts/deploy.sh content   local images and database
#   scripts/deploy.sh all       both, code first

set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "${ROOT}"

SSH_KEY="${DEPLOY_SSH_KEY:-${HOME}/.ssh/maison_form_deploy}"
SSH_HOST="${DEPLOY_HOST:-157.173.209.200}"
SSH_PORT="${DEPLOY_PORT:-65002}"
SSH_USER="${DEPLOY_USER:-u644719343}"
REMOTE_ROOT="${DEPLOY_PATH:-/home/u644719343/domains/darkgrey-elk-814338.hostingersite.com/maison-form}"
REMOTE_PHP="${DEPLOY_PHP:-/opt/alt/php84/usr/bin/php}"
REMOTE_WP="${DEPLOY_WP:-/usr/local/bin/wp}"
LOCAL_URL="${DEPLOY_LOCAL_URL:-https://maison-form.lndo.site}"
LIVE_URL="${DEPLOY_LIVE_URL:-https://darkgrey-elk-814338.hostingersite.com}"

REMOTE="${SSH_USER}@${SSH_HOST}"
RSYNC_RSH="ssh -i ${SSH_KEY} -p ${SSH_PORT} -o BatchMode=yes"

ssh_remote() {
  ssh -i "${SSH_KEY}" -p "${SSH_PORT}" -o BatchMode=yes "${REMOTE}" "$@"
}

require_key() {
  if [[ ! -f "${SSH_KEY}" ]]; then
    echo "Missing SSH key: ${SSH_KEY}" >&2
    exit 1
  fi
}

deploy_code() {
  require_key

  if [[ ! -f vendor/autoload.php || ! -f web/wp/wp-load.php || ! -f web/app/themes/agency-starter/vendor/autoload.php ]]; then
    echo "Install Composer dependencies and WordPress before deploying." >&2
    exit 1
  fi

  echo "Building the theme"
  npm run build --prefix web/app/themes/agency-starter

  if [[ ! -f web/app/themes/agency-starter/public/build/manifest.json ]]; then
    echo "Theme build did not write public/build/manifest.json" >&2
    exit 1
  fi

  echo "Copying files"
  rsync -az --delete \
    --exclude '.git' \
    --exclude '.github' \
    --exclude '.env' \
    --exclude 'auth.json' \
    --exclude 'node_modules' \
    --exclude 'web/app/uploads' \
    --exclude 'web/app/cache' \
    --exclude 'web/app/themes/agency-starter/public/hot' \
    -e "${RSYNC_RSH}" \
    ./ "${REMOTE}:${REMOTE_ROOT}/"

  echo "Clearing compiled templates"
  ssh_remote "cd '${REMOTE_ROOT}' && '${REMOTE_PHP}' '${REMOTE_WP}' acorn view:clear && rm -f '${REMOTE_ROOT}/web/app/themes/agency-starter/public/hot'"

  echo "Live code updated: ${LIVE_URL}"
}

deploy_content() {
  require_key

  echo "This copies local images and replaces the live database."
  echo "Content that exists only on the live site will be replaced."
  printf "Type yes to continue: "
  read -r answer

  if [[ "${answer}" != "yes" ]]; then
    echo "Stopped."
    exit 1
  fi

  echo "Copying images"
  rsync -az \
    -e "${RSYNC_RSH}" \
    web/app/uploads/ \
    "${REMOTE}:${REMOTE_ROOT}/web/app/uploads/"

  dump="$(mktemp /tmp/maison-form.XXXXXX.sql)"

  echo "Exporting the local database"
  lando wp db export - > "${dump}"

  if [[ ! -s "${dump}" ]] || ! head -n 1 "${dump}" | grep -q "MySQL dump"; then
    rm -f "${dump}"
    echo "Database export did not look like a MySQL dump." >&2
    exit 1
  fi

  echo "Importing on the live site"
  scp -P "${SSH_PORT}" -i "${SSH_KEY}" -o BatchMode=yes \
    "${dump}" \
    "${REMOTE}:/home/${SSH_USER}/maison-form.sql"

  ssh_remote "cd '${REMOTE_ROOT}' && '${REMOTE_PHP}' '${REMOTE_WP}' db import '/home/${SSH_USER}/maison-form.sql' && '${REMOTE_PHP}' '${REMOTE_WP}' search-replace '${LOCAL_URL}' '${LIVE_URL}' --all-tables && '${REMOTE_PHP}' '${REMOTE_WP}' search-replace 'http://maison-form.lndo.site' '${LIVE_URL}' --all-tables && rm -f '/home/${SSH_USER}/maison-form.sql'"

  rm -f "${dump}"
  echo "Live content updated: ${LIVE_URL}"
}

case "${1:-code}" in
  code)
    deploy_code
    ;;
  content)
    deploy_content
    ;;
  all)
    deploy_code
    deploy_content
    ;;
  *)
    echo "Use: scripts/deploy.sh [code|content|all]" >&2
    exit 1
    ;;
esac
