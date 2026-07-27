# R09 负责人真机缺陷返修审计

状态：`CLOSED_BY_OWNER_DEVICE_REPAIR_FINAL_VERIFY_AND_PRODUCTION_DEPLOY`

本批证据来自 2026-07-27 负责人真机截图。原图包含 UID、邀请码、余额、收款账号等隐私，不进入 Git；本文件只记录脱敏缺陷和源码归属。下表“当前状态”是缺陷受理时的历史快照，最终关闭状态以 `R09_OWNER_DEVICE_REPAIR_FINAL_VERIFY.md` 为准。

| 截图时间 | 页面 | 缺陷 | 源码归属 | 当前状态 |
|---|---|---|---|---|
| 18:48:13 | 刷新卡购买 | 购买后只显示支付成功，未可靠进入收银台 | `xigua_hb/include/c_refresh.php`、`sxtc.php`、通用消息跳转、Android 外链路由 | REPAIRING |
| 18:48:34 | 首页 | 搜索控件侵入状态栏、首屏空白过大、头条项目仍是旧暖橙卡片 | `xigua_hb/index.php`、R04 discovery CSS | HEADLINE_COMPONENT_BROWSER_PASS; FULL_PAGE_REPAIRING |
| 18:49:03 | 项目详情 | 标题过大、防骗提醒遮挡正文、作者区和私信按钮比例失衡 | `xigua_hb/jl_jy_v.php`、R05 detail CSS | REPAIRING |
| 18:49:20 | 超级头条 | 项目选择区无法触摸滚动、底栏遮住购买区、底栏旧样式 | `tb_toutiao/super_main.htm`、R07 promotion CSS | REPAIRING |
| 18:49:50 | 个人中心 | 标题与通知图标重叠、底部导航旧样式 | `xigua_hb/my_new.php`、R06 account CSS | REPAIRING |
| 18:50:36-18:50:43 | 我的资产 | 金额选项过窄、记录列表仍为旧暖橙视觉 | `xigua_hb/qianbao.php`、R07 finance CSS | REPAIRING |
| 18:51:03 | 签到奖励提现 | 顶部比例和首屏空白异常 | `tb_cus_xiguahh/tx.htm`、R07 sign-wallet CSS | REPAIRING |
| 18:51:58 | 实名认证成功 | 成功态模板未换肤，仍为旧米黄橙红视觉 | `xiaomy_certification/rzres_1.htm`、certification CSS | REPAIRING |
| 18:52:22 | 头条购买 | 项目选择区无法触摸滚动、底栏遮住购买区、底栏旧样式 | `tb_toutiao/main.htm`、R07 promotion CSS | REPAIRING |
| 18:52:31 | 我发布的项目 | 日期压住分割线和操作区 | `xigua_hb/mypub_item_new.php` | REPAIRING |
| 18:53:03 | 发布项目 | 发布按钮侵入状态栏、上传区和表单空白过大 | `xigua_hb/pub.php` | REPAIRING |
| 18:53:53 | 信息管理 | 页头与标签区之间空白过大 | `xigua_hb/manage.php` | REPAIRING |
| 18:54:25 | 支付跳转 | App 拦截外部收银台链接 | Android `ExternalIntentRouter` | REPAIRING |

硬结论：旧的 `39/39 REDESIGNED_VERIFIED` 结论已被真机证据否决。受影响页面在本次受影响页浏览器几何检查、协议门禁和回滚验证完成前不得恢复为 PASS。

追加首页头条反馈已完成组件级返修：三档 Android H5 视口下与普通卡同宽、同左右边界、同 8px 圆角，无旧暖橙可见样式。证据见 `R09_HOME_HEADLINE_REPAIR_VERIFY.md`；这不等于首页全页关版。

## 2026-07-27 21:30 续点

- 最新 79 文件候选 SHA-256：`67024c74a668b2522a31cc3c352ff570ac68f99109076b455acb440ed9f21ef1`。
- 预发布部署：`20260727T212746+0800`；备份：`/www/staging/tg-h5-ui-r08/private/change-backups/20260727T212746+0800-r09-production-candidate`。
- 已修复九个共享底栏模板的 `Unexpected token '}'`，并为个人中心广告脚本恢复服务器本地 jQuery；静态门禁 PASS。
- 已删除账户、钱包、详情、签到钱包、头条购买、签到和认证页中重复写死的 28/36px 顶部 inset，统一由 `env(safe-area-inset-top)` 负责；原生继续保持 edge-to-edge 零 padding。
- Codex Browser 对已打开的 `127.0.0.1:28088` 预发布页执行重载时被客户端 URL 策略拒绝。不得使用其他浏览器控制面绕过，也不得把静态门禁代替视觉 PASS。
- 清理状态：认证桥 OFF、浏览器 origin bridge OFF、头条夹具 COUNT=0、本机 28088 无监听、预发布 POST=405。

## 2026-07-27 最终关闭

- 最终79文件候选 SHA-256：`9deca6adaf7579bbbe8944d0725b3be61dbb6f3a1892fea07d339225f167ae15`。
- 最终预发布：`20260727T224528+0800`；14个受影响真实点击页面的 `390x844` 浏览器核验全部PASS。
- 生产部署：`20260727T233853+0800`；79文件精确哈希、5条关键路由、本地CSS与公共UI CDN门禁PASS。
- Android外链与安全区源码服务器构建：`20260727T235000+0800`；APK SHA-256 `5c69f3c4e64e214e901fae5574ec8b54c464e1cd19a907896742feb0327aa027`，已复制桌面但未由Codex安装或运行。
- 证据：`R09_OWNER_DEVICE_REPAIR_FINAL_VERIFY.md`、`R09_OWNER_REPAIR_PRODUCTION_DEPLOY.md`、`../android-owner-repair/R09_ANDROID_EXTERNAL_NAV_BUILD.md`。
