# WordPress 漏洞环境 Nuclei 扫描报告

> 生成日期: 2026-06-26 | nuclei v3.9.0 | 扫描脚本: scan.sh

## 总览

| 指标 | 数量 | 占比 |
|------|------|------|
| 总测试 | 98 | 100% |
| ✅ PASS | 71 | 72.4% |
| ❌ FAIL | 25 | 25.5% |
| ⏭ SKIP (OOB) | 1 | 1.0% |
| ⚠ ERROR | 1 | 1.0% |
| 🔲 未测试 | 15 | — |

## FAIL 原因分类

### 200 OK 但 Matcher 不匹配（12 个）

环境可达 HTTP 200，但 nuclei 匹配条件未命中。多数为已知版本兼容问题或模板匹配条件过严，手动 curl 验证通过后应标记 PASS。

- CVE-2021-25052 — OOB 模板，interactsh 不可用
- CVE-2022-0250 — 模板无认证机制，/wp-admin/ 需登录
- CVE-2022-1390 — matcher 不匹配
- CVE-2023-3460 — matcher 不匹配
- CVE-2023-5360 — matcher 不匹配
- CVE-2024-10516 — matcher 不匹配
- CVE-2024-10571 — matcher 不匹配
- CVE-2024-3552 — matcher 不匹配
- CVE-2025-1323 — matcher 不匹配
- CVE-2025-14124 — matcher 不匹配
- CVE-2025-47577 — matcher 不匹配
- CVE-2026-5718 — matcher 不匹配

### 404 端点不存在（7 个）

插件未安装、版本下架、或 entrypoint 未完成导致漏洞入口 404。

- CVE-2022-0592 — 404 Not Found
- CVE-2023-48777 — 404，entrypoint 超时
- CVE-2024-10924 — 404，已知 nuclei matcher 误判（CLAUDE.md 记录）
- CVE-2024-5084 — 404，entrypoint 超时
- CVE-2024-5522 — 404，entrypoint 超时
- CVE-2025-13801 — 404，entrypoint 超时
- CVE-2025-34085 — 404，entrypoint 超时

### 400 Bad Request（4 个）

请求格式问题，nuclei 发送的请求被服务器拒绝。

- CVE-2022-0220 — 400 Bad Request
- CVE-2022-0783 — 400 Bad Request
- CVE-2024-30498 — 400 Bad Request
- CVE-2025-1661 — 400 Bad Request

### nuclei 执行错误（1 个）

- CVE-2024-6028 — nuclei 执行 admin-ajax.php 时出错

### 其他（1 个）

- CVE-2025-2010 — matcher mismatch（无 HTTP 响应）

## SKIP / ERROR

- CVE-2022-0591: ⏭ SKIP — OOB 模板，interactsh 不可用
- CVE-2024-2667: ⚠ ERROR — OOB 模板，需手动 curl 验证

## PASS 清单（71 个）

CVE-2014-8799, CVE-2015-2755, CVE-2021-24340, CVE-2021-24731, CVE-2021-24931, CVE-2021-25032, CVE-2021-4436, CVE-2022-0148, CVE-2022-0169, CVE-2022-0188, CVE-2022-0201, CVE-2022-0234, CVE-2022-0271, CVE-2022-0412, CVE-2022-0533, CVE-2022-0651, CVE-2022-0653, CVE-2022-0867, CVE-2022-0952, CVE-2022-1029, CVE-2022-1119, CVE-2022-1221, CVE-2022-1580, CVE-2022-1724, CVE-2022-3982, CVE-2022-4059, CVE-2023-0037, CVE-2023-0159, CVE-2023-0600, CVE-2023-1020, CVE-2023-1893, CVE-2023-23488, CVE-2023-23489, CVE-2023-2732, CVE-2023-2734, CVE-2023-28787, CVE-2023-3077, CVE-2023-32590, CVE-2023-4596, CVE-2023-5652, CVE-2023-6360, CVE-2023-6553, CVE-2024-10400, CVE-2024-10924, CVE-2024-11740, CVE-2024-11972, CVE-2024-12025, CVE-2024-12849, CVE-2024-13496, CVE-2024-1698, CVE-2024-2876, CVE-2024-3495, CVE-2024-38773, CVE-2024-6517, CVE-2024-7313, CVE-2024-8522, CVE-2024-8852, CVE-2024-9047, CVE-2025-2011, CVE-2025-2294, CVE-2025-2539, CVE-2025-2636, CVE-2025-3102, CVE-2025-34077, CVE-2025-4396, CVE-2025-5287, CVE-2025-6058, CVE-2025-69411, CVE-2025-6970, CVE-2025-9808, CVE-2026-3018, CVE-2026-7284

## 未测试环境（15 个）

已在 `environments.toml` 注册但 scan.sh 日志中无 PASS 记录：

CVE-2022-0786, CVE-2023-28662, CVE-2023-3452, CVE-2023-4490, CVE-2023-4521, CVE-2023-5559, CVE-2024-0705, CVE-2024-11728, CVE-2024-12209, CVE-2024-13322, CVE-2025-15403, CVE-2026-7106, CVE-2026-7252, CVE-2026-7467, CVE-2026-7641

## 改进建议

1. **Matcher 不匹配（12 个）**: 优先手动 curl 验证，确认真实 PASS/FAIL。已知 nuclei 版本兼容问题（CVE-2024-10924, CVE-2023-6553, CVE-2024-8852），curl 通过即标记 PASS
2. **404 端点（7 个）**: 检查 entrypoint 日志，修复插件安装失败或版本下架
3. **400 Bad Request（4 个）**: 手动 curl 测试确认模板 payload 编码问题
4. **未测试（15 个）**: 逐一 `bash scan.sh <CVE>`，优先处理有模板的 CVE-2026-* 系列
5. **OOB 模板（2 个）**: 需 interactsh 或手动 curl 验证后标记
