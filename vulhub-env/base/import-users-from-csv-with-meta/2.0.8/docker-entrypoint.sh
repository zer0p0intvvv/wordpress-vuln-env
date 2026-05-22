#!/bin/bash
set -Eeuo pipefail

WORDPRESS_PATH="/var/www/html"
WP_URL="${WORDPRESS_URL:-http://localhost:8120}"
WP_TITLE="${WORDPRESS_TITLE:-Vulhub Test}"
WP_ADMIN_USER="${WORDPRESS_ADMIN_USER:-admin}"
WP_ADMIN_PASSWORD="${WORDPRESS_ADMIN_PASSWORD:-admin}"
WP_ADMIN_EMAIL="${WORDPRESS_ADMIN_EMAIL:-admin@example.com}"
WP_PLUGIN_SLUG="${WORDPRESS_PLUGIN_SLUG:-import-users-from-csv-with-meta}"
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

# --- Multisite setup ---
echo "Converting to WordPress Multisite (subdirectory mode)..."
wp core multisite-convert --title="Vulhub Network" --allow-root 2>/dev/null || true

# Write multisite .htaccess rules
cat > "$WORDPRESS_PATH/.htaccess" << 'HTACCESS'
RewriteEngine On
RewriteBase /
RewriteRule ^index\.php$ - [L]

# Multisite subdirectory rules
RewriteRule ^([_0-9a-zA-Z-]+/)?wp-admin$ $1wp-admin/ [R=301,L]

RewriteCond %{REQUEST_FILENAME} -f [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^ - [L]
RewriteRule ^([_0-9a-zA-Z-]+/)?(wp-(content|admin|includes).*) $2 [L]
RewriteRule ^([_0-9a-zA-Z-]+/)?(.*\.php)$ $2 [L]
RewriteRule . index.php [L]
HTACCESS

wp rewrite flush --allow-root >/dev/null || true

# Create subsite (blog ID 2) — this is the target site for privilege escalation
echo "Creating subsite..."
wp site create --slug=subsite --title="Subsite 2" --email="$WP_ADMIN_EMAIL" --allow-root 2>/dev/null || \
    echo "Note: subsite may already exist"

# Activate plugin network-wide so it's available on all sites
if wp plugin list --allow-root --field=name | grep -qx "$WP_PLUGIN_SLUG"; then
    wp plugin activate "$WP_PLUGIN_SLUG" --network --allow-root || \
    wp plugin activate "$WP_PLUGIN_SLUG" --allow-root || true
fi

# Simulate prerequisite: admin imports CSV containing wp_2_capabilities column.
# This stores the column header in acui_columns option (plugin tracks imported columns)
# and enables "Show fields in profile?" so subscribers see the field in their profile.
wp eval '
$columns = ["user_login", "user_email", "user_pass", "role", "wp_2_capabilities"];
update_option("acui_columns", $columns);
update_option("acui_show_fields_profile", "yes");
echo "acui_columns set: " . implode(", ", $columns) . "\n";
echo "acui_show_fields_profile: yes\n";
' --allow-root 2>/dev/null || echo "Note: acui option setup may need verification"

# Create subscriber user for privilege escalation attack
echo "Creating subscriber user..."
wp user create subscriber subscriber@example.com \
    --role=subscriber --user_pass=subscriber --allow-root 2>/dev/null || true

echo "Setup complete. CVE-2026-7641 environment ready at $WP_URL"
echo "Admin:      $WP_ADMIN_USER / $WP_ADMIN_PASSWORD"
echo "Subscriber: subscriber / subscriber"
echo "Subsite:    $WP_URL/subsite/"
echo "Exploit: Login as subscriber, POST /wp-admin/profile.php with wp_2_capabilities=a:1:{s:13:\"administrator\";b:1;}"

trap - EXIT
wait "$APACHE_PID"
