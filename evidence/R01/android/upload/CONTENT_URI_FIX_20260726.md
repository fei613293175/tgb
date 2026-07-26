# H5 选图上传 content URI 修复

- 日期：2026-07-26
- 状态：源码、本机、历史 Android 36 instrumentation、服务器正式签名构建 PASS；真实上传等待负责人异步真机反馈

## 问题

当前实现已经通过 Photo Picker/SAF 返回选中图片，但 WebView 同时设置了
`allowContentAccess=false`。系统选择器返回的是受限 `content://` URI；
禁止 content access 会造成“选择器能打开、图片也能选，但 H5 无法可靠读取并上传”
的假通过。

## 修复

- `allowContentAccess=true`：只让 WebView 使用系统授予当前选择项的 content URI；
- `allowFileAccess=false`：继续禁止 raw `file://`；
- `MIXED_CONTENT_NEVER_ALLOW`：继续禁止 HTTPS 页面加载不安全 HTTP 内容；
- Manifest 仍只有网络权限，没有添加 `READ_MEDIA_IMAGES`、
  `READ_EXTERNAL_STORAGE`、`WRITE_EXTERNAL_STORAGE` 或所有文件访问权限；
- 新增 Android instrumentation，在 Android 36 真实 WebView 上断言上述三项配置。

## 本机证据

- `testDebugUnitTest lintDebug assembleDebug assembleDebugAndroidTest`：PASS
- Debug APK 字节数：`19256336`
- Debug APK SHA-256：
  `b19a34cab43790ed96010f80c549cf10ddd84bbf4cfe6dfaed90e60d8048affa`

## 独立与服务器证据

- GitHub Actions 历史 Run `30192427296`：PASS
- Android 36 instrumentation：1 项，0 failure，0 error，0 skip
- 服务器 build ID：`20260726T152600+0800`
- 正式 APK 字节数：`18153952`
- 正式 APK SHA-256：
  `7b83c986ffc47481f0f8613a82ea22c80100b8a7fa55066825e3dbefa9e21398`
- 桌面交付：`推广宝-1.0.0-R01-相册上传修复.apk`

真正的发布、头像、名片、聊天四入口上传仍必须使用隔离数据在实体机完成端到端
验证；配置与 instrumentation 通过不能替代服务端收到文件的证据。依据 D-020，
该反馈异步返回且不阻塞后续版本开发，但在 R09 生产发布前必须关闭。
