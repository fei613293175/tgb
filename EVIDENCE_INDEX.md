# 证据索引

## 1. 截图规则

- 公共页面可保存现状截图。
- 登录后含个人身份、联系方式、钱包、订单、聊天或认证信息的页面不保存原始截图，除非使用隔离账号并完成脱敏。
- 文件名采用 `PAGE-ID-viewport-state.png`。
- 改后证据按版本放入 `evidence/Rxx/`。

## 2. 当前截图

| 页面 | 文件 |
|---|---|
| 登录 | `evidence/screenshots/PUBLIC-LOGIN-390x844.png` |
| 注册 | `evidence/screenshots/PUBLIC-REGISTER-390x844.png` |
| 短信登录 | `evidence/screenshots/PUBLIC-SMS-LOGIN-390x844.png` |
| 服务协议 | `evidence/screenshots/LEGAL-SERVICE-AGREEMENT-390x844-full.png` |
| 隐私政策 | `evidence/screenshots/LEGAL-PRIVACY-390x844.png` |
| 登录后首页 | `evidence/screenshots/AUTH-HOME-390x844.png` |

登录后首页截图必须在使用前再次人工确认不含账号识别信息。

## 3. 品牌资产

- 正式浅色源图：`assets/brand/tuiguangbao-app-icon-master-light-v2.png`
- 被否决深色草案：`evidence/design-drafts/rejected-dark-app-icon-v1.png`

## 4. 未保存截图的真实体验

41 类页面的真实观察结论记录于：

- `02_PAGE_EXPERIENCE_LOG.md`
- `03_PAGE_LEDGER.csv`

不保存个人页面截图是隐私保护选择，不代表这些页面未体验。

## 5. 后续证据目录

```text
evidence/
  R00/
    server-facts/
    page-template-map/
    staging/
    rollback/
  R01/
    android/
    apk/
    screenshots/
    payment/
    upload/
  R02...R09/
    before/
    after/
    network/
    console/
    regression/
    rollback/
```

证据中不得出现明文凭据、Cookie、formhash、签名密码或真实身份资料。

## 6. R00 结构与舞台证据

- 页面源码发现：`evidence/R00/page-template-map/SOURCE_DISCOVERY.md`
- 64 行机器映射：`12_PAGE_SOURCE_MAP.csv`
- 副作用计划：`13_SIDE_EFFECT_TEST_PLAN.md`
- 舞台结果：`evidence/R00/staging/R00_STAGING_RESULT.md`
- 失败尝试：`evidence/R00/staging/FAILED_ATTEMPTS.md`
- 恢复验证：`evidence/R00/rollback/RESTORE_VALIDATION.md`
- R00 关版：`version-results/R00_RESULT.md`

## 7. R01 Android 证据

- 首包实体机反馈：`evidence/R01/android/device/PHYSICAL_DEVICE_FEEDBACK_20260726.md`
- 首包截图：`evidence/R01/android/device/R01-first-apk-loading-feedback-20260726.jpg`
- 去除顶部进度条实体机复验：`evidence/R01/android/device/NO_PROGRESS_RETEST_20260726.md`
- 服务器 AAPT/APKSigner/SHA-256 原始报告：`evidence/R01/android/server/`
- 去除顶部进度条的服务器重建：`evidence/R01/android/server-no-progress/SERVER_BUILD_RESULT_20260726.md`
- GitHub Actions 首次失败与修复：`evidence/R01/android/github-actions/RUN_30191417131_FAILURE.md`
- GitHub Actions 第二次失败与修复：`evidence/R01/android/github-actions/RUN_30191460571_FAILURE.md`
- GitHub Actions 第三次失败与修复：`evidence/R01/android/github-actions/RUN_30191530737_FAILURE.md`
- GitHub Actions 第四次失败与修复：`evidence/R01/android/github-actions/RUN_30191686953_FAILURE.md`
- GitHub Actions 第五次失败与修复：`evidence/R01/android/github-actions/RUN_30191778258_FAILURE.md`
- GitHub Actions 首个全绿 run：待修复重跑后补入 `evidence/R01/android/github-actions/`
