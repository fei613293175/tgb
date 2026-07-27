# R09 收银台真实点击与视觉验收

- 日期：`2026-07-27`
- 页面：`FINANCE-PAYMENT`
- 结论：`PASS`
- 测试口径：Android H5 UA，隔离合成会员购买数据

## 1. 范围证明

收银台不是通过直接 URL 或源码发现纳入范围。本次在隔离环境中按真实用户路径完成以下点击链：

```text
MEMBERSHIP-VIP
  -> 年度会员“立即开通”
  -> 确认购买
  -> FINANCE-PAYMENT
```

证据不记录订单号、UID、Cookie、`formhash`、余额、支付账号或其他身份数据。该点击只证明收银台页面进入视觉范围，不证明订单列表页可达，也不证明 Android 真机可以完成第三方支付。

## 2. 基线与最小覆盖

- 原始 `source/plugin/tb_pay/template/touch/main.htm` SHA-256：`ccae4d5f80d1c8f7ff71803c99ddd01644a76601c4c588acededdd78cd65fc82`
- R00-R08 隔离站与生产基线哈希一致。
- 覆盖文件：2 个。
  - `source/plugin/tb_pay/template/touch/main.htm`
  - `source/plugin/tb_pay/static/tgb-r09/cashier-light-grid-r09.css`
- 仓库安全制品：`deliverables/r09-cashier-overlay-v3.tar.gz`
- 制品 SHA-256：`1d8775866e2abec21ce18824a485d3deaf125106edbbc56ad5b4f19f26264dc7`
- 制品只包含本地 CSS 与哈希前置精确转换器，不包含带支付账号/地址的完整生产模板；服务器从已验证基线生成的最终模板 SHA-256 为 `83661a7871894331bdf6f6543f792c236e871f3267ac787291eaab80e70abe86`。
- 静态协议门禁：`PASS`

支付入口、支付方式循环和筛选、radio 协议、隐藏容器、`FormData` 字段、POST 地址、JSON 分流、异步 H5 跳转、网关 HTML 注入以及业务 PHP 均保持不变。

## 3. 浏览器审核

改前主视口记录：

- `before/CASHIER-390x844.png`
- `before/CASHIER-390x844.json`

第一轮浏览器审核发现页面底部仍显示旧的 `.` 占位符；覆盖层删除该纯视觉占位后重新部署并重新执行全部验收，首轮截图不作为最终 PASS 证据。

最终精确像素截图与几何记录：

| 视口 | 截图 | 几何 |
|---|---|---|
| `360x800` | `after/CASHIER-360x800.png` | `after/CASHIER-360x800.json` |
| `390x844` | `after/CASHIER-390x844.png` | `after/CASHIER-390x844.json` |
| `430x932` | `after/CASHIER-430x932.png` | `after/CASHIER-430x932.json` |

最终结果：

- 横向溢出：`0`
- 视口外关键控件：`0`
- 旧品牌可见：`0`
- 旧 `.` 占位可见：`0`
- 可见破图：`0`
- 控制台错误：`0`
- 顶部栏：`52px`，三视口均从 `top=0` 开始
- 底部支付区：`72px`，三视口均贴合视口底部
- 单选触控区：每个均为 `44x44px`

控制台与资源记录：`console/CASHIER-CONSOLE-390x844.json`。

审核过程中没有选择任何支付方式，也没有点击“立即支付”，没有请求支付接口、跳转第三方网关或拉起第三方 App。

## 4. 部署与回滚

- 最终舞台部署：`20260727T152357+0800`
- 舞台备份：`/www/staging/tg-h5-ui-r08/private/change-backups/20260727T152357+0800-r09-cashier`
- 生产部署：`20260727T152930+0800`
- 生产备份：`/www/staging/tg-h5-ui-r08/private/production-cashier-backups/20260727T152930+0800`
- 支付核心 PHP 哈希：部署前后未变化

生产发布仅包含上述两个视觉文件，不包含 Android 源码、支付控制器、订单页或其他插件页面。

## 5. 隔离清理

- 夹具状态：`OFF`
- 清理历史：`/www/staging/tg-h5-ui-r08/private/r09-cashier-fixture-history/20260727T153121+0800`
- 为点击链创建的合成会员订单：1 条，已清理
- `tb_pay` 支付订单：0 条
- 认证桥：`OFF`
- 浏览器来源桥：`OFF`
- 隔离站 POST 门禁：`405`

## 6. 边界

本项 `PASS` 只代表收银台真实可达、浅色换肤完成、业务协议静态等价且三视口视觉验收通过。以下事项不由本证据证明：

- 订单列表页进入视觉范围；
- 真实订单支付成功、失败、取消或回调；
- Android 真机拉起支付宝及返回；
- 任何真实余额、积分或账务变化。
