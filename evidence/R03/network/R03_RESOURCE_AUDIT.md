# R03 资源与控制台审计

## 结论

- 11 页在 360x800、390x844、430x932 三档视口共完成 33 次运行时审计。
- 公共 UI CDN 请求：`0`。
- HTTP 4xx / 资源加载失败：`0`。
- JavaScript 异常 / 控制台错误：`0`。
- 新认证样式使用本站版本化 CSS；在线 Tailwind 已移除。
- 认证页 Font Awesome 改为服务器本地资源；jQuery 在依赖脚本前加载。
- 短信页业务脚本使用已存在的 `jq` 别名，不再发生 `$ is not defined`。

## 本地资源

- `source/plugin/xigua_hb/static/tgb-r03/auth-r03.css`
- `source/plugin/xigua_hb/static/tgb-r03/auth-light-grid-r03.css`
- `m/template/css/tgb-r03-legal.css`
- R02 本地品牌 SVG 和服务器既有 Font Awesome / jQuery

静态门禁限制认证编译 CSS 小于 32 KiB，并扫描 `cdn.tailwindcss.com`、jsDelivr、
cdnjs 和 unpkg。浏览器证据同时检查实际请求、状态码、异常和错误日志，不能用源码扫描
替代运行时结论。

逐视口原始审计数据位于 `evidence/R03/after/R03-BROWSER-*.json`。

