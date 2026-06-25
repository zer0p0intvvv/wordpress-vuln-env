---
name: wp-vuln-env
description: "Create WordPress plugin vulnerability Docker environments for exploit reproduction. Use when user wants to set up a CVE test environment, reproduce a WordPress plugin vulnerability, create a Docker lab for WP plugin testing, or debug nuclei templates against a vulnerable WP instance."
---

# WordPress 漏洞环境搭建工作流

全流程：`.env` 配置 → `generate.sh` 拉模板 → `scan.sh` 构建+验证 → 合并到主项目。

## 快速开始（task 独立文件夹）

每个 CVE 一个独立 task 文件夹，自包含全部文件：

```
task-<CVE-ID>/
├── .env                  # CVE_ID / PLUGIN_SLUG / PLUGIN_VERSION / WEB_PORT / MYSQL_PORT / PHP_VER
├── generate.sh           # ① 下载 nuclei 模板 + 打印参数（只需一次）
├── scan.sh               # ② 构建 → 等待 → nuclei → 判定 → 产出（可反复跑）
├── Dockerfile            # 容器定义（FROM wordpress:6.4-phpX.X-apache）
├── docker-compose.yml    # 服务编排（端口用变量，任务内 scan.sh 自动 source .env）
├── docker-entrypoint.sh  # WP 安装 + 插件激活 + CVE 特定初始化
├── mu-plugins/           # MU-plugin 强制激活（Windows CRLF 兼容）
├── nuclei-templates/     # generate.sh 下载到此
└── output/               # scan.sh 产出：result.txt / nuclei-raw.jsonl / register-*.{toml,md,conf}
```

### 主循环

```bash
bash generate.sh    # 一次：下载模板 + 确认参数
bash scan.sh        # 反复：构建 → 等待 → nuclei → 判定
```

`scan.sh` 自动完成：build → up → 等待 WP 就绪 → 跑 nuclei → 判定 PASS/FAIL → 清理容器 → 写 `output/`。

## 工作流步骤

### Step 0: 查 CVE 信息

用 `vulnx-cve` 技能查 CVE 详情，确认：插件名、漏洞版本、漏洞类型、是否有 nuclei 模板。

### Step 1: 读 nuclei 模板（关键！）

在写任何代码前，**必须先读模板**，从中提取：

| 模板内容 | 对应环境需求 | 本次 CVE-2026-3018 示例 |
|----------|-------------|----------------------|
| path/URL | 需要什么页面/路由 | `/newsletter-management/` → 需要创建 WP 页面 |
| 参数 | SQLi/XSS payload 入口 | `wpmlsubscriber_id` → 需要 unsubscribe 端点 |
| matchers | 强特征是什么 | `duration>=6` → time-based SQLi，需要 SLEEP 生效 |
| flow | 多步请求？ | `http(1) && http(2)` → 第一步 readme.txt 校验 |
| auth 标记 | 需要登录？ | 无 → 无需认证 |

**反查插件源码**：模板路径打不中时，搜插件源码中该路由/钩子的入口条件，找出缺失的前置数据。

### Step 2: 编写 Dockerfile

```dockerfile
FROM wordpress:6.4-php8.2-apache

RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends curl less default-mysql-client unzip; \
    rm -rf /var/lib/apt/lists/*

RUN curl -fsSL -o /usr/local/bin/wp https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar && \
    chmod +x /usr/local/bin/wp && wp --info --allow-root

# 【推荐】预下载插件 zip 到镜像（绕过运行时 wp.org 下载延迟）
# --http1.1 避免 HTTP/2 断开；--retry 防网络抖动
RUN curl -fsSL --retry 5 --retry-delay 3 --http1.1 \
    -o /tmp/<plugin-slug>.<version>.zip \
    "https://downloads.wordpress.org/plugin/<plugin-slug>.<version>.zip"

# MU-plugin 强制激活（Windows CRLF 兼容）
RUN mkdir -p /var/www/html/wp-content/mu-plugins
COPY mu-plugins/force-activate.php /var/www/html/wp-content/mu-plugins/
RUN apt-get update && apt-get install -y dos2unix && \
    dos2unix /var/www/html/wp-content/mu-plugins/force-activate.php && \
    chown www-data:www-data /var/www/html/wp-content/mu-plugins/force-activate.php && \
    apt-get remove -y dos2unix && apt-get autoremove -y && \
    rm -rf /var/lib/apt/lists/*

# 替换官方 entrypoint
RUN mv /usr/local/bin/docker-entrypoint.sh /usr/local/bin/docker-entrypoint-original.sh
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh && \
    dos2unix /usr/local/bin/docker-entrypoint.sh 2>/dev/null || true

CMD ["apache2-foreground"]
```

