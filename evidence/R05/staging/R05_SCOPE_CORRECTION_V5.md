# R05 v5 真实点击范围纠偏结果

日期：2026-07-27
状态：`PASS_FOR_SCOPE_DEPLOYMENT`，R05 整体仍为 `IN_PROGRESS`

## 结论

- R05 v3 因混入直接 URL、隐藏 DOM 和源码页面，状态保持 `INVALIDATED_SCOPE_DRIFT`。
- v5 包含真实点击已证的内容详情、举报、发布、我的项目和项目审核页面，以及首页隐藏分类纠偏文件，共 12 个文件。
- 评论、点赞、收藏、红包、名片、成员页和条件信息流没有新增视觉选择器；成员页和私信详情仍等待隔离点击。
- 业务控件协议、模板流程、业务 URL 和业务脚本相对 R04 基线不变。

## 制品

- 文件：`deliverables/r05-click-proven-overlay-v5.tar.gz`
- SHA-256：`b6fd61f320b1f15726608abf69f610a27ea744e37e42fdf62108769b42f09de9`
- 大小：52,515 bytes
- 文件数：12
- 本地门禁：`scripts/test-r05-v5-scope-overlay.ps1`，结果 `PASS`

## 隔离部署

- 部署 ID：`20260727T051035+0800`
- 舞台：`/www/staging/tg-h5-ui-r05/site`
- 备份：`/www/staging/tg-h5-ui-r05/private/change-backups/20260727T051035+0800-click-proven-v5`
- 部署脚本：`scripts/remote/r05_deploy_click_proven_v5.sh`
- 结果：12 个允许文件安装，18 个超范围文件恢复或移除，PHP lint `PASS`，首页 `200`，POST `405`。

## 独立校验

- HTTP：首跳 `302`，最终 `200`，POST `405`
- 主库表数：680
- UCenter 表数：607
- 生产稳定代码：`UNCHANGED`
- 生产稳定代码 SHA-256：`91a57ef67b34b9f650becc9413b03288f48b46f1e9452bc779cf03944ec38984`
- 隔离舞台制品清单 SHA-256：`573ac4a1fa467ef595704b062805c299c6ee28a3c0da32456721dc72266525d7`

## 当前未完成

- 九个早期公开/搜索页面需按 G28 补当前点击边，或暂时降级为候选。
- 成员页和私信详情需合成数据隔离点击与回滚。
- 因此不得生成 `R05_RESULT.md`，不得将 R05 标为 `PASS`，不得推进 R06。
