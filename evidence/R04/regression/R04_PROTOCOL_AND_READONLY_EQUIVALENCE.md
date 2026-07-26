# R04 协议与只读等价证据

- 日期：2026-07-26
- 结论：PASS

## 源码协议

- 首页搜索仍是 GET `plugin.php`，字段 `id=xigua_hb`、`ac=cat`、`keyword`、`st`、`idu` 不变。
- 首页默认列表仍使用 `ac=list_item&inajax=1&from=index&pagesize=...&page=`。
- 搜索/分类仍由 `cat.inc.php`、`c_cat.php` 和 `template/touch/cat.php` 承载，分页仍是 `ac=list_item&inajax=1&pagesize=20&page=`。
- 首页源码中的 `data-id=5/14/15/13` 是原站零高隐藏条件协议，不是用户可见分类。它们保持不可见、不可点击、不占位；只读 GET 路由检查不等于首页点击验收。
- 文章列表仍由 `c_article_li.php` 返回 `tid_article.php` XML 片段；当前配置未渲染文章标签，探针返回合法 65 字节 XML、0 个 `marticle`。
- 底部导航顺序和目标保持：首页、签到、分红、我的；发布入口目标未变。
- `scripts/test-r04-static.ps1` 对 5 文件白名单、表单/链接/事件属性签名和隐藏分类 CSS 守卫执行检查，结果 PASS。

## 运行时协议

```text
[R04-RUNTIME] PASS
[R04-RUNTIME] HOME=200 SEARCH_POPULATED=200 SEARCH_EMPTY=200 CATEGORY=200 ARTICLE=200 POST=405
[R04-RUNTIME] HIDDEN_CONDITIONAL_ROUTES=5:200 14:200 15:200 13:200 ARTICLE_BYTES=65 ARTICLE_ENTRIES=0
[R04-RUNTIME] BUSINESS_COUNTERS_UNCHANGED=PASS
```

## 副作用

- 全部页面和 AJAX 探针只使用 GET；发布、购买、点赞、评论、收藏、奖励和支付均未点击。
- 测试前后 `xigua_hb_pub` 行数、浏览累计、点赞累计及投票日志行数逐值一致。
- 最终认证桥为 `BRIDGE=OFF`，POST 405，完整 verifier PASS。
- 生产稳定代码 SHA-256 保持 `91a57ef67b34b9f650becc9413b03288f48b46f1e9452bc779cf03944ec38984`。
