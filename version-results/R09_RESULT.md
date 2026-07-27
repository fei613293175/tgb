# R09 Result

- Status: `IN PROGRESS / ASYNC OWNER FEEDBACK OPEN`
- Date: `2026-07-27`
- Scope: final real-reachable H5 visual candidate and production release
- Current reproducible base candidate: v3, 78 files, plus the independent five-file member/chat v5 overlay
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
