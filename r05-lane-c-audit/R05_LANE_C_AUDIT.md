# R05 Lane C Audit

- Recorded: `2026-07-27T01:34:02+08:00`
- Scope: `SAFETY-REPORT`, `CARD-ADD`, `CARD-UPLOAD`
- Baseline: `r05-baseline-selected`
- Overlay: `r05-site-overlay-lane-c`
- Asset version: `20260727-r05-c1`
- Deployment: not performed

## Result

Lane C is ready for main-thread integration and staged runtime testing. Static protocol, flow, URL, hidden-control, and public UI CDN gates pass. This is not a runtime PASS and does not close R05.

## Template Audit

| Page | Template | Visual coverage | Protocol result |
|---|---|---|---|
| SAFETY-REPORT | `source/plugin/xigua_hj/template/touch/index.php` | Light Grid report form, tag controls, inputs, submit action, focus and reduced-motion states | Form action/method, `formhash`, referer, tag ID, fields and `set_typeid` unchanged |
| CARD-ADD | `source/plugin/tb_cus_card/template/touch/add.htm` | Light Grid header, type tabs, upload panels, text fields, pricing cards, warning and submit action | Upload/delete endpoints, IDs, input attributes, price selection, POST fields and redirect unchanged |
| CARD-UPLOAD | `source/plugin/tb_cus_card/template/touch/shownext.htm` | Light Grid task timeline, account copy, screenshot uploader, sample link, warning and submit action | Copy/upload/delete behavior, `files[]`, `logid`, `tauserinfo`, POST fields and redirect unchanged |

## Corrections

- Replaced all critical `body:has(...)` selectors with explicit body page classes for older Android WebView compatibility.
- Removed the hidden marker nodes previously used to trigger `:has()`, so no source-only marker can become visible, clickable, or occupy layout.
- Added fixed cache keys to all three owned CSS references.
- Removed two remote decorative back-arrow image requests. The original return links and `javascript:window.history.go(-1);` behavior remain; the arrow is rendered by page-local CSS.
- Added `overflow-x: hidden` and Flex fallbacks before newer CSS values, plus visible focus and disabled states.
- Preserved the legacy GBK bytes of the report template; only the versioned CSS link and page class differ from its baseline bytes.

## Static Evidence

```text
[R05-LANES] PASS
[R05-LANES] templates=22 css=3 protocol=UNCHANGED flow=UNCHANGED urls=UNCHANGED
[R05-LANES] hidden_controls=GUARDED public_ui_cdn=NO_INCREASE
```

- `body:has(...)`: 0
- Lane C hidden marker nodes: 0
- New public UI CDN dependencies: 0
- Owned unversioned asset references: 0
- CSS brace balance: card `63/63`, report `26/26`
- Local PHP executable: unavailable; main thread must run PHP lint in the R05 staging environment.

## Owned File Hashes

```text
source/plugin/tb_cus_card/static/tgb-r05/card-light-grid-r05.css|38C072DED80EB9C272485ED307F9FC2B88F6887ABC104F13CBDED81777D33352
source/plugin/tb_cus_card/template/touch/add.htm|3855DF30ADD1D8AC71555FD624DD6A13C440AE815AB08B3D33AF797D7207B001
source/plugin/tb_cus_card/template/touch/shownext.htm|108E4D5AA602EBB1388C2FEB35517202418E7907D8505D515DF610E8CA367189
source/plugin/xigua_hj/static/tgb-r05/report-light-grid-r05.css|89156FAF7F0C95D386B7FC14F42FEA9A8ABB5F5BD8D3CC91317E430251547B4C
source/plugin/xigua_hj/template/touch/index.php|C1B9E3AE63188F2F7E00160B4509775846F46519C55D1A5D282A98E5744ACB52
```

## Main-Thread Runtime Gates

- Merge Lane C into the single R05 overlay without overwriting other lanes.
- Run PHP lint and owned-asset HTTP 200/cache-key checks on R05 staging.
- Verify 360x800, 390x844, and 430x932 for overflow, safe-area placement, 44px targets, 16px inputs, long text, upload preview, keyboard and dialogs.
- Test report and upload only with reversible fixtures. `CARD-UPLOAD` requires a legitimate isolated `logid`; do not use a payment-finalization branch or real account data.
- Confirm body classes are present before first paint and both remote arrow URLs are absent from network requests.
