#!/usr/bin/env bash
# scan.sh — WordPress 漏洞环境自动化扫描（优化版）
#
# 优化点（相比原版）：
#   - Ctrl+C 自动清理所有已启动的容器
#   - 预构建 base 镜像，避免每个 CVE 重复 build
#   - wp-login 就绪后轮询插件 REST 路由，替代固定 sleep 15
#   - 支持断点续跑（--resume，默认开启；--no-resume 关闭）
#   - 并行执行（-j N，默认 4）
#   - 智能重试：连接错误才重试，matcher 不命中不再浪费次数
#   - judge 用纯 bash，不再每个 CVE 启动 python3
#
# 用法：
#   bash scan.sh                       # 跑全部发现的环境
#   bash scan.sh CVE-2024-11740 ...    # 只跑指定 CVE
#   bash scan.sh -j 8                   # 8 并行
#   bash scan.sh --source official     # 只跑官方模板的环境
#   bash scan.sh --source custom       # 只跑自建模板的环境
#   bash scan.sh --list                # 仅列出，不执行
#   bash scan.sh --no-build            # 跳过预构建 base 镜像
#   bash scan.sh --keep                # 跑完不销毁容器
#   bash scan.sh --no-resume           # 从头跑（忽略之前的 PASS 记录）
#   bash scan.sh --resume-from LOGDIR  # 从指定日志目录续跑

set -o pipefail   # macOS 自带 bash 3.2，无 mapfile、空数组在 set -u 下会误报，故不用 -u

BASE_DIR="$(cd "$(dirname "$0")" && pwd)"
NUCLEI="${NUCLEI_BIN:-$HOME/工具/nuclei}"
TEMPLATE_DIR="$BASE_DIR/nuclei-templates"
TEMPLATE_NEW_DIR="$BASE_DIR/nuclei-template-new"
VULHUB_DIR="$BASE_DIR/vulhub-env"
PARAMS_CONF="$BASE_DIR/nuclei-params.conf"
WP_WAIT_TIMEOUT=120
NUCLEI_TIMEOUT=60
NUCLEI_RETRIES=2            # 最多 2 次（降自 3）
RETRY_INTERVAL=5            # 间隔 5s（降自 10）
PLUGIN_POLL_MAX=30          # 等插件就绪最长 60s (30×2s)
SCAN_JOBS=${SCAN_JOBS:-4}   # 并行数

# ── 选项解析 ────────────────────────────────────
DO_PREBUILD=1; KEEP=0; LIST_ONLY=0; DO_RESUME=1; SOURCE_FILTER=""
RESUME_FROM=""
declare -a WANT_CVES=()
while [ $# -gt 0 ]; do
  case "$1" in
    --no-build) DO_PREBUILD=0 ;;
    --keep)     KEEP=1 ;;
    --list)     LIST_ONLY=1 ;;
    --resume)   DO_RESUME=1 ;;
    --no-resume) DO_RESUME=0 ;;
    --resume-from) shift; RESUME_FROM="$1" ;;
    --resume-from=*) RESUME_FROM="${1#--resume-from=}" ;;
    --source)   shift; SOURCE_FILTER="$1" ;;
    --source=*) SOURCE_FILTER="${1#--source=}" ;;
    -j)         shift; SCAN_JOBS="$1" ;;
    -j*)        SCAN_JOBS="${1#-j}" ;;
    -h|--help)  sed -n '2,25p' "$0"; exit 0 ;;
    CVE-*)      WANT_CVES+=("$1") ;;
    *) echo "未知参数: $1" >&2; exit 2 ;;
  esac
  shift
done

# ── 日志目录（支持续跑）──────────────────────────
if [ -n "$RESUME_FROM" ]; then
  RUN_DIR="$RESUME_FROM"
  TS="$(basename "$RUN_DIR" | sed 's/^scan-//')"
  [ -d "$RUN_DIR" ] || { echo "日志目录不存在: $RUN_DIR" >&2; exit 1; }
else
  TS="$(date +%Y%m%d-%H%M%S)"
  RUN_DIR="$BASE_DIR/logs/scan-$TS"
