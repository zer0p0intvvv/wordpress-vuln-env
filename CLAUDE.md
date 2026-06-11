# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Purpose

自动生成 WordPress 插件漏洞的 Docker 测试环境，用于 nuclei 模板验证和漏洞复现。

`docs/Project.md` 保存当前项目进度及所有已完成环境的端口表，工作流变更时请同步维护。CVE 数量、端口占用以 manifest / `environments.toml` 为准，本文件不重复统计。

项目数据分两类，对应两条独立工作流：

---

## 工作流 A：有模板 → 搭建验证环境

针对 projectdiscovery/nuclei-templates 中已有的 WordPress 插件 CVE 模板，搭建对应漏洞环境并验证模板能命中。

### 数据来源

CVE 信息通过 `vulnx-cve` 技能从 ProjectDiscovery 数据库读取，写入 **`cve-manifest-a.json`**。

```bash
# 查单个 CVE
python3 .claude/skills/vulnx-cve/scripts/cve.py CVE-XXXX-XXXX --format json

# 搜索有 nuclei 模板的 WP 插件 CVE
vulnx search "wordpress plugin && is_template:true" --limit 20 --json --silent
```

manifest 条目包含：`cve_id` / `plugin_name` / `target` / `description` / `severity` / `cvss_score` / `vulnerability_type` / `references` / `diff_links` / `has_poc` / `is_kev`

### 步骤

1. 用 `vulnx-cve` 查 CVE，补充到 `cve-manifest-a.json`
2. 在 `nuclei-templates/` 中确认官方模板存在，读取模板确认：端口、认证要求、OOB 标记
3. 在 `vulhub-env/<plugin-slug>/<CVE-ID>/` 下创建 `docker-compose.yml` + `docker-entrypoint.sh`，参考下文「技术约束与已知坑」「Entrypoint 模式参考」两节
4. 启动环境，用**官方模板原文**运行 nuclei 验证（具体流程见下文「测试工作流」）
5. 在 `vulhub-env/environments.toml`、`nuclei-templates/INDEX.md`、`test-batch.sh` 中补充条目

### ⚠️ 硬性规则：官方 nuclei 模板不可修改

- 官方模板是**验证基准**，不是调试对象。模板打不中 → 说明环境有问题，去修环境
- **严禁**修改 `nuclei-templates/` 下来自官方库的任何 `.yaml` 文件
- 如果官方模板存在已知 matcher 误判（见"Nuclei 版本兼容性"章节），手动 curl 验证通过后标记 PASS，不修改模板
- 唯一例外：自行编写的 `CVE-2026-xxxx.yaml`（工作流 B 产出）可以修改

### 命令

```bash
# 启动单个环境
cd vulhub-env/<plugin-slug>/<CVE-ID>
docker compose up -d --build

# nuclei 验证（无认证）—— 使用官方模板原文，不做任何修改
# 注：项目约定 nuclei 二进制在 ~/工具/nuclei，团队成员可改为本地 PATH
~/工具/nuclei -t nuclei-templates/CVE-XXXX-XXXX.yaml -u http://localhost:<PORT>

# nuclei 验证（需 admin 认证）
~/工具/nuclei -t nuclei-templates/CVE-XXXX-XXXX.yaml -u http://localhost:<PORT> \
  -V "username=admin" -V "password=admin"

# 检查端口冲突
lsof -i :<port>
```

---

## 工作流 B：当前暂缓

> **状态：已搭建若干环境但模板验证链路不可靠，暂不新增。**
>
> 工作流 B 涉及纯靠漏洞描述搭建环境并自行编写 nuclei 模板。
> 已有数据记录在 `cve-manifest-b.json`，其中部分条目带 `custom_template: true` 字段表示已写自建模板，但其可靠性尚未建立完整验证链路。
>
> **当前所有新任务聚焦工作流 A。** 已存在的工作流 B 环境保留，但不在本轮推进。

---

## 共用：架构

### 两层基础镜像体系

每个漏洞环境的 `docker-compose.yml` 中的 `build:` 指向 `vulhub-env/base/` 下的某个目录，分两种类型：

1. **通用基础镜像** `base/wordpress/6.4/`：适用于插件仍可从 WP.org 在线安装的情况。`docker-entrypoint.sh` 读取 `WORDPRESS_PLUGIN_SLUG` / `WORDPRESS_PLUGIN_VERSION` 环境变量自动安装并激活插件，WordPress 安装、DB 等待、permalink 配置全部在 entrypoint 中自动完成。

