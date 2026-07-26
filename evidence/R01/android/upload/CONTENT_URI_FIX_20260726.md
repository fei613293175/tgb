# H5 选图上传 content URI 修复

- 日期：2026-07-26
- 状态：源码与本机构建 PASS，等待 GitHub Android 36 instrumentation 和新服务器签名包

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

真正的发布、头像、名片、聊天四入口上传仍必须使用隔离数据在实体机完成端到端
验证；配置与 instrumentation 通过不能替代服务端收到文件的证据。