fi
RAW_DIR="$RUN_DIR/raw"
TMP_DIR="$RUN_DIR/tmp"       # 每个 CVE 的临时结果（并行安全）
mkdir -p "$RAW_DIR" "$TMP_DIR"
SUMMARY="$RUN_DIR/summary.txt"
RESULTS="$RUN_DIR/results.jsonl"
RUNLOG="$RUN_DIR/run.log"
CLEANUP_LIST="$RUN_DIR/.containers"   # 已启动的容器列表，用于 trap 清理

log() { echo "[$(date +%H:%M:%S)] $*" | tee -a "$RUNLOG"; }

# ── Ctrl+C / 异常退出时自动清理已启动的容器 ──────
cleanup_containers() {
  if [ -f "$CLEANUP_LIST" ] && [ -s "$CLEANUP_LIST" ]; then
    echo "" >&2
    log "收到中断信号，清理已启动的容器..."
    while IFS=$'\t' read -r cve dir; do
      [ -z "$cve" ] && continue
      docker compose -f "$dir/docker-compose.yml" down -v --remove-orphans </dev/null >/dev/null 2>&1 || true
      log "  已清理: $cve"
    done < "$CLEANUP_LIST"
    : > "$CLEANUP_LIST"
  fi
}
trap 'cleanup_containers; exit 130' INT TERM
trap 'cleanup_containers' EXIT

[ -x "$NUCLEI" ] || { echo "找不到 nuclei 可执行文件: $NUCLEI (可用 NUCLEI_BIN 覆盖)" >&2; exit 1; }

# ── 辅助函数 ──────────────────────────────────────

params_for() {
  [ -f "$PARAMS_CONF" ] || return 0
  awk -v cve="$1" '
    /^[[:space:]]*#/ {next} /^[[:space:]]*$/ {next}
    $1==cve { $1=""; sub(/^[[:space:]]+/,""); print; exit }
  ' "$PARAMS_CONF"
}

discover() {
  find "$VULHUB_DIR" -maxdepth 3 -name docker-compose.yml ! -path '*/base/*' 2>/dev/null \
  | while read -r compose; do
      dir="$(dirname "$compose")"
      cve="$(basename "$dir")"
      [[ "$cve" == CVE-* ]] || continue
      port="$(grep -oE '[0-9]+:80([^0-9]|$)' "$compose" | head -1 | cut -d: -f1)"
      [ -n "$port" ] || { echo "WARN $cve: compose 中未找到 :80 端口映射" >&2; continue; }
      printf '%s\t%s\t%s\n' "$cve" "$dir" "$port"
    done | sort -u
}

