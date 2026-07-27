# R09 Member And Chat Progress

Date: `2026-07-27`

Status: `IMPLEMENTED LOCALLY / STATIC GATES PASS / VISUAL ACCEPTANCE PENDING`

## Scope Proof

- Required UA: `Mozilla/5.0 (Linux; Android 15; Pixel 9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 TuiGuangBaoAndroid/1.0.0`.
- Viewport: `390x844`.
- Chat chain: authenticated home -> personal center -> messages -> synthetic conversation row -> chat detail.
- Synthetic conversation row exposed approximately `10992.8px2`; its center was hit-tested and the real click reached `/plugin.php?id=xigua_lt&ac=chat&touid={sanitized}`.
- Member chain: chat detail -> synthetic message avatar -> member detail.
- The message avatar measured `40x40`, exposed `1600px2`, passed center hit-testing and the real click reached `/plugin.php?id=xigua_hb&ac=member&uid={sanitized}`.
- Before screenshots: `before/CHAT-DETAIL-390x844.png` and `before/MEMBER-DETAIL-390x844.png`.
- No UID, cookie, password, chat body or real identity is stored in this evidence.

## Local Implementation

- Overlay: `r09-member-chat-overlay/`.
- Immutable source baseline: `r09-member-chat-baseline-selected/`; the static gate does not depend on `.runtime` or chat history.
- Files: two templates plus two page-scoped local CSS files.
- Artifact: `deliverables/r09-member-chat-overlay-v1.tar.gz`.
- Artifact SHA-256: `da6c164ffe1c4a8875dfcc352d69385d0c389f0078566c76ff1bdc2f6220733e`.
- Static gate: `scripts/test-r09-member-chat-overlay.ps1`.
- Static result: original hashes PASS; approved visual delta only PASS; frozen business markers PASS; public UI CDN count 0.
- Member template removes the two `img.imehui.com` UI images and replaces them with local CSS geometry plus the existing local icon font.
- Chat template does not change `chat_li.php`, `lt.css`, forms, fields, URLs, AJAX payloads, polling or attachment protocols.

## Staging Attempt And Rollback

- Staging deploy ID: `20260727T123509+0800`.
- Backup: `/www/staging/tg-h5-ui-r08/private/change-backups/20260727T123509+0800-r09-member-chat`.
- Deployment itself passed the four-file minimal-overlay and predeploy-hash gates.
- Visual acceptance did not run because the in-app browser retained a connection-refused error document and Browser Use security policy rejected further actions on that tab.
- OS and server curl probes subsequently proved the SSH tunnel and staging listener were healthy; this is not page acceptance and cannot replace a screenshot.
- Because visual acceptance was missing, staging was restored immediately.
- Restored member template SHA-256: `e787a81ab9306a0dc5d4b97e82de585d37f71831bec8ae31603eb0e5c41afbf8`.
- Restored chat template SHA-256: `b0e370ebcb8aee006c88e4c26dbb6a1ad57693fd9a245303a20181b51f8857bb`.
- Synthetic fixture cleanup: PASS.
- Authentication bridge: OFF.
- Browser origin bridge: OFF and restored to `http://tg-h5-ui-r08.local:18088`.
- Staging POST gate: `405`.
- Local SSH tunnel: stopped.

## Exact Resume Point

1. Re-enable the existing browser-origin bridge, authentication bridge and `r09_member_chat_fixture.sh` in their documented order.
2. Rebuild the four-file archive from `r09-member-chat-overlay/`, confirm its SHA-256 and deploy with `scripts/remote/r09_deploy_member_chat.sh`.
3. Open both pages with the required Android H5 UA at `390x844` in a fresh in-app Browser tab.
4. Save after screenshots and geometry JSON only after checking overflow, overlap, clipping, safe areas, long bubbles, avatar, cover, statistics, buttons, tabs and public asset requests.
5. Run `360x800` and `430x932` only if the main viewport exposes a shared breakpoint or safe-area defect.
6. Clean the fixture and bridges, verify POST `405`, then prepare the production minimal deployment. Do not publish before visual acceptance passes.
