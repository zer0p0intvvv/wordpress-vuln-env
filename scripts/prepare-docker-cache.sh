#!/bin/bash
set -Eeuo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
CACHE_DIR="$ROOT_DIR/docker-cache"
PLUGIN_DIR="$CACHE_DIR/plugins"
TOOL_DIR="$CACHE_DIR/tools"
WP_CLI_URL="https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar"

mkdir -p "$PLUGIN_DIR" "$TOOL_DIR"
chmod +x "$TOOL_DIR/wp"

download_plugin() {
    local slug="$1" version="$2"
    local target_dir="$PLUGIN_DIR/$slug/$version"
    local url="https://downloads.wordpress.org/plugin/${slug}.${version}.zip"

    if [ -d "$target_dir" ]; then
        echo "cache hit: $target_dir"
        return 0
    fi

    echo "downloading: $url"
    local tmp_zip
    tmp_zip=$(mktemp /tmp/plugin-XXXXXX.zip)
    # --noproxy '*' 绕过本地代理（Clash/V2ray 等）对 downloads.wordpress.org 的 TLS 隧道干扰
    if curl -fsSL --retry 5 --retry-delay 2 --noproxy '*' -o "$tmp_zip" "$url"; then
        mkdir -p "$target_dir"
        unzip -qo "$tmp_zip" -d "$(dirname "$target_dir")"
        # WP.org zip 顶层是 slug/ 目录，挪到 version 子目录
        if [ -d "$(dirname "$target_dir")/$slug" ]; then
            mv "$(dirname "$target_dir")/$slug"/* "$target_dir/"
            rm -rf "$(dirname "$target_dir")/$slug"
        fi
        rm -f "$tmp_zip"
        echo "  extracted → $target_dir/"
    else
        echo "  WARN: 下载失败 (plugin 可能已从 WP.org 下架)"
        rm -f "$tmp_zip"
    fi
}

collect_compose_plugins() {
    find "$ROOT_DIR/vulhub-env" -name docker-compose.yml -print | while read -r compose; do
        awk '
            /WORDPRESS_PLUGIN_SLUG:|PLUGIN_SLUG:/ {
                slug=$NF
                gsub(/"/, "", slug)
            }
            /WORDPRESS_PLUGIN_VERSION:|PLUGIN_VERSION:/ {
                version=$NF
                gsub(/"/, "", version)
                if (slug != "" && slug != "\"\"" && version != "" && version != "\"\"") {
                    print slug " " version
                }
                slug=""
                version=""
            }
        ' "$compose"
    done
}

collect_inline_plugins() {
    rg -No --no-filename 'wp plugin install ([a-z0-9-]+) --version=([0-9][A-Za-z0-9.\-]*)' \
        "$ROOT_DIR/vulhub-env" \
        -g 'docker-entrypoint.sh' 2>/dev/null || true \
        | sed -E 's#wp plugin install ([a-z0-9-]+) --version=([0-9A-Za-z.\-]+)#\1 \2#'
}

collect_archive_plugins() {
    rg -No --no-filename 'downloads\.wordpress\.org/plugin/([a-z0-9-]+)\.([0-9][A-Za-z0-9.\-]*)\.zip' \
        "$ROOT_DIR/vulhub-env" "$ROOT_DIR/templates" \
        -g 'Dockerfile' -g 'docker-entrypoint.sh' 2>/dev/null || true \
        | sed -E 's#downloads\.wordpress\.org/plugin/([a-z0-9-]+)\.([0-9A-Za-z.\-]*)\.zip#\1 \2#'
}

# WP-CLI phar：直接下载到 tools/，不解压
if [ ! -f "$TOOL_DIR/wp-cli.phar" ]; then
    echo "downloading wp-cli: $WP_CLI_URL"
    curl -fsSL --retry 3 --noproxy '*' -o "$TOOL_DIR/wp-cli.phar" "$WP_CLI_URL" || true
else
    echo "cache hit: $TOOL_DIR/wp-cli.phar"
fi

{
    collect_compose_plugins
    collect_inline_plugins
    collect_archive_plugins
} | awk 'NF == 2 { print $1, $2 }' | sort -u | while read -r slug version; do
    download_plugin "$slug" "$version"
done

echo "docker cache ready: $CACHE_DIR"
