#!/bin/bash
set -Eeuo pipefail

DOCKER_CACHE_ROOT="${DOCKER_CACHE_ROOT:-/docker-cache}"
DOCKER_CACHE_PLUGIN_DIR="${DOCKER_CACHE_PLUGIN_DIR:-$DOCKER_CACHE_ROOT/plugins}"
DOCKER_CACHE_TOOL_DIR="${DOCKER_CACHE_TOOL_DIR:-$DOCKER_CACHE_ROOT/tools}"

cache_error() {
    echo "ERROR: $*" >&2
    return 1
}

ensure_docker_cache() {
    [ -d "$DOCKER_CACHE_ROOT" ] || cache_error "docker cache is not mounted at $DOCKER_CACHE_ROOT"
}

ensure_wp_cli() {
    ensure_docker_cache

    if command -v wp >/dev/null 2>&1; then
        return 0
    fi

    if [ -x "$DOCKER_CACHE_TOOL_DIR/wp" ]; then
        install -m 0755 "$DOCKER_CACHE_TOOL_DIR/wp" /usr/local/bin/wp
        return 0
    fi

    if [ -f "$DOCKER_CACHE_TOOL_DIR/wp-cli.phar" ]; then
        cat > /usr/local/bin/wp <<'EOF'
#!/bin/sh
exec php /docker-cache/tools/wp-cli.phar "$@"
EOF
        chmod +x /usr/local/bin/wp
        return 0
    fi

    cache_error "missing cached WP-CLI at $DOCKER_CACHE_TOOL_DIR/wp-cli.phar"
}

activate_plugin_from_disk() {
    local slug="$1"
    local scope="${2:-site}"

    if ! wp plugin is-installed "$slug" --allow-root >/dev/null 2>&1; then
        return 1
    fi

    if [ "$scope" = "network" ]; then
        wp plugin activate "$slug" --network --allow-root >/dev/null
    else
        wp plugin activate "$slug" --allow-root >/dev/null
    fi
}

install_plugin_from_local_cache() {
    local slug="$1"
    local version="${2:-}"
    local scope="${3:-site}"

    [ -n "$slug" ] || return 0

    ensure_wp_cli

    if activate_plugin_from_disk "$slug" "$scope"; then
        echo "Plugin $slug already present on disk, activated via local files."
        return 0
    fi

    [ -n "$version" ] || cache_error "plugin $slug is not present on disk and no version was provided"

    local plugin_dir="$DOCKER_CACHE_PLUGIN_DIR/$slug/$version"
    if [ ! -d "$plugin_dir" ]; then
        cache_error "missing cached plugin directory: $plugin_dir (expected at $plugin_dir)"
    fi

    local target="/var/www/html/wp-content/plugins/$slug"
    echo "Installing plugin from cache directory: $slug $version"
    if [ -d "$target" ]; then
        rm -rf "$target"
    fi
    cp -a "$plugin_dir" "$target"
    chown -R www-data:www-data "$target"

    if [ "$scope" = "network" ]; then
        wp plugin activate "$slug" --network --allow-root >/dev/null
    else
        wp plugin activate "$slug" --allow-root >/dev/null
    fi
}
