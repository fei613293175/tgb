# R02 隔离预发布环境结果

日期：2026-07-26
结论：`PASS`

## 隔离事实

- 站点目录：`/www/staging/tg-h5-ui-r02/site`
- 私有证据目录：`/www/staging/tg-h5-ui-r02/private`
- 监听：`127.0.0.1:18082`
- 访问方式：SSH 隧道
- 数据库：R02 独立主库和 UCenter 库
- 主库表数：680
- UCenter 表数：607
- 默认允许方法：`GET`、`HEAD`
- `POST`：405
- 快照 ID：`20260726T155049+0800`

## 最终复核

- 首跳 HTTP：302
- 跟随后 HTTP：200
- 跳转次数：1
- 页面为真实 HTML：PASS
- R02 响应标记：PASS
- 生产稳定代码未变化：PASS
- 生产稳定代码清单 SHA-256：
  `91a57ef67b34b9f650becc9413b03288f48b46f1e9452bc779cf03944ec38984`
- 原始恢复制品清单 SHA-256：
  `5333ea8150f836430716a124ecda4ce3a4ada958e9e8edd97bcb92710a8f2c37`

## 共享壳层部署

最终部署：

- 部署 ID：`20260726T184740+0800`
- 覆盖包 SHA-256：
  `bb77e85e27607c130ba7303874f4f1ab5e72f4564775d187a1fd0d7ca739d042`
- 移动路径最终 HTTP：200
- 桌面引导最终 HTTP：200
- 部署前备份：
  `/www/staging/tg-h5-ui-r02/private/change-backups/20260726T184740+0800`
- 本地覆盖层与隔离站 19 个白名单文件逐文件 SHA-256：全部一致

共享壳层文件：

- `index.php`
- `source/plugin/xigua_hb/template/touch/common_header.php`
- `source/plugin/xigua_hb/template/touch/common_nav.php`
- `source/plugin/xigua_hb/static/tgb-r02/light-grid-r02.css`
- `source/plugin/xigua_hb/static/tgb-r02/brand-mark-r02.svg`
- `source/plugin/xigua_hb/static/tgb-r02/chat-r02.svg`
- 本地 Remixicon 3.5.0 CSS/WOFF2
- 11 个实际底部导航模板，包括 `my_new.php`、`vip.php` 和 `tab*.php`

本地 Remixicon WOFF2 SHA-256：
`b0d0b7e5101a1b8a54268b9188da520d19d74df9b35714a8ddb5987fad990591`。
`my_new.php` 和 `vip.php` 复用服务器现有本地 Font Awesome 4.7.0，未新增
公共 CDN。

## 环境修复

- 显式向 PHP-FPM 传递 User-Agent、Cookie、Accept、Referer 和
  X-Requested-With；否则最小 Nginx vhost 不能真实复现 H5/登录行为。
- 首次模板更新需要 `data/template` 运行目录；该目录只属于 R02
  隔离站，不进入生产源码覆盖。
- `config/config_ucenter.php` 必须连接 R02 UCenter 库，并把
  `UC_DBTABLEPRE` 指向 R02 UCenter 库，不能残留生产库限定符。
- 所有修复均先备份、后语法检查、再重载；未打印数据库密码。

## 临时视觉测试窗口

为诊断登录链路，曾短暂启用仅回环可见的 R02 视觉测试模式：

- 只对隔离数据库临时允许 POST；
- 固定 Android User-Agent；
- 测试完成后立即恢复原 Nginx 配置；
- 恢复后再次确认 POST=405；
- 审计历史：
  `/www/staging/tg-h5-ui-r02/private/visual-test-history/20260726T162835+0800`

生产站未进入该窗口，生产代码与数据均未修改。

## 登录后视觉夹具与回收

- 合成账号仅存在于 R02 主库/UCenter 克隆库，关联行与 UID 一致性通过；不含
  真实手机号、真实身份或负责人账号变更。
- 随机密码未输出、未落盘；临时回环登录桥只用于浏览器视觉取证。
- 登录桥回收历史：
  `/www/staging/tg-h5-ui-r02/private/auth-bridge-history/20260726T183947+0800`
- 强制 UA 回收历史：
  `/www/staging/tg-h5-ui-r02/private/mobile-ua-test-history/20260726T183948+0800`
- 回收后桥目录不存在、强制 UA 状态为 OFF、`POST=405`。

强制 UA 激活期间，部署 `20260726T183919+0800` 在桌面入口门禁得到 302，按
预期判定失败。恢复动态 UA 后，同一代码族重新部署并通过；该失败没有被删除或
改写为成功。

## 响应式预览清理

三档受控响应式证据生成后，临时入口 `site/__r02_test__` 已从站点目录移走，
并只读归档到：

`/www/staging/tg-h5-ui-r02/private/preview-history/20260726T-responsive-matrix`

清理后重新运行完整 R02 验证：

- `GET`/跟随跳转：302 → 200
- `POST`：405
- 主库/UCenter 表数：680/607
- 生产稳定代码 SHA-256：
  `91a57ef67b34b9f650becc9413b03288f48b46f1e9452bc779cf03944ec38984`
- 原始制品清单 SHA-256：
  `5333ea8150f836430716a124ecda4ce3a4ada958e9e8edd97bcb92710a8f2c37`

结论仍为 `PASS`，临时证据入口没有残留在隔离站点。

最终部署后再次完整复核：主库/UCenter 表数仍为 680/607，生产稳定代码
SHA-256 仍为
`91a57ef67b34b9f650becc9413b03288f48b46f1e9452bc779cf03944ec38984`。
