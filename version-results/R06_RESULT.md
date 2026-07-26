# R06 Result

- Status: `PASS`
- Closed: `2026-07-27`
- Scope: 8 click-proven account and communication pages only
- Overlay: `deliverables/r06-account-communication-overlay-v9.tar.gz`
- Overlay files: 13
- Overlay bytes: 36624
- SHA-256: `5b12f061d32632d0d0a1114962cb7790b1a706805f9715cbdf48dfcd2d356761`
- Staging deploy ID: `20260727T062617+0800`
- Backup: `/www/staging/tg-h5-ui-r06/private/change-backups/20260727T062617+0800-account-communication-v1`

## Scope correction

- The visible contact entry renders the Discuz mobile fallback prompt, not the dormant `userext` form.
- The dormant form was restored to its exact baseline and excluded from the final overlay.
- Chat detail remains excluded because its visible entry has not completed isolated replay.

## Gates

- Static protocol gate: PASS
- Controls, routes, parameters, template flow and business scripts: unchanged
- Android viewports: 8 pages x 360x800, 390x844 and 430x932 = 24 PASS
- Horizontal overflow: 0
- Public Tailwind/UI CDN requests: 0
- Legacy brand titles: 0
- Stuck loading states: 0
- Production POST guard: 405
- Production stable-code SHA-256: `91a57ef67b34b9f650becc9413b03288f48b46f1e9452bc779cf03944ec38984`
- Temporary auth bridge, browser-origin bridge and local SSH tunnel: OFF

## Evidence

- `evidence/R06/after/R06-V9-BROWSER-MATRIX.json`
- `evidence/R06/after/matrix-v9/`
- `evidence/R06/staging/R06_SCOPE_CORRECTION_V7.md`

The folders under `evidence/R06/invalid-captures/` are quarantined tool-output failures and are not acceptance evidence.
