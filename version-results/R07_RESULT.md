# R07 Result

- Status: `PASS`
- Closed: `2026-07-27`
- Scope: 7 click-proven finance, membership and promotion pages only
- Overlay: `deliverables/r07-click-proven-overlay-v6.tar.gz`
- Overlay files: 12
- Overlay bytes: 39397
- SHA-256: `7114c2eecf4151c709fdea3ea8e5e6fd0fa36cbb57a9b416ef71d21fb327dd6c`
- Staging deploy ID: `20260727T072400+0800`
- Backup: `/www/staging/tg-h5-ui-r07/private/change-backups/20260727T072400+0800-click-proven-v1`

## Scope correction

- `CONTENT-REFRESH-PACK` is rendered by `sxtc.php`; `refresh.php` is the visible "use now" popup on that page.
- Both files are reachable within the same click-proven page and are included in the final overlay.
- Orders and checkout remain excluded because no click-proven sandbox destination exists.

## Gates

- Static protocol gate: PASS
- Forms, links, routes, values, template flow and business scripts: unchanged
- Android viewports: 7 pages x 360x800, 390x844 and 430x932 = 21 PASS
- Screenshot pixels: all 21 exact
- Horizontal overflow: 0
- Public Tailwind/UI CDN requests: 0
- Stuck loading states: 0
- Staging POST guard: 405
- Production stable-code SHA-256: `91a57ef67b34b9f650becc9413b03288f48b46f1e9452bc779cf03944ec38984`
- Temporary certification fixture, auth bridge, browser-origin bridge and local SSH tunnel: OFF

## Known baseline behavior

- Both headline templates emit an existing `Unexpected token '}'` browser error.
- Their script blocks are byte-equivalent to the R06 baseline after reversing approved visual-only changes; R07 did not change or mask this business-script behavior.

## Global drift audit

- Requirement: only seven click-proven pages changed; orders, checkout and hidden routes remain excluded.
- Hard gates: light UI, business protocol unchanged and staged rollback all passed.
- Progress: R07 is closed and `TASK-R08-001` is the unique `IN_PROGRESS` task.
- Reuse: local Light Grid tokens, local icon fonts, scoped CSS and exact-pixel CDP screenshots remain the standard path.
- Lessons: final-template mapping and stable-frame screenshot rules were added as L-066 and L-067.
- Automation: GitHub Actions remains manual `workflow_dispatch` only; owner device feedback remains asynchronous.
- Result: no unresolved requirement drift remains in R07.

## Evidence

- `evidence/R07/after/R07-V6-FINAL-BROWSER-MATRIX.json`
- `evidence/R07/after/matrix-v6-final/`
