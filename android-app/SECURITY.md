# Android 安全边界

- WebView 仅内嵌 `https://tg.suewammes.com`，禁止 HTTP、用户信息 URL 和任意主机。
- 不创建 JavaScript Bridge，不允许文件系统访问，不忽略 SSL 错误。
- 外部 HTTPS 只在用户手势下交给系统浏览器。
- 支付仅允许来自站内当前页的 `alipays`/`alipay` + `platformapi` + 官方支付宝包组合。
- `intent://` 仅提取通过白名单的数据 URI，拒绝显式组件、选择器和其他包，不直接执行解析得到的 Intent。
- 文件上传使用 Photo Picker/SAF，不请求广泛存储权限。
- Cookie 只用于站内会话，第三方 Cookie 默认关闭；Cookie 不写日志。
- 下载仅接受站内 HTTPS URL。
