# R09 Result

- Status: `IN PROGRESS / ASYNC OWNER FEEDBACK OPEN`
- Date: `2026-07-27`
- Scope: final real-reachable H5 visual candidate and production release
- Current reproducible base candidate: v3, 78 files, plus the independent five-file member/chat v5 overlay and sensitive-safe cashier v3 transform overlay
- Current SHA-256: `a65d0e510cea5399c0b49ed53dbdea6bbacb9714bc34d54a952ef211a8f8e389` (identical across two consecutive builds)
- Historical deployed candidate: v2, 81 files, SHA-256 `108ab6d6c10be84a892aec223e24bcd134f86be30bc48c33a60f4180fd99dd3e`
- Production deploy ID: `20260727T090142+0800`
- Backup: `/www/staging/tg-h5-ui-r08/private/production-release-backups/20260727T090142+0800`

## Outcome

- R02-R08 overlays and the eight-file R09 visible-brand overlay were assembled and deployed as historical v2.
- The later Android-H5-UA click audit found that member agreement, help and about had no visible parent entry; v2 had therefore carried three direct-URL visual files beyond the owner's scope.
- Scope correction `20260727T113640+0800` restored those three files from the exact pre-R09 production backup. Candidate v3 excludes them and contains 78 files.
- No dormant plugin, business script, route, form protocol, payment protocol or Android source was added or changed.
- The final `390x844` quick pass found no H5 overflow, login redirect, public UI CDN or old visible brand residue.
- Production file hashes matched the candidate; home and App landing returned HTTP 200 during deployment.
- The five-page online smoke passed after deployment.
- Chat detail and member detail passed direct-click Android H5 review at `360x800`, `390x844` and `430x932`, then received a five-file backup-first production deployment.
- The membership VIP purchase confirmation real-click chain proved the cashier in scope. Its two-file Light Grid overlay passed exact-pixel three-viewport review and backup-first production deployment without invoking any payment action.

## Android H5 UA Scope Closeout

- Required UA: Android mobile plus `TuiGuangBaoAndroid/1.0.0`; desktop-UA observations are invalid for scope and UI acceptance.
- Added real click edges for registration, SMS login, service agreement, privacy, search and anti-fraud.
- Search `_blank` navigation was captured from the user-gesture `Page.windowOpen` event; its child rendered 20 results at `390x844` with no overflow or old visible brand.
- Anti-fraud was clicked from the isolated visible detail control. The 9 observed detail-view increments and related row sets were restored; temporary bridges and tunnel were closed.
- Production restore backup: `/www/staging/tg-h5-ui-r08/private/production-scope-corrections/20260727T113640+0800`.
- Restored hashes:
  - `m/hyxy.html`: `1b2182ba72e7d219a927731e063fffcfe5dcb1aec003f90a1aaedc128307dade`
  - `m/help.html`: `bbe0ff48d8731952b5f0a03faa714c9a4720b970ca890836b0739b4ee23f65f6`
  - `m/gywm.html`: `e7d451e6616a6be28b91b2e546402fe81cd1667a5b15726a808355fdc9912f55`

## Rollback

```bash
bash /tmp/r09_rollback_production.sh --apply-rollback /www/staging/tg-h5-ui-r08/private/production-release-backups/20260727T090142+0800
```

## Open Async Feedback

- Owner device photo upload.
- Alipay launch and return from a real checkout.
- Android replacement APK status-bar and bottom-safe-area visual feedback.
- Any owner-reported H5 visual defect.

These items do not trigger another global matrix. Each report is handled as a targeted repair under D-035.

The H5 cashier visual PASS below does not close the Android Alipay launch/return item above.

## Member And Chat Addendum