2. **插件专用镜像** `base/<plugin-slug>/<version>/`：插件源码预先 `COPY` 进镜像（`plugins/` 目录），不再依赖 WP.org。适用于插件已从 WP.org 移除、版本已下架、或依赖链复杂的情况。**激活方式优先使用 MU-plugin**（见「技术约束」第 9 条），而非 entrypoint 中的 `wp plugin activate`，可彻底规避 Windows CRLF / wp-cli 时序问题。

### 文件关系

- `cve-manifest-a.json` — 工作流 A 数据源；**仅收录 vulnx `is_template:true` 确认的官方模板 CVE**；由 `vulnx-cve` 富化，包含 description / references / diff_links 等字段。当前总数以文件实际行数为准
- `cve-manifest-b.json` — 无官方模板的 CVE 集合；其中部分有本地自建模板（`custom_template: true` 字段标记），自建模板**不等同于官方模板**，不受"不可修改"约束。当前总数以文件实际行数为准
- `vulhub-env/environments.toml` — 所有环境的元数据索引（name / cve / path / dockerfile / tags），不含端口，端口在各环境的 `docker-compose.yml` 里；新增环境后必须追加
- `nuclei-templates/INDEX.md` — 模板索引，记录每个模板的端口、认证要求、OOB 标记
- `test-batch.sh` — 批量测试脚本，内部硬编码 CVE:PORT 对和认证白名单；新增环境需手动追加对应条目

### 端口分配

- Web 端口段：从 8088 起按 manifest 顺序占用；新环境取 `environments.toml` 中已用最大 Web 端口 +1
- MySQL 端口段：从 3307 起，规则同上
- 提交前用 `lsof -i :<port>` 确认无冲突

---

## 共用：技术约束与已知坑

### 1. `create_function()` 兼容性（硬性约束）
使用该函数的插件必须运行在 PHP 7.4。新环境不要尝试 PHP 8.x。其他插件优先在 PHP 8.2 上测试，只有确实失败才降级（降级会引入 Debian bullseye 仓库签名问题，不要仅凭模板说明就切换）。

### 2. WP.org 插件安装容错（系统性风险）

`wp plugin install` 走 WP.org API，以下情况会失败：

| 失败原因 | 表现 | 案例 |
|---|---|---|
| SSL 故障 | `cURL error 35: OpenSSL SSL_ERROR_SYSCALL` | 间歇性，重试可解 |
| 插件关闭 (closed) | API 返回 "plugin not found"，但 CDN zip 仍可下载 | subscribe-to-category |
| 版本不存在 | API 返回 404，SVN 中该版本号缺失 | hunk-companion 1.8.9（实际 SVN 只有 1.8.8） |

**解决方案：`base/wordpress/6.4/docker-entrypoint.sh` 已内置三级回退**——

1. 插件目录已存在 → 直接 `wp plugin activate`
2. `wp plugin install --version=$VER` 走 WP.org API
3. API 失败 → `curl` 从 `https://downloads.wordpress.org/plugin/{slug}.{version}.zip` 下载 → `unzip` → `wp plugin activate`

CDN 直链下载绕过了 API 层，即便插件已关闭（closed）、短期 SSL 故障也能装。**大部分环境无需为插件安装问题单独创建 base 镜像**，直接用通用 `base/wordpress/6.4` 即可。

**只有以下情况才需要插件专用 base 镜像**（`base/<plugin-slug>/<version>/`）：
- 需要额外的 MU-plugin 或代码注入（如 hunk-companion 需强制加载 import 模块注册 REST 路由）
- 插件依赖复杂（如需要特定主题或第三方库）
- CDN 上该版本 zip 也不存在，只能本地缓存

### 3. 插件版本 vs 漏洞真实可用性
"漏洞版本"可能已被 silently patched，不能只看版本号：
- simple-file-list 3.2.7 加了 `realpath()` 检测，模板的相对路径 `../` 被拦截
- 选 CVE 时要下载该版本实际验证漏洞 sink 是否仍然存在

### 4. 类未实例化问题
部分插件的 REST API 类在插件加载时未自动实例化（如 CVE-2024-2667 v0.1.0.22），需要在 entrypoint 中手动 `new` 实例。

### 5. REST 路由注册时机
部分插件的 REST 控制器继承 `WP_REST_Controller`，但在 `plugins_loaded` 时该类尚未定义，导致 fatal error，REST 路由从未注册。需在 `init` 钩子之后延迟加载（如 html5-video-player 的 `VideoController`）。

