# TG H5 全站视觉重构——无状态接续入口

项目编号：`TG-H5-UI-REDESIGN`  
事实快照：`2026-07-26`  
目标站点：`tg.suewammes.com`  
生产目录：`/www/wwwroot/tg.suewammes.com`  
插件目录：`/www/wwwroot/tg.suewammes.com/source/plugin`

## 唯一目标

对所有用户能从 H5 前端进入的页面进行完整视觉重构，使颜色、排版、组件、间距、圆角、图标和状态反馈形成与现状完全不同的新视觉体系；业务功能、业务规则、数据含义、请求参数、表单提交、权限、金额、积分、路由和插件行为必须保持完全一致。

“业务功能完全一样”是最高优先级硬边界。视觉重构不等于业务重写。

同时开发名为“推广宝”的 Kotlin 原生 Android App，把 H5 安全承载在 App 中，支持相册图片上传、支付宝等白名单第三方 App 拉起、支付返回、状态栏安全区和离线错误处理。签名 Release APK 必须不低于 10 MiB。H5 与 App 的名称、Logo、浅色视觉和交互状态必须统一。

## 收到“立即开始”或“立即开发”时

不要依赖聊天记录，不要先改代码。必须在本目录执行：

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\continuity.ps1 -Mode resume
```

然后按顺序完整阅读：

1. `00_PROJECT_CHARTER.md`
2. `06_HARD_GATES.md`
3. `CURRENT_STATUS.yaml`
4. `NEXT_TASK.yaml`
5. `03_PAGE_LEDGER.csv`
6. `12_PAGE_SOURCE_MAP.csv`
7. `13_SIDE_EFFECT_TEST_PLAN.md`
8. `04_VISUAL_SYSTEM.md`
9. `05_VERSION_ROADMAP.md`
10. `07_TEST_MATRIX.md`
11. `10_ANDROID_APP_SPEC.md`
12. `14_ANDROID_TOOLCHAIN.md`
13. `15_GITHUB_ACTIONS.md`
14. `DECISIONS.md`
15. `LESSONS_LEARNED.md`

只执行 `NEXT_TASK.yaml` 指定的当前任务，不得跳版本，不得直接修改生产站。

## 当前状态摘要

- 已通过真实手机 UA 体验并记录 41 类前端页面。
- 已登录测试首页、内容、账户、钱包、发布、聊天、奖励等主链路。
- 已冻结新视觉方向“晴空电光 / Light Grid”，所有页面以浅色背景为主。
- `R00` 已完成：隔离只读舞台、可恢复快照、64/64 页面源码映射、副作用计划和 Android 工具链门禁均已通过。
- `R02` 已完成开发关版：推广宝品牌、H5 公共浅色壳层、本地图标和登录后浏览器证据已通过；App 安全区实体机截图作为异步累计门禁保留。
- `R03` 已完成开发关版：11 个认证、协议、帮助和关于页面通过三档运行时、资源、协议等价和受控认证门禁；法律正文保持冻结。
- 当前版本：`R04`，状态：`IN_PROGRESS`。
- 当前唯一任务：建立独立 R04 舞台并开发首页、搜索、分类和文章列表；不得提前把 R05-R09 页面标记为已改造。
- R01 服务器签名包仍是当前 Android 包；R02/R03 未改正式 App 源码，因此未重建 APK。GitHub Actions 自动测试已按 D-020 停用，负责人真机反馈异步返回且不阻塞持续开发。
- 本交接包不保存任何站点账号、数据库密码、验证码、Cookie、formhash、真实 UID、真实金额或身份资料。

## 文档权威顺序

发生冲突时按以下顺序裁决：

1. `06_HARD_GATES.md`
2. `00_PROJECT_CHARTER.md`
3. `CURRENT_STATUS.yaml`
4. `NEXT_TASK.yaml`
5. `DECISIONS.md`
6. `05_VERSION_ROADMAP.md`
7. 其他记录
8. 聊天上下文

聊天内容永远不能覆盖硬门禁。需要改变边界时，必须先形成新的书面决策并更新文档与校验和。

## 文件导航

- `00_PROJECT_CHARTER.md`：项目目标、范围、成功标准
- `01_BASELINE_FACTS.md`：服务器、Discuz、插件、数据库和现状事实
- `02_PAGE_EXPERIENCE_LOG.md`：真实体验结论与问题
- `03_PAGE_LEDGER.csv`：机器可读页面台账
- `04_VISUAL_SYSTEM.md` / `STYLE_TOKENS.json`：视觉体系和设计令牌
- `05_VERSION_ROADMAP.md`：R00—R09 版本顺序
- `06_HARD_GATES.md`：任何版本都不能绕过的门禁
- `07_TEST_MATRIX.md`：功能等价、视觉、兼容、回滚测试
- `08_CONTINUITY_PROTOCOL.md`：换电脑、换 AI 接续协议
- `09_REMOTE_ACCESS_AND_SAFETY.md`：远程访问和生产安全
- `10_ANDROID_APP_SPEC.md`：Android 包名、权限、支付、状态栏、签名和体积
- `11_BRAND_GUIDE.md`：推广宝品牌和 Logo 规则
- `12_PAGE_SOURCE_MAP.csv`：64 个页面到入口/控制器/模板/资源/副作用门禁的映射
- `13_SIDE_EFFECT_TEST_PLAN.md`：只读与受控写入测试协议
- `14_ANDROID_TOOLCHAIN.md`：JDK/SDK/Gradle 恢复和签名保管
- `15_GITHUB_ACTIONS.md`：GitHub 干净构建、模拟器、截图、日志和失败闭环
- `EVIDENCE_INDEX.md`：现状证据索引
- `VERSION_CLOSEOUT_TEMPLATE.md`：大版本关版模板
- `CURRENT_STATUS.yaml` / `NEXT_TASK.yaml`：唯一当前状态和下一动作
- `DECISIONS.md` / `LESSONS_LEARNED.md`：决定和踩坑记录
- `MANIFEST_SHA256.txt`：交接包完整性校验

## 禁止事项速记

- 禁止把任何凭据写进本包、源代码、日志或截图。
- 禁止在生产目录直接试改。
- 禁止更改 URL、参数名、表单 action/method、隐藏字段、`formhash`、模板变量或插件标识。
- 禁止点击真实购买、提现、签到、领取、发红包、发消息、注销、认证、支付等会改变数据的动作。
- 禁止用“页面能打开”替代业务功能完全一致的证据。
- 禁止使用运行时 CDN 作为新视觉依赖。
- 禁止页面出现大面积深色背景、深色头部或深色启动页。
- 禁止 App 接受任意外部 scheme、绕过 SSL 或申请无必要的全盘存储权限。
- 禁止未完成关版和漂移审计就推进下一版本。
