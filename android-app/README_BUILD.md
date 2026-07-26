# 推广宝 Android 构建

## 前置

从交接包根目录执行：

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\android\bootstrap-toolchain.ps1
powershell -ExecutionPolicy Bypass -File .\scripts\android\verify-toolchain.ps1
```

工程固定：

- applicationId：`com.suewammes.tuiguangbao`
- minSdk：23
- compileSdk：37
- targetSdk：36
- AGP：9.2.1
- Gradle Wrapper：9.4.1
- Java/Kotlin 字节码目标：17

## Debug

```powershell
..\scripts\android\build-android.ps1 -Variant debug
```

## Release 签名

正式 keystore 与密码不得放在本目录。构建脚本只读取：

```text
TGB_KEYSTORE_PATH
TGB_KEYSTORE_PASSWORD
TGB_KEY_ALIAS
TGB_KEY_PASSWORD
```

R01 的正式 alias 固定为 `tuiguangbao`。未提供变量时不得把未签名产物标记为 Release。

项目标准流程由脚本从交接包外的 `.private\tg-signing` 加载并只在当前进程设置这些变量：

```powershell
..\scripts\android\create-signing-material.ps1
..\scripts\android\build-android.ps1 -Variant release
```

脚本不输出密码。`.private` 必须与交接 ZIP 分开安全迁移。
