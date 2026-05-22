# Project: WordPress 漏洞环境自动化生成

## 概述

自动生成 WordPress 插件漏洞的 Docker 测试环境，用于 nuclei 模板验证和漏洞复现。

## 主要工作流
1、输入；2、创建环境；3、收集模版；4、验证；5、修复；6、提交                        

## 目录结构

```
wordpress漏洞环境自动化生成/
├── .claude/
│   └── CLAUDE.md                # Claude Code 项目指引
├── docs/
│   └── Project.md               # 本文件 - 项目进度与工作流
├── vulhub-env/                  # 交付产物：Docker 漏洞环境
│   ├── base/                    # 基础镜像
│   │   ├── wordpress/6.4/       # 通用 WP 镜像（在线装插件）
│   │   └── <plugin>/<version>/  # 专用镜像（本地源码）
│   ├── <plugin>/<CVE-ID>/       # 每个漏洞独立目录
│   │   ├── docker-compose.yml
│   │   ├── README.md
│   │   └── README.zh-cn.md
│   ├── environments.toml        # 端口注册表
│   ├── start.sh                 # 批量管理脚本
│   └── README.md
└── nuclei-templates/            # 官方 nuclei 模板
    ├── INDEX.md                 # 模板索引（含端口映射）
    └── CVE-XXXX-XXXX.yaml      # 25 个官方模板
```

## 已完成环境（27 个）

| # | 插件 | CVE | 类型 | Web | MySQL | 认证 | OOB | Nuclei |
|---|------|-----|------|-----|-------|------|-----|--------|
| 1 | email-subscribers | CVE-2024-2876 | SQLi | 8088 | 3307 | No | No | PASS |
| 2 | secure-copy-content-protection | CVE-2021-24931 | SQLi | 8089 | 3316 | No | No | PASS |
| 3 | paid-member-subscriptions | CVE-2023-23488 | SQLi | 8090 | 3309 | No | No | PASS |
| 4 | permalink-manager | CVE-2022-0201 | XSS | 8091 | 3310 | No | No | PASS |
| 5 | learnpress | CVE-2022-0271 | XSS | 8092 | 3311 | No | No | PASS |
| 6 | capability-manager-enhanced | CVE-2021-25032 | CSRF | 8093 | 3312 | Yes | No | PASS |
| 7 | sitemap-by-click5 | CVE-2022-0952 | CSRF | 8094 | 3313 | No | No | PASS |
| 8 | 3dprint-lite | CVE-2021-4436 | File Upload | 8095 | 3314 | No | No | PASS |
| 9 | hash-form | CVE-2024-5084 | File Upload | 8096 | 3315 | No | No | PASS |
| 10 | dukapress | CVE-2014-8799 | LFI | 8097 | 3317 | No | No | PASS |
| 11 | ab-google-map-travel | CVE-2015-2755 | XSS | 8098 | 3318 | Yes | No | PASS |
| 12 | button-generation | CVE-2021-25052 | RFI | 8099 | 3319 | Yes | Yes | **FAIL** |
| 13 | wp-simple-firewall | CVE-2024-7313 | XSS | 8100 | 3320 | Yes | No | PASS |
| 14 | wp-statistics | CVE-2021-24340 | SQLi | 8101 | 3321 | No | No | PASS |
| 15 | pie-register | CVE-2021-24731 | SQLi | 8102 | 3322 | No | No | PASS |
| 16 | photo-gallery | CVE-2022-0169 | SQLi | 8103 | 3323 | No | No | PASS |
| 17 | cryptocurrency-widgets-pack | CVE-2022-4059 | SQLi | 8104 | 3324 | No | No | PASS |
| 18 | my-calendar | CVE-2023-6360 | SQLi | 8105 | 3325 | No | No | PASS |
| 19 | gamepress | CVE-2024-13496 | SQLi | 8106 | 3326 | Yes | No | PASS |
| 20 | depicter | CVE-2025-2011 | SQLi | 8107 | 3327 | No | No | PASS |
| 21 | booking-calendar | CVE-2022-3982 | File Upload | 8108 | 3328 | No | No | PASS |
| 22 | instawp-connect | CVE-2024-2667 | File Upload | 8109 | 3329 | No | Yes | **FAIL** |
| 23 | mystickyelements | CVE-2022-0148 | XSS | 8110 | 3330 | Yes | No | PASS |
| 24 | simple-membership | CVE-2022-1724 | XSS | 8111 | 3331 | No | No | PASS |
| 25 | ds-cf7-math-captcha | CVE-2024-6517 | XSS | 8113 | 3333 | No | No | PASS |
| 26 | drag-and-drop-multiple-file-upload-cf7 | CVE-2026-5718 | File Upload | 8114 | 3334 | No | No | 待验证 |
| 27 | registrationmagic | CVE-2025-15403 | Priv Esc | 8115 | 3335 | No | No | - |
| 28 | easy-elements | CVE-2026-7284 | Priv Esc | 8116 | 3336 | No | No | 待验证 |
| 29 | highland-software-custom-role-manager | CVE-2026-7106 | Priv Esc | 8117 | 3337 | Yes | No | 待验证 |
| 30 | wp-optimize | CVE-2026-7252 | File Deletion→RCE | 8118 | 3338 | Yes | No | 待验证 |
| 31 | expand-maker | CVE-2026-7467 | Priv Esc | 8119 | 3339 | Yes | No | 待验证 |
| 32 | import-users-from-csv-with-meta | CVE-2026-7641 | Priv Esc (Multisite) | 8120 | 3340 | Yes | No | 待验证 |

