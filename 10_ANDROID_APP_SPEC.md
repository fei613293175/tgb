# 10 推广宝 Android 原生 App 规范

## 1. 固定产品身份

| 项目 | 决定 |
|---|---|
| App 名称 | 推广宝 |
| applicationId | `com.suewammes.tuiguangbao` |
| 语言 | Kotlin |
| 架构 | 单 Activity + 原生 WebView 容器 |
| 主站 | `https://tg.suewammes.com/` |
| minSdk | 23 |
| compile/target 基线 | R01 冻结为 compileSdk 37 / targetSdk 36；提升编译 API 不等于提前改变目标运行行为 |
| 版本起点 | `versionCode 1` / `versionName 1.0.0` |
| 图标源图 | `assets/brand/tuiguangbao-app-icon-master-light-v2.png` |
| 页面主题 | 仅浅色；不提供深色主题 |

## 2. 工程边界

App 是业务 H5 的原生承载层，不重写服务器业务。它负责：

- 加载允许域名内的 H5；
- 处理系统状态栏/导航栏和 H5 安全区；
- 将 H5 文件选择映射到系统相册；
- 将受信任支付 URL 映射到支付宝等第三方 App；
- 处理返回、刷新、下载、离线和错误；
- 管理 Cookie、会话和 WebView 生命周期；
- 提供“推广宝”启动页、图标和应用身份。

业务页面、登录、订单、金额和支付结果仍以服务器为准。

## 3. 建议目录

```text
android-app/
  app/
    src/main/
      AndroidManifest.xml
      java/com/suewammes/tuiguangbao/
        MainActivity.kt
        web/TgbWebViewClient.kt
        web/TgbChromeClient.kt
        web/ExternalIntentRouter.kt
        web/FileChooserCoordinator.kt
        web/AllowedHosts.kt
      res/
        drawable/
        mipmap-*/
        values/
        xml/
        raw/
  build.gradle.kts
  settings.gradle.kts
  gradle.properties
  README_BUILD.md
  scripts/verify-apk.ps1
```

## 4. WebView 行为

- 只在 WebView 内加载 `https://tg.suewammes.com` 及书面批准的同业务主机。
- `http:`、证书错误、域名不匹配、任意重定向不得被静默放行。
- 启用 JavaScript、DOM Storage 和业务需要的 Cookie。
- 第三方 Cookie 仅在真实支付/登录回归证明需要时开启并记录决定。
- 禁止 WebView 文件系统任意访问和不必要的通用 JavaScript Bridge。
- User-Agent 追加稳定标记：`TuiGuangBaoAndroid/{versionName}`，不替换系统 UA。
- `target="_blank"`、下载、文件选择、返回历史、离线重试都要有明确处理。
- WebView 可后退时先后退；不可后退时二次确认退出 App。
- 页面恢复后重新拉取/刷新支付订单状态，App 本地不判定支付成功。

## 5. 顶部状态栏与 H5 安全区

固定采用“WebView edge-to-edge，H5 单一拥有页面安全区”：

1. 状态栏背景使用浅色 `#F4F7FB` 或白色，图标使用深色。
2. WebView 铺到系统栏后方；原生根容器和 WebView 的四边 padding 必须为 `0`，不得把 `statusBars.top` 或 `navigationBars.bottom` 再加到 H5 外层。
3. H5 继续使用现有 `env(safe-area-inset-top/bottom)` 和页面顶栏规则处理需要避让的控件；原生不得消费后再重复补偿。
4. 状态栏可以覆盖 H5 顶栏的浅色背景层，但关键标题、返回键和输入控件不得被系统图标遮住；不得为避免遮挡而额外制造第二段空白。
5. App UA 标记或只读环境标识仅用于选择安全区策略，不得影响业务逻辑。

必须留存以下对比截图：

- 浏览器 390×844：首页、详情、发布、聊天、收银台；
- App 真机同页面；
- 有刘海/打孔屏和手势导航设备；
- 横竖屏策略（默认锁定竖屏时也需验证系统旋转提示不会破坏页面）。

验收：状态栏下方不得重复出现原生 inset 空白；标题栏背景允许穿透系统栏，关键控件不得被系统图标或手势区遮挡。源码门禁验证原生根容器零 padding，最终几何由负责人真机截图异步验收。

## 6. 相册权限与图片上传

H5 入口：

- `input[type=file]`
- `accept="image/*"`、指定 MIME、单选/多选
- 取消选择
- 页面销毁/旋转后的回调

原生策略：

1. 首选 Android Photo Picker 或 Storage Access Framework；
2. Android 13+ 在确需传统媒体浏览回退时请求 `READ_MEDIA_IMAGES`；
3. Android 14+ 按系统能力支持“选择的照片”访问；
4. Android 12 及以下仅在回退路径请求 `READ_EXTERNAL_STORAGE`，并限制 `maxSdkVersion`；
5. 不请求 `WRITE_EXTERNAL_STORAGE`，不申请所有文件访问；
6. 使用 Activity Result API，将最终 `Uri[]` 回传 `WebChromeClient.FileChooserParams`；
7. WebView 保持 `allowFileAccess=false`，但必须启用 `allowContentAccess=true`，
   使系统只授予当前选择项的 `content://` URI 能被 H5 上传读取；
