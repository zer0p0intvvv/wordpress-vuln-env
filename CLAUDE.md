# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Purpose

自动生成 WordPress 插件漏洞的 Docker 测试环境，用于 nuclei 模板验证和漏洞复现。从 CVE-INFO JSON 文件中提取漏洞信息，生成可直接 `docker-compose up` 的环境，并形成一个可复用的skills。
## docs
docs/Project.md 保存当前的项目的进度以及工作流，可以查看，当工作流和项目进度发生改变的时候，请及时维护；该文档用于保存项目相关的所有信息，不限于进度和工作流，可以随时更新

## Architecture

- **输入**: CVE-INFO JSON 文件（包含 CVE ID、插件名、版本号、漏洞类型等）
- **输出**: 每个 CVE 一个独立目录，包含 `docker-compose.yml`、`Dockerfile`、插件源码、entrypoint 脚本
- **验证**: 配套 nuclei 模板自动检测漏洞是否可触发

### 环境配置

- **PHP 版本**: 7.4（必须 — 几乎所有目标插件使用 `create_function()`，PHP 8.0+ 不兼容）
- **源码策略**: 使用本地源码而非 `wp plugin install`，避免 WordPress.org 移除插件后无法下载
- **端口分配**: HTTP 8098-8113 / MySQL 3318-3333（每个环境递增）

## Key Technical Constraints

1. **`create_function()` 兼容性**: 这是硬性约束。所有使用该函数的插件必须运行在 PHP 7.4 上。新环境不要尝试 PHP 8.x。
2. **插件从 WP.org 移除**: 如 `gallery`（CVE-2022-1946），必须提前缓存源码，不能依赖在线安装。
3. **类未实例化问题**: 部分插件的 REST API 类或漏洞类在插件加载时未自动实例化（如 CVE-2024-2667 v0.1.0.22），需要在 entrypoint 中手动 `new` 实例。
4. **Nuclei 检测分类**:
   - **直接检测** (~47%): 无需认证，nuclei 可直接触发
   - **需 admin 认证** (~20%): 环境需预设管理员 cookie 或 Basic Auth
   - **需数据初始化** (~13%): entrypoint 必须创建前置数据（gallery、upload 目录等）
   - **OOB 限制** (~13%): 需 out-of-band 回连，nuclei 受限但手动验证可利用

## Entrypoint 常见模式

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

## Working with CVE-INFO JSON

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

新增环境前，先检查已有目录避免重复创建。

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

