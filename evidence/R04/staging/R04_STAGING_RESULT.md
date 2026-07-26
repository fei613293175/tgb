# R04 隔离舞台结果

- 快照：`20260726T211724+0800`
- 来源：已关闭的 R03 舞台
- 站点：`/www/staging/tg-h5-ui-r04/site`
- 私有证据：`/www/staging/tg-h5-ui-r04/private`
- 监听：`127.0.0.1:18084`，仅 SSH 隧道
- 方法：GET / HEAD；POST 405
- 主库表：680；UCenter 表：607
- 最终纠偏部署：`20260726T233007+0800`
- 覆盖归档：`r04-site-overlay-v11.tar.gz`
- 覆盖归档 SHA-256：`bedb3ae60d63de7a7410a2952c459d47d4df96b6532df8ee6da86e9f7eb8cbc2`
- 归档大小：28,890 字节；覆盖文件：5
- 备份：`/www/staging/tg-h5-ui-r04/private/change-backups/20260726T233007+0800`

## 最终核验

- 首跳 302，最终 GET 200，重定向 1 次，POST 405。
- 生产稳定代码未变化：`91a57ef67b34b9f650becc9413b03288f48b46f1e9452bc779cf03944ec38984`。
- 舞台制品清单：`1a431b1a74b0fe30ffe233a31e88420ae82290cc8a07f5243316e6088f32e6f3`。
- 认证桥于 `20260727T001345+0800` 移除，状态 `BRIDGE=OFF`；移除后完整 verifier PASS。
- 生产目录未部署 R04，生产业务未改动。
