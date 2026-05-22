#!/bin/bash
set -Eeuo pipefail

WORDPRESS_PATH="/var/www/html"
WP_URL="${WORDPRESS_URL:-http://localhost:8118}"
WP_TITLE="${WORDPRESS_TITLE:-Vulhub Test}"
WP_ADMIN_USER="${WORDPRESS_ADMIN_USER:-admin}"
WP_ADMIN_PASSWORD="${WORDPRESS_ADMIN_PASSWORD:-admin}"
WP_ADMIN_EMAIL="${WORDPRESS_ADMIN_EMAIL:-admin@example.com}"
WP_PLUGIN_SLUG="${WORDPRESS_PLUGIN_SLUG:-wp-optimize}"
DEBUG_MODE="${DEBUG_MODE:-false}"

/usr/local/bin/docker-entrypoint-original.sh "$@" &
APACHE_PID=$!

cleanup() {
    if kill -0 "$APACHE_PID" 2>/dev/null; then
        kill "$APACHE_PID" 2>/dev/null || true
    fi
}
trap cleanup EXIT

echo "Waiting for WordPress files..."
until [ -f "$WORDPRESS_PATH/wp-includes/version.php" ] && [ -f "$WORDPRESS_PATH/wp-config.php" ]; do
    sleep 1
done

cd "$WORDPRESS_PATH"

if [ "$DEBUG_MODE" = "true" ]; then
    wp config set WP_DEBUG true --raw --allow-root 2>/dev/null || true
    wp config set WP_DEBUG_LOG true --raw --allow-root 2>/dev/null || true
    wp config set WP_DEBUG_DISPLAY true --raw --allow-root 2>/dev/null || true
fi

echo "Waiting for MySQL..."
DB_READY=0
for i in $(seq 1 60); do
    if wp db check --allow-root --quiet 2>/dev/null; then
        DB_READY=1; break
    fi
    sleep 2
done
[ "$DB_READY" != "1" ] && echo "ERROR: MySQL not ready" >&2 && exit 1

if ! wp core is-installed --allow-root 2>/dev/null; then
    echo "Installing WordPress..."
    wp core install \
        --url="$WP_URL" \
        --title="$WP_TITLE" \
        --admin_user="$WP_ADMIN_USER" \
        --admin_password="$WP_ADMIN_PASSWORD" \
        --admin_email="$WP_ADMIN_EMAIL" \
        --skip-email \
        --allow-root
fi

wp option update siteurl "$WP_URL" --allow-root >/dev/null
wp option update home "$WP_URL" --allow-root >/dev/null
wp option update permalink_structure '/%postname%/' --allow-root >/dev/null
wp rewrite flush --allow-root >/dev/null || true

# Activate plugin (bundled in image)
if wp plugin list --allow-root --field=name | grep -qx "$WP_PLUGIN_SLUG"; then
    wp plugin activate "$WP_PLUGIN_SLUG" --allow-root || true
else
    echo "WARNING: Plugin $WP_PLUGIN_SLUG not found" >&2
fi

# Create author user for file deletion testing
echo "Creating author user..."
wp user create author author@example.com \
    --role=author --user_pass=author --allow-root 2>/dev/null || true

# Pre-create a canary file as the deletion target for nuclei verification
CANARY_DIR="$WORDPRESS_PATH/wp-content/uploads/wpo_canary"
mkdir -p "$CANARY_DIR"
echo "wpo-canary-file" > "$CANARY_DIR/canary.txt"
chown -R www-data:www-data "$CANARY_DIR"

echo "Setup complete. CVE-2026-7252 environment ready at $WP_URL"
echo "Admin:  $WP_ADMIN_USER / $WP_ADMIN_PASSWORD"
echo "Author: author / author"
echo "Exploit: Login as author, set original-file meta on attachment to /var/www/html/wp-config.php, trigger cleanup"

trap - EXIT
wait "$APACHE_PID"
