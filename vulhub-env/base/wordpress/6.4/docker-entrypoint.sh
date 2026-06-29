#!/bin/bash
set -Eeuo pipefail

WORDPRESS_PATH="/var/www/html"
WP_URL="${WORDPRESS_URL:-http://localhost:8088}"
WP_TITLE="${WORDPRESS_TITLE:-Vulhub Test}"
WP_ADMIN_USER="${WORDPRESS_ADMIN_USER:-admin}"
WP_ADMIN_PASSWORD="${WORDPRESS_ADMIN_PASSWORD:-admin}"
WP_ADMIN_EMAIL="${WORDPRESS_ADMIN_EMAIL:-admin@example.com}"
WP_PLUGIN_SLUG="${WORDPRESS_PLUGIN_SLUG:-}"
WP_PLUGIN_VERSION="${WORDPRESS_PLUGIN_VERSION:-}"
DEBUG_MODE="${DEBUG_MODE:-false}"

# shellcheck disable=SC1091
source /docker-cache/lib/cache.sh
ensure_wp_cli

# Start the original WordPress entrypoint in the background.
# Pre-create MU-plugin files for known plugins that need forced module loading
mkdir -p /var/www/html/wp-content/mu-plugins
# hunk-companion: import module only loads for ThemeHunk themes, force it
if [ "$WP_PLUGIN_SLUG" = "hunk-companion" ]; then
    cat > /var/www/html/wp-content/mu-plugins/load-hunk-import.php << 'MUEOF'
<?php
// Force HTTPS scheme so site_url() returns https:// (required for nuclei matcher)
$_SERVER['HTTPS'] = 'on';

add_action('rest_api_init', function() {
    $base = WP_PLUGIN_DIR . '/hunk-companion/import/';
    if (!file_exists($base . 'import.php')) return;
    require_once $base . 'import.php';
    // class-installation.php is loaded by 'init' hook in inc.php, but init has already
    // fired when rest_api_init runs - require it directly so tp_install() can instantiate
    // HUNK_COMPANION_SITES_BUILDER_SETUP without a fatal error
    if (!class_exists('HUNK_COMPANION_SITES_BUILDER_SETUP')) {
        require_once $base . 'core/class-installation.php';
    }
}, 0);
MUEOF
    echo "MU-plugin created for hunk-companion REST route"
fi

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

# Inject debug settings if DEBUG_MODE is enabled
if [ "$DEBUG_MODE" = "true" ]; then
    echo "DEBUG_MODE enabled: adding WP_DEBUG + SQL logging..."
    wp config set WP_DEBUG true --raw --allow-root 2>/dev/null || true
    wp config set WP_DEBUG_LOG true --raw --allow-root 2>/dev/null || true
    wp config set WP_DEBUG_DISPLAY true --raw --allow-root 2>/dev/null || true
    wp config set SAVEQUERIES true --raw --allow-root 2>/dev/null || true
fi

echo "Waiting for MySQL..."
# 解析 WORDPRESS_DB_HOST（格式 host:port 或纯 host）
DB_HOST="${WORDPRESS_DB_HOST%%:*}"
DB_PORT="${WORDPRESS_DB_HOST##*:}"
[ "$DB_PORT" = "$WORDPRESS_DB_HOST" ] && DB_PORT=3306

DB_READY=0
for i in $(seq 1 120); do
    # mysqladmin 不支持 host:port 格式，需分拆 -h / -P
    if mysqladmin ping -h"$DB_HOST" -P"$DB_PORT" -u"$WORDPRESS_DB_USER" -p"$WORDPRESS_DB_PASSWORD" --silent 2>/dev/null; then
        DB_READY=1
        break
    fi
    # 每 10 轮穿插一次 wp db check 作为 fallback
    if [ $((i % 10)) -eq 0 ] && wp db check --allow-root --quiet 2>/dev/null; then
        DB_READY=1
        break
    fi
    sleep 2
done

if [ "$DB_READY" != "1" ]; then
    echo "ERROR: MySQL is not ready after 240 seconds." >&2
    exit 1
fi

echo "MySQL is ready."

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

    echo "WordPress installed. Admin: $WP_ADMIN_USER / $WP_ADMIN_PASSWORD"
else
    echo "WordPress is already installed."
fi

# Configure WordPress
wp option update siteurl "$WP_URL" --allow-root >/dev/null
wp option update home "$WP_URL" --allow-root >/dev/null
wp option update permalink_structure '/%postname%/' --allow-root >/dev/null
wp rewrite flush --allow-root >/dev/null || true

# Install and activate plugin from local cache
if [ -n "$WP_PLUGIN_SLUG" ]; then
    install_plugin_from_local_cache "$WP_PLUGIN_SLUG" "${WP_PLUGIN_VERSION:-}"
    echo "Plugin $WP_PLUGIN_SLUG activated."
fi

echo "Setup complete. WordPress is running at $WP_URL"
[ "$DEBUG_MODE" = "true" ] && echo "Debug logs: /var/www/html/wp-content/debug.log"

trap - EXIT
wait "$APACHE_PID"
