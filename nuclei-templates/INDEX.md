# Nuclei Templates Index

官方 nuclei 模板，来源: `projectdiscovery/nuclei-templates`

## 模板清单

| CVE | 漏洞类型 | 认证 | OOB | 环境目录 | 端口 |
|-----|----------|------|-----|----------|------|
| CVE-2014-8799 | LFI | No | No | dukapress/CVE-2014-8799 | 8097 |
| CVE-2015-2755 | Stored XSS (CSRF) | Yes | No | ab-google-map-travel/CVE-2015-2755 | 8098 |
| CVE-2021-24340 | SQLi (time-based) | No | No | wp-statistics/CVE-2021-24340 | 8101 |
| CVE-2021-24731 | SQLi (time-based) | No | No | pie-register/CVE-2021-24731 | 8102 |
| CVE-2021-24931 | SQLi (time-based) | No | No | secure-copy-content-protection/CVE-2021-24931 | 8089 |
| CVE-2021-25032 | CSRF/Missing Auth | Yes | No | capability-manager-enhanced/CVE-2021-25032 | 8093 |
| CVE-2021-25052 | RFI | Yes | Yes | button-generation/CVE-2021-25052 | 8203 |
| CVE-2021-4436 | File Upload RCE | No | No | 3dprint-lite/CVE-2021-4436 | 8095 |
| CVE-2022-0148 | Reflected XSS | Yes | No | mystickyelements/CVE-2022-0148 | 8110 |
| CVE-2022-0169 | SQLi (union-based) | No | No | photo-gallery/CVE-2022-0169 | 8103 |
| CVE-2022-0201 | Reflected XSS | No | No | permalink-manager/CVE-2022-0201 | 8091 |
| CVE-2022-0250 | Reflected XSS | Yes | No | wpcf7-redirect/CVE-2022-0250 | 8204 |
| CVE-2022-0271 | Reflected XSS | No | No | learnpress/CVE-2022-0271 | 8092 |
| CVE-2022-0412 | SQLi (time-based) | No | No | ti-woocommerce-wishlist/CVE-2022-0412 | 8205 |
| CVE-2022-0533 | Reflected XSS | Yes | No | ditty-news-ticker/CVE-2022-0533 | 8200 |
| CVE-2022-0952 | CSRF | No | No | sitemap-by-click5/CVE-2022-0952 | 8094 |
| CVE-2022-1724 | Reflected XSS | No | No | simple-membership/CVE-2022-1724 | 8111 |
| CVE-2022-3982 | File Upload RCE | No | No | booking-calendar/CVE-2022-3982 | 8108 |
| CVE-2022-4059 | SQLi (time-based) | No | No | cryptocurrency-widgets-pack/CVE-2022-4059 | 8104 |
| CVE-2023-23488 | SQLi (time-based) | No | No | paid-member-subscriptions/CVE-2023-23488 | 8090 |
| CVE-2023-2732 | Auth Bypass | No | No | mstore-api/CVE-2023-2732 | 8138 |
| CVE-2023-2734 | Auth Bypass | No | No | mstore-api/CVE-2023-2734 | 8210 |
| CVE-2023-3077 | SQLi (time-based) | No | No | mstore-api/CVE-2023-3077 | 8211 |
| CVE-2023-6360 | SQLi (time-based) | No | No | my-calendar/CVE-2023-6360 | 8105 |
| CVE-2024-13496 | SQLi (time-based) | Yes | No | gamepress/CVE-2024-13496 | 8106 |
| CVE-2024-2667 | File Upload | No | Yes | instawp-connect/CVE-2024-2667 | 8109 |
| CVE-2024-2876 | SQLi (time-based) | No | No | email-subscribers/CVE-2024-2876 | 8088 |
| CVE-2024-5084 | File Upload RCE | No | No | hash-form/CVE-2024-5084 | 8096 |
| CVE-2024-6517 | Reflected XSS | No | No | ds-cf7-math-captcha/CVE-2024-6517 | 8113 |
| CVE-2024-7313 | Reflected XSS | Yes | No | wp-simple-firewall/CVE-2024-7313 | 8100 |
| CVE-2025-2011 | SQLi (union-based) | No | No | depicter/CVE-2025-2011 | 8107 |
| CVE-2026-5718 | File Upload RCE | No | No | drag-and-drop-multiple-file-upload-cf7/CVE-2026-5718 | 8114 |
| CVE-2026-7106 | Privilege Escalation | Yes | No | highland-software-custom-role-manager/CVE-2026-7106 | 8117 |
| CVE-2026-7252 | File Deletion → RCE | Yes | No | wp-optimize/CVE-2026-7252 | 8118 |
| CVE-2026-7284 | Privilege Escalation | No | No | easy-elements/CVE-2026-7284 | 8116 |
| CVE-2026-7467 | Privilege Escalation | Yes | No | expand-maker/CVE-2026-7467 | 8119 |
| CVE-2026-7641 | Privilege Escalation (Multisite) | Yes | No | import-users-from-csv-with-meta/CVE-2026-7641 | 8120 |
| CVE-2026-3018 | SQL Injection (Time-Based) | No | No | newsletters-lite/CVE-2026-3018 | 8201 |

## 统计

- 总计: 36 个模板
- 无需认证: 25 个
- 需认证 (username/password): 12 个 (CVE-2015-2755, CVE-2021-25032, CVE-2021-25052, CVE-2022-0148, CVE-2022-0250, CVE-2022-0533, CVE-2024-7313, CVE-2024-13496, CVE-2026-7106, CVE-2026-7252, CVE-2026-7467, CVE-2026-7641)
- 使用 OOB (interactsh): 2 个 (CVE-2021-25052, CVE-2024-2667)

## 使用方法

```bash
# 无认证模板
~/工具/nuclei -t nuclei-templates/CVE-XXXX-XXXX.yaml -u http://localhost:<PORT>

# 需认证模板
~/工具/nuclei -t nuclei-templates/CVE-XXXX-XXXX.yaml -u http://localhost:<PORT> -V "username=admin" -V "password=admin"

# OOB 模板（使用官方 interactsh 服务）
~/工具/nuclei -t nuclei-templates/CVE-XXXX-XXXX.yaml -u http://localhost:<PORT>
```