wanted() {
  [ ${#WANT_CVES[@]} -eq 0 ] && return 0
  for c in "${WANT_CVES[@]}"; do [[ "$c" == "$1" ]] && return 0; done
  return 1
}

tpl_source() {
  if [ -f "$TEMPLATE_DIR/$1.yaml" ]; then echo official
  elif [ -f "$TEMPLATE_NEW_DIR/$1.yaml" ]; then echo custom
  else echo none; fi
}

resolve_template() {
  if [ -f "$TEMPLATE_DIR/$1.yaml" ]; then echo "$TEMPLATE_DIR/$1.yaml"
  elif [ -f "$TEMPLATE_NEW_DIR/$1.yaml" ]; then echo "$TEMPLATE_NEW_DIR/$1.yaml"; fi
}

src_ok() {
  [ -z "$SOURCE_FILTER" ] && return 0
  case ",$SOURCE_FILTER," in *",$1,"*) return 0 ;; esac
  return 1
}

selected() {
  wanted "$1" || return 1
  src_ok "$(tpl_source "$1")" || return 1
  return 0
}

progress() {
  local cur=$1 tot=$2 width=24 i pct=0 filled=0 bar=""
  [ "$tot" -gt 0 ] && { pct=$(( cur * 100 / tot )); filled=$(( cur * width / tot )); }
  for ((i=0; i<width; i++)); do
    if [ "$i" -lt "$filled" ]; then bar="${bar}#"; else bar="${bar}-"; fi
  done
  printf '[%s] %3d%% (%d/%d)' "$bar" "$pct" "$cur" "$tot"
}

# 从 compose 文件提取插件 slug
extract_plugin_slug() {
  local compose="$1/docker-compose.yml"
  # 优先匹配 WORDPRESS_PLUGIN_SLUG，其次 PLUGIN_SLUG（新版 entrypoint 模板使用）
  local slug
  slug=$(grep 'WORDPRESS_PLUGIN_SLUG:' "$compose" 2>/dev/null | \
    sed 's/.*WORDPRESS_PLUGIN_SLUG:\s*//;s/[[:space:]]*$//' | tail -1)
  [ -n "$slug" ] || slug=$(grep 'PLUGIN_SLUG:' "$compose" 2>/dev/null | \
    sed 's/.*PLUGIN_SLUG:\s*//;s/[[:space:]]*$//' | tail -1)
  echo "$slug"
}

# 检查是否已 PASS（断点续跑）
is_already_done() {
  [ "$DO_RESUME" != "1" ] && return 1
  [ -f "$RESULTS" ] && grep -q "\"$1\".*\"PASS\"" "$RESULTS" 2>/dev/null
}

# ── wait_wp：两步检测 + 轮询插件就绪 ──────────────
#   Step1: /wp-json/ 返回 → WordPress 核心就绪
#   Step2: wp-login.php 出现登录表单 → 数据库安装完成
#   Step3: 轮询容器日志 "Setup complete" + /wp-json/ 含插件/WooCommerce 命名空间
wait_wp() {
  local port=$1 plugin_slug=$2 compose_file=${3:-}
  local deadline=$((SECONDS + WP_WAIT_TIMEOUT))

  # Step 1: wait for /wp-json/
  while [ $SECONDS -lt $deadline ]; do
    curl -sf --max-time 3 "http://localhost:$port/wp-json/" -o /dev/null 2>/dev/null && break
    sleep 3
  done

  # Step 2: wait for wp-login to show login form
  while [ $SECONDS -lt $deadline ]; do
    if curl -sf --max-time 3 "http://localhost:$port/wp-login.php" 2>/dev/null | grep -q '<form.*wp-login'; then
      break
    fi
    sleep 5
  done

  # Step 3: poll for plugin readiness (container logs + REST namespace)
  local poll_deadline=$((SECONDS + PLUGIN_POLL_MAX * 2))
  while [ $SECONDS -lt $poll_deadline ]; do
    # 3a: 检查容器日志是否出现 "Setup complete"（最可靠的信号）
    if [ -n "$compose_file" ]; then
      local container_id
      container_id=$(docker compose -f "$compose_file" ps -q web 2>/dev/null)
      if [ -n "$container_id" ] && docker logs "$container_id" 2>/dev/null | grep -q "Setup complete"; then
        return 0
      fi
    fi
    # 3b: 检查 REST 命名空间（plugin_slug 或 woocommerce）
    if [ -n "$plugin_slug" ]; then
      if curl -sf --max-time 3 "http://localhost:$port/wp-json/" 2>/dev/null | grep -qi "$plugin_slug"; then
        return 0
      fi
      # WooCommerce REST API 命名空间检查（wc/v3 路由不在根 wp-json 中）
      if curl -sf --max-time 3 "http://localhost:$port/wp-json/wc/v3/" 2>/dev/null | grep -q '"namespace"'; then
        return 0
      fi
    fi
    sleep 2
  done
  # 超时也不阻塞
  return 0
}

# ── judge：纯 bash 解析 nuclei jsonl ─────────────
#   输出三行 → STATUS / matcher_name / evidence
judge() {
  local raw="$1"
  if [ -s "$raw" ] && grep -q '"matcher-status":\s*true' "$raw" 2>/dev/null; then
    local mname ev
    mname=$(grep -o '"matcher-name":"[^"]*"' "$raw" 2>/dev/null | head -1 | sed 's/"matcher-name":"//;s/"$//')
    ev=$(grep -o '"extracted-results":\[[^]]*\]' "$raw" 2>/dev/null | head -1 | \
         sed 's/"extracted-results":\[//;s/\]//;s/^"//;s/"$//;s/","/ | /g' | tr '\n' ' ' | cut -c1-120)
    printf 'PASS\n%s\n%s\n' "$mname" "$ev"
  else
    printf 'FAIL\n\n\n'
  fi
}

# 检查 nuclei stderr 是否为连接级错误（值得重试）
is_connection_error() {
  local log="$1"
  grep -qiE 'connection refused|no route to host|context deadline exceeded|i/o timeout|connect:|no such host' "$log" 2>/dev/null
}

# ── 预构建：找到所有唯一的 build context，各 docker build 一次 ──
prebuild_base_images() {
  local filtered_list="$1"
  local seen_file="$TMP_DIR/.seen_builds"
  : > "$seen_file"
  local count=0

  while IFS=$'\t' read -r cve dir port; do
    local compose="$dir/docker-compose.yml"
    local build_dir
    build_dir=$(grep 'build:' "$compose" 2>/dev/null | head -1 | sed 's/^[[:space:]]*build:[[:space:]]*//')
    [ -z "$build_dir" ] && continue
    build_dir=$(cd "$dir" && cd "$build_dir" 2>/dev/null && pwd || echo "")
    [ -z "$build_dir" ] && continue

    # bash 3.2 无关联数组，用文件去重
    if grep -qFx "$build_dir" "$seen_file" 2>/dev/null; then continue; fi
    echo "$build_dir" >> "$seen_file"
    count=$((count+1))

    log "预构建基础镜像 ($count): $build_dir"
    docker build -q -t "vulhub-base:$(echo "$build_dir" | md5 2>/dev/null | head -c 8 || echo "$count")" \
      "$build_dir" </dev/null >>"$RUN_DIR/prebuild.log" 2>&1 || \
      log "  WARN: 预构建失败，运行时将自动构建"
  done < "$filtered_list"
  log "预构建完成，共 $count 个基础镜像"
}

# ── run_one_cve：单 CVE 全部流程（在子 shell 中执行，并行安全）──
run_one_cve() {
  local cve=$1 dir=$2 port=$3
  local job_log="$TMP_DIR/$cve.log"
  local job_result="$TMP_DIR/$cve.result"
  local raw="$RAW_DIR/$cve.jsonl"
  local nlog="$RAW_DIR/$cve.nuclei.log"
  local dlog="$TMP_DIR/$cve.docker.log"

  # 所有输出重定向到 job_log，保持主日志干净
  exec >>"$job_log" 2>&1

  echo "[$(date +%H:%M:%S)] 开始 $cve"

  # ── 0. 断点续跑检查 ──
  if is_already_done "$cve"; then
    echo "  已在之前运行中 PASS，跳过"
    printf 'PASS\t%s\t%s\n' "" "resume-skip" > "$job_result"
    return 0
  fi

  local template tokens
  template="$(resolve_template "$cve")"
  tokens="$(params_for "$cve")"

  # 模板缺失
  if [ -z "$template" ]; then
    printf 'ERROR\t%s\t%s\n' "" "模板缺失" > "$job_result"
    return 0
  fi

  # OOB / skip 参数解析
  local is_oob=0 do_skip=0
  declare -a extra=()
  for t in $tokens; do
    case "$t" in
      auth) extra+=(-V "username=admin" -V "password=admin") ;;
      oob)  is_oob=1 ;;
      skip) do_skip=1 ;;
      *)    extra+=("$t") ;;
    esac
  done

  if [ "$do_skip" -eq 1 ]; then
    printf 'SKIP\t%s\t%s\n' "" "conf skip" > "$job_result"
    return 0
  fi

  # ── 1. 启动 docker 环境 ──
  # 记录到清理列表，Ctrl+C 时自动销毁
  printf '%s\t%s\n' "$cve" "$dir" >> "$CLEANUP_LIST"

  if [ "$DO_PREBUILD" -eq 1 ]; then
    docker compose -f "$dir/docker-compose.yml" up -d --build --quiet-pull </dev/null >>"$dlog" 2>&1
  else
    docker compose -f "$dir/docker-compose.yml" up -d --quiet-pull </dev/null >>"$dlog" 2>&1
  fi

  if [ $? -ne 0 ]; then
    printf 'ERROR\t%s\t%s\n' "" "docker_up_failed" > "$job_result"
    return 0
  fi

  # ── 2. 等待 WordPress 就绪（含插件路由轮询）──
  local plugin_slug
  plugin_slug=$(extract_plugin_slug "$dir")

  if ! wait_wp "$port" "$plugin_slug" "$dir/docker-compose.yml"; then
    printf 'ERROR\t%s\t%s\n' "" "wp_not_ready" > "$job_result"
    [ "$KEEP" -eq 1 ] || docker compose -f "$dir/docker-compose.yml" down -v --remove-orphans </dev/null >>"$dlog" 2>&1
    return 0
  fi

  echo "  环境已就绪 (plugin=$plugin_slug)"

  # ── 3. nuclei 智能试错 ──
  #    连接错误 → 重试；matcher 不命中 → 不重试
  local st=FAIL mn="" ev=""
  local attempt=0

  while [ "$attempt" -lt "$NUCLEI_RETRIES" ]; do
    attempt=$((attempt+1))

    "$NUCLEI" -t "$template" -u "http://localhost:$port" \
              -jsonl -ms -timeout "$NUCLEI_TIMEOUT" -silent "${extra[@]}" \
              </dev/null >"$raw" 2>"$nlog" || true

    { IFS= read -r st; IFS= read -r mn; IFS= read -r ev; } < <(judge "$raw")

    if [ "$st" = "PASS" ]; then
      echo "  第 $attempt/$NUCLEI_RETRIES 次 → PASS"
      break
    fi

    # 智能判断是否值得重试
    if [ "$attempt" -lt "$NUCLEI_RETRIES" ]; then
      if is_connection_error "$nlog"; then
        echo "  第 $attempt/$NUCLEI_RETRIES 次未命中 (连接错误)，${RETRY_INTERVAL}s 后重试..."
        sleep "$RETRY_INTERVAL"
      else
        echo "  第 $attempt/$NUCLEI_RETRIES 次未命中 (matcher 不匹配)，跳过重试"
        break
      fi
    else
      echo "  ${NUCLEI_RETRIES} 次均未命中 → FAIL"
    fi
  done

  # ── 4. 写入结果 ──
  printf '%s\t%s\t%s\n' "$st" "$mn" "$ev" > "$job_result"

  # ── 5. 清理容器 ──
  if [ "$KEEP" -eq 1 ]; then
    echo "  --keep: 保留容器"
  else
    docker compose -f "$dir/docker-compose.yml" down -v --remove-orphans </dev/null >>"$dlog" 2>&1
    # 从清理列表中移除（已正常清理）
    grep -v "^$cve\t" "$CLEANUP_LIST" > "${CLEANUP_LIST}.tmp" 2>/dev/null && mv "${CLEANUP_LIST}.tmp" "$CLEANUP_LIST" 2>/dev/null
  fi

  unset extra
  return 0
}

