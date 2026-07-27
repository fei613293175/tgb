# TG H5 全站视觉重构——无状态接续入口

项目编号：`TG-H5-UI-REDESIGN`  
事实快照：`2026-07-27`
目标站点：`tg.suewammes.com`  
生产目录：`/www/wwwroot/tg.suewammes.com`  
插件目录：`/www/wwwroot/tg.suewammes.com/source/plugin`

## 唯一目标

只对普通真实用户能从当前生产 H5 的可见控件逐级点击进入的页面进行完整视觉重构，使颜色、排版、组件、间距、圆角、图标和状态反馈形成与现状完全不同的新视觉体系；直接 URL、隐藏 DOM、源码路由和无入口插件页不属于视觉范围。业务功能、业务规则、数据含义、请求参数、表单提交、权限、金额、积分、路由和插件行为必须保持完全一致。

“业务功能完全一样”是最高优先级硬边界。视觉重构不等于业务重写。

同时开发名为“推广宝”的 Kotlin 原生 Android App，把 H5 安全承载在 App 中，支持相册图片上传、支付宝等白名单第三方 App 拉起、支付返回、状态栏安全区和离线错误处理。签名 Release APK 必须不低于 10 MiB。H5 与 App 的名称、Logo、浅色视觉和交互状态必须统一。

## 收到“继续开发”“立即开发”或“立即开始”时

