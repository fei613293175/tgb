# R05 隔离舞台建立结果

- 日期：2026-07-27
- 来源：已关闭并纠偏验证的 R04 舞台
- 快照：`20260727T003603+0800`
- 站点：`/www/staging/tg-h5-ui-r05/site`
- 私有证据：`/www/staging/tg-h5-ui-r05/private`
- 监听：`127.0.0.1:18085`，仅 SSH 隧道
- 主数据库：`tgb_stage_r05_main`，680 表
- UCenter 数据库：`tgb_stage_r05_uc`，607 表
- 写守卫：GET / HEAD；POST 405
- 创建脚本：PASS
- 独立 verifier：PASS
- 制品清单 SHA-256：`573ac4a1fa467ef595704b062805c299c6ee28a3c0da32456721dc72266525d7`
- 生产稳定代码 SHA-256：`91a57ef67b34b9f650becc9413b03288f48b46f1e9452bc779cf03944ec38984`

R05 使用独立站点、数据库、用户、Nginx 配置、监听端口和备份目录。生产未修改，
R04 舞台仍保持关闭状态；R05 任何受控写测试必须另开最小窗口并完整回滚。
