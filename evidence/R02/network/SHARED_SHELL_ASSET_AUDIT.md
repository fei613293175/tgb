# R02 共享壳层资源审计

日期：2026-07-26
结论：`PASS_WITH_REMAINING_AUTH_AND_ANDROID_EVIDENCE`

## 新增运行时资源

| 资源 | 方式 | 远程依赖 |
|---|---|---|
| Light Grid 令牌与组件 CSS | 本站版本化 CSS | 无 |
| 推广宝品牌标志 | 本站 SVG | 无 |
| 顶栏聊天图标 | 本站 SVG | 无 |
| 旧页面 iconfont | 保留现有本站资源 | 无新增 |

未引入在线 Tailwind、Google Fonts、公共 iconfont CDN 或新的第三方 UI
运行时依赖。

## 门禁

- CSS 响应包含 `Light Grid R02` 标记：PASS
- 两个 SVG 响应包含真实 `<svg` 标记：PASS
- CSS/SVG HTTP：200
- SVG MIME：`image/svg+xml`
- 外链旧聊天图已替换为本站 SVG：PASS
- 桌面引导页品牌 SVG 浏览器加载宽高：192×192
- 桌面引导页真实渲染：
  `evidence/R02/after/DESKTOP-SPLASH-1265x712.jpg`
- 截图 SHA-256：
  `da30c805c925b4461b62e19c22d5ed3e36006f96569ab26b3320ea6599951e8d`

## 响应式组件矩阵

真实 iframe 视口加载旧站 `custom.css` 与 R02 CSS，避免把嵌套在桌面页中的
固定宽度画布误当成手机媒体查询。三档均同时验证 iframe、文档、body 和设备
容器的 `scrollWidth == clientWidth`：

| 视口 | 旧 CSS 根字号 | 横向溢出 | 视觉证据 |
|---|---:|---|---|
| 360×800 | 20px | 无 | `evidence/R02/after/R02-LIGHT-GRID-360x800.jpg` |
| 390×844 | 20px | 无 | `evidence/R02/after/R02-LIGHT-GRID-390x844.jpg` |
| 430×932 | 22px | 无 | `evidence/R02/after/R02-LIGHT-GRID-430x932.jpg` |

该矩阵是共享组件和断点契约证据，不冒充登录后真实业务页面证据。

## 假阳性修复

早期部署的新静态目录受 `umask 077` 影响成为 root-only。Nginx 找不到
资源后回退到 `index.php`，旧门禁只检查 HTTP 200，因而错误通过。

现已同时加固：

1. R02 自有静态目录固定为 `www:www`、0755；
2. 文件固定为可读权限；
3. 资产检查除 HTTP 200 外必须匹配 CSS/SVG 内容标记；
4. 浏览器必须确认图片 `naturalWidth`、`naturalHeight` 和真实截图。

## 尚未关闭

- 登录后首页的共享顶栏、底栏和卡片视觉复核；
- Android WebView 中 R02 H5 与原生状态栏不叠加的实机证据。

以上仍是 R02 关版门禁，当前不得把 R02 标为完成。
