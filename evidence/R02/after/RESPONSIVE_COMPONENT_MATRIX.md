# R02 响应式共享组件矩阵

日期：2026-07-26
结论：`PASS`

## 方法

- 测试入口：`tests/r02/light-grid-preview-frame.html`
- iframe 内页：`tests/r02/light-grid-preview.html`
- iframe 视口严格设为 360×800、390×844、430×932。
- 内页同时加载现有 `custom.css` 和 R02 `light-grid-r02.css`。
- 浏览器读取 iframe 内真实 `innerWidth`、`innerHeight`、根字号，以及
  document、body、组件三层 scroll/client 宽度。
- 原始浏览器视口分段截图仅用于生成证据，错误裁切和临时切片不进入仓库。

## 结果

| 视口 | 根字号 | document | body | 组件 | 结果 |
|---|---:|---|---|---|---|
| 360×800 | 20px | 360=360 | 360=360 | 360=360 | PASS |
| 390×844 | 20px | 390=390 | 390=390 | 390=390 | PASS |
| 430×932 | 22px | 430=430 | 430=430 | 430=430 | PASS |

表内等式为 `scrollWidth=clientWidth`，证明没有横向溢出。430px 档实际触发
旧站 `@media (min-width: 414px)`，因此不是把桌面媒体查询结果缩小后截图。

## 证据

| 文件 | SHA-256 |
|---|---|
| `R02-LIGHT-GRID-360x800.jpg` | `700736c843a98bed1a9f289ad72368f58f01f7f62578ce2d2374871f45dbbbfb` |
| `R02-LIGHT-GRID-390x844.jpg` | `21a0613c060c1ccf75a8fc6f83a2c94284f21e28cd448f8aad82283d2fc0205b` |
| `R02-LIGHT-GRID-430x932.jpg` | `9d2362ba7b2027ff465f1300b3b3ce83b598155eef757354a7115c24d7534272` |

## 边界

这是共享组件和响应式断点的受控证据，不是登录后真实业务页截图。登录后共享
壳层视觉以及 Android WebView 状态栏安全区仍按 R02 剩余门禁单独关闭。
