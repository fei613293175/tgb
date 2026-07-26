# GitHub Actions 自动构建与回归

文档状态：R01 已接入，正在完成首次全绿远程运行

仓库：`https://github.com/fei613293175/tgb`

## 1. 角色

GitHub Actions 是独立于本机和服务器的第三重验证环境：

1. 本机负责快速单元测试、Lint 和修改反馈；
2. 服务器既有隔离镜像负责最终正式签名 Release APK；
3. GitHub Actions 负责干净检出、Debug APK 打包、Android 36 模拟器安装/启动、截图、日志与回归。

GitHub CI 的 Debug APK 不替代服务器正式签名包。正式签名材料不得上传为仓库文件、Artifact、Cache 或日志。

## 2. 自动任务

工作流：`.github/workflows/android-ci.yml`

每次推送到 `main`、Pull Request 或手动触发时必须完成：

- 使用固定提交 SHA 的 GitHub Actions；
- Temurin JDK 21；
- 固定 Command-line Tools；稳定通道安装 Platform/Build Tools，API 37 编译平台通过
  显式 `--channel=3` 安装并断言 `android.jar`；
- Gradle Wrapper 完整性校验和依赖严格校验；
- `testDebugUnitTest`；
- `lintDebug`；
- `assembleDebug`；
- APK 不低于 10 MiB；
- 包名、App 名、minSdk、targetSdk、权限与 Debug 签名检查；
- 数据库标识和明显明文凭据扫描；
- Android 36 / Pixel 5 / x86_64 模拟器安装；
- 启动 `MainActivity` 并确认进程存活；
- 保存启动截图、UI hierarchy、Activity/Window/Package dumpsys；
- 保存全量与 App 进程日志，并拦截 crash/ANR；
- 验证移动 UA 的公网 H5 入口最终返回 HTTP 200；
- 无论成功失败均上传 APK、测试报告、截图和日志，保留 14 天。

## 3. 失败闭环

任何红灯均不得标记版本通过：

1. 下载失败 Job 的 Artifact 和日志；
2. 在 `evidence/Rxx/android/github-actions/` 记录 run URL、commit SHA、失败门禁和脱敏摘要；
3. 在本机或服务器复现；
4. 修改源码或工作流；
5. 重新推送并运行同一门禁；
6. 只有替代运行全绿且截图人工检查无明显视觉偏差，才可关闭问题。

不得使用 `continue-on-error`、删除失败断言、关闭依赖验证或扩大权限来制造绿灯。

API 37 在稳定通道不可见时，不得把 `compileSdk`/依赖版本静默降级。工作流必须保留
已冻结的 `compileSdk 37 / targetSdk 36`，并显式选择可提供该编译平台的 SDK 通道。

## 4. 截图和 UI 复核

CI 自动保存未登录公共入口启动截图。截图至少人工检查：

- 启动页和页面以浅色为主；
- 顶部内容不被状态栏遮挡；
- 没有空白页、系统崩溃页、SSL 警告或桌面二维码页；
- 品牌名为“推广宝”；
- 页面主体没有明显横向裁切。

登录后、资金和身份页面不在公共 CI 中注入真实账号。它们继续使用隔离预发布账号、脱敏证据和副作用门禁。

## 5. 产物边界

- CI Artifact：Debug APK、测试报告、日志、截图，只用于验证；
- 服务器交付：正式签名 Release APK，复制到负责人本机桌面；
- Git 仓库：源码、文档、工作流和脱敏证据，不提交 APK、keystore、密码、Cookie、Token 或真实个人截图。

## 6. 新电脑接续

克隆仓库后先执行：

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\continuity.ps1 -Mode resume
```

然后核对 `CURRENT_STATUS.yaml`、`NEXT_TASK.yaml` 和最近一次 GitHub Actions run。若本地 manifest 与提交不一致，停止开发并按 `08_CONTINUITY_PROTOCOL.md` 排查，不能直接重建校验和掩盖差异。