### 6. 插件依赖链必须完整
- royal-elementor-addons → 依赖 Elementor（且 Elementor 版本需与 WP 6.4 兼容，最新版要求 WP 6.6+）
- ti-woocommerce-wishlist → 依赖 WooCommerce
- 依赖插件不能装最新版，需指定兼容版本

**如何确定兼容版本**：WP.org 插件页 "Advanced View" → "Previous Versions" 下拉框，挑选发布时间早于目标 WP 版本发布日的最新版；或看插件 `readme.txt` 的 `Requires at least` / `Tested up to` 字段。

### 7. 数据初始化深度
有些环境需要远超"激活插件"的初始化：
- wd-google-maps：需创建 `gmwd_maps`/`gmwd_markers`/`gmwd_options` 表，首页插入短代码 `[Google_Maps_WD map=1]`
- ultimate-member：需创建注册页面并设置 `_um_mode=register` + `_um_is_default=1` postmeta
- really-simple-ssl：需在 `rsssl_options` 中启用 `login_protection_enabled` 才能加载 two-fa 模块

**如何发现此类需求**：nuclei FAIL → 看 nuclei 模板的 path/参数 → 反查插件源码中该路由/钩子的入口条件（是否依赖某 option、postmeta、表存在），把这些前置数据在 entrypoint 中创建出来。

### 9. Windows CRLF 行尾与插件激活时序问题

**现象**：在 Windows 主机上 `docker compose up -d --build` 后插件 inactive，nuclei 第一步 matcher 失败。

**根因**（双重）：
1. **CRLF 行尾**：Windows git 默认 `core.autocrlf=true`，`.sh` 文件被转为 `\r\n`，容器内报 `bad interpreter: ^M`，entrypoint 完全不执行
2. **wp-cli 时序**：即使脚本正常执行，Windows Docker Desktop（WSL2/Hyper-V）启动比 Linux 慢，`wp plugin activate` 可能在 WordPress 正在写入 `wp_options` 的窗口期内失败，加上 `|| true` 完全无声无息

**解决方案：MU-plugin 强制激活**（已在 ds-cf7-math-captcha 验证，参考 `base/ds-cf7-math-captcha/2.0.1/mu-plugins/`）

在插件专用 base 目录下建 `mu-plugins/force-activate-<plugin-slug>.php`：

```php
<?php
add_filter('option_active_plugins', function ($plugins) {
    $target = '<plugin-slug>/<plugin-slug>.php';
    if (!in_array($target, (array) $plugins, true)) {
        $plugins[] = $target;
    }
    return $plugins;
});
```

Dockerfile 中 COPY 并 dos2unix：

```dockerfile
RUN mkdir -p /var/www/html/wp-content/mu-plugins
COPY mu-plugins/force-activate-<plugin-slug>.php /var/www/html/wp-content/mu-plugins/
RUN dos2unix /var/www/html/wp-content/mu-plugins/force-activate-<plugin-slug>.php && \
    chown www-data:www-data /var/www/html/wp-content/mu-plugins/force-activate-<plugin-slug>.php
```

**原理**：MU-plugin 在 WordPress 每次请求时自动加载，通过 `option_active_plugins` filter 注入激活状态，完全绕过 wp-cli 和 shell 脚本。纯 PHP，无平台依赖，build 成功即插件必然激活。

**附**：根目录 `.gitattributes` 已配置 `*.sh eol=lf`，防止 Windows git clone 时转换行尾，与 Dockerfile 中的 `dos2unix` 形成双重保险。

### 8. Nuclei 检测分类（建环境时判断初始化需求）
- **直接检测** (~47%): 无需认证，nuclei 可直接触发
- **需 admin 认证** (~20%): 需预设管理员 cookie 或 Basic Auth
- **需数据初始化** (~13%): entrypoint 必须创建前置数据
- **OOB 限制** (~13%): 需 out-of-band 回连，nuclei 受限，手动 curl 验证后标记 PASS

---

## 共用：Entrypoint 模式参考

下文占位符约定：`<plugin-slug>` 为插件目录名（如 `royal-elementor-addons`）、`<plugin-version>` 为版本号、`<ClassName>` 为 PHP 类名。

