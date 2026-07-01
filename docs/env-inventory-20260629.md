# Environment Inventory 2026-06-29

## 总览

- `vulhub-env/*/CVE-*` 环境目录: `113`
- 官方 nuclei 模板: `97`
- 自建 nuclei 模板: `5`
- 无模板环境: `11`

## `scan.sh` 当前可用基线

依据 [docs/scan-report-20260626.md](/Users/zer0p0int/Desktop/wordpress漏洞环境自动化生成/docs/scan-report-20260626.md):

- 已测试: `98`
- PASS: `71`
- FAIL: `25`
- SKIP: `1`
- ERROR: `1`
- 未测试: `15`

说明:

- 根目录 [scan.sh](/Users/zer0p0int/Desktop/wordpress漏洞环境自动化生成/scan.sh) 当前是主批量扫描入口。
- `scan.sh` 可发现 `vulhub-env/*/CVE-*/docker-compose.yml`，并根据 `nuclei-templates/` / `nuclei-template-new/` 自动选择模板。
- OOB 与已知 matcher 兼容问题仍需人工复核，详见 `CLAUDE.md` 与 `docs/scan-report-20260626.md`。

## 模板覆盖分类

### 有官方模板的环境

共 `97` 个，对应 `nuclei-templates/*.yaml`。

### 有自建模板的环境

共 `5` 个，对应 `nuclei-template-new/*.yaml`:

- `CVE-2026-7106`
- `CVE-2026-7252`
- `CVE-2026-7284`
- `CVE-2026-7467`
- `CVE-2026-7641`

### 当前无模板的环境

共 `11` 个:

- `CVE-2022-0786`
- `CVE-2023-28662`
- `CVE-2023-3452`
- `CVE-2023-4490`
- `CVE-2023-4521`
- `CVE-2023-5559`
- `CVE-2024-0705`
- `CVE-2024-11728`
- `CVE-2024-12209`
- `CVE-2024-13322`
- `CVE-2025-15403`

## 目录合规性观察

- `113/113` 个环境目录都存在 `docker-compose.yml`。
- 大多数环境可被 `scan.sh` 发现并调度。
- `CVE-2024-10400` 使用 `build.context + dockerfile` 形式，已同步修正 `scan.sh` 预构建逻辑以兼容这类目录。
- `81` 个环境目录缺少 `README.md` 与 `README.zh-cn.md`。这会影响可读性，但不阻断单目录运行。

## 已修正问题

- `vulhub-env/environments.toml` 中 `CVE-2022-0533` 的 `path` 与真实目录不一致，已修正为 `dynamic-featured-image/CVE-2022-0533`。
- 根目录历史测试结果 `test-results.txt` 已移除，避免与 `scan.sh` 日志链路混淆。

## 当前建议

1. 后续把 `docs/scan-report-20260626.md` 的 PASS/FAIL 结果逐步回写到统一索引，而不是继续维护多份手工统计。
2. 针对 `81` 个缺失 README 的环境，后续可按模板批量补齐最小运行说明。
3. `nuclei-templates/INDEX.md` 仍是旧版 40 条索引，建议后续重建为自动生成版本。
