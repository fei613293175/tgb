# R01 content URI 修复服务器构建结果

- 日期：2026-07-26
- 结果：PASS
- 对应源码提交：`f57f41196ef8d53f171e4b13a518cf82d7d4a8ab`
- 构建环境：服务器既有 Docker 镜像 `hhy-android-toolchain:r08-api36-cache`
- 权威构建目录：`/opt/tg-android-r01/builds/20260726T152600+0800`
- 源码归档 SHA-256：`217ec9d09e6c4eaedfa899d3324ccc48341985c0c589fbdecdc34ce0769d1bd0`
- 任务：`testDebugUnitTest lintRelease assembleRelease`
- 任务结果：BUILD SUCCESSFUL
- 正式 APK 字节数：`18153952`
- 正式 APK SHA-256：`7b83c986ffc47481f0f8613a82ea22c80100b8a7fa55066825e3dbefa9e21398`
- 证书 SHA-256：`622faaef8c659efafcf67bf7ddd9fdd8409186a5f4043d33de368db7cb668854`
- 包名：`com.suewammes.tuiguangbao`
- versionCode/versionName：`1` / `1.0.0`
- minSdk/targetSdk/compileSdk：`23` / `36` / `37`
- App 名：`推广宝`
- 签名：v1 PASS、v2 PASS、v3 PASS
- 桌面交付：`C:\Users\小白\Desktop\推广宝-1.0.0-R01-相册上传修复.apk`
- 桌面文件字节数与 SHA-256：与服务器完全一致

## 修复与门禁

- `allowContentAccess=true`，允许系统 Photo Picker/SAF 授予的受限 `content://`；
- `allowFileAccess=false`，继续禁止 raw `file://`；
- mixed content：NEVER_ALLOW；
- 未新增广泛相册或存储权限；
- 构建前扫描确认没有重新引入原生顶部进度条。

真实 H5 上传和第三方支付 App 拉起/返回仍由负责人异步真机测试；开发不等待，
但这些项目继续保留为 R09 生产发布前必须清零的累计门禁。

## 原始报告

- `aapt-badging.txt`
- `apksigner-report.txt`
- `tuiguangbao-1.0.0-release.apk.sha256`
- `tuiguangbao-1.0.0-release.apk.bytes`

## 归档失败与修复

首次归档在服务器解压后把 `gradlew` 变成 CRLF，构建在 Gradle 启动前以
`cannot execute: required file not found` 停止。未修改构建断言或绕过测试。
随后只打包 Git 跟踪的工作树文件，检查 `gradlew` 为 LF，通过 3 MiB 分片传输，
服务器合并后以大小和 SHA-256 双重确认，再使用新的不可变 build ID 成功构建。

## 脱敏与签名保管

keystore 和签名环境继续位于服务器 root-only 私有目录，未进入源码归档、Git、
桌面旁报告或交接 manifest。本文不记录任何密码。
