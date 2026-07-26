# R04 资源与控制台审计

- 日期：2026-07-26
- 修正部署：`20260726T233007+0800`
- 结论：PASS

## 本地资源

- CSS：`source/plugin/xigua_hb/static/tgb-r04/discovery-light-grid-r04.css`，20,848 字节。
- JS：`source/plugin/xigua_hb/static/tgb-r04/discovery-r04.js`，1,011 字节。
- 首页和搜索/分类模板使用本站版本键 `20260726-r04-2`；该键强制淘汰曾显示旧隐藏分类的错误 CSS 缓存。
- 未新增 Tailwind、Google Fonts、jsDelivr、cdnjs、unpkg 或公共 iconfont；图标继续复用 R02 本地字体与本站 SVG。

## 运行时

- 360/390/430 三档首页、搜索、分类没有 R04 插件页 console error/warn。
- 第一次诊断重载曾得到两个未关联 URL 的瞬时 DNS 失败；立即启用 request-id 关联后重载，最终 `Network.loadingFailed=0`、HTTP 4xx=0，不作为稳定回归。
- `discovery-r04.js` 只观察 `#list` DOM 并维护原有空态，不包含 `fetch`、`XMLHttpRequest` 或 jQuery AJAX。
- 静态门禁禁止 CSS 再出现 `[style*="height:0px"]` 解隐藏选择器。

## Android 边界

- Android 原生顶部加载进度条继续保持移除。
- R04 未修改 Android 源码或打包资源，因此不重建 APK。
