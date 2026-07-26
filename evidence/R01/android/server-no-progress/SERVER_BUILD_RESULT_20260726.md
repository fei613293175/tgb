# R01 去除顶部进度条服务器构建结果

- 日期：2026-07-26
- 结果：PASS
- 构建环境：服务器既有 Docker 镜像 `hhy-android-toolchain:r08-api36-cache`
- 权威构建目录：`/opt/tg-android-r01/builds/20260726T143800+0800`
- 源码归档 SHA-256：`867c478b97b5a5b01e3849597057cfdaf2451f64865865a6d2960a621b105058`
- 任务：`testDebugUnitTest lintRelease assembleRelease`
- 任务结果：BUILD SUCCESSFUL
- 正式 APK 字节数：`18153898`
- 正式 APK SHA-256：`a03066bae79f67bafe5f0061d9cba88017839a9efd2690a83f196bbcf0c93bb8`
- 证书 SHA-256：`622faaef8c659efafcf67bf7ddd9fdd8409186a5f4043d33de368db7cb668854`
- 包名：`com.suewammes.tuiguangbao`
- versionCode/versionName：`1` / `1.0.0`
- minSdk/targetSdk/compileSdk：`23` / `36` / `37`
- App 名：`推广宝`
- 签名：v1 PASS、v2 PASS、v3 PASS
- 桌面交付：`推广宝-1.0.0-R01-去除顶部进度条.apk`

## 变更断言

构建前脚本扫描 Android 主源码，确认不存在：

- `ProgressBar`
- `onProgressChanged`
- `progressBarStyleHorizontal`

因此该 APK 不包含首包截图所示的原生顶部线性页面加载进度实现。负责人随后完成实体机复验并确认该进度条已经消失，结果 PASS；见 `../device/NO_PROGRESS_RETEST_20260726.md`。

## 原始报告

- `aapt-badging.txt`
- `apksigner-report.txt`
- `tuiguangbao-1.0.0-release.apk.sha256`
- `tuiguangbao-1.0.0-release.apk.bytes`

## 脱敏与签名保管

keystore 与四个签名环境变量保存在服务器 root-only 私有目录，未进入源码归档、Git、APK 旁报告或交接 manifest。本文不记录任何密码。

## 失败尝试

两个新通用脚本问题均在正式构建前被门禁阻止：

1. `20260726T142915+0800`：Docker 镜像已有 `/bin/bash` entrypoint，脚本重复传入 `bash`，容器将 Bash 二进制当脚本执行而停止；
2. `20260726T143400+0800`：签名环境固定指向 `/run/tgb-signing`，首次通用脚本挂载到 `/signing`，`validateSigningRelease` 正确停止。

修复为向镜像 entrypoint 直接传 `-lc`，并把 root-only 私有目录只读挂载到 `/run/tgb-signing`。未关闭或绕过签名验证。
