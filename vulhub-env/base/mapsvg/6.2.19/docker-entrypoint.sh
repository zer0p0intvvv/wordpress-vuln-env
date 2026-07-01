#!/bin/bash
set -Eeuo pipefail

WP_PATH="/var/www/html"
WP_URL="${WORDPRESS_URL:-http://localhost:8192}"
WP_TITLE="${WORDPRESS_TITLE:-Vulhub Test}"
WP_ADMIN_USER="${WORDPRESS_ADMIN_USER:-admin}"
WP_ADMIN_PASSWORD="${WORDPRESS_ADMIN_PASSWORD:-admin}"
WP_ADMIN_EMAIL="${WORDPRESS_ADMIN_EMAIL:-admin@example.com}"

# 启动原始 WordPress entrypoint（后台）
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

# ── 安装并激活插件 ──
PLUGIN_SLUG="mapsvg"
PLUGIN_VERSION="6.2.19"
PLUGIN_DIR="$WP_PATH/wp-content/plugins/$PLUGIN_SLUG"
ZIP="/tmp/${PLUGIN_SLUG}.${PLUGIN_VERSION}.zip"
INSTALLED=0

# ① 本地 zip 预下载（Dockerfile 已缓存）
if [ ! -d "$PLUGIN_DIR" ] && [ -f "$ZIP" ]; then
    unzip -oq "$ZIP" -d "$WP_PATH/wp-content/plugins/"
    chown -R www-data:www-data "$PLUGIN_DIR" 2>/dev/null || true
    wp plugin activate "$PLUGIN_SLUG" --allow-root && INSTALLED=1
    rm -f "$ZIP"
fi

# ② 本地已有目录
if [ "$INSTALLED" != "1" ] && [ -d "$PLUGIN_DIR" ]; then
    wp plugin activate "$PLUGIN_SLUG" --allow-root && INSTALLED=1
fi

# ── 预创建地图 post（确保 REST endpoint 返回有效 JSON 响应）──
wp --allow-root eval '
$exists = get_posts(["post_type" => "mapsvg", "posts_per_page" => 1, "post_status" => "any"]);
if (empty($exists)) {
    wp_insert_post(["post_title" => "Sample Map", "post_type" => "mapsvg", "post_status" => "publish"]);
    wp_insert_post(["post_title" => "Sample Map 2", "post_type" => "mapsvg", "post_status" => "publish"]);
}
' 2>/dev/null || true

chown -R www-data:www-data "$WP_PATH/wp-content/uploads/" 2>/dev/null || true
echo "Setup complete. WordPress: $WP_URL"
trap - EXIT; wait "$APACHE_PID"
