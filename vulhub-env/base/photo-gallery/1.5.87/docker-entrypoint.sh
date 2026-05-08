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

# Install and activate plugin
if [ -n "$WP_PLUGIN_SLUG" ]; then
    INSTALL_CMD="wp plugin install $WP_PLUGIN_SLUG --allow-root"
    if [ -n "$WP_PLUGIN_VERSION" ]; then
        INSTALL_CMD="$INSTALL_CMD --version=$WP_PLUGIN_VERSION"
    fi
    INSTALL_CMD="$INSTALL_CMD --activate"

    echo "Installing plugin: $WP_PLUGIN_SLUG ${WP_PLUGIN_VERSION:-(latest)}"
    eval "$INSTALL_CMD" || true
fi

# Create sample gallery for SQLi testing (CVE-2022-0169)
echo "Creating sample gallery..."
wp db query "INSERT INTO wp_bwg_gallery (name, published, gallery_type) VALUES ('Test Gallery', 1, 'thumbnail')" --allow-root 2>/dev/null || true
wp db query "INSERT INTO wp_bwg_shortcode (id, tagtext) VALUES (1, '[Best_Wordpress_Gallery id=\"1\"]')" --allow-root 2>/dev/null || true

echo "Setup complete. WordPress is running at $WP_URL"
[ "$DEBUG_MODE" = "true" ] && echo "Debug logs: /var/www/html/wp-content/debug.log"

trap - EXIT
wait "$APACHE_PID"
