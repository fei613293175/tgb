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

旧 41 类页面观察已混入直接 URL 和源码页，只保留为历史视觉参考，不再证明 UI 范围。当前范围证据记录于：

- `02_PAGE_EXPERIENCE_LOG.md`
- `03_PAGE_LEDGER.csv`
- `16_RUNTIME_CLICK_AUDIT.md`
- `17_RUNTIME_CLICK_GRAPH.csv`

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
- H5 选图上传 content URI 修复：`evidence/R01/android/upload/CONTENT_URI_FIX_20260726.md`
- content URI 修复服务器正式构建：`evidence/R01/android/server-content-uri/SERVER_BUILD_RESULT_20260726.md`
- 支付 H5 网关生产源码审计：`evidence/R01/android/payment/PAYMENT_GATEWAY_SOURCE_AUDIT_20260726.md`
- 支付 H5 兼容修复服务器正式构建：`evidence/R01/android/server-payment-h5/SERVER_BUILD_RESULT_20260726.md`
- GitHub Actions 首次失败与修复：`evidence/R01/android/github-actions/RUN_30191417131_FAILURE.md`
- GitHub Actions 第二次失败与修复：`evidence/R01/android/github-actions/RUN_30191460571_FAILURE.md`
- GitHub Actions 第三次失败与修复：`evidence/R01/android/github-actions/RUN_30191530737_FAILURE.md`
- GitHub Actions 第四次失败与修复：`evidence/R01/android/github-actions/RUN_30191686953_FAILURE.md`
- GitHub Actions 第五次失败与修复：`evidence/R01/android/github-actions/RUN_30191778258_FAILURE.md`
- GitHub Actions 第六次失败与修复：`evidence/R01/android/github-actions/RUN_30191901834_FAILURE.md`
- GitHub Actions 首个全绿 run：`evidence/R01/android/github-actions/RUN_30192012488_SUCCESS.md`
- GitHub Actions Android 36 截图：`evidence/R01/android/github-actions/RUN_30192012488-launch-1080x2340.png`
- GitHub Actions content URI instrumentation 最终历史全绿 run：`evidence/R01/android/github-actions/RUN_30192427296_SUCCESS.md`
- R01 开发关版与全局漂移审计：`version-results/R01_RESULT.md`

## 8. R02 品牌与共享壳层证据

- R02 隔离预发布结果：`evidence/R02/staging/R02_STAGING_RESULT.md`
- R02 共享资源与假阳性门禁：
  `evidence/R02/network/SHARED_SHELL_ASSET_AUDIT.md`
- R02 桌面引导真实截图：
  `evidence/R02/after/DESKTOP-SPLASH-1265x712.jpg`
- R02 共享组件真实响应式矩阵：
  `evidence/R02/after/R02-LIGHT-GRID-360x800.jpg`、
  `evidence/R02/after/R02-LIGHT-GRID-390x844.jpg`、
  `evidence/R02/after/R02-LIGHT-GRID-430x932.jpg`
- R02 响应式矩阵方法、数值与哈希：
  `evidence/R02/after/RESPONSIVE_COMPONENT_MATRIX.md`
- R02 响应式矩阵使用真实 360/390/430 iframe 视口加载旧站 CSS 与 R02
  CSS；它证明共享组件断点和无横向溢出，不替代登录后业务页面证据。
- R02 登录夹具限制：
  `evidence/R02/regression/LOGIN_FIXTURE_LIMIT.md`
- R02 登录后共享壳层运行时审计：
  `evidence/R02/after/AUTHENTICATED_SHARED_SHELL_AUDIT.md`
- R02 登录后共享壳层截图：
  `evidence/R02/after/AUTH-HOME-R02-390x844.jpg`
- R02 Android WebView 安全区源码、构建和设备待验状态：
  `evidence/R02/android/INSET_EVIDENCE_STATUS.md`
- R02 开发关版与全局漂移审计：
  `version-results/R02_RESULT.md`

## 9. R03 认证与法律信息证据

- R03 隔离舞台与生产不变性：
  `evidence/R03/staging/R03_STAGING_RESULT.md`
- R03 本地资源、网络和控制台审计：
  `evidence/R03/network/R03_RESOURCE_AUDIT.md`
- R03 表单协议、法律正文冻结和受控认证：
  `evidence/R03/regression/R03_PROTOCOL_AND_AUTH_EQUIVALENCE.md`
- R03 11 页 x 3 视口运行时和截图审计：
  `evidence/R03/after/R03_RUNTIME_SCREENSHOT_AUDIT.md`
- R03 三档机器可读浏览器数据：
  `evidence/R03/after/R03-BROWSER-360x800.json`、
  `evidence/R03/after/R03-BROWSER-390x844.json`、
  `evidence/R03/after/R03-BROWSER-430x932.json`
- R03 14 文件逐项哈希：
  `evidence/R03/after/R03-OVERLAY-SHA256.txt`
- R03 部署、备份和白名单回滚：
  `evidence/R03/rollback/R03_DEPLOYMENT_ROLLBACK.md`
- R03 开发关版与全局漂移审计：
  `version-results/R03_RESULT.md`

注册页三张截图已在保存前将邀请编号替换为 `[已脱敏]`。不得尝试恢复或输出原值。

## 10. R04 首页与发现证据

- R04 隔离舞台、纠偏部署和生产不变性：
  `evidence/R04/staging/R04_STAGING_RESULT.md`
- 首页、搜索和真实分类三档截图与裁剪后几何矩阵：
  `evidence/R04/after/R04_RUNTIME_SCREENSHOT_AUDIT.md`、
  `evidence/R04/after/R04-BROWSER-MATRIX.json`
