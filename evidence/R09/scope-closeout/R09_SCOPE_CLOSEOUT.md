# R09 真实可达范围收尾

- 日期：2026-07-27
- 视口：`390x844`
- User-Agent：Android 15 mobile UA，并追加 `TuiGuangBaoAndroid/1.0.0`
- 原则：桌面 UA、直接 URL、源码字符串和历史模板均不能证明前端视觉范围。

## 已补真实点击边

- 公开登录页 -> 注册账号、验证码登录、服务协议、隐私政策。
- 已登录首页 -> 搜索。搜索表单使用 `_blank`；`Page.windowOpen` 的用户手势事件
  捕获到真实子 URL，子页重新施加 Android H5 UA 后加载 20 条结果。
- 隔离项目详情 -> 防骗提醒 -> `/m/fpsm.html`。详情访问前后由
  `r05_detail_get_rollback.sh` 快照和恢复，视图计数、访问日志、日任务行集及资金
  不变量全部恢复；临时认证桥、来源桥和本机隧道全部关闭。

## 移出视觉范围

- `/m/hyxy.html`
- `/m/help.html`
- `/m/gywm.html`

生产 Android H5 的个人中心和设置中心均未渲染上述路由的链接或可见文本。
它们因此属于直接 URL / 源码页面，不属于负责人要求的“真实用户逐一点击能进入”范围。
R09 候选 v3 明确排除这三个文件，并用带哈希前置条件的生产纠偏脚本恢复其
R09 上线前文件；历史 R03/R09 制品仍保留作审计证据。

候选 v3 使用固定 ustar 时间和确定性 gzip，连续两次构建 SHA-256 均为
`a65d0e510cea5399c0b49ed53dbdea6bbacb9714bc34d54a952ef211a8f8e389`；其
78 个文件已在生产逐文件通过 SHA-256 校验。该候选未重复部署到 R08 舞台。

## 浏览器结果

- 搜索结果：`20` 个项目条目，`innerWidth=390`，无横向溢出，旧品牌可见数 `0`。
- 防诈骗页：`innerWidth=390`，`scrollWidth=390`，无横向溢出。
- 证据：
  - `R09_PUBLIC_ENTRY_CLICK_AUDIT.json`
  - `R09_SEARCH_ENTRY_CLICK_AUDIT.json`
  - `R09_ANTI_FRAUD_CLICK_AUDIT.json`
  - `PUBLIC-LOGIN-PARENT-390x844.png`
  - `AUTH-HOME-SEARCH-PARENT-390x844.png`
  - `SEARCH-RESULT-390x844.png`
  - `ANTI-FRAUD-PARENT-390x844.png`
  - `ANTI-FRAUD-CHILD-390x844.png`