# ══════════════════════════════════════════════════════
# 主流程
# ══════════════════════════════════════════════════════

ENV_LIST="$RUN_DIR/.envs.tsv"
discover > "$ENV_LIST"
[ -s "$ENV_LIST" ] || { echo "未发现任何漏洞环境" >&2; exit 1; }

if [ "$LIST_ONLY" -eq 1 ]; then
  printf '%-18s %-7s %-9s %s\n' "CVE" "PORT" "SOURCE" "PARAMS"
  while IFS=$'\t' read -r cve dir port; do
    selected "$cve" || continue
    printf '%-18s %-7s %-9s %s\n' "$cve" "$port" "$(tpl_source "$cve")" "$(params_for "$cve")"
  done < "$ENV_LIST"
  rm -rf "$RUN_DIR"
  exit 0
fi

# 过滤出本次要跑的 CVE 列表
FILTERED_LIST="$RUN_DIR/.filtered.tsv"
: > "$FILTERED_LIST"
while IFS=$'\t' read -r cve dir port; do
  selected "$cve" || continue
  printf '%s\t%s\t%s\n' "$cve" "$dir" "$port"
done < "$ENV_LIST" > "$FILTERED_LIST"

RUN_TOTAL=$(wc -l < "$FILTERED_LIST" | tr -d ' ')
[ "$RUN_TOTAL" -gt 0 ] || { echo "没有匹配的漏洞环境" >&2; exit 0; }