## Nuclei 模板状态

- 官方模板: 25/25 全部从 projectdiscovery/nuclei-templates 获取
- 自定义模板: 6 个 (CVE-2026-5718, CVE-2026-7284, CVE-2026-7106, CVE-2026-7252, CVE-2026-7467, CVE-2026-7641)
- 模板目录: `nuclei-templates/`
- 索引文件: `nuclei-templates/INDEX.md`
- 需认证模板: 9 个（CVE-2015-2755, CVE-2021-25032, CVE-2021-25052, CVE-2022-0148, CVE-2024-7313, CVE-2024-13496, CVE-2026-7106, CVE-2026-7252, CVE-2026-7467, CVE-2026-7641）
- OOB 模板: 2 个（CVE-2021-25052, CVE-2024-2667）

## 验证流程
### 单独验证
```bash
# 1. 启动环境
cd vulhub-env/<plugin>/<CVE-ID>
docker compose up -d --build

# 2. 等待就绪
sleep 15

# 3. 运行 nuclei 模板
# 无认证
~/工具/nuclei -t nuclei-templates/CVE-XXXX-XXXX.yaml -u http://localhost:<PORT>

# 需认证
~/工具/nuclei -t nuclei-templates/CVE-XXXX-XXXX.yaml -u http://localhost:<PORT> -V "username=admin" -V "password=admin"

```
### 批量验证
、、、bash

bash /Users/zer0p0int/Desktop/wordpress漏洞环境自动化生成/test-all.sh
结果保存:/Users/zer0p0int/Desktop/wordpress漏洞环境自动化生成/test-results.txt
、、、
## 端口规划

- Web: 8088-8120（已用），新环境从 8121 开始
- MySQL: 3307-3340（已用），新环境从 3341 开始
- 检查冲突: `lsof -i :<port>`

## Nuclei 验证结果（2026-05-08）

- 测试工具: nuclei 3.8.0
- 通过: 23/25 (92%)
- OOB 限制: 2 个（需 interactsh 服务器）

### 修复记录

| CVE | 问题 | 修复 |
|-----|------|------|
| CVE-2023-23488 | 插件在 WP.org 已关闭 | 创建本地源码 base 镜像 |
| CVE-2024-13496 | 环境用 gamepress, 模板检测 gamipress | 改用 gamipress 2.7.0 base 镜像 |
| CVE-2024-13496 | 模板需 admin 认证 + matcher 格式错误 | 添加登录步骤 + 修复 matcher |
| CVE-2021-25032 | 插件未激活 + 请求顺序错误 | entrypoint 自动激活 + 调整请求顺序 |

### 当前卡点（OOB）

| CVE | 说明 |
|-----|------|
| CVE-2024-2667 | 模板使用 interactsh OOB, 需外部 OOB 服务器 |
| CVE-2021-25052 | 模板使用 interactsh OOB, 需外部 OOB 服务器 |

1. PHP 7.4 必须（create_function 兼容性）
2. 使用本地源码，不依赖 WP.org 在线安装
3. 部分插件需在 entrypoint 中手动实例化类
4. 模板检测分类: 直接检测(47%) / 需认证(20%) / 需数据初始化(13%) / OOB限制(13%)

### 需求点
1、主要需要点 主要工作流
2、检测