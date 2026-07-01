#!/bin/bash
set -Eeuo pipefail

WP_PATH="/var/www/html"
WP_URL="${WORDPRESS_URL:-http://localhost:8088}"
WP_TITLE="${WORDPRESS_TITLE:-Vulhub Test}"
WP_ADMIN_USER="${WORDPRESS_ADMIN_USER:-admin}"
WP_ADMIN_PASSWORD="${WORDPRESS_ADMIN_PASSWORD:-admin}"
WP_ADMIN_EMAIL="${WORDPRESS_ADMIN_EMAIL:-admin@example.com}"
PLUGIN_SLUG="${PLUGIN_SLUG:-}"
PLUGIN_VERSION="${PLUGIN_VERSION:-}"

# Start the original WordPress entrypoint in the background.
/usr/local/bin/docker-entrypoint-original.sh "$@" &
APACHE_PID=$!
trap 'kill $APACHE_PID 2>/dev/null || true' EXIT

echo "Waiting for WordPress files..."
until [ -f "$WP_PATH/wp-includes/version.php" ] && [ -f "$WP_PATH/wp-config.php" ]; do
    sleep 1
done

cd "$WP_PATH"

echo "Waiting for MySQL..."
DB_READY=0
for i in $(seq 1 60); do
    if wp db check --allow-root --quiet 2>/dev/null; then
        DB_READY=1
        break
    fi
    sleep 2
done

if [ "$DB_READY" != "1" ]; then
    echo "ERROR: MySQL is not ready after 120 seconds." >&2
    exit 1
fi

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

# 激活插件（已预拷贝到 plugins/ 目录）
if [ -n "$PLUGIN_SLUG" ]; then
    if [ -d "$WP_PATH/wp-content/plugins/$PLUGIN_SLUG" ]; then
        echo "Activating plugin: $PLUGIN_SLUG (pre-copied)"
        wp plugin activate "$PLUGIN_SLUG" --allow-root || true
    fi
fi

echo "Setup complete. WordPress is running at $WP_URL"
trap - EXIT
wait "$APACHE_PID"
