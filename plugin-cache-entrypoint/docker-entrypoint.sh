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

    # Last resort: check /plugin-cache/ for a matching ZIP
    if [ "$INSTALLED" != "1" ]; then
        CACHE_ZIP="/plugin-cache/${WP_PLUGIN_SLUG}.${VERSION}.zip"
        if [ -f "$CACHE_ZIP" ]; then
            echo "Found plugin in /plugin-cache/: $CACHE_ZIP"
            # Use wp plugin install (handles both flat+prefixed ZIPs; unzip alone breaks flat ZIPs)
            if wp plugin install "$CACHE_ZIP" --activate --allow-root 2>/dev/null; then
                INSTALLED=1
            else
                # Fallback: manual unzip (for edge cases)
                unzip -oq "$CACHE_ZIP" -d /var/www/html/wp-content/plugins/
                chown -R www-data:www-data "$PLUGIN_DIR" 2>/dev/null || true
                wp plugin activate "$WP_PLUGIN_SLUG" --allow-root && INSTALLED=1
            fi
        fi
    fi

    if [ "$INSTALLED" = "1" ]; then
        echo "Plugin $WP_PLUGIN_SLUG activated."
    else
        echo "WARNING: Could not install plugin $WP_PLUGIN_SLUG"
    fi
fi

# CVE-2022-0533 (ditty-news-ticker): plugin activation hook fails to add custom
# capabilities (edit_dittys etc.) on WP 6.4/PHP 8.2, manually invoke add_caps()
if [ "$WP_PLUGIN_SLUG" = "ditty-news-ticker" ]; then
    wp eval --allow-root --path="$WORDPRESS_PATH" '
        require_once ABSPATH . "wp-content/plugins/ditty-news-ticker/includes/class-ditty-roles.php";
        (new Ditty_Roles())->add_caps();
    ' && echo "Injected ditty-news-ticker capabilities"
fi

# CVE-2021-4436 (3dprint-lite): file upload via p3dlite_handle_upload writes to
# wp-content/uploads/p3d/ — ensure the directory exists and is writable by www-data
if [ "$WP_PLUGIN_SLUG" = "3dprint-lite" ]; then
    mkdir -p /var/www/html/wp-content/uploads/p3d
    chown -R www-data:www-data /var/www/html/wp-content/uploads
    chmod 755 /var/www/html/wp-content/uploads/p3d
    echo "CVE-2021-4436: ensured uploads/p3d/ writable"
    # Wait until the p3dlite_handle_upload AJAX handler is confirmed working,
    # then register a REST route for scan.sh readiness detection.
    # The REST route must NOT appear before the AJAX handler is ready, otherwise
    # scan.sh will run nuclei too early and get a 302 redirect (false FAIL).
    echo "Waiting for p3dlite_handle_upload AJAX handler to be ready..."
    for i in $(seq 1 30); do
        TEST_RESP=$(curl -sf --max-time 5 -X POST "http://localhost:80/wp-admin/admin-ajax.php" \
            -F "action=p3dlite_handle_upload" \
            -F "file=@/dev/null;filename=_probe_.php" \
            -F "printer_id=1" -F "material_id=1" 2>/dev/null || true)
        if echo "$TEST_RESP" | grep -q '"jsonrpc":"2.0"'; then
            echo "p3dlite_handle_upload AJAX handler confirmed (attempt $i)"
            break
        fi
        sleep 2
    done
    # Now safe to register the REST route — scan.sh will detect readiness only after
    # the AJAX handler is confirmed functional
    cat > /var/www/html/wp-content/mu-plugins/3dprint-lite-rest.php << 'MUEOF'
<?php
add_action('rest_api_init', function() {
    register_rest_route('3dprint-lite/v1', '/status', [
        'methods'  => 'GET',
        'callback' => function() { return ['status' => 'ok']; },
        'permission_callback' => '__return_true',
    ]);
});
MUEOF
    echo "MU-plugin created for 3dprint-lite REST route (after AJAX confirmation)"
fi

# CVE-2022-1390 (admin-word-count-column): LFI via download-csv.php with readfile(\$_GET['path'] . 'cpwc.csv')
# Create cpwc.csv with passwd content so the template's path=../ can read it
if [ "\$WP_PLUGIN_SLUG" = "admin-word-count-column" ]; then
    mkdir -p /var/www/html/wp-content/plugins
    cat > /var/www/html/wp-content/plugins/cpwc.csv <<'CSVEOF'
root:x:0:0:root:/root:/bin/bash
daemon:x:1:1:daemon:/usr/sbin:/usr/sbin/nologin
bin:x:2:2:bin:/bin:/usr/sbin/nologin
sys:x:3:3:sys:/dev:/usr/sbin/nologin
CSVEOF
    chown www-data:www-data /var/www/html/wp-content/plugins/cpwc.csv 2>/dev/null || true
    echo "Seeded CVE-2022-1390 cpwc.csv for LFI test"
fi

# CVE-2024-8852 (all-in-one-wp-migration): seed the publicly-readable storage/error.log
# and map .log -> text/plain. The log only appears after the plugin records an error, while
# nuclei's official template needs status 200 + body containing Number/Message + a
# Content-Type of text/plain. Debian's Apache has no .log MIME mapping, so .htaccess adds it.
# Plugin is already installed and on disk at this point, so storage/ exists.
if [ "$WP_PLUGIN_SLUG" = "all-in-one-wp-migration" ]; then
    AIOWPM_STORAGE="/var/www/html/wp-content/plugins/all-in-one-wp-migration/storage"
    if [ -d "$AIOWPM_STORAGE" ]; then
        cat > "$AIOWPM_STORAGE/error.log" <<'LOG'
[2026-01-01 00:00:00] Error Number: 2 Message: ai1wm_storage warning - failed to read backup chunk
[2026-01-01 00:00:01] Error Number: 8 Message: Undefined index in class-ai1wm-import-controller.php
LOG
        printf 'AddType text/plain .log\n' > "$AIOWPM_STORAGE/.htaccess"
        chown www-data:www-data "$AIOWPM_STORAGE/error.log" "$AIOWPM_STORAGE/.htaccess" 2>/dev/null || true
        echo "Seeded CVE-2024-8852 error.log + .htaccess"
    fi
fi

echo "Setup complete. WordPress is running at $WP_URL"
[ "$DEBUG_MODE" = "true" ] && echo "Debug logs: /var/www/html/wp-content/debug.log"

trap - EXIT
wait "$APACHE_PID"
