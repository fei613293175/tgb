# R09 Member And Chat Closeout

Date: `2026-07-27`

Status: `VISUAL PASS / PRODUCTION DEPLOYED / FIXTURES CLEANED`

## Scope Proof

- Required UA: Android mobile plus `TuiGuangBaoAndroid/1.0.0`.
- Chat chain: authenticated home -> personal center -> messages -> synthetic conversation row -> chat detail.
- Member chain: chat detail -> synthetic message avatar -> member detail.
- Both links were center hit-tested and reached by real clicks. Direct URL navigation was not used as scope proof.
- Before screenshots are stored under `before/`; synthetic after evidence is stored under `after/`.
- No password, cookie, formhash, real chat body, real identity or production UID is stored in this evidence.

## Final Implementation

- Overlay: `r09-member-chat-overlay/`.
- Immutable source baseline: `r09-member-chat-baseline-selected/`.
- Files: three templates plus two page-scoped local CSS files.
- Artifact: `deliverables/r09-member-chat-overlay-v5.tar.gz`.
- Artifact SHA-256: `3689201ab9e58f8244206ff2968233d9a476ab2571a4cf637f1b42f89fe790da`.
- Static gate: `scripts/test-r09-member-chat-overlay.ps1`.
- Static result: original hashes PASS; approved visual delta only PASS; frozen business markers PASS; public UI CDN count 0; deployment permission safety PASS.
- Member page includes its dedicated `wdk_header.php`; the old external return image is replaced by the existing local icon font.
- Chat forms, fields, AJAX URLs, polling, upload types, attachment behavior and message protocol remain unchanged.

## Browser Acceptance

- `390x844`: both pages visually inspected from the real click chain.
- `360x800` and `430x932`: added because the main pass exposed shared header and scrollbar concerns.
- All three viewports reported `scrollWidth === clientWidth` and no out-of-bounds visible elements.
- Chat: long bubble wrapping, report banner, 60px legacy-compatible header, attachment controls and fixed composer passed. The global blue page scrollbar was locally hidden without disabling touch scrolling.
- Member: return/title/more controls, statistics, follow/message actions, tabs and empty state passed. The initial decorative bars and empty boxes were rejected during review and replaced by a clean light-grid cover.
- The isolated avatar URL points to blocked port `18088`; this is recorded as an evidence-environment limitation, not a production CSS defect.
- Evidence: `after/CHAT-DETAIL-390x844.png`, `after/CHAT-DETAIL-390x844.json`, `after/MEMBER-DETAIL-390x844.png`, and `after/MEMBER-DETAIL-390x844.json`.
- Boundary screenshots are also stored for `360x800` and `430x932`.

## Deployment And Rollback

- Final staging deploy ID: `20260727T142041+0800`.
- Staging backup: `/www/staging/tg-h5-ui-r08/private/change-backups/20260727T142041+0800-r09-member-chat`.
- Production preflight confirmed the three exact original template hashes and both new CSS paths absent.
- Production deploy ID: `20260727T142812+0800`.
- Production backup: `/www/staging/tg-h5-ui-r08/private/production-member-chat-backups/20260727T142812+0800`.
- Production script: `scripts/remote/r09_deploy_member_chat_production.sh`.
- Online smoke: chat list HTTP 200, member CSS HTTP 200, chat CSS HTTP 200, public UI CDN 0.
- Rollback command:

```bash
bash /tmp/r09_deploy_member_chat_production.sh --apply-rollback _ _ 20260727T142812+0800
```

## Cleanup

- Synthetic peer, conversation and visit logs: removed.
- Authentication bridge: OFF.
- Browser-origin bridge: OFF and restored to `http://tg-h5-ui-r08.local:18088`.
- Diagnostic endpoints and local diagnostic file: removed.
- SSH tunnel `28088 -> 18088`: stopped.
- Staging POST gate: `405`.