### Step 3: 编写 docker-compose.yml

```yaml
services:
  web:
    build: .
    depends_on:
      - db
    ports:
      - "${WEB_PORT:-8088}:80"
    environment:
      WORDPRESS_DB_HOST: db:3306
      WORDPRESS_DB_USER: wordpress
      WORDPRESS_DB_PASSWORD: wordpress
      WORDPRESS_DB_NAME: wordpress
      WORDPRESS_URL: http://localhost:${WEB_PORT:-8088}
      WORDPRESS_TITLE: Vulhub Test
      WORDPRESS_ADMIN_USER: admin
      WORDPRESS_ADMIN_PASSWORD: admin
      WORDPRESS_ADMIN_EMAIL: admin@example.com
      PLUGIN_SLUG: ${PLUGIN_SLUG:-}
      PLUGIN_VERSION: "${PLUGIN_VERSION:-}"
  db:
    image: mysql:5.7
    platform: linux/amd64
    ports:
      - "${MYSQL_PORT:-3307}:3306"
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: wordpress
      MYSQL_USER: wordpress
      MYSQL_PASSWORD: wordpress
```

> **关键**：合并到主项目时，端口和 PLUGIN_SLUG/PLUGIN_VERSION 必须**硬编码**（主项目 `scan.sh` 不 source `.env`）。

### Step 4: 编写 docker-entrypoint.sh

完整模板见下方「Entrypoint 模式库」。核心流程：

```
① 后台启动 Apache
② 等待 WordPress 文件 + MySQL 就绪
③ wp core install（首次）
④ 设置 permalink
⑤ 【关键】CVE 特定初始化（预创建页面/数据/option）
⑥ 安装插件（优先本地 zip → wp.org API → CDN）
⑦ 插件激活后初始化（check_tables / REST 路由注册等）
⑧ 输出 "Setup complete"
```

### Step 5: MU-plugin 强制激活

```php
<?php
// mu-plugins/force-activate.php
add_filter('option_active_plugins', function ($plugins) {
    $target = '<plugin-slug>/<plugin-main-file>.php';
    if (!in_array($target, (array) $plugins, true)) {
        $plugins[] = $target;
    }
    return $plugins;
});
```

**为什么用 MU-plugin 而不是 `wp plugin activate`**：
- 纯 PHP filter，无 wp-cli / shell 依赖
- 彻底规避 Windows CRLF 时序问题
- WordPress 每次请求自动加载

### Step 6: 运行 scan.sh

```bash
bash scan.sh              # 构建+测试
bash scan.sh --keep       # 保留容器用于调试
bash scan.sh --no-build   # 跳过构建（已构建过）
```

### Step 7: 合并到主项目

```bash
# 复制环境文件
cp -r docker-compose.yml Dockerfile docker-entrypoint.sh mu-plugins/ \
   ../vulhub-env/<plugin-slug>/<CVE-ID>/

# 复制模板
cp nuclei-templates/<CVE-ID>.yaml ../nuclei-templates/

# 追加登记信息（手动编辑）
# ../vulhub-env/environments.toml ← output/register-env.toml
# ../nuclei-templates/INDEX.md     ← output/register-index.md
# ../nuclei-params.conf            ← output/register-params.conf（仅认证/OOB）

# 硬编码 docker-compose.yml 端口和 PLUGIN_SLUG/PLUGIN_VERSION
# 主项目 scan.sh 不 source .env！

# 最终验证
cd ../ && bash scan.sh <CVE-ID>
```

---

## Entrypoint 模式库

以下模式按需组合使用。

### 模式 0: 骨架

```bash
#!/bin/bash
set -Eeuo pipefail

WP_PATH="/var/www/html"
WP_URL="${WORDPRESS_URL:-http://localhost:8088}"
PLUGIN_SLUG="${PLUGIN_SLUG:-}"
PLUGIN_VERSION="${PLUGIN_VERSION:-}"

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
    wp core install --url="$WP_URL" --title="${WORDPRESS_TITLE:-Vulhub Test}" \
        --admin_user="${WORDPRESS_ADMIN_USER:-admin}" \
        --admin_password="${WORDPRESS_ADMIN_PASSWORD:-admin}" \
        --admin_email="${WORDPRESS_ADMIN_EMAIL:-admin@example.com}" \
        --skip-email --allow-root
fi

wp option update siteurl "$WP_URL" --allow-root >/dev/null
wp option update home "$WP_URL" --allow-root >/dev/null
wp option update permalink_structure '/%postname%/' --allow-root >/dev/null
wp rewrite flush --allow-root >/dev/null || true
```