```bash
# ① 等待 WordPress 就绪（所有 entrypoint 的第一步）
until wp core is-installed 2>/dev/null; do sleep 2; done

# ② 本地 zip 安装（优先于 wp plugin install）
if [ ! -d "/var/www/html/wp-content/plugins/<plugin-slug>" ]; then
    unzip -oq /tmp/<plugin-slug>.<plugin-version>.zip -d /var/www/html/wp-content/plugins/
    chown -R www-data:www-data /var/www/html/wp-content/plugins/<plugin-slug>
fi
wp plugin activate <plugin-slug> --allow-root

# ③ 安装依赖插件（指定兼容版本）
wp plugin install elementor --version=3.18.0 --activate --allow-root || \
    unzip -oq /tmp/elementor.3.18.0.zip -d /var/www/html/wp-content/plugins/
wp plugin activate elementor --allow-root

# ④ 手动实例化未注册的 REST 类
php -r "require_once '/var/www/html/wp-content/plugins/<plugin-slug>/<file>.php'; new <ClassName>();"

# ⑤ REST 路由延迟注册（避免 plugins_loaded 时 WP_REST_Controller 未定义）
wp --allow-root eval '
add_action("init", function() {
    require_once "/var/www/html/wp-content/plugins/<plugin-slug>/<rest-controller>.php";
    (new <Namespace>\<ClassName>())->register_routes();
});
'

# ⑥ 创建插件依赖的自定义数据表
wp --allow-root eval '
global $wpdb;
$wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}gmwd_maps (id int, title varchar(255), published int)");
$wpdb->query("INSERT INTO {$wpdb->prefix}gmwd_maps VALUES (1, \"Test\", 1)");
'

# ⑦ 修改插件选项以加载漏洞模块
wp --allow-root option get rsssl_options --format=json | \
    jq '. + {"login_protection_enabled": 1}' | \
    wp --allow-root option set rsssl_options --format=json

# ⑧ MU-plugin 强制激活（Windows 兼容方案，替代 wp plugin activate）
# 文件放在 base/<plugin-slug>/<version>/mu-plugins/ 目录，Dockerfile 中 COPY 进镜像
# WordPress 每次请求自动加载 mu-plugins/，无需 wp-cli，无平台依赖
# 文件内容：
#   <?php
#   add_filter('option_active_plugins', function ($plugins) {
#       $target = '<plugin-slug>/<plugin-slug>.php';
#       if (!in_array($target, (array) $plugins, true)) {
#           $plugins[] = $target;
#       }
#       return $plugins;
#   });
```

---

## 共用：Nuclei 模板

- 模板目录: `nuclei-templates/`
- 工作流 A 模板：来自 projectdiscovery/nuclei-templates 官方库，按原始 CVE 编号命名
- 工作流 B 模板：自行编写，以 `CVE-2026-xxxx` 编号，存放在同一目录
- 需认证模板：运行时加 `-V "username=admin" -V "password=admin"`（具体清单见 `nuclei-templates/INDEX.md`）
- OOB 模板（如 CVE-2021-25052 / CVE-2024-2667）：使用 `{{interactsh-url}}`，自动验证受限，手动 curl 验证后标记 PASS

### Nuclei 版本兼容性

> 本节基于撰写时使用的 nuclei v3.4.10；升级 nuclei 后请重新核对以下条目。

以下已知 matcher 误判，手动 curl 验证通过则标记 PASS：
- `CVE-2024-10924`（really-simple-ssl）: 第三步 word matcher 返回 `0 matches`
- `CVE-2023-6553`（backup-backup）: `len(body)==0` + `status_code==200` DSL 不命中
- `CVE-2024-8852`（all-in-one-wp-migration）: Apache 不返回 `Content-Type: text/plain`

---

## 共用：测试工作流

### 整体分工

```
test-batch.sh          纯机械执行：启动容器 → 运行 nuclei → 输出原始 jsonl
      ↓
Agent 读取 jsonl       判断每条结果：强特征是否命中
      ↓
lark-doc 写入飞书      按格式追加到日志文档
      ↓
人工在飞书核对          确认 evidence 是否真实，附注异常
```

### 第一步：运行脚本

```bash
# 跑全部（manifest-a.json，批大小 10）
bash test-batch.sh

# 脚本最后一行输出 jsonl 文件路径，例如：
# /path/to/nuclei-raw-20260603-1430.jsonl
```

脚本只负责执行，**不做 PASS/FAIL 判断**。

### 第二步：Agent 读取 jsonl 并判断

读取 jsonl，对每一条记录按以下标准判断：

**命中（PASS）条件：同时满足**
1. nuclei 输出了真实的 JSON 匹配行（`matched` 非 false）
2. 存在 `matcher-name` 或 `extracted-results` 字段
3. `extracted-results` 的内容与漏洞强相关（见下表）

