# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Purpose

自动生成 WordPress 插件漏洞的 Docker 测试环境，用于 nuclei 模板验证和漏洞复现。从JSON文件中提取漏洞信息，生成可直接 `docker-compose up` 的环境。
## docs
@../docs/Project.md 保存当前的项目的进度以及工作流，可以查看，当工作流和项目进度发生改变的时候，请及时维护；
## Architecture

- **输入**: JSON 文件（包含 CVE ID、插件名、版本号、漏洞类型等）
    JSON 文件按来源/批次分文件，结构大致为：
    ```json
    {
      "cve_id": "CVE-XXXX-XXXX",
      "plugin_slug": "xxx",
      "plugin_version": "x.x.x",
      "vuln_type": "SQLi/XSS/LFI/RCE",
      "description": "...",
      "references": ["..."]
    }
    ```
- **输出**: 每个 CVE 一个独立目录，包含 `docker-compose.yml`、`Dockerfile`、插件源码、entrypoint 脚本
- **验证**: 配套 nuclei 模板自动检测漏洞是否可触发，使用nuclei命令或者test-all.sh批量脚本

### 环境配置

- **PHP 版本**: 7.4（必须 — 几乎所有目标插件使用 `create_function()`，PHP 8.0+ 不兼容）
- **源码策略**: 优先使用`wp plugin install`，当WordPress.org 移除插件后无法下载，使用本地源码
- **端口分配**: HTTP 8098-8113 / MySQL 3318-3333（每个环境递增）
## 专属问题
  ### Key Technical Constraints

    1. **`create_function()` 兼容性**: 这是硬性约束。所有使用该函数的插件必须运行在 PHP 7.4 上。新环境不要尝试 PHP 8.x。
    2. **插件从 WP.org 移除**: 如 `gallery`（CVE-2022-1946），必须提前缓存源码，不能依赖在线安装。
    3. **类未实例化问题**: 部分插件的 REST API 类或漏洞类在插件加载时未自动实例化（如 CVE-2024-2667 v0.1.0.22），需要在 entrypoint 中手动 `new` 实例。
    4. **Nuclei 检测分类**:
      - **直接检测** (~47%): 无需认证，nuclei 可直接触发
      - **需 admin 认证** (~20%): 环境需预设管理员 cookie 或 Basic Auth
      - **需数据初始化** (~13%): entrypoint 必须创建前置数据（gallery、upload 目录等）
      - **OOB 限制** (~13%): 需 out-of-band 回连，nuclei 受限但手动验证可利用

    5. **WP.org SSL 间歇性故障（系统性风险）**:
       验证中发现大量环境因 `cURL error 35: OpenSSL SSL_ERROR_SYSCALL` 导致 `wp plugin install` 失败。
       - entrypoint 中不要依赖 `wp plugin install` 作为唯一安装方式
       - 优先预下载插件 zip 到本地缓存，entrypoint 通过 `unzip` 本地安装
       - 若必须在线安装，entrypoint 需包含重试逻辑（sleep + 重试 3 次）

    6. **插件版本 vs 漏洞真实可用性**:
       有些"漏洞版本"实际上已被 silently patched，不能直接按版本号选 CVE：
       - simple-file-list 3.2.7 加了 `realpath()` 检测，模板用的相对路径 `../` 被拦截
       - swift-performance-lite WP.org 已下架 `<2.3.7.2` 版本，无法获取漏洞版
       - 选 CVE 时不仅要查版本号，还要下载该版本实际验证漏洞 sink 是否仍然存在

    7. **REST 路由注册时机**:
       部分插件的 REST 控制器继承 `WP_REST_Controller`，但在 `plugins_loaded` 时该类尚未定义，导致 fatal error，REST 路由从未注册。
       - 如 html5-video-player 的 `VideoController` 需在 `init` 钩子之后延迟加载
       - entrypoint 中可通过 `wp eval` 在 `init` 后手动触发 `register_rest_route()`

    8. **插件依赖链必须完整**:
       很多漏洞插件依赖其他插件/主题：
       - royal-elementor-addons → 依赖 Elementor（且 Elementor 版本需兼容 WP 版本）
       - ti-woocommerce-wishlist → 依赖 WooCommerce
       - 依赖插件也必须是漏洞兼容版本，不能装最新版（Elementor 最新版要求 WP 6.6+，base 镜像是 WP 6.4）

    9. **PHP 7.4 vs 8.2 的实际差异**:
       模板声明需要 PHP 7.4（如 wp-file-upload），但漏洞在 PHP 8.2 上也能触发。
       - 不要仅凭模板说明就切换 PHP 7.4（会引入 Debian bullseye 仓库签名问题）
       - 优先在 PHP 8.2 上测试，只有确实触发失败时才降级

    10. **数据初始化深度**:
        有些环境需要远超"激活插件"的初始化：
        - wd-google-maps：需创建数据库表 `gmwd_maps`/`gmwd_markers`/`gmwd_options`，并在首页插入正确短代码 `[Google_Maps_WD map=1]`
        - ultimate-member：需创建注册页面并设置 `_um_mode=register` + `_um_is_default=1` postmeta，且需配置表单字段
        - really-simple-ssl：需在 `rsssl_options` 数组中启用 `login_protection_enabled` 才能加载 two-fa 模块

    11. **Nuclei 版本兼容性**:
        当前环境使用 nuclei v3.4.10，部分官方模板 matcher 识别存在问题：
        - really-simple-ssl 模板第三步 matcher 明明条件满足但返回 `0 matches`
        - backup-backup 模板 `len(body)==0` + `status_code==200` 不命中
        - 建议升级到 nuclei 最新版，或手动用 curl 验证后标记 PASS

  ### Entrypoint 常见模式

    创建新环境时，entrypoint 脚本通常需要处理：

    ```bash
    # 1. 等待 WordPress 就绪
    until wp core is-installed 2>/dev/null; do sleep 2; done

    # 2. 激活目标插件
    wp plugin activate <plugin-slug>

    # 3. 数据初始化（按需）
    wp post create --post_type=gallery --post_title='test' --post_status=publish
    mkdir -p /var/www/html/wp-content/uploads/<plugin-dir>

    # 4. 手动实例化未注册的类（PHP require + new）
    php -r "require_once '/var/www/html/wp-content/plugins/<plugin>/<file>.php'; new <ClassName>();"
    ```

    ### 新增 Entrypoint 模式（验证中发现的）

    ```bash
    # 5. 本地缓存安装（避免 WP.org SSL 故障）
    if [ ! -d "/var/www/html/wp-content/plugins/<plugin-slug>" ]; then
        unzip -oq /tmp/<plugin-slug>.<version>.zip -d /var/www/html/wp-content/plugins/
        chown -R www-data:www-data /var/www/html/wp-content/plugins/<plugin-slug>
    fi
    wp plugin activate <plugin-slug> --allow-root

    # 6. 安装插件依赖链
    # 如 royal-elementor-addons 需要 Elementor
    wp plugin install elementor --version=<compatible-version> --activate --allow-root || \
        unzip -oq /tmp/elementor.<version>.zip -d /var/www/html/wp-content/plugins/
    wp plugin activate elementor --allow-root

    # 7. 创建插件自定义数据表（如 wd-google-maps）
    wp --allow-root eval '
    global $wpdb;
    $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}gmwd_maps (...)");
    $wpdb->query("INSERT INTO {$wpdb->prefix}gmwd_maps (id, title, published) VALUES (1, \"Test\", 1)");
    '

    # 8. 为依赖 REST API 的插件延迟注册路由
    wp --allow-root eval '
    add_action("init", function() {
        require_once "/var/www/html/wp-content/plugins/<plugin>/<rest-controller>.php";
        $controller = new <Namespace>\<ClassName>();
        $controller->register_routes();
    });
    '

    # 9. 修复插件配置使其加载漏洞模块
    # 如 really-simple-ssl 需启用 login_protection 才能加载 two-fa
    wp --allow-root option get um_options --format=json | \
        jq '. + {"login_protection_enabled": 1}' | \
        wp --allow-root option set um_options --format=json
    ```


