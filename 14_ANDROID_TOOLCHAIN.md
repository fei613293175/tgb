# Android 工具链与签名保管

文档状态：R00 已验证  
验证日期：2026-07-26

## 1. 当前机器验证结果

- Android SDK：存在于当前用户的标准本地 SDK 目录。
- 已安装平台：`android-36`、`android-36.1`、`android-37.0`。
- 已安装 Build Tools：`36.0.0`、`36.1.0`。
- Platform Tools / ADB：`37.0.0-14910828`。
- Command-line Tools：`20.0`。
- Gradle 缓存：可运行 `9.4.1`，R01 仍以工程 Wrapper 锁定的版本为准。
- JDK：Microsoft OpenJDK `21.0.12+8-LTS`，官方 ZIP 的 SHA-256 已与官方校验文件一致。

Android Studio 自带 `jbr` 在本机是不完整安装（缺少可执行文件），不得依赖它。项目通过工作区外的 `.toolchain` 目录使用免安装 JDK，不要求管理员权限，也不修改全局 PATH。

## 2. 换电脑恢复

在交接包根目录执行：

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\android\bootstrap-toolchain.ps1
powershell -ExecutionPolicy Bypass -File .\scripts\android\verify-toolchain.ps1
```

脚本从 Microsoft 官方主版本 URL 下载 Windows x64 JDK ZIP，同时下载官方 SHA-256 文件；校验通过后才解压。Android SDK 若不在标准目录，通过 `-AndroidSdkRoot` 明确传入。

`.toolchain` 是机器缓存，不进入交接包和源码。JDK 版本升级必须重新记录版本、校验和和 Android 构建结果。

## 3. R01 版本锁定

- 工程必须提交 Gradle Wrapper，不能依赖全局 `gradle`。
- `compileSdk`/`targetSdk` 以 R01 工程实际可构建且符合发布要求的版本冻结。
- Kotlin、Android Gradle Plugin、AndroidX 版本写入工程并生成依赖锁定/校验材料。
- 每次构建脚本显式定位 JDK 与 Android SDK，不假设系统 PATH 已配置。

## 4. 签名保管

- 正式 alias 固定为 `tuiguangbao`，RSA 4096。
- R01 首次 Release 前生成唯一正式 keystore。
- keystore 与密码只能保存到交接包外的私有目录/负责人密码管理器；不得进入源码、ZIP、日志、证据或聊天。
- Gradle 仅从环境变量或未入库的本机 `keystore.properties` 读取。
- 负责人是签名材料的最终保管者；换电脑时必须通过独立安全渠道迁移签名材料。
- 无正式签名材料时允许构建 Debug/未发布测试包，但不得把它标记为正式 Release。

## 5. R00 结论

JDK、SDK、ADB、SDK Manager 和 Gradle 的基础能力已经实测可运行。系统级 MSI 因管理员确认被取消后，采用免安装 JDK 方案恢复，不构成 R01 阻断。正式签名材料将在 R01 创建并交由负责人独立保管。

## 6. 服务器正式构建

服务器复用既有 `hhy-android-toolchain:r08-api36-cache` 镜像。每次使用新的不可变 build id：

```bash
/opt/tg-android-r01/incoming/build-on-server.sh \
  <build-id> \
  /opt/tg-android-r01/incoming/source.tar.gz \
  <existing-root-only-signing-dir>
```

权威脚本在交接包 `scripts/android/build-on-server.sh`。源码、私有签名和 artifacts 必须分目录；私有目录 `700`、文件 `600`，只读挂载到容器 `/run/tgb-signing`。构建执行单元测试、Release Lint、签名 APK、10 MiB、AAPT、APKSigner、品牌/包名/SDK 和 SHA-256 门禁，再下载到负责人本机桌面。