### 模式 A: 插件安装（四级回退）

```bash
if [ -n "$PLUGIN_SLUG" ]; then
    PLUGIN_DIR="$WP_PATH/wp-content/plugins/$PLUGIN_SLUG"
    ZIP="/tmp/${PLUGIN_SLUG}.${PLUGIN_VERSION}.zip"
    INSTALLED=0

    # ① 本地 zip 预下载（Dockerfile 已缓存，瞬时）
    if [ ! -d "$PLUGIN_DIR" ] && [ -f "$ZIP" ]; then
        unzip -oq "$ZIP" -d "$WP_PATH/wp-content/plugins/"
        chown -R www-data:www-data "$PLUGIN_DIR" 2>/dev/null || true
        wp plugin activate "$PLUGIN_SLUG" --allow-root && INSTALLED=1
        rm -f "$ZIP"
    fi

    # ② 本地已有（插件专用镜像回退）
    if [ "$INSTALLED" != "1" ] && [ -d "$PLUGIN_DIR" ]; then
        wp plugin activate "$PLUGIN_SLUG" --allow-root && INSTALLED=1
    fi

    # ③ WP.org API（在线下载，慢）
    if [ "$INSTALLED" != "1" ]; then
        INSTALL_CMD="wp plugin install $PLUGIN_SLUG --allow-root --activate"
        [ -n "$PLUGIN_VERSION" ] && INSTALL_CMD="$INSTALL_CMD --version=$PLUGIN_VERSION"
        eval "$INSTALL_CMD" 2>/dev/null && INSTALLED=1 || echo "WP.org API 失败，尝试 CDN..."
    fi

    # ④ CDN 直链（在线下载，慢）
    if [ "$INSTALLED" != "1" ] && [ -n "$PLUGIN_VERSION" ]; then
        URL="https://downloads.wordpress.org/plugin/${PLUGIN_SLUG}.${PLUGIN_VERSION}.zip"
        if curl -fsSL --retry 3 --max-time 30 -o "$ZIP" "$URL"; then
            unzip -oq "$ZIP" -d "$WP_PATH/wp-content/plugins/"
            chown -R www-data:www-data "$PLUGIN_DIR" 2>/dev/null || true
            wp plugin activate "$PLUGIN_SLUG" --allow-root && INSTALLED=1
            rm -f "$ZIP"
        fi
    fi

    [ "$INSTALLED" = "1" ] && echo "Plugin $PLUGIN_SLUG activated" || echo "WARNING: plugin install failed"
fi
```

### 模式 B: 预创建 WordPress 页面

当 nuclei 模板路径是 WordPress 页面 slug 时使用：

```bash
# 在插件激活前创建页面并设置 option，避免插件 update_options() 重复创建
echo "Pre-creating <page-slug> page..."
PAGE_ID=$(wp post create --post_type=page \
    --post_title='<Page Title>' \
    --post_name='<page-slug>' \
    --post_content='[shortcode]' \
    --post_status=publish --porcelain --allow-root 2>/dev/null)
if [ -n "$PAGE_ID" ] && [ "$PAGE_ID" -gt 0 ]; then
    wp option update <plugin_prefix>managementpost "$PAGE_ID" --allow-root
    echo "Page created: ID=$PAGE_ID slug=<page-slug>"
fi
```

**CVE-2026-3018 实例**：nuclei 模板路径 `/newsletter-management/`，插件默认创建 `manage-subscriptions`。必须在插件激活前创建 slug 正确的页面，并设置 `wpmlmanagementpost` option 指向它。

### 模式 C: 初始化插件自定义表

某些插件的 `check_tables()` 有 `is_admin()` 守卫，`wp eval` 运行时需定义 `DOING_AJAX`：

```bash
echo "Initializing plugin tables..."
wp --allow-root eval "
if (!defined('DOING_AJAX')) { define('DOING_AJAX', true); }
\$plugin = WPMAIL();  // 或其它全局函数/对象
if (method_exists(\$plugin, 'check_tables')) {
    \$plugin->check_tables();
    echo 'Tables checked.'.PHP_EOL;
}
"
```

### 模式 D: 依赖插件安装