**强特征对照表：**

| 漏洞类型 | 强特征 evidence | 弱/无效 evidence |
|---|---|---|
| SQLi time-based | `duration >= 5`（配合 SLEEP payload） | 单纯 status 200 |
| SQLi union/error | MD5 定值（如 `e48e13207`）、DB 报错信息 | "success" 字符串 |
| LFI / 文件读取 | `root:x:0:0`、`DB_PASSWORD`、`define('` | 页面标题、插件名 |
| RCE | `uid=` + `gid=` + `groups=` 同时出现 | 单独 uid= |
| XSS 反射型 | 注入的精确 payload 原样反射 | 含 script 的任意响应 |
| 文件上传 RCE | 自定义唯一标记（echo 输出） | HTTP 200 + 文件名 |
| 权限绕过 | 管理员专属字段（`wp_capabilities`、`role:administrator`） | 任意 JSON 响应 |
| SSRF | 内网/metadata 内容回显（如 `169.254.169.254` 响应体） | 单纯 200 状态码 |
| XXE | 外部实体引用回显的文件内容（payload 中含 `<!ENTITY>` 是区分点） | 仅 XML 解析错误 |
| CSRF | 状态变更前后差异（对比验证，而非响应字符串） | 任意 200 响应 |

**未命中（FAIL）**：nuclei 无输出，或输出中 `matched: false`。
**跳过（SKIP）**：`skipped: true`（OOB 模板），需人工 curl 验证。
**异常（ERROR）**：`error: wp_not_ready`，环境问题，需排查 entrypoint。

### 第三步：写入飞书

doc_id: `Daoxdyn1toejyhxnNWucMIRinKc`（仓库公开后建议把此 ID 移到本地 `.env` 引用，避免直接写入文档），用 `lark-cli docs +update --command append` 追加。

每个 CVE 写一条（格式见下文「环境搭建日志规则」）
### 第四步：人工核对要点

- 看 `evidence` 是否确实是漏洞触发证据（对照上方强特征表）
- SKIP 条目需手动 curl 测试后在飞书备注 PASS/FAIL
- FAIL 条目检查是否属于已知 matcher 误判（见"Nuclei 版本兼容性"章节）

---

## git 使用规则

所有 git 命令在 `wordpress漏洞环境自动化生成/` 目录下执行（或用 `git -C /Users/zer0p0int/Desktop/wordpress漏洞环境自动化生成/`）。

完成验证后通过 `git-commit-push` Skill 提交并推送到 `https://github.com/zer0p0intvvv/wordpress-vuln-env.git`。提交信息用中文，说明"改了什么、为什么改、如何确认验证通过"。禁止 `git push --force` 或其他改写历史的命令（`git reset --hard <已推送的提交>` + push、`git rebase` 已推送分支等同样禁止）。

---

## 环境搭建日志规则

在执行环境搭建相关任务时，通过 `lark-doc` 把过程记录到飞书文档。

### 记录方式

- **doc_id**: `Daoxdyn1toejyhxnNWucMIRinKc`
- **URL**: https://rcn285sv4kcb.feishu.cn/docx/Daoxdyn1toejyhxnNWucMIRinKc
- 文档已存在，直接用 `lark-cli docs +update --command append` 追加，禁止重复创建。
- **不要每一条命令都记**，只记有意义的节点。

### 记录时机（满足任一就记一条）

- 完成一个阶段性步骤（装好依赖、初始化某个服务、配置文件写好）
- 遇到报错以及最终的解决办法（失败的尝试也要记，往往比成功步骤更有参考价值）
- 任务结束时写一条总结

### 记录格式

```
每个 CVE 开头（只写一次）：
CVE-XXXX-XXXX ✅/❌
插件：xxx-plugin　版本：x.x.x　类型：SQLi/XSS/…　端口：81XX

每条操作日志：
🔹 [时间] nuclei 验证
操作：运行 nuclei-templates/CVE-XXXX-XXXX.yaml
结果：✅ PASS / ❌ FAIL / ⏭ SKIP
强特征：matcher=<matcher-name>  evidence=<extracted-results 前120字符>
备注：<异常说明，或"无">
```

- 正常步骤用 🔹，失败步骤用 🔸，nuclei 验证结果用 ✅/❌
- 不记录 Dockerfile / docker build 等执行细节
- 密钥、token、密码用 `<已隐藏>` 代替