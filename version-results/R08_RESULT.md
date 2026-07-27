# R08 Result

- Status: `PASS`
- Closed: `2026-07-27`
- Scope: 5 real-reachable growth, reward and internal App landing pages only
- Overlay: `deliverables/r08-click-proven-overlay-v2.tar.gz`
- Overlay files: 10
- Overlay bytes: 56134
- SHA-256: `3d62e35884e920dd9d168c262beb99e4eb134711e0a25bc65ce7fef903659060`
- Staging deploy ID: `20260727T080940+0800`
- Backup: `/www/staging/tg-h5-ui-r08/private/change-backups/20260727T080940+0800-click-proven-v1`

## Scope

- Included: invitation, team, sign-in, match dividend and internal App download landing.
- Excluded: real sign-in, dividend claim, reward task, external sharing and downstream download actions.
- Dormant R09 plugins remain excluded because no visible parent entry exists.

## Gates

- Static allowlist and protocol gate: PASS
- Forms, links, routes, values, template flow and business scripts: unchanged
- Android viewports: 5 pages x 360x800, 390x844 and 430x932 = 15 PASS
- Screenshot pixels: all 15 exact
- Horizontal overflow: 0
- Public Tailwind/UI CDN requests: 0
- Visible 推广宝 branding: 15/15
- Staging POST guard: 405
- Production stable-code SHA-256: `91a57ef67b34b9f650becc9413b03288f48b46f1e9452bc779cf03944ec38984`
- Browser session and local SSH tunnel: OFF

## Known baseline behavior

- The dividend page emits an existing `Unexpected token '}'` browser error.
- Its business scripts are byte-equivalent to the R07 baseline after ignoring removed presentation-only CDN loaders; R08 did not change or mask this behavior.

## Evidence

- `evidence/R08/after/R08-V2-FINAL-BROWSER-MATRIX.json`
- `evidence/R08/after/matrix-v1/`
