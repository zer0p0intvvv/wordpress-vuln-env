# Vulhub Environment Manager

统一管理 WordPress 插件漏洞的 Docker 环境，用于漏洞复现和 Nuclei 模板开发。

## 环境总览

| CVE | 插件 | 类型 | Web 端口 | MySQL 端口 |
|-----|------|------|---------|-----------|
| CVE-2024-2876 | email-subscribers | SQLI | 8088 | 3307 |
| CVE-2021-24931 | secure-copy-content-protection | SQLI | 8089 | 3308 |
| CVE-2023-23488 | paid-member-subscriptions | SQLI | 8090 | 3309 |
| CVE-2022-0201 | permalink-manager | XSS | 8091 | 3310 |
| CVE-2022-0271 | learnpress | XSS | 8092 | 3311 |
| CVE-2021-25032 | capability-manager-enhanced | CSRF | 8093 | 3312 |
| CVE-2022-0952 | sitemap-by-click5 | CSRF | 8094 | 3313 |
| CVE-2021-4436 | 3dprint-lite | UPLOAD | 8095 | 3314 |
| CVE-2024-5084 | hash-form | UPLOAD | 8096 | 3315 |

## 使用方式

```bash
# 启动单个环境
cd email-subscribers/CVE-2024-2876
docker compose up -d

# 批量构建所有环境
for d in */CVE-*/; do (cd "$d" && docker compose build &); done; wait

# 批量启动
for d in */CVE-*/; do (cd "$d" && docker compose up -d &); done; wait
```

所有环境统一账号 `admin` / `admin`。

## 调试模式

修改 `docker-compose.yml` 中 `DEBUG_MODE: "true"`，或临时传入：

```bash
DEBUG_MODE=true docker compose up -d
```

调试模式开启：`WP_DEBUG` / `WP_DEBUG_LOG` / `WP_DEBUG_DISPLAY` / `SAVEQUERIES`

日志路径：容器内 `/var/www/html/wp-content/debug.log`

### Xdebug 远程调试

```bash
docker compose build --build-arg INSTALL_XDEBUG=true
```

IDE 连接 `host.docker.internal:9003`。

## 添加新环境

```bash
# 编辑 generate.sh 添加条目，然后运行
bash generate.sh
```

或手动创建：
1. `base/wordpress/6.4/` 为通用基础镜像（自动从 WordPress.org 安装插件）
2. `<plugin>/<CVE-ID>/docker-compose.yml` 配置插件名和版本
3. `<plugin>/<CVE-ID>/README.md` + `README.zh-cn.md` 文档
4. `environments.toml` 注册

## 基础镜像说明

- `base/wordpress/6.4/` — 通用 WordPress 镜像，通过环境变量 `WORDPRESS_PLUGIN_SLUG` 和 `WORDPRESS_PLUGIN_VERSION` 自动安装指定版本插件
- `base/email-subscribers/5.7.13/` — 专用镜像，插件源码 COPY 进镜像（用于本地有插件源码的场景）
