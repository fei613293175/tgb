# GitHub Actions 历史记录与停用规则

文档状态：自 2026-07-26 / D-020 起停止自动运行，仅保留历史手动入口

仓库：`https://github.com/fei613293175/tgb`

## 1. 当前规则

GitHub 继续作为源码和无状态交接仓库，但 GitHub Actions 不再承担后续版本的自动测试或关版门禁：

1. `.github/workflows/android-ci.yml` 只能保留 `workflow_dispatch`；
2. 禁止 `push`、`pull_request`、`schedule` 等自动触发；
3. 不要求开发者等待 Actions，负责人真机反馈也采用异步方式；
4. 后续验证以本机自动检查、服务器隔离构建和负责人异步真机反馈为准；
5. 除非负责人以后形成新的书面决定，不得恢复自动触发。

历史 CI Debug APK 不能替代服务器正式签名包。正式签名材料不得上传为仓库文件、Artifact、Cache 或日志。

## 2. 历史运行

R01 曾使用 GitHub Actions 完成干净检出、编译、单元测试、Lint、Android 36 模拟器安装/启动、截图、日志与 instrumentation 复验。最终有效的历史全绿运行包括：

- Run `30192012488`：首个全绿运行，截图确认原生顶部进度条消失；
- Run `30192427296`：提交 `f57f41196ef8d53f171e4b13a518cf82d7d4a8ab`，Android 36 instrumentation 证明允许受限 `content://`、禁止 raw file 和 mixed content。

这些结果只证明对应历史提交，不自动覆盖后续 H5 或 Android 版本。

## 3. 保留工作流的原因

工作流源码保留是为了：

- 保留 R01 已验证过的模拟器和 APK 门禁实现；
- 便于审计历史失败与修复；
- 如果负责人未来明确要求，可手动运行特定提交做辅助诊断。

手动运行是可选诊断，不是默认流程，也不能替代服务器正式包或实体机验证。

## 4. 当前构建与失败闭环

每个可测试大版本：

1. 本机运行适用的单元测试、Lint、静态扫描和源码门禁；
2. 把准确源码提交传到服务器隔离构建目录；
3. 服务器生成正式签名 Release APK，并验证包名、App 名、SDK、权限、体积、签名和 SHA-256；
4. 将 APK 复制到负责人桌面并校验本机与服务器 SHA-256 一致；
5. 不等待负责人反馈，按 `NEXT_TASK.yaml` 继续开发；
6. 负责人异步返回问题后，立即登记并进入“分析→修改→重新构建→重新交付→复测”闭环。

相册真实上传、第三方支付 App 拉起/返回等实体机专属项目可以异步完成，但不得被删除或误报为已验证；它们是 R09 生产发布前必须清零的累计门禁。

## 5. 产物边界

- 本机产物：只用于快速反馈，不冒充正式交付；
- 服务器交付：正式签名 Release APK，复制到负责人本机桌面；
- 负责人实体机：异步安装和业务体验反馈；
- Git 仓库：源码、文档、停用后的手动工作流和脱敏证据；
- 禁止提交 APK、keystore、密码、Cookie、Token 或真实个人截图。

## 6. 新电脑接续

克隆仓库后先执行：

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\continuity.ps1 -Mode resume
```

然后核对 `CURRENT_STATUS.yaml`、`NEXT_TASK.yaml`、当前 commit、最近服务器构建和桌面 APK 记录。不得因为看见历史 Actions 文件或绿色 run 而恢复自动测试。若本地 manifest 与提交不一致，停止开发并按 `08_CONTINUITY_PROTOCOL.md` 排查，不能直接重建校验和掩盖差异。
