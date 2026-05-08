#!/bin/bash
# vulhub-env 一键管理脚本
# Usage: ./start.sh [start|stop|restart|status|build|logs] [CVE-ID]

set -euo pipefail
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"

# 颜色
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
NC='\033[0m'

# 查找所有 CVE 环境目录
find_envs() {
    find "$SCRIPT_DIR" -mindepth 3 -maxdepth 3 -name "docker-compose.yml" -path "*/CVE-*/*" | sort | while read f; do
        dirname "$f"
    done
}

# 解析环境信息
get_info() {
    local dir="$1"
    local cve=$(basename "$dir")
    local plugin=$(basename "$(dirname "$dir")")
    local port=$(grep -oP '(?<=- ")\d+(?=:80")' "$dir/docker-compose.yml" 2>/dev/null || echo "?")
    echo "$cve|$plugin|$port|$dir"
}

# 启动
do_start() {
    local target="${1:-}"
    echo -e "${CYAN}[*] Starting environments...${NC}"
    local count=0
    while IFS= read -r dir; do
        local info=$(get_info "$dir")
        local cve=$(echo "$info" | cut -d'|' -f1)
        if [[ -n "$target" && "$cve" != "$target" ]]; then continue; fi
        local plugin=$(echo "$info" | cut -d'|' -f2)
        local port=$(echo "$info" | cut -d'|' -f3)
        echo -e "  ${GREEN}→${NC} $cve ($plugin) :$port"
        (cd "$dir" && docker compose up -d --build 2>&1 | tail -1) &
        ((count++))
    done < <(find_envs)
    wait
    echo -e "${GREEN}[+] Started $count environments${NC}"
}

# 停止
do_stop() {
    local target="${1:-}"
    echo -e "${CYAN}[*] Stopping environments...${NC}"
    while IFS= read -r dir; do
        local info=$(get_info "$dir")
        local cve=$(echo "$info" | cut -d'|' -f1)
        if [[ -n "$target" && "$cve" != "$target" ]]; then continue; fi
        echo -e "  ${RED}×${NC} $cve"
        (cd "$dir" && docker compose down 2>&1 | tail -1) &
    done < <(find_envs)
    wait
    echo -e "${GREEN}[+] Done${NC}"
}

# 状态
do_status() {
    echo -e "${CYAN}[*] Environment status:${NC}"
    printf "  %-20s %-35s %-8s %-10s\n" "CVE" "Plugin" "Web" "Status"
    printf "  %-20s %-35s %-8s %-10s\n" "---" "------" "---" "------"
    while IFS= read -r dir; do
        local info=$(get_info "$dir")
        local cve=$(echo "$info" | cut -d'|' -f1)
        local plugin=$(echo "$info" | cut -d'|' -f2)
        local port=$(echo "$info" | cut -d'|' -f3)
        local status=$(cd "$dir" && docker compose ps --format json 2>/dev/null | head -1 | python3 -c "import sys,json; d=json.load(sys.stdin); print(d.get('State','stopped'))" 2>/dev/null || echo "stopped")
        local color="$RED"
        [[ "$status" == "running" ]] && color="$GREEN"
        printf "  %-20s %-35s %-8s ${color}%-10s${NC}\n" "$cve" "$plugin" ":$port" "$status"
    done < <(find_envs)
}

# 构建
do_build() {
    echo -e "${CYAN}[*] Building images...${NC}"
    while IFS= read -r dir; do
        local cve=$(basename "$dir")
        echo -e "  ${YELLOW}⚙${NC} $cve"
        (cd "$dir" && docker compose build 2>&1 | tail -1) &
    done < <(find_envs)
    wait
    echo -e "${GREEN}[+] Build complete${NC}"
}

# 日志
do_logs() {
    local target="${1:-}"
    if [[ -z "$target" ]]; then
        echo -e "${RED}[!] Usage: ./start.sh logs <CVE-ID>${NC}"
        exit 1
    fi
    while IFS= read -r dir; do
        local cve=$(basename "$dir")
        if [[ "$cve" == "$target" ]]; then
            (cd "$dir" && docker compose logs -f)
            return
        fi
    done < <(find_envs)
    echo -e "${RED}[!] CVE not found: $target${NC}"
}

# 主逻辑
case "${1:-status}" in
    start)   do_start "${2:-}" ;;
    stop)    do_stop "${2:-}" ;;
    restart)
        do_stop "${2:-}"
        sleep 2
        do_start "${2:-}"
        ;;
    status)  do_status ;;
    build)   do_build ;;
    logs)    do_logs "${2:-}" ;;
    *)
        echo "Usage: $0 {start|stop|restart|status|build|logs} [CVE-ID]"
        echo ""
        echo "  start    - 启动所有环境（或指定 CVE）"
        echo "  stop     - 停止所有环境（或指定 CVE）"
        echo "  restart  - 重启所有环境（或指定 CVE）"
        echo "  status   - 查看所有环境状态"
        echo "  build    - 构建所有镜像"
        echo "  logs     - 查看指定环境日志"
        ;;
esac