## Nuclei Templates

- 官方模板目录: `nuclei-templates/`（从 projectdiscovery/nuclei-templates 获取）
- 索引: `nuclei-templates/INDEX.md`（含端口映射和认证/OOB 标记）
- 命名: `CVE-XXXX-XXXX.yaml`
- 当前 25 个模板与 vulhub-env 环境一一对应
- 需认证模板（5个）使用 `-V "username=admin" -V "password=admin"`
- OOB 模板（2个）使用官方 interactsh 服务

### Template Conventions

- 需要认证的模板使用 `cookie` 或自定义 header 变量
- OOB 检测使用 `{{interactsh-url}}`

### Nuclei 版本兼容性注意事项

当前项目使用 nuclei v3.4.10，与部分官方模板存在 matcher 兼容性问题：
- **really-simple-ssl (CVE-2024-10924)**: 第三步 `word` matcher 条件满足但返回 `0 matches`
- **backup-backup (CVE-2023-6553)**: `len(body)==0` + `status_code==200` 的 DSL matcher 不命中
- **all-in-one-wp-migration (CVE-2024-8852)**: Apache 对 `.log` 文件未发送 `Content-Type: text/plain`，导致 `contains(tolower(header), 'text/plain')` 失败
- **建议**: 若 nuclei 未命中但手动 curl 验证成功，应标记为 PASS 并记录 nuclei 版本问题

## git 使用规则

### 仓库结构

- `.git` 目录位于 `wordpress漏洞环境自动化生成/` 下（即 `claude-code/BlackWidow/.git`）
- 仅 `wordpress漏洞环境自动化生成` 目录纳入版本控制

### 命令执行

所有 git 命令必须在 `wordpress漏洞环境自动化生成/` 目录下执行，或使用`git -C /Users/zer0p0int/Desktop/wordpress漏洞环境自动化生成/` 指定路径。

### 提交规则

- 每次完成代码修改并验证可用性后，必须通过 `git-commit-push` Skill提交代码并推送到私有仓库
- 提交前必须先检查工作区状态，禁止混入无关改动或临时调试文件
- 提交信息必须说明"改了什么、为什么改、如何验证通过"
- 提交信息用中文描述

### 推送规则

- 推送到公共仓库 origin（`https://github.com/zer0p0intvvv/wordpress-vuln-env.git`）
- 禁止改写历史（如强推）覆盖他人提交，除非用户明确批准

### 异常处理

若 push 失败（权限、冲突、网络），必须先记录失败原因并完成修复，再继续推送，禁止跳过。