- Click proof: PASS for chat list -> chat detail and chat avatar -> member detail with a synthetic isolated peer.
- Local overlay: five files under `r09-member-chat-overlay/`, including the member page's dedicated `wdk_header.php`.
- Archive: `deliverables/r09-member-chat-overlay-v5.tar.gz`.
- Archive SHA-256: `3689201ab9e58f8244206ff2968233d9a476ab2571a4cf637f1b42f89fe790da`.
- Static business-protocol and local-resource gate: PASS.
- Final staging deploy ID: `20260727T142041+0800`.
- Visual gate: PASS at `360x800`, `390x844` and `430x932`; no horizontal overflow, clipped controls or dark surfaces.
- Visual corrections included the member header, empty state and simplified cover, chat placeholder removal, long-bubble wrapping, report bounds, fixed composer bounds and page-scrollbar suppression.
- Production deploy ID: `20260727T142812+0800`.
- Production backup: `/www/staging/tg-h5-ui-r08/private/production-member-chat-backups/20260727T142812+0800`.
- Online smoke: chat list and both new CSS assets returned HTTP 200; public UI CDN count remained zero.
- Cleanup: synthetic peer, message, visit log, auth bridge, browser-origin bridge, diagnostic endpoints and local tunnel removed; staging POST returned `405`.
- Evidence: `evidence/R09/member-chat/after/` and `evidence/R09/member-chat/R09_MEMBER_CHAT_PROGRESS.md`.

## Android Inset Repair

- Owner evidence showed an extra native status-bar-height blank area on every App page.
- Root cause: native root WindowInsets padding duplicated H5 header/safe-area spacing.
- Source fix: WebView remains edge-to-edge; native root and WebView use zero inset padding; instrumentation contract updated accordingly.
- H5 browser check: home header `68.8px`, sign header `56px`, both at `top: 0` with no horizontal overflow at `390x844`.
- Server build ID: `20260727T095500+0800`
- Source archive SHA-256: `5e57c1437d267a4cf4d39336a1c424dc045d1de6ac4530f5b64bf6ac9974398f`
- Signed APK bytes: `18154540`
- Signed APK SHA-256: `5625827329af115a0be70d15d4c8b210171e6edb093805a47b62aac1c1947e9f`
- Desktop delivery: `C:\Users\小白\Desktop\推广宝-1.0.0-R09-顶部导航紧凑修复.apk`
- Handoff artifact: `deliverables/android/R09/tuiguangbao-1.0.0-R09-compact-top-inset.apk`
- APK installation/runtime validation remains owner-only and asynchronous under D-036.

## Cashier Addendum

- Scope proof: authenticated isolated membership VIP page -> visible annual membership control -> visible confirmation -> `FINANCE-PAYMENT`; URL parameters and the synthetic order identifier are excluded from repository evidence.
- Scope boundary: this proof grants only the cashier page. `FINANCE-ORDERS`, external gateway pages and Android third-party App behavior do not inherit scope or PASS.
- Local overlay: `r09-cashier-overlay/`, consisting only of `source/plugin/tb_pay/template/touch/main.htm` and `source/plugin/tb_pay/static/tgb-r09/cashier-light-grid-r09.css`.
- Frozen baseline: `r09-cashier-baseline/SHA256SUMS.txt`; the complete sensitive template and controllers are intentionally not stored in the repository.
- Archive: `deliverables/r09-cashier-overlay-v3.tar.gz`, SHA-256 `1d8775866e2abec21ce18824a485d3deaf125106edbbc56ad5b4f19f26264dc7`. It contains only local CSS and an exact hash-guarded transformer; generated template SHA-256 is `83661a7871894331bdf6f6543f792c236e871f3267ac787291eaab80e70abe86`.
- Static gate: `scripts/test-r09-cashier-overlay.ps1` passed the baseline hash list, exact hash-guarded visual transform, untouched business-script boundary, sensitive-value zero-storage scan, local CSS and deployment rollback checks.
- First staging attempt exposed a visible legacy spacer dot; staging deploy `20260727T152103+0800` was rolled back. Corrected v2 staging deploy: `20260727T152357+0800`.
- Visual gate: exact PNG and geometry evidence passed at `360x800`, `390x844` and `430x932`; no horizontal overflow, out-of-bounds element, old visible brand, dark surface, visible broken image or console entry. Header is 52px, pay button 48px and radio targets 44px.
- Forbidden actions: no payment method was selected and `.paybtn` was never clicked. No external gateway, balance deduction or payment POST was exercised.
- Production deploy ID: `20260727T152930+0800`.
- Production backup: `/www/staging/tg-h5-ui-r08/private/production-cashier-backups/20260727T152930+0800`.
- Production smoke: deployed site files match the verified final template/CSS hashes; repository-safe v3 reproduces the same template. `tb_pay.inc.php`, `module/main.php` and `module/pay.php` hashes are unchanged; CSS returned `200 text/css`.
- Repository-safe v3 was assembled after deployment to remove the full sensitive template from source control. Server verification regenerated the exact deployed template hash and `--verify-only` reported `DEPLOYED_VERIFIED`; production bytes were not changed again.
- Rollback:

