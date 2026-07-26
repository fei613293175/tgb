# R04 部署与回滚

- 最终舞台部署：`20260726T233007+0800`
- 覆盖文件：5
- 归档 SHA-256：`bedb3ae60d63de7a7410a2952c459d47d4df96b6532df8ee6da86e9f7eb8cbc2`
- 本地归档：`deliverables/r04-site-overlay-v11.tar.gz`
- 服务器备份：`/www/staging/tg-h5-ui-r04/private/change-backups/20260726T233007+0800`

回滚只能对白名单文件逐项执行：先核对 `BEFORE_SHA256.txt`、`AFTER_SHA256.txt` 和
`CREATED_FILES.txt`，将备份恢复到 R04 舞台，清理模板缓存，再执行 PHP lint、资源内容、
首页/分类/文章 GET、POST=405、数据库计数和生产稳定哈希门禁。不得把舞台备份直接覆盖生产。

R04 未部署生产；当前回滚目标只可能是 R04 舞台。
