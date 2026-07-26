# R02 Android WebView 安全区证据状态

日期：2026-07-26
状态：`SOURCE_AND_BUILD_PASS_DEVICE_SCREENSHOT_ASYNC_PENDING`

## 已完成

- R01 原生实现保持不变：`WindowCompat.setDecorFitsSystemWindows(false)`。
- 根 `FrameLayout` 读取 status/navigation bars，并只在根容器设置四向 padding。
- 根容器返回已消费的系统栏 insets，WebView 不再接收第二份系统栏边距。
- H5 R02 CSS 只在 `@media (display-mode: browser)` 为浏览器固定头部追加
  `safe-area-inset-top`；App WebView 不命中该浏览器作用域。
- 新增 instrumentation 断言：根容器 padding 等于系统栏 insets，WebView 自身
  top/bottom padding 均为 0。
- 本机 Debug 门禁 PASS：单元测试、Lint、主 APK、instrumentation APK 编译。
- Debug APK：19,256,336 bytes；SHA-256：
  `4c99e43aa3f85015f92d8586be24d7eef99c01a12a9c8b1a46f1d0073556820b`。

## 本机设备执行结果

Android 36 x86_64 AVD 分别尝试：

1. AEHD 硬件加速、无快照、SwiftShader；
2. 纯软件加速、无快照、SwiftShader。

两次都在系统启动前退出，ADB 只短暂出现 `offline`，没有
`sys.boot_completed=1`，因此不能运行 instrumentation 或生成可信 App 截图。
测试后已停止残留 emulator 进程并关闭 ADB server。

## 裁决

- 不能用浏览器截图、静态源码或离线 AVD 冒充 App 真机截图。
- R02 没有修改 Android 正式源码或打包资源，R01 服务器签名 APK 仍是当前包；
  不为测试源码变化重建正式 Release，也不向桌面交付 Debug 包。
- 依据 D-020/G25，设备专属截图异步返回，不阻塞后续安全开发，但必须保留为
  累计发布门禁，最迟 R09 生产发布前在实体机关闭。