log "nuclei: $NUCLEI ($("$NUCLEI" -version 2>&1 | grep -oE 'v[0-9.]+' | head -1))"
log "日志目录: $RUN_DIR"
log "本次共 $RUN_TOTAL 个环境 | 并行度: $SCAN_JOBS | 重试: ${NUCLEI_RETRIES}次/${RETRY_INTERVAL}s间隔"
log "预构建: $([ "$DO_PREBUILD" -eq 1 ] && echo 是 || echo 否) | 续跑: $([ "$DO_RESUME" -eq 1 ] && echo 是 || echo 否)"

: > "$SUMMARY"; : > "$RESULTS"

# ── 续跑模式下，将已有结果加载到 SUMMARY/RESULTS ──
if [ "$DO_RESUME" = "1" ] && [ -f "$SUMMARY" ]; then
  log "续跑模式：跳过已 PASS 的 CVE"
fi

# ── 0. 预构建 base 镜像（一次性，所有 CVE 共享 Docker 层缓存）──
if [ "$DO_PREBUILD" -eq 1 ]; then
  log "━━━ 预构建基础镜像 ━━━"
  prebuild_base_images "$FILTERED_LIST"
fi

# ── 1. 并行启动所有 CVE ──────────────────────────
log "━━━ 开始并行测试 (${SCAN_JOBS} 并发) ━━━"

