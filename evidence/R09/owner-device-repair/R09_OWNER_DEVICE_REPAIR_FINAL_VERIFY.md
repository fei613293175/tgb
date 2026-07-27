# R09 负责人真机缺陷返修最终浏览器核验

核验日期：2026-07-27

核验环境：R08 隔离预发布，`TuiGuangBaoAndroid/1.0.0 Android`，CSS 视口 `390x844`。

候选：79 files，SHA-256 `9deca6adaf7579bbbe8944d0725b3be61dbb6f3a1892fea07d339225f167ae15`，预发布部署 `20260727T224528+0800`。

## 负责人问题关闭结果

| 页面 | 最终结果 | 关键证据 |
| --- | --- | --- |
| AUTH-HOME | PASS | 头条卡与普通卡同 `x=16`、同宽 `358.4px`、同 `8px` 圆角；浅紫边框、左强调线和头条标签形成差异；横向溢出 0。 |
| CONTENT-DETAIL | PASS | 标题 20px，作者卡与内容卡同宽，私信按钮 `84x48`；页面宽度 390；详情 GET 引起的 5 次浏览及日志已精确回滚。 |
| CONTENT-PUBLISH | PASS | 页头 60px，返回和发布控件均完整可见；表单首屏无状态栏重复占位，未提交表单。 |
| CONTENT-MY-PUBLICATIONS | PASS | 8 条视觉夹具真实渲染；首卡 `x=16 / w=353.6`，页头 60px，旧占位 0；日期、排名和管理按钮不重叠。 |
| CONTENT-REVIEW | PASS | 页头 60px，标签从 y=60 开始，旧占位 0；活动指示条为不透明品牌蓝。 |
| ACCOUNT-MY | PASS | 标题和右侧操作不重叠，底栏约 64.9px 且“我的”正确高亮；横向溢出 0。 |
| ACCOUNT-CERTIFICATION | PASS | 合法成功态已改为浅色结果卡；页头 56px，底部返回按钮完整可见；认证夹具已删除。 |
| FINANCE-WALLET | PASS | 收益/提现两态均无溢出；页头 52px；活动边框和短条均为品牌蓝。 |
| FINANCE-SIGN-WITHDRAW | PASS | 余额、金额、绑定和提示卡首屏无错位；页面宽度 390；未执行提现。 |
| CONTENT-REFRESH-PACK | PASS | 可见表单保持原生 POST；字段为 `formhash`、`couponid`、`form[viptype]`、`dosubmit=1`；未点击购买。 |
| PROMOTION-HEADLINE | PASS | 项目容器 `clientHeight=358 / scrollHeight=848`，实际滚动至 260/284；滚动后项目仍可选；购买按钮底部 768.6，小于底栏顶部 779.2。 |
| PROMOTION-SUPER-HEADLINE | PASS | 项目容器 `clientHeight=358 / scrollHeight=836`，实际滚动至 260/284；滚动后项目仍可选；购买按钮未被底栏覆盖。 |
| REWARD-SIGNIN | PASS | 公告关闭后首屏卡片和底栏完整；底栏 65px，“签到”正确高亮；未点击签到。 |
| REWARD-MATCH-DIVIDEND | PASS | 会员锁定态卡片完整，底栏约 64.8px，“分红”正确高亮；未执行领取或购买。 |

## 截图目录

最终截图均位于 `evidence/R09/owner-device-repair/after/`，文件名以页面 ID 开头；其中头条、超级头条、我的项目、实名认证、签到和分红使用 `final` 后缀。

## 清理门禁

- `r09_mypub_visual_fixture.sh status`: `COUNT=0`
- `r09_headline_visual_fixture.sh status`: `COUNT=0`
- `r08_certification_fixture.sh status`: `OFF`
- `r08_auth_bridge.sh status`: `BRIDGE=OFF`
- `r08_browser_origin_bridge.sh status 28088`: `STATUS=OFF`
- `r09_visual_test_mode.sh status`: `STATUS=OFF POST_HTTP=405`
- 本机 `28088`: 无监听

真实购买、支付、提现、签到、发布、删除、认证提交均未执行。
