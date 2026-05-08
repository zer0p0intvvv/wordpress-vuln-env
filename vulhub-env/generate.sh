#!/bin/bash
# Batch generate vulhub-env environments for WordPress plugin vulnerabilities
# Usage: bash generate.sh

set -euo pipefail
cd "$(dirname "$0")"

BASE_BUILD="../../base/wordpress/6.4"
WEB_PORT=8089
DB_PORT=3308

generate_env() {
    local plugin_slug="$1"
    local cve_id="$2"
    local plugin_version="$3"
    local vuln_type="$4"
    local description="$5"
    local description_zh="$6"
    local exploit_detail="$7"
    local exploit_detail_zh="$8"

    local dir="${plugin_slug}/${cve_id}"
    mkdir -p "$dir"

    # docker-compose.yml
    cat > "$dir/docker-compose.yml" <<YAML
services:
  web:
    build: ${BASE_BUILD}
    depends_on:
      - db
    ports:
      - "${WEB_PORT}:80"
    environment:
      WORDPRESS_DB_HOST: db:3306
      WORDPRESS_DB_USER: wordpress
      WORDPRESS_DB_PASSWORD: wordpress
      WORDPRESS_DB_NAME: wordpress
      WORDPRESS_URL: http://localhost:${WEB_PORT}
      WORDPRESS_TITLE: Vulhub Test
      WORDPRESS_ADMIN_USER: admin
      WORDPRESS_ADMIN_PASSWORD: admin
      WORDPRESS_ADMIN_EMAIL: admin@example.com
      WORDPRESS_PLUGIN_SLUG: ${plugin_slug}
      WORDPRESS_PLUGIN_VERSION: "${plugin_version}"
      DEBUG_MODE: "false"
  db:
    image: mysql:5.7
    ports:
      - "${DB_PORT}:3306"
    environment:
      - MYSQL_ROOT_PASSWORD=root
      - MYSQL_DATABASE=wordpress
      - MYSQL_USER=wordpress
      - MYSQL_PASSWORD=wordpress
YAML

    # README.md
    cat > "$dir/README.md" <<MD
# ${plugin_slug} <= ${plugin_version} - ${vuln_type}

[中文版本(Chinese version)](README.zh-cn.md)

${description}

## Vulnerability Details

- **Affected Versions**: ${plugin_slug} <= ${plugin_version}
- **Vulnerability Type**: ${vuln_type}
- **Authentication Required**: No
- **Attack Vector**: Network

## Environment Setup

\`\`\`
docker compose up -d
\`\`\`

Access \`http://localhost:${WEB_PORT}\`, admin/admin.

## Exploitation

${exploit_detail}

![](1.png)

## References

- <https://nvd.nist.gov/vuln/detail/${cve_id}>
MD

    # README.zh-cn.md
    cat > "$dir/README.zh-cn.md" <<MD
# ${plugin_slug} <= ${plugin_version} - ${vuln_type}

${description_zh}

## 漏洞详情

- **影响版本**: ${plugin_slug} <= ${plugin_version}
- **漏洞类型**: ${vuln_type}
- **是否需要认证**: 不需要
- **攻击向量**: 网络

## 环境搭建

\`\`\`
docker compose up -d
\`\`\`

访问 \`http://localhost:${WEB_PORT}\`，账号 admin，密码 admin。

## 漏洞利用

${exploit_detail_zh}

![](1.png)

## 参考链接

- <https://nvd.nist.gov/vuln/detail/${cve_id}>
MD

    echo "[+] Generated: ${dir} (web:${WEB_PORT} db:${DB_PORT})"
    WEB_PORT=$((WEB_PORT + 1))
    DB_PORT=$((DB_PORT + 1))
}

# ============================================================
# SQLI
# ============================================================

generate_env "secure-copy-content-protection" "CVE-2021-24931" "2.8.1" "SQL Injection" \
"The Secure Copy Content Protection and Content Locking WordPress plugin before 2.8.2 does not escape the sccp_id parameter of the ays_sccp_results_export_file AJAX action (available to both unauthenticated and authenticated users) before using it in a SQL statement, leading to an SQL injection." \
"Secure Copy Content Protection WordPress插件在2.8.2之前的版本中，未对ays_sccp_results_export_file AJAX动作的sccp_id参数进行转义就直接用于SQL语句，导致SQL注入漏洞。该接口对未授权和授权用户均可用。" \
"The vulnerability is in the \`ays_sccp_results_export_file\` AJAX action. The \`sccp_id\` parameter is directly concatenated into a SQL query without escaping. Send a crafted POST request to \`/wp-admin/admin-ajax.php\` with action=ays_sccp_results_export_file and a malicious sccp_id payload." \
"漏洞存在于\`ays_sccp_results_export_file\` AJAX动作中。\`sccp_id\`参数被直接拼接到SQL查询中，未经转义。向\`/wp-admin/admin-ajax.php\`发送构造的POST请求，设置action=ays_sccp_results_export_file和恶意的sccp_id载荷。"

generate_env "paid-member-subscriptions" "CVE-2023-23488" "2.9.7" "SQL Injection" \
"The Paid Memberships Pro WordPress Plugin, version < 2.9.8, is affected by an unauthenticated SQL injection vulnerability in the 'code' parameter of the '/pmpro/v1/order' REST route." \
"Paid Memberships Pro WordPress插件2.9.8之前的版本在'/pmpro/v1/order' REST路由的'code'参数中存在未授权SQL注入漏洞。" \
"The vulnerability is in the REST endpoint \`/wp-json/pmpro/v1/order\`. The \`code\` parameter is not properly sanitized before being used in a SQL query. Send a GET request to \`/wp-json/pmpro/v1/order?code=<SQL_PAYLOAD>\` to trigger the injection." \
"漏洞位于REST端点\`/wp-json/pmpro/v1/order\`。\`code\`参数在用于SQL查询前未经充分清理。发送GET请求到\`/wp-json/pmpro/v1/order?code=<SQL_PAYLOAD>\`触发注入。"

# ============================================================
# XSS
# ============================================================

generate_env "permalink-manager" "CVE-2022-0201" "2.2.14" "Reflected XSS" \
"The Permalink Manager Lite WordPress plugin before 2.2.15 and Permalink Manager Pro WordPress plugin before 2.2.15 do not sanitise and escape the _wp_http_referer parameter before outputting it back in the response of an AJAX action, leading to a reflected Cross-Site Scripting (XSS) vulnerability." \
"Permalink Manager Lite/Pro WordPress插件2.2.15之前的版本未对_wp_http_referer参数进行清理和转义就直接在AJAX动作的响应中输出，导致反射型XSS漏洞。" \
"The vulnerability is in an AJAX action response. The \`_wp_http_referer\` parameter is reflected in the response without sanitization. Inject JavaScript via the referer parameter: \`?_wp_http_referer=<script>alert(1)</script>\`." \
"漏洞存在于AJAX动作响应中。\`_wp_http_referer\`参数未经清理就直接反射到响应中。通过referer参数注入JavaScript：\`?_wp_http_referer=<script>alert(1)</script>\`。"

generate_env "learnpress" "CVE-2022-0271" "4.1.5" "Reflected XSS" \
"The LearnPress WordPress plugin before 4.1.6 does not sanitise and escape the lp-dismiss-notice before outputting it back via the admin_notices hook, leading to a Reflected Cross-Site Scripting (XSS) vulnerability." \
"LearnPress WordPress插件4.1.6之前的版本未对lp-dismiss-notice参数进行清理和转义，就通过admin_notices钩子输出，导致反射型XSS漏洞。" \
"The vulnerability is triggered via the \`admin_notices\` hook. The \`lp-dismiss-notice\` parameter value is reflected in the admin page without escaping. Access \`/wp-admin/?lp-dismiss-notice=<script>alert(1)</script>\` while authenticated." \
"漏洞通过\`admin_notices\`钩子触发。\`lp-dismiss-notice\`参数值未经转义就反射到管理页面中。在已认证状态下访问\`/wp-admin/?lp-dismiss-notice=<script>alert(1)</script>\`。"

# ============================================================
# CSRF
# ============================================================

generate_env "capability-manager-enhanced" "CVE-2021-25032" "2.3.0" "CSRF" \
"The PublishPress Capabilities Pro WordPress plugin before 2.3.1 does not have authorisation and CSRF checks when updating the plugin's settings via the init hook. This makes it possible for unauthenticated attackers to update arbitrary blog options, such as the default role and make any new registered user with an administrator role." \
"PublishPress Capabilities Pro WordPress插件2.3.1之前的版本在通过init钩子更新插件设置时没有授权和CSRF检查，导致未授权攻击者可以更新任意博客选项，如默认角色，使新注册用户自动获得管理员角色。" \
"The vulnerability allows unauthenticated attackers to update WordPress options via the init hook. Send a POST request to the site with specially crafted parameters to change the default_role to administrator. No CSRF token is required." \
"该漏洞允许未授权攻击者通过init钩子更新WordPress选项。向站点发送特制的POST请求，将default_role改为administrator。不需要CSRF令牌。"

generate_env "sitemap-by-click5" "CVE-2022-0952" "1.0.35" "CSRF" \
"The Sitemap by click5 WordPress plugin before 1.0.36 does not have authorisation and CSRF checks when updating options via a REST endpoint. This makes it possible for unauthenticated attackers to update arbitrary options, such as users_can_register and default_role to administrator." \
"Sitemap by click5 WordPress插件1.0.36之前的版本在通过REST端点更新选项时没有授权和CSRF检查，导致未授权攻击者可以更新任意选项，如将users_can_register和default_role设为administrator。" \
"The vulnerability is in a REST endpoint that allows updating WordPress options without authentication. Send a POST request to the REST API to set default_role=administrator and users_can_register=1." \
"漏洞位于一个允许未授权更新WordPress选项的REST端点。向REST API发送POST请求设置default_role=administrator和users_can_register=1。"

# ============================================================
# UPLOAD
# ============================================================

generate_env "3dprint-lite" "CVE-2021-4436" "1.9.1.4" "Unauthenticated File Upload RCE" \
"The 3DPrint Lite WordPress plugin before 1.9.1.5 does not have any authorisation and does not check the uploaded file in its 3dprint_lite_upload_file function, allowing unauthenticated attackers to upload arbitrary files, including PHP files, and achieve Remote Code Execution." \
"3DPrint Lite WordPress插件1.9.1.5之前的版本在3dprint_lite_upload_file函数中没有任何授权检查和文件类型验证，允许未授权攻击者上传任意文件（包括PHP文件），实现远程代码执行。" \
"The vulnerability is in the \`3dprint_lite_upload_file\` AJAX action. Upload a PHP file via POST to \`/wp-admin/admin-ajax.php\` with action=3dprint_lite_upload_file. The uploaded file is placed in the uploads directory and can be accessed directly for RCE." \
"漏洞存在于\`3dprint_lite_upload_file\` AJAX动作中。通过POST向\`/wp-admin/admin-ajax.php\`上传PHP文件，设置action=3dprint_lite_upload_file。上传的文件会被放置在uploads目录中，可直接访问实现RCE。"

generate_env "hash-form" "CVE-2024-5084" "1.1.0" "Unauthenticated File Upload RCE" \
"The Hash Form - Drag & Drop Form Builder plugin for WordPress is vulnerable to arbitrary file uploads due to missing file type validation in all versions up to, and including, 1.1.0. This makes it possible for unauthenticated attackers to upload arbitrary files, including PHP files, and achieve Remote Code Execution." \
"Hash Form - Drag & Drop Form Builder WordPress插件1.1.0及以下所有版本由于缺少文件类型验证，存在任意文件上传漏洞，允许未授权攻击者上传任意文件（包括PHP文件），实现远程代码执行。" \
"The plugin lacks file type validation on its file upload endpoint. Upload a PHP webshell via the form upload functionality. The uploaded file can be accessed in the uploads directory for code execution." \
"插件的文件上传端点缺少文件类型验证。通过表单上传功能上传PHP webshell。上传的文件可在uploads目录中访问以执行代码。"

echo ""
echo "[*] Generated 8 environments. Ports ${WEB_PORT}-$((WEB_PORT-1)) used."
echo "[*] To build all: for d in */CVE-*/; do (cd \"\$d\" && docker compose build &); done; wait"
