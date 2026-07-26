# R01 支付 H5 兼容修复服务器构建结果

- 日期：2026-07-26
- 结果：PASS
- 对应源码提交：`3692ec4f1487533e3f17d54f0f6360e8265bcc29`
- 构建环境：服务器既有 Docker 镜像 `hhy-android-toolchain:r08-api36-cache`
- 权威构建目录：`/opt/tg-android-r01/builds/20260726T155100+0800`
- 源码归档 SHA-256：`16ca292b4c367502eb25e2099e24e9d773d084fe6229a1463c6c8604971d8989`
- 任务：`testDebugUnitTest lintRelease assembleRelease`
- 任务结果：BUILD SUCCESSFUL
- 正式 APK 字节数：`18154781`
- 正式 APK SHA-256：`0f2b8c1dbdbab544623a214d002b7a4611e03fde806f7f534de6712cfbdfa975`
- 证书 SHA-256：`622faaef8c659efafcf67bf7ddd9fdd8409186a5f4043d33de368db7cb668854`
- 包名：`com.suewammes.tuiguangbao`
- versionCode/versionName：`1` / `1.0.0`
- minSdk/targetSdk/compileSdk：`23` / `36` / `37`
- App 名：`推广宝`
- 签名：v1 PASS、v2 PASS、v3 PASS
- 桌面交付：`C:\Users\小白\Desktop\推广宝-1.0.0-R01-支付H5兼容修复.apk`
- 桌面字节数和 SHA-256：与服务器完全一致

## 本版额外断言

- 3 组 `HostPolicyTest` 通过；
- 精确支付 H5 域名可从本站进入 WebView；
- 相似后缀与不可信来源被策略拒绝；
- 支付宝 deep link 仍受 scheme、host 和 package 三重约束；
- 没有重新引入原生顶部进度条；
- 没有新增存储或相册广泛权限。

## 原始报告

- `aapt-badging.txt`
- `apksigner-report.txt`
- `tuiguangbao-1.0.0-release.apk.sha256`
- `tuiguangbao-1.0.0-release.apk.bytes`

## 实体机边界

最新版 APK 已放到桌面，负责人反馈异步返回，开发不等待。真实支付 H5 的后续
重定向域名、支付宝拉起、取消/成功返回和订单刷新仍是累计发布门禁，R09 生产
发布前必须有脱敏真机证据。
