# R09 Result

- Status: `IN PROGRESS / MEMBER AND CHAT VISUAL ACCEPTANCE OPEN`
- Date: `2026-07-27`
- Scope: final real-reachable H5 visual candidate and production release
- Current reproducible candidate: v3, 78 files
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
- Chat detail and member detail are now real-click-proven and locally redesigned, but their Android-H5 after screenshots and geometry verification remain open. The attempted staging deployment was rolled back, so they are not production changes.

These items do not trigger another global matrix. Each report is handled as a targeted repair under D-035.

## Member And Chat Addendum

- Click proof: PASS for chat list -> chat detail and chat avatar -> member detail with a synthetic isolated peer.
- Local overlay: four files under `r09-member-chat-overlay/`.
- Archive: `deliverables/r09-member-chat-overlay-v1.tar.gz`.
- Archive SHA-256: `da6c164ffe1c4a8875dfcc352d69385d0c389f0078566c76ff1bdc2f6220733e`.
- Static business-protocol gate: PASS.
- Attempted staging deploy ID: `20260727T123509+0800`.
- Visual gate: PENDING because the in-app Browser error tab was blocked by Browser Use security policy.
- Safety response: staging restored to original member/chat template hashes; synthetic peer, message, visit log, auth bridge, browser-origin bridge and local tunnel removed; POST returned `405`.
- Exact resume instructions: `evidence/R09/member-chat/R09_MEMBER_CHAT_PROGRESS.md`.

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
