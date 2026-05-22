#!/bin/bash
set -Eeuo pipefail

WORDPRESS_PATH="/var/www/html"
WP_URL="${WORDPRESS_URL:-http://localhost:8116}"
WP_TITLE="${WORDPRESS_TITLE:-Vulhub Test}"
WP_ADMIN_USER="${WORDPRESS_ADMIN_USER:-admin}"
WP_ADMIN_PASSWORD="${WORDPRESS_ADMIN_PASSWORD:-admin}"
WP_ADMIN_EMAIL="${WORDPRESS_ADMIN_EMAIL:-admin@example.com}"
WP_PLUGIN_SLUG="${WORDPRESS_PLUGIN_SLUG:-easy-elements}"
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

# Allow user registration (required for the vulnerability)
wp option update users_can_register 1 --allow-root >/dev/null
wp option update default_role subscriber --allow-root >/dev/null

# Install mu-plugin that exposes the easy_elements_nonce via REST API.
# Needed because the nonce is only output when an Elementor login-register widget renders.
mkdir -p "$WORDPRESS_PATH/wp-content/mu-plugins"
cat > "$WORDPRESS_PATH/wp-content/mu-plugins/vulhub-eel-nonce.php" << 'MUPLUGIN'
<?php
add_action('rest_api_init', function() {
    register_rest_route('vulhub/v1', '/eel-nonce', [
        'methods'             => 'GET',
        'callback'            => function() {
            return ['nonce' => wp_create_nonce('easy_elements_nonce')];
        },
        'permission_callback' => '__return_true',
    ]);
});
MUPLUGIN
chown www-data:www-data "$WORDPRESS_PATH/wp-content/mu-plugins/vulhub-eel-nonce.php"

echo "Setup complete. CVE-2026-7284 environment ready at $WP_URL"
echo "Admin: $WP_ADMIN_USER / $WP_ADMIN_PASSWORD"
echo "Nonce: GET $WP_URL/wp-json/vulhub/v1/eel-nonce"
echo "Exploit: POST /wp-admin/admin-ajax.php action=eel_register with role=administrator"

trap - EXIT
wait "$APACHE_PID"
