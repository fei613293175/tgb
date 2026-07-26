# R01 支付 H5 网关源码审计

- 日期：2026-07-26
- 方式：服务器生产源码只读搜索；未创建订单、未调用支付接口、未读取或记录密钥
- 结论：旧 Android 路由存在误拦截真实异步支付跳转的风险，已按最小白名单修复

## 发现

当前收银台模板会在用户点击后先执行 AJAX，再使用
`window.location.href=data.msg` 跳转。由于最终跳转发生在异步回调内，Android
WebView 的最终请求不一定保留 `hasGesture=true`。

生产插件源码中找到的支付 H5 入口包括：

- `tb_pay/module/pay.php`：
  `https://sandcash.mixienet.com.cn/pay/h5/qrcode`
- `tb_pay/lib/shande/YunPayment.class.php`：
  `https://sandcash.mixienet.com.cn/pay/h5/fastpayment`
- `tb_pay/lib/fuylink/epay.config.php`：
  `https://fuylink.cy253.top/`
- 支付宝 WAP/兼容代码：
  `openapi.alipay.com`、`mapi.alipay.com`、`wappaygw.alipay.com`
- 虎皮椒配置说明与代码路径：
  `api.xunhupay.com`

只记录域名和无敏感参数的固定源码路径；没有把 App ID、商户号、密钥、订单号、
金额或签名写入证据。

## 修复策略

- 仅从本站或已批准支付页进入上述精确 HTTPS host；
- 支付 H5 留在 WebView 内，以便随后拦截支付宝 deep link；
- 支付宝仍严格限制为 `alipays|alipay`、host=`platformapi`、
  package=`com.eg.android.AlipayGphone`；
- 普通外部 HTTPS 仍要求真实手势并交给系统；
- HTTP、任意 scheme、相似域名后缀、尾随点、userinfo、显式 component 和
  selector 继续禁止。

## 自动验证

新增 `HostPolicyTest` 三组测试，覆盖：

- 只接受精确本站 host；
- 只接受登记支付 host，拒绝后缀欺骗；
- 只允许本站或登记收银台作为支付宝拉起来源。

本机 `testDebugUnitTest lintDebug assembleDebug assembleDebugAndroidTest`：PASS。
真实支付网关可能继续重定向到尚未在源码中出现的域名；依据 D-020，该信息由
负责人异步真机反馈补充。任何新增 host 必须先取得脱敏样本、补测试和书面决定，
不能临时放宽为通配符。
