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

# Install and activate plugin (with CDN fallback for closed/removed plugins)
if [ -n "$WP_PLUGIN_SLUG" ]; then
    PLUGIN_DIR="/var/www/html/wp-content/plugins/$WP_PLUGIN_SLUG"
    VERSION="${WP_PLUGIN_VERSION:-}"
    INSTALLED=0

    # Already on disk (e.g. plugin-specific base image)
    if [ -d "$PLUGIN_DIR" ]; then
        echo "Plugin $WP_PLUGIN_SLUG already present, activating..."
        wp plugin activate "$WP_PLUGIN_SLUG" --allow-root && INSTALLED=1
    fi

    # Try wp plugin install (WP.org API)
    if [ "$INSTALLED" != "1" ]; then
        echo "Installing plugin from WP.org: $WP_PLUGIN_SLUG ${VERSION:-(latest)}"
        INSTALL_CMD="wp plugin install $WP_PLUGIN_SLUG --allow-root --activate"
        [ -n "$VERSION" ] && INSTALL_CMD="$INSTALL_CMD --version=$VERSION"
        if eval "$INSTALL_CMD" 2>/dev/null; then
            INSTALLED=1
        else
            echo "WP.org API failed, trying CDN download..."
        fi
    fi

    # Fallback: download zip from WP.org CDN (handles closed/removed plugins + SSL issues)
    if [ "$INSTALLED" != "1" ] && [ -n "$VERSION" ]; then
        CDN_URL="https://downloads.wordpress.org/plugin/${WP_PLUGIN_SLUG}.${VERSION}.zip"
        CDN_FILE="/tmp/${WP_PLUGIN_SLUG}.${VERSION}.zip"
        echo "Downloading $CDN_URL"
        if curl -fsSL --retry 3 --max-time 30 -o "$CDN_FILE" "$CDN_URL"; then
            unzip -oq "$CDN_FILE" -d /var/www/html/wp-content/plugins/
            chown -R www-data:www-data "$PLUGIN_DIR" 2>/dev/null || true
            wp plugin activate "$WP_PLUGIN_SLUG" --allow-root && INSTALLED=1
            rm -f "$CDN_FILE"
        else
            echo "CDN download failed for $WP_PLUGIN_SLUG $VERSION"
        fi
    fi

    if [ "$INSTALLED" = "1" ]; then
        echo "Plugin $WP_PLUGIN_SLUG activated."
    else
        echo "WARNING: Could not install plugin $WP_PLUGIN_SLUG"
    fi
fi

echo "Setup complete. WordPress is running at $WP_URL"
[ "$DEBUG_MODE" = "true" ] && echo "Debug logs: /var/www/html/wp-content/debug.log"

trap - EXIT
wait "$APACHE_PID"
