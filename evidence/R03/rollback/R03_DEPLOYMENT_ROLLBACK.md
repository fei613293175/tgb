# R03 部署与回滚证据

- 最终部署 ID：`20260726T204235+0800`
- 覆盖文件：14 个，严格白名单
- 覆盖包 SHA-256：
  `87c52be0333750dbfcb7f7cd69b0d12ad34e5c506b63c0601abedf56e6c944b4`
- 备份：
  `/www/staging/tg-h5-ui-r03/private/change-backups/20260726T204235+0800`
- 本地 / 服务器逐文件哈希：14 / 14 一致
- 哈希清单：`evidence/R03/after/R03-OVERLAY-SHA256.txt`
- 哈希清单 SHA-256：
  `9129b2a4dc4502409b12a7cbb05aadb966faf2d66d0750da9e84e93f1e0dd5fb`

部署脚本在覆盖前验证归档 SHA-256 和精确文件白名单，并把每个既有文件保存到该部署
ID 的私有备份目录。恢复时只能按该次部署记录的 14 个相对路径逐一复制，随后执行：

```bash
bash /www/staging/tg-h5-ui-r03/private/scripts/r03_verify_staging.sh
```

不得用宽泛递归复制覆盖整个站点。最终部署后完整验证通过：生产稳定代码未变化、舞台
数据库表数一致、默认 `POST=405`。本版未发布到生产，因此没有生产回滚动作。

