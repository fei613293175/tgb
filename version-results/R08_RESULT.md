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
- Original final matrix: `INVALIDATED` because it captured the login page after the session expired.
- Replacement runtime evidence: R09 authenticated `390x844` quick regression, all five R08 routes reached their real pages with no overflow, public UI CDN or old visible brand.
- Static protocol, business-script and local-resource gates: PASS
- Staging POST guard: 405
- Production stable-code SHA-256: `91a57ef67b34b9f650becc9413b03288f48b46f1e9452bc779cf03944ec38984`
- Browser session and local SSH tunnel: OFF

## Known baseline behavior

- The dividend page emits an existing `Unexpected token '}'` browser error.
- Its business scripts are byte-equivalent to the R07 baseline after ignoring removed presentation-only CDN loaders; R08 did not change or mask this behavior.

## Evidence

- Invalid historical capture retained only for audit: `evidence/R08/after/R08-V2-FINAL-BROWSER-MATRIX.json`
- Valid replacement: `evidence/R09/R09_QUICK_BROWSER_REGRESSION.md`
