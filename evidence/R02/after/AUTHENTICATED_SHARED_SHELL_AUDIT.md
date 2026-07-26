# R02 登录后共享壳层审计

日期：2026-07-26
结论：`PASS`

## 环境与隐私

- 页面：`AUTH-HOME`
- 路由：`/plugin.php?id=xigua_hb&mobile=2`
- 环境：仅 SSH 隧道可访问的 R02 staging
- 会话：staging-only 合成视觉账号；未使用或修改生产账号
- 临时登录桥和强制 Android UA 已回收，最终 `POST=405`
- 截图不包含手机号、密码、Cookie、Token、formhash、钱包或身份资料

## 浏览器结果

显式浏览器视口为 390x844：

- 页面标题：`推广宝`
- `documentElement clientWidth/scrollWidth`：390/390
- `body clientWidth/scrollWidth`：390/390
- 页面背景：`rgb(244, 247, 251)`
- 搜索输入：44px 高、16px 字号
- 搜索按钮：64x44px、16px 字号
- 底部导航：64px 高；四项顺序保持首页、签到、分红、我的
- 发布按钮：58x58px
- 滚动条旧暖金色已替换为 `rgba(39, 100, 255, 0.52)`，轨道使用浅背景
- 本地 Remixicon 样式已声明；新增公共 UI CDN 请求为 0

截图：`AUTH-HOME-R02-390x844.jpg`

- 浏览器截图 API 输出的是去除工具边缘后的 386x834 内容面，未缩放或裁切；
  上述 390x844 视口及溢出数值来自同一页面运行时。
- SHA-256：
  `c28487e62c6ec2d95fd44f358018ac3f98dfe0f515aa139e4e6f18e76df3557d`

## 控制台归因

页面仍能观察到原站两个既有错误：底部导航可选脚本有一个未配对闭合符，首页
`updateIndicator` 在目标元素不存在时空引用。它们不是 R02 新增回归：

- 生产与 staging 未改的 `index.php` SHA-256 均为
  `e0efe964878cff326edecbbad1d4ea792c173a132414eef3a78fe30172d55be9`；
- 生产与 staging 的 `tab1.php` 相关脚本片段 SHA-256 均为
  `0c28f550e879c408f1cc742007a9a103ca1c45f34e49ca7910e28ea47ced6022`。

R02 只记录并归因，不提前修改属于 R04 的首页业务模板。后续所属页面版本必须
在不改变导航路由和事件语义的前提下关闭这些错误。

## 边界

本证据关闭 R02 登录后浏览器共享壳层门禁；Android WebView 的原生系统栏与
H5 安全区截图仍未关闭，R02 保持 `IN_PROGRESS`。