8. 正确处理 `content://`、MIME、文件大小、取消和权限拒绝；
9. 不把相册路径、图片或 EXIF 私密信息写入日志。

测试矩阵：

- 首次允许、拒绝、拒绝后再次选择；
- 单图、多图、取消、大图、HEIC/JPEG/PNG；
- 发布、名片、头像、聊天四个实际上传入口；
- Android 8/10/12/13/14/15 代表设备或云真机。

## 7. 第三方 App 与支付宝

按负责人 2026-07-27 最新真机反馈，App 不再以支付域名或来源白名单阻断合法第三方导航；安全边界改为协议能力和 Intent 清洗。

路由规则：

- 所有 HTTPS 页面和第三方收银台重定向留在主 WebView，新窗口也加载到主 WebView；
- 支付宝及其他可浏览第三方 App scheme 使用 `Intent.ACTION_VIEW + CATEGORY_BROWSABLE`；
- `intent://` 使用安全解析，必须移除显式组件/选择器等危险覆盖并加 `CATEGORY_BROWSABLE`；
- 合法 HTTPS 不依赖来源域名或瞬时用户手势，不得显示“不受信任外链”拦截提示；
- `intent:` 强制 ACTION_VIEW，清除 component、selector、flags、extras 和 clipData，再添加浏览器安全类别；
- 无可处理 App 时打开支付方提供的 HTTPS 回退或展示可理解错误；
- `javascript:`、`file:`、`data:`、`content:`、`about:`、`blob:` 和 HTTP 降级不得外跳；
- 不允许 `onReceivedSslError` 中直接 `proceed()`。

支付宝测试：

1. 沙箱/最小可控订单进入收银台；
2. 点击支付能拉起已安装支付宝；
3. 未安装支付宝有正确回退；
4. 取消支付返回推广宝；
5. 支付完成返回后由服务端查询确认订单；
6. App 被系统回收后仍能恢复并刷新状态；
7. 重复返回不产生重复订单或重复扣款。

## 8. 品牌与浅色主题

- 启动页使用 `#F4F7FB` 或白色背景，居中显示浅色版品牌图形和“推广宝”字标。
- 状态栏、导航栏、离线页、错误页、权限说明均使用浅色主题。
- Android 13+ themed icon 需要单色前景版本；不得直接自动把彩色 PNG 染黑。
- 自适应图标拆分 foreground/background，关键图形保持在安全区。
- App label、最近任务标题、通知渠道名（若未来启用）、关于页统一为“推广宝”。

## 9. APK 体积

签名 Release APK 必须不低于 10 MiB。

执行：

```powershell
$apk = Get-Item -LiteralPath .\app\build\outputs\apk\release\app-release.apk
if ($apk.Length -lt 10485760) { throw "APK 小于 10 MiB：$($apk.Length) bytes" }
```

要求：

- 体积在最终签名 APK 上测量；
- 不在签名后追加无效数据；
- 优先通过必要的 AndroidX WebView 兼容依赖、离线错误/帮助资源、品牌密度资源自然达到；
- 若仍不足，新增资源必须有真实离线用途和清单说明；
- 同时记录 APK SHA-256，避免“达到体积但文件损坏”。

## 10. 签名方案

- 正式 alias：`tuiguangbao`
- 密钥：RSA 4096，长期有效；
- keystore 文件和密码只由负责人安全保管，不进 Git、不进交接包；
- Gradle 从环境变量或本机未入库的 `keystore.properties` 读取；
- 首个 Release 关版记录 SHA-256 证书指纹；
- 所有后续版本必须使用同一证书；
- Debug 签名 APK 只能测试，不能替代正式交付。

## 11. 构建与交付证据

每个 App 版本必须交付：

- 可复现的源码和 Gradle lock/version 信息；
- 签名 Release APK；
- APK 字节数和 SHA-256；
- applicationId、versionCode、versionName；
- 签名证书 SHA-256；
- `aapt dump badging` 或等价包信息；
- 真机启动、相册、上传、支付宝拉起/返回、状态栏截图全部由负责人测试并异步返回；Codex 不安装或运行 APK；
- WebView 控制台和 Android logcat 脱敏结果；
- 回滚到上一 APK 的验证记录。

按 D-036，负责人电脑不得由 Codex 安装 Android SDK、ADB、模拟器或测试 APK。Codex 的 Android 交付边界是源码、服务器签名构建和桌面 APK；真机功能结论只能由负责人反馈形成。H5 页面仍由 Codex 使用浏览器体验和截图验收。
