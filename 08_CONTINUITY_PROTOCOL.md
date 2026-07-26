# 08 无状态接续协议

## 1. 目标

换电脑、换 AI、没有聊天记录时，只要拥有完整交接包并说“立即开始”或“立即开发”，执行者就能：

- 确认交接包未损坏；
- 知道唯一当前版本和任务；
- 知道业务、视觉、品牌、Android 和生产边界；
- 找到证据、决定和踩坑；
- 按正确顺序继续，而不是重新需求访谈。

## 2. 接管步骤

在交接包根目录运行：

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\continuity.ps1 -Mode resume
```

如果校验失败：

- 立即停止开发；
- 不自动“修复”校验和；
- 对比文件来源和最后一次关版记录；
- 由负责人确认正确版本后再重建 manifest。

如果校验通过，依次读：

1. `06_HARD_GATES.md`
2. `00_PROJECT_CHARTER.md`
3. `CURRENT_STATUS.yaml`
4. `NEXT_TASK.yaml`
5. `03_PAGE_LEDGER.csv`
6. `12_PAGE_SOURCE_MAP.csv`
7. `13_SIDE_EFFECT_TEST_PLAN.md`
8. `04_VISUAL_SYSTEM.md`
9. `10_ANDROID_APP_SPEC.md`
10. `14_ANDROID_TOOLCHAIN.md`
11. `15_GITHUB_ACTIONS.md`
12. `07_TEST_MATRIX.md`
13. `DECISIONS.md`
14. `LESSONS_LEARNED.md`
15. 当前版本关版记录（若存在）

## 3. 状态唯一性

- `CURRENT_STATUS.yaml` 是“现在做到哪里”的唯一机器可读事实。
- `NEXT_TASK.yaml` 是“下一步做什么”的唯一机器可读事实。
- 两者必须指向同一项目、当前版本和兼容任务。
- 文档中的未来路线不能被误认为已完成。
- 聊天中说过但未写入状态/决定的内容不具有接续效力。
- 本项目已有 D-013 的连续开发授权；关版通过后自动切换到下一版，不再等待重复授权，但任何高风险动作仍受 G00-G24 约束。

## 4. 开发前检查

- SSH 别名可用且目标正确；
- 生产/预发布目录清晰，不把生产当工作区；
- 无凭据进入文件；
- 当前任务边界和验收条件完整；
- 页面台账状态准确；
- Android 包名/签名连续性材料可用（涉及 App 时）；
- GitHub 远程、当前 commit 和最近一次 Actions 结果明确（涉及 App/自动化时）；
- 备份、回滚和隔离测试数据可用；
- 未发现另一个 `IN_PROGRESS` 大版本。

## 5. 开发中记录

任何非显然事实立即落盘：

- 复用的组件/选择器/脚本；
- 页面与模板映射；
- 业务协议 diff；
- GET 或按钮副作用；
- 控制台错误、WebView 差异；
- 失败尝试和解决办法；
- 负责人决定；
- 新风险与门禁建议。

不得等到会话结束再凭记忆补写。

## 6. 大版本关版

使用 `VERSION_CLOSEOUT_TEMPLATE.md` 建立：

`version-results/Rxx_RESULT.md`

然后执行：

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\continuity.ps1 -Mode drift-audit
```

必须逐项检查：

- 需求是否漂移；
- 业务功能是否仍完全一致；
- 页面是否漏改；
- 是否复用了正确的视觉/组件优点；
- 是否引入深色大块、暖橙旧风格或品牌混用；
- Android 权限、支付、状态栏、体积和签名是否漂移；
- 服务器正式包与 GitHub Actions 独立复验是否都有可追溯证据；
- 踩坑是否沉淀；
- 规则是否需要加固；
- 当前状态、下一任务和校验和是否同步。

## 7. 更新校验和

仅在所有文档和关版材料确认正确后重建 `MANIFEST_SHA256.txt`。manifest 不包含自身。

重建后必须在干净复制目录再次运行：

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\continuity.ps1 -Mode verify
```

## 8. 阻断规则

以下情况停止受影响任务并写入状态：

- 凭据/签名缺失；
- 生产与预发布目标无法辨别；
- 无法证明业务等价；
- 支付/提现等没有隔离数据；
- manifest 失败；
- 当前状态冲突；
- 安全风险可能在复制/构建中泄密；
- 需要改变包名、业务规则、法律主体或插件配置。

困难、耗时、页面多不是停止理由；应记录检查点并继续同一任务。