count=0; pass=0; fail=0; skip=0; err=0

while IFS=$'\t' read -r cve dir port; do
  count=$((count+1))

  # 等待一个空闲槽位（bash 3.2 兼容写法）
  while [ "$(jobs -r | wc -l | tr -d ' ')" -ge "$SCAN_JOBS" ]; do
    sleep 0.5
  done

  log "$(progress "$count" "$RUN_TOTAL")  $cve  :$port [$(tpl_source "$cve")]"
  run_one_cve "$cve" "$dir" "$port" &
done < "$FILTERED_LIST"

# 等所有后台任务完成
log "等待剩余任务完成..."
wait

# ── 2. 聚合结果（按原始顺序）──────────────────────
log "━━━ 汇总结果 ━━━"

while IFS=$'\t' read -r cve dir port; do
  job_result="$TMP_DIR/$cve.result"

  if [ ! -f "$job_result" ]; then
    # 任务异常退出（如被 kill）
    echo "{\"cve\":\"$cve\",\"port\":$port,\"status\":\"ERROR\",\"reason\":\"job_lost\"}" >> "$RESULTS"
    printf '%s\t%-7s %-6s %s\n' "$cve" "$port" "ERROR" "job_lost" >> "$SUMMARY"
    err=$((err+1))
    continue
  fi

  { IFS=$'\t' read -r st mn ev; } < "$job_result"

  case "$st" in
    PASS)
      echo "{\"cve\":\"$cve\",\"port\":$port,\"status\":\"PASS\",\"matcher_name\":\"$mn\",\"evidence\":\"$ev\"}" >> "$RESULTS"
      printf '%s\t%-7s %-6s %s\n' "$cve" "$port" "PASS" "m=$mn  $ev" >> "$SUMMARY"
      log "PASS $cve  ${mn:+matcher=$mn}  ${ev:+evidence=$ev}"
      pass=$((pass+1))
      ;;
    FAIL)
      echo "{\"cve\":\"$cve\",\"port\":$port,\"status\":\"FAIL\",\"matcher_name\":\"$mn\",\"evidence\":\"$ev\"}" >> "$RESULTS"
      printf '%s\t%-7s %-6s %s\n' "$cve" "$port" "FAIL" "m=$mn  $ev" >> "$SUMMARY"
      log "FAIL $cve"
      fail=$((fail+1))
      ;;
    SKIP)
      echo "{\"cve\":\"$cve\",\"port\":$port,\"status\":\"SKIP\",\"reason\":\"$ev\"}" >> "$RESULTS"
      printf '%s\t%-7s %-6s %s\n' "$cve" "$port" "SKIP" "$ev" >> "$SUMMARY"
      log "SKIP $cve ($ev)"
      skip=$((skip+1))
      ;;
    ERROR)
      echo "{\"cve\":\"$cve\",\"port\":$port,\"status\":\"ERROR\",\"reason\":\"$ev\"}" >> "$RESULTS"
      printf '%s\t%-7s %-6s %s\n' "$cve" "$port" "ERROR" "$ev" >> "$SUMMARY"
      log "ERROR $cve ($ev)"
      err=$((err+1))
      ;;
  esac
done < "$FILTERED_LIST"

# ── 汇总 ────────────────────────────────────────
{
  echo ""
  echo "==================== 汇总 ===================="
  echo "总计 $RUN_TOTAL  |  PASS $pass  FAIL $fail  SKIP $skip  ERROR $err"
  echo "明细: $SUMMARY"
  echo "判定记录: $RESULTS"
  echo "原始 jsonl: $RAW_DIR/"
} | tee -a "$RUNLOG"

echo "$RESULTS"   # 最后一行输出判定结果文件路径
