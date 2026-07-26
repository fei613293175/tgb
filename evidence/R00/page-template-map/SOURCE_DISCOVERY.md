# R00 源码发现证据

采集日期：2026-07-26  
目标：冻结页面入口、控制器、模板、共享资源与写操作风险，不记录任何凭据。

## 1. `xigua_hb` 主分派

- 入口：`source/plugin/xigua_hb/xigua_hb.inc.php`。
- 公共路由白名单、登录条件和动态控制器列表位于 `source/plugin/xigua_hb/common.php`。
- 默认首页通过 `_indexhb_page` 与 `include/city.inc.php`，落到 `template/touch/index.php`。
- `view`/`hong_list` 共用 `include/view.inc.php`，详情模板由运行时 `$vtpl` 选择。
- `my` 加载 `include/mynew.inc.php` 并显式映射为 `template/touch/my_new.php`。
- `member` 加载 `include/member.inc.php` 并显式映射为 `member_new.php`。
- `pub`、`mypub`、`manage`、`pay`、`qianbao`、`tixian` 使用独立 include。
- `myset`、`myaddr`、`mycover`、`mytx`、`fav`、`refresh` 等走 `include/c_{ac}.php` 动态控制器与同名 touch 模板。
- 列表类路由包含 `comment_li`、`qianbao_li`、`myorder_li`、`member_li` 等 AJAX 分片，不能只验证完整页。

主要共享资源包括 `static/app.js`、`custom.css`、`static/css/member_new.css`、`mynew.css`、`resume.css`、`taocan.css`、`chat.css`、`countUp.js`、jQuery/Zepto、jQuery WeUI、Swiper、Cropper、City Picker、Geolocation 与 html2canvas。

## 2. 例外页面

- `xigua_member/profile.inc.php` 同时承担控制器和完整 HTML 输出，使用 `images/xigua.css`、jQuery 与 `jquery.form`。
- `view/view.inc.php` 的签到分支进入 `module/site/sign.php`，主体页面由 PHP/内联 HTML/JS 输出。
- Discuz 登录页位于 `template/default/touch/member/login.htm`，当前还依赖 CDN 样式/图标及本地基础插件资源。
- 手机注册/短信登录由 `tb_cus_mobilereg` hook 与独立模板实现。
- 法律、帮助、关于页是 `/m/*.html` 静态文件，不经过插件 touch 模板。

这些例外必须拥有单独的 R03/R06/R08 检查项，不能以“公共主题已加载”代替验证。

## 3. 独立插件映射

| 页面族 | 入口/控制器 | 模板要点 |
|---|---|---|
| 聊天 | `xigua_lt.inc.php` + `include/chat.inc.php` | `touch/chats.php`、`touch/chat.php` 与 AJAX 分片 |
| 邀请/团队 | `xigua_hh.inc.php` | `invite.php`、`myfans.php`、`fans_li.php` |
| 第二钱包提现 | `tb_cus_xiguahh/tx.inc.php` → `module/tx/main.php` | `touch/tx.htm` |
| 举报 | `xigua_hj.inc.php` | `touch/index.php` |
| 名片 | `tb_cus_card.inc.php` → `module/site/tb_*.php` | `add.htm`、`shownew.htm`、`myorderedit.htm` 等 |
| 实名认证 | `xiaomy_certification.inc.php` → `module/xiaomy_main.php` | `webstressapipay.htm` 与结果模板 |
| 联系资料 | `tb_credit.inc.php` → `module/userext.php` | `touch/userext.htm` |
| 注销 | `deluser.inc.php` → `module/site/main.php` | `touch/main.htm` |
| 头条 | `tb_toutiao.inc.php` → `module/main.php`/`super_main.php` | `main.htm`、`super_main.htm` |
| 支付 | `tb_pay.inc.php` → `module/main.php` | `touch/main.htm` 与订单/结果状态 |
| 匹配分红 | `tb_cus_pipei.inc.php` → `module/main.php` | `touch/main.htm` |
| 残余插件 | `xigua_hs`、`xigua_sp`、`tb_jjd`、`tb_cus_adv`、`tb_cus_taojing` | 动态 module 与多套 touch 模板，R09 以可达性关账 |

## 4. 冻结结果

- `03_PAGE_LEDGER.csv` 的 64 个 `page_id` 已在 `12_PAGE_SOURCE_MAP.csv` 一一覆盖。
- 运行时已观察、仅源码发现、休眠启用、外部和原生计划页面使用不同状态，不把“源码存在”误报为“用户可达”。
- 写副作用门禁集中记录于 `13_SIDE_EFFECT_TEST_PLAN.md`。
- 本证据只描述结构，不包含数据库口令、登录密码、Cookie、Token、手机号或第三方密钥。
