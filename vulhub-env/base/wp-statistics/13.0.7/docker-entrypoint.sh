#!/bin/bash
set -Eeuo pipefail

WP_PATH="/var/www/html"
WP_URL="${WORDPRESS_URL:-http://localhost:8088}"
WP_TITLE="${WORDPRESS_TITLE:-Vulhub Test}"
WP_ADMIN_USER="${WORDPRESS_ADMIN_USER:-admin}"
WP_ADMIN_PASSWORD="${WORDPRESS_ADMIN_PASSWORD:-admin}"
WP_ADMIN_EMAIL="${WORDPRESS_ADMIN_EMAIL:-admin@example.com}"

# ── Start original WordPress entrypoint (background) ──
/usr/local/bin/docker-entrypoint-original.sh "$@" &
APACHE_PID=$!
trap 'kill $APACHE_PID 2>/dev/null || true' EXIT

echo "Waiting for WordPress files..."
until [ -f "$WP_PATH/wp-includes/version.php" ] && [ -f "$WP_PATH/wp-config.php" ]; do sleep 1; done

cd "$WP_PATH"

echo "Waiting for MySQL..."
for i in $(seq 1 60); do
    if wp db check --allow-root --quiet 2>/dev/null; then break; fi
    sleep 2
done

if ! wp core is-installed --allow-root 2>/dev/null; then
    echo "Installing WordPress..."
    wp core install --url="$WP_URL" --title="$WP_TITLE" \
        --admin_user="$WP_ADMIN_USER" --admin_password="$WP_ADMIN_PASSWORD" \
        --admin_email="$WP_ADMIN_EMAIL" --skip-email --allow-root
    echo "Admin: $WP_ADMIN_USER / $WP_ADMIN_PASSWORD"
fi

wp option update siteurl "$WP_URL" --allow-root >/dev/null
wp option update home "$WP_URL" --allow-root >/dev/null
wp option update permalink_structure '/%postname%/' --allow-root >/dev/null
wp rewrite flush --allow-root >/dev/null || true

# ── Plugin: wp-statistics 13.0.7 ──
# Plugin files are pre-copied into /var/www/html/wp-content/plugins/wp-statistics
# at Docker build time. Activate it and create required DB tables.

echo "Activating wp-statistics..."
wp plugin activate wp-statistics --allow-root 2>/dev/null || true

# Create DB tables that register_activation_hook normally handles.
# Since we activate post-boot, the hook may already have fired.
echo "Creating wp-statistics DB tables..."
wp --allow-root eval '
require_once WP_CONTENT_DIR . "/plugins/wp-statistics/includes/class-wp-statistics-db.php";
require_once WP_CONTENT_DIR . "/plugins/wp-statistics/includes/class-wp-statistics-install.php";
$installer = new \WP_STATISTICS\Install();
$installer->install(false);
' 2>/dev/null || true

# Verify activation worked
if wp plugin status wp-statistics --allow-root 2>/dev/null | grep -qi "active"; then
    echo "wp-statistics is active"
else
    echo "WARNING: wp-statistics could not be activated, trying alternative method..."
    # Fallback: re-activate
    wp plugin activate wp-statistics --allow-root 2>/dev/null || true
fi

chown -R www-data:www-data "$WP_PATH/wp-content/uploads/" 2>/dev/null || true
echo "Setup complete. WordPress: $WP_URL"
trap - EXIT; wait "$APACHE_PID"
