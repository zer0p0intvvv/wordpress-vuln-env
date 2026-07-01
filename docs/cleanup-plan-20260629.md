# Cleanup Plan 2026-06-29

## 目标

整理当前 WordPress 漏洞环境仓库，明确哪些资产属于正式运行链路，哪些属于历史测试残留，并把目录结构收敛到“每个 `CVE-*` 目录都能单独启动和扫描”的最小标准。

## 当前基线

- 主扫描链路以根目录 [scan.sh](/Users/zer0p0int/Desktop/wordpress漏洞环境自动化生成/scan.sh) 为准。
- 环境根目录为 `vulhub-env/<plugin-slug>/<CVE-ID>/`。
- 官方模板目录为 `nuclei-templates/`，自建模板目录为 `nuclei-template-new/`。
- `scan.sh` 运行日志应保留在 `logs/scan-*`；其它散落的测试产物默认视为可清理对象。

## 清理原则

1. 不修改官方 `nuclei-templates/*.yaml`。
2. 不删除 `scan.sh` 需要的正式资产：
   - `vulhub-env/**/docker-compose.yml`
   - `vulhub-env/base/**`
   - `nuclei-templates/**`
   - `nuclei-template-new/**`
   - `vulhub-env/environments.toml`
3. 删除根目录已过时或重复的测试产物，优先清理：
   - `test-results.txt`
   - 根目录空 `logs/.DS_Store`
4. 补齐或修正会影响资产管理的索引不一致项。
5. 对大批量缺失 README 的环境，先在仓库级索引里标出，不在本轮强行补写 80+ 份说明文档。

## 本轮计划

1. 生成最新环境/模板覆盖盘点文档。
2. 修正索引与实际目录不一致的问题。
3. 清理明确可删的测试残留。
4. 更新 `vulhub-env/README.md`，让它反映当前真实结构和运行方式。
5. 复核 `scan.sh` 可发现性与目录一致性。

## 最小独立运行标准

每个 `vulhub-env/<plugin>/<CVE>/` 至少应满足：

- 存在 `docker-compose.yml`
- `docker-compose.yml` 中存在可解析的 Web 端口映射
- `build` 指向有效的基础镜像目录或构建上下文
- 能通过根目录 `scan.sh CVE-XXXX-XXXX` 被发现和调度

README / 中英文说明属于推荐项，不作为本轮阻塞条件。