不要依赖聊天记录，不要先改代码。必须在本目录执行：

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\continuity.ps1 -Mode resume
```

然后按顺序完整阅读：

1. `16_RUNTIME_CLICK_AUDIT.md`
2. `17_RUNTIME_CLICK_GRAPH.csv`
3. `00_PROJECT_CHARTER.md`
4. `06_HARD_GATES.md`
5. `CURRENT_STATUS.yaml`
6. `NEXT_TASK.yaml`
7. `03_PAGE_LEDGER.csv`
8. `12_PAGE_SOURCE_MAP.csv`
9. `13_SIDE_EFFECT_TEST_PLAN.md`
10. `04_VISUAL_SYSTEM.md`
11. `05_VERSION_ROADMAP.md`
12. `07_TEST_MATRIX.md`
13. `10_ANDROID_APP_SPEC.md`
14. `14_ANDROID_TOOLCHAIN.md`
15. `15_GITHUB_ACTIONS.md`
16. `DECISIONS.md`
17. `LESSONS_LEARNED.md`

只执行 `NEXT_TASK.yaml` 指定的当前任务，不得跳版本，不得直接修改生产站。

## 当前状态摘要

- 旧“41 类前端页面”统计已于 2026-07-27 失效：其中混入直接 URL、源码路由和隐藏控件；当前点击图已去重并形成 48 条边，其中 41 条实点、2 条同页替代入口、5 条隐藏或不存在入口。
- 已登录测试首页、内容、账户、钱包、发布、聊天、奖励等主链路。
- 已冻结新视觉方向“晴空电光 / Light Grid”，所有页面以浅色背景为主。
- `R00` 已完成：隔离只读舞台、可恢复快照、64/64 页面源码映射、副作用计划和 Android 工具链门禁均已通过。
- `R02` 已完成开发关版：推广宝品牌、H5 公共浅色壳层、本地图标和登录后浏览器证据已通过；App 安全区实体机截图作为异步累计门禁保留。
- `R03` 已完成开发关版：11 个认证、协议、帮助和关于页面通过三档运行时、资源、协议等价和受控认证门禁；法律正文保持冻结。
- `R04` 已完成纠偏关版：首页只保留原站可见的搜索、头条、内容卡片、发布和底栏；旧零高分类 DOM 保持不可见、不可点击、不占位。
- `R05` 已完成真实点击范围纠偏和五类页面开发；评论、点赞、收藏、红包和名片等隐藏或直接路由保持视觉范围外。
- `R06`、`R07`、`R08` 已完成实现；R08 旧最终矩阵误截登录页，已失效并由 R09 的真实认证 `390x844` 全局快速回归替代。
- 当前版本：`R09`，它是路线图最后一个大版本。H5 开发关版已通过，状态保持 `IN_PROGRESS` 仅用于负责人异步真机验收和由反馈触发的定向返修；不得创建 R10 或重新扩大页面范围。
- 私信详情与成员详情已从真实点击链完成五文件换肤，Codex 实拍审核 `360x800`、`390x844`、`430x932` 后于 `20260727T142812+0800` 最小部署生产；回滚备份和证据均已固化。
- 收银台已由“我的 -> 开通会员 -> 确认购买”真实点击链证明可达，完成两文件浅色换肤、三档精确像素实拍和生产部署 `20260727T152930+0800`；订单中心没有可见父入口，仍在视觉范围外。
- 当前唯一任务：全局漂移关账、线上轻量冒烟和异步问题快速返修；`xigua_hs`、`xigua_sp`、`tb_jjd`、广告、淘金等没有真实前端入口的插件继续保持视觉范围外，不再开发隐藏页面。
- 后续普通 UI 返修固定采用三门禁快速通道：真实入口、业务协议不变、可回滚。默认只跑 `390x844` 主视口和受影响页面，不再重复全量三视口截图。
- 负责人反馈旧 R01 App 的原生 WindowInsets 使所有 H5 顶栏出现双空白；R09 已按 D-037 改为 WebView edge-to-edge、原生零 inset padding，并在服务器生成新签名 APK `C:\Users\小白\Desktop\推广宝-1.0.0-R09-顶部导航紧凑修复.apk`，等待负责人异步真机核验。GitHub Actions 自动测试仍停用。
- H5 体验只能使用 Android mobile UA，并追加 `TuiGuangBaoAndroid/1.0.0`；桌面 UA 不得用于范围、点击或视觉结论。R09 已补登录链、搜索和防诈骗真实点击证据，并将无父入口的会员协议、帮助、关于恢复为上线前文件；最终非 Android 视觉集合为 39 条（38 个业务 H5 页面加 1 个桌面兼容入口），39/39 `REDESIGNED_VERIFIED`。当前可重现候选为 78 文件 v5，SHA-256 `c87633b032784b7a634496c50f3bb424a6668271dd6b43e2b6ad561cc2410734`。
- 按 D-036，Codex 不得在本机安装 Android/ADB/模拟器环境，也不得安装或测试 APK；只负责原生源码开发、必要时服务器签名构建并把 APK 放到桌面，真机测试由负责人完成。
- 安全续点：当前工作树不再保存生产数据库标识，收银台部署脚本改为运行时 `EXPECTED_PRODUCTION_DB`；Git 历史仍有 1 个旧提交包含该标识。由于该值可能同时是数据库口令，项目最终安全验收前必须由负责人轮换口令并明确授权或自行完成历史清理，普通后续提交不能消除历史暴露。
- 最终完成口径：R09 H5 开发已 PASS，但整个项目仍为 `IN_PROGRESS`。必须运行 `scripts/test-final-project-readiness.ps1`；负责人六项真机矩阵或数据库安全整改任一未完成时，该脚本按设计返回非零，禁止最终标签和完成声明。
- 上述限制不适用于 H5：每个修改页面必须由 Codex 在浏览器中实际打开体验，逐页检查几何，并通过代表页截图比对变形、错位、溢出和遮挡。
- 长任务每 30 分钟及每个阶段边界必须执行进展自查；连续两次自查没有新增制品、验证结果、部署或已解决事实时，必须更换方法并执行下一项具体动作，禁止重复阅读和无限分析冒充进展。
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
- `16_RUNTIME_CLICK_AUDIT.md`：UI 范围的唯一判定方法
- `17_RUNTIME_CLICK_GRAPH.csv`：从真实父入口到子页面的点击证据；没有父边不得标为 `IN_SCOPE`
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
- 禁止把 DOM 或路由存在当作用户可达功能；改前不可见、不可点击的节点改后仍必须不可见、不可点击且不占位。
- 禁止使用运行时 CDN 作为新视觉依赖。
- 禁止页面出现大面积深色背景、深色头部或深色启动页。
- 禁止 App 接受任意外部 scheme、绕过 SSL 或申请无必要的全盘存储权限。
- 禁止未完成关版和漂移审计就推进下一版本。
- 禁止 Codex 在负责人电脑安装 Android SDK、ADB、模拟器或测试 APK；禁止以此为由跳过 H5 浏览器体验和截图比对。