```bash
wp plugin install elementor --version=3.18.0 --activate --allow-root
```

### 模式 E: REST 类手动实例化

```bash
php -r "require_once 'wp-content/plugins/<slug>/<file>.php'; new ClassName();"
```

### 模式 F: REST 路由延迟注册

```bash
wp --allow-root eval '
add_action("init", function() {
    require_once "/var/www/html/wp-content/plugins/<slug>/<rest-controller>.php";
    (new Namespace\ClassName())->register_routes();
});
'
```

### 模式 G: 修改插件选项

```bash
wp --allow-root option get <option_name> --format=json | \
    jq '. + {"key": "value"}' | \
    wp --allow-root option set <option_name> --format=json
```

---

## 调试检查清单

FAIL 时按顺序排查：

### 1. 容器是否正常？

```bash
docker compose ps                          # 容器状态
docker compose logs web | tail -30         # entrypoint 是否执行到 "Setup complete"
docker compose logs web | grep -E "ERROR|Fatal|WARNING"
```

### 2. WordPress 是否就绪？

```bash
curl -s -o /dev/null -w "%{http_code}" http://localhost:<PORT>/wp-json/
curl -s http://localhost:<PORT>/wp-login.php | grep -q '<form.*wp-login' && echo "OK"
```

### 3. 插件是否激活？

```bash
docker compose exec web wp plugin list --allow-root
docker compose exec web ls /var/www/html/wp-content/plugins/<slug>/
curl -s -o /dev/null -w "%{http_code}" http://localhost:<PORT>/wp-content/plugins/<slug>/readme.txt
```

### 4. 漏洞入口是否可达？

```bash
# 从模板复制 path，手动 curl
curl -sv http://localhost:<PORT>/<template-path> 2>&1 | grep "< HTTP"
```

### 5. 是否需要前置数据？

- 搜插件源码中该路由/钩子的入口条件
- 是否依赖某个 option / postmeta / 自定义表？
- 是否依赖某个 WordPress 页面/短代码？

### 6. 插件激活 hooks 是否触发？

- `register_activation_hook` 不会被 MU-plugin 触发
- 需在 entrypoint 中手动调用 `check_tables()` / `update_options()` 等

### 7. 时序问题？

- `wait_wp` 是否在 entrypoint 完成前就返回？
- 解决：预下载 zip 到镜像、预创建页面、减少运行时操作

---

## 常见问题速查

| 问题 | 原因 | 解决 |
|------|------|------|
| readme.txt 404 | 插件未安装 | 检查 entrypoint 日志，确认 PLUGIN_SLUG/PLUGIN_VERSION 已设置 |
| 漏洞页面 404 | 插件未创建所需页面 | 在 entrypoint 中预创建页面（模式 B） |
| `Call to undefined function WPMAIL()` | `wp eval` 运行时插件未加载 | 确保插件先 `wp plugin activate`，再 eval |
| "table already exists" 错误 | `check_tables()` 重复调用 | 无害，忽略；首次容器启动时为正常行为 |
| `check_table()` 不创建表 | `is_admin()` 守卫 | 定义 `DOING_AJAX` 绕过（模式 C） |
| nuclei type=flow 但 matcher-status=false | 第一步校验失败 | 检查 readme.txt 是否 200，内容是否含模板要求的字符串 |
| wait_wp 超时 | 插件下载慢 | 预下载 zip 到镜像（Dockerfile 中 RUN curl） |
| 主项目 scan.sh FAIL 但手动 nuclei PASS | 时序竞争 | 预下载 + 预创建，减少 entrypoint 运行时的网络操作 |
| 容器启动后 wp-json 500 | WordPress 未安装完成 | 正常，等 entrypoint 完成 |
| `wp option update` 权限错误 | option 名称不对 | 检查插件源码中 `$this->pre` 前缀 |

## 合并到主项目注意事项

1. **端口硬编码**：`${WEB_PORT:-8088}:80` → `8201:80`，`${MYSQL_PORT:-3307}:3306` → `3421:3306`
2. **PLUGIN_SLUG/PLUGIN_VERSION 硬编码**：主项目 `scan.sh` 不 source `.env`
3. **URL 硬编码**：`http://localhost:${WEB_PORT:-8088}` → `http://localhost:8201`
4. **environments.toml**：追加 `[[environment]]` 块，含 `name` / `notes` 字段
5. **INDEX.md**：追加表格行
6. **最终在主项目运行 `bash scan.sh <CVE-ID>` 确认 PASS**