```bash
bash /tmp/r09_deploy_cashier_production.sh --apply-rollback _ _ 20260727T152930+0800
```

- Cleanup: one synthetic membership order was removed, zero payment orders existed, debug-log state was restored, all bridges and the tunnel were closed, and staging POST returned `405`.
- Evidence: `evidence/R09/cashier/R09_CASHIER_PROGRESS.md`, `evidence/R09/cashier/before/`, `evidence/R09/cashier/after/` and `evidence/R09/cashier/console/`.

## Cashier Manual Drift Audit

- Business behavior unchanged: YES. Payment JavaScript from the local jQuery boundary to EOF is byte-identical; radio names/values, FormData fields, POST route, response branches, HTML gateway injection and asynchronous redirect remain frozen.
- Requirement drift: NO. Only the real-click cashier page was changed; orders and external gateways remain excluded without an independent parent edge.
- Light visual gate: PASS. Warm orange, old visible branding, external return artwork and broken visible icons were replaced by the local Light Grid presentation and CSS fallback.
- Android drift: NONE. No Android source, APK, signing, permission or host-policy file changed, so no APK rebuild was performed.
- Browser review: PASS after an actual rejection/fix cycle. The first visible spacer defect was fixed and exact pixel dimensions were verified for all three PNGs.
- Fixture cleanup: PASS after correcting a legacy-table export assumption; the cleanup script itself verifies zero remaining synthetic rows.
- Documentation sync: PASS. The final manifest and continuity checks below cover the cashier v3 artifact, click graph and progress guard; R09 remains `IN_PROGRESS` only for asynchronous owner feedback.

## Member And Chat Manual Drift Audit

- Business behavior unchanged: YES. Route/query strings, forms, hidden fields, AJAX endpoints, polling, image/video upload types, follow, message and block protocols remain frozen by the normalization and marker gate.
- Requirement drift: NO. Both pages have complete real-click parent edges; no direct-URL, hidden control or dormant plugin was added.
- Reachable-page omission in this batch: NO. The batch is exactly chat detail and its avatar-linked member detail.
- Light visual gate: PASS. No dark page background or old warm-orange dominant surface was introduced.
- Reused strengths: existing Light Grid tokens, local icon font, WeUI interaction behavior and 44px controls were retained.
- Android drift: NONE. This five-file H5 overlay does not modify APK source, signing, permissions, upload bridge or payment allowlist, so no APK rebuild is required.
- Browser review: PASS. Codex inspected both real pages and rejected the first member-cover decoration before the final screenshots; all three viewport geometries have zero horizontal overflow.
- Pitfalls recorded: D-040 captures mandatory screenshot-based design rejection; L-073 captures the private-backup permission regression and safe restore rule.
- Documentation sync: PASS. `CURRENT_STATUS.yaml`, `NEXT_TASK.yaml`, result, evidence index, decision, lesson, progress record and `MANIFEST_SHA256.txt` agree on v5, five files, visual PASS and production deployment.
- Subtask conclusion: `PASS`. R09 remains `IN_PROGRESS` only for asynchronous owner device and future targeted feedback listed above.