- 首页纠偏截图证明 `data-id=5/14/15/13` 的旧分类暴露高度、可见数和命中数均为 0；截图内容仅做当前 DOM 脱敏，不修改服务器。
- R04 本地资源和控制台审计：
  `evidence/R04/network/R04_RESOURCE_AUDIT.md`
- 协议、隐藏条件路由和业务计数等价：
  `evidence/R04/regression/R04_PROTOCOL_AND_READONLY_EQUIVALENCE.md`
- 5 文件逐项 SHA-256 与 v11 回滚：
  `evidence/R04/after/R04-OVERLAY-SHA256.txt`、
  `evidence/R04/rollback/R04_DEPLOYMENT_ROLLBACK.md`
- R04 开发关版与漂移纠偏：
  `version-results/R04_RESULT.md`

## 11. R05 真实点击范围纠偏证据

- 负责人纠偏原图：
  `evidence/R05/reachability-correction/owner-feedback-card-add.png`、
  `evidence/R05/reachability-correction/owner-feedback-comments.png`
- 生产首页及首页到个人中心的脱敏证据：
  `evidence/R05/reachability-correction/production-home-390x844.png`、
  `evidence/R05/reachability-correction/home-to-my.json`
- 个人中心可见控件与 R05 三个实点页面：
  `evidence/R05/reachability-correction/my-visible-entries.json`、
  `evidence/R05/reachability-correction/r05-clicked-candidates.json`
- 设置页及其七个安全子入口实点：
  `evidence/R05/reachability-correction/settings-clicked-entries.json`
- 个人中心双钱包、VIP、邀请、团队、认证、刷新卡、推广、签到、分红和站内 App 下载落地页实点：
  `evidence/R05/reachability-correction/account-hub-clicked-entries.json`
- 钱包三种安全状态及消息/成员副作用边界：
  `evidence/R05/reachability-correction/wallet-and-message-reachability.json`
- 隔离详情实点、举报可达、隐藏功能零暴露与 GET 回滚：
  `evidence/R05/reachability-correction/isolated-detail-click-audit.json`、
  `evidence/R05/reachability-correction/isolated-detail-report-entry-390x100.jpg`

上述 JSON 不保存 UID、手机号、余额、收款账户、聊天对象、聊天正文或客服 token。当前点击图为 39 条边，其中 31 条已实点，3 条可见入口因 GET 副作用等待隔离重放，其余 5 条专门记录隐藏或不存在的功能不得进入视觉范围。

- R05 v5 范围纠偏部署、门禁和回滚位置：
  `evidence/R05/staging/R05_SCOPE_CORRECTION_V5.md`
- R05 v5 三视口、首页隐藏分类和超范围控件真实浏览器矩阵：
  `evidence/R05/after/R05-V5-BROWSER-MATRIX.json`

v5 制品 `deliverables/r05-click-proven-overlay-v5.tar.gz` 共 12 个文件，SHA-256 为 `b6fd61f320b1f15726608abf69f610a27ea744e37e42fdf62108769b42f09de9`。浏览器矩阵覆盖 360x800、390x844、430x932，三个详情视口的超范围可见操作均为空；390x844 首页旧分类可见数为 0。

## 12. R06-R07 点击范围 UI 证据

- R06 账户与沟通关版：`version-results/R06_RESULT.md`
- R06 三视口矩阵：`evidence/R06/after/R06-V9-BROWSER-MATRIX.json`
- R07 资金、会员与推广关版：`version-results/R07_RESULT.md`
- R07 最终 7 页 x 3 视口矩阵：`evidence/R07/after/R07-V6-FINAL-BROWSER-MATRIX.json`
- R07 21 张精确像素截图：`evidence/R07/after/matrix-v6-final/`

R07 最终制品 `deliverables/r07-click-proven-overlay-v6.tar.gz` 共 12 个文件，SHA-256 为 `7114c2eecf4151c709fdea3ea8e5e6fd0fa36cbb57a9b416ef71d21fb327dd6c`。订单、收银台和未获点击证明的页面不在覆盖包内。

## 13. R08-R09 最终视觉与生产发布

- R08 旧 `R08-V2-FINAL-BROWSER-MATRIX.json` 因会话过期实际截到登录页，保留为无效历史证据，不得再用于关版。
- R09 真实认证单次主视口回归：`evidence/R09/R09_QUICK_BROWSER_REGRESSION.md`
- R09 81 文件候选清单：`evidence/R09/preflight/R09_FILES_SHA256.txt`
- R09 预期覆盖关系：`evidence/R09/preflight/R09_LAYER_OVERRIDES.tsv`
- R09 上线结果和回滚位置：`version-results/R09_RESULT.md`
- R09 原生顶部双空白根因与 H5 首页/签到浏览器几何截图：`evidence/R09/after/R09_ANDROID_INSET_BROWSER_CHECK.md`
- R09 Android H5 UA 公开登录、搜索、防诈骗点击与三页范围纠偏：`evidence/R09/scope-closeout/R09_SCOPE_CLOSEOUT.md`

R09 原始上线候选 v2 SHA-256 为 `108ab6d6c10be84a892aec223e24bcd134f86be30bc48c33a60f4180fd99dd3e`，生产部署 ID 为 `20260727T090142+0800`。范围纠偏后的确定性候选 v3 为 78 文件，连续两次构建 SHA-256 均为 `a65d0e510cea5399c0b49ed53dbdea6bbacb9714bc34d54a952ef211a8f8e389`；三页恢复部署 ID 为 `20260727T113640+0800`。顶部双空白返修服务器签名 APK SHA-256 为 `5625827329af115a0be70d15d4c8b210171e6edb093805a47b62aac1c1947e9f`，真机验证由负责人异步完成。后续普通 UI 返修按 D-035 只验证受影响页面，不再生成重复全量截图矩阵。
