# R09 Quick Browser Regression

- Date: 2026-07-27
- Candidate: `r09-production-candidate-v2.tar.gz`
- Candidate SHA-256: `108ab6d6c10be84a892aec223e24bcd134f86be30bc48c33a60f4180fd99dd3e`
- Viewport: `390x844`
- Method: one read-only pass over the authoritative real-reachable route set

## Result

- 29 route/state requests checked at the main H5 viewport.
- All redesigned H5 routes: no horizontal overflow, no authentication redirect, no public UI CDN, no visible `签米` or `创脉引擎` residue.
- Home, wallet and invitation pages received direct visual review and remained light, readable and usable.
- Content detail reused the closed R05 result because neither its template nor CSS changed in the R09 brand overlay.
- The root desktop compatibility route redirected to Discuz mobile guidance under the forced Android test UA; it is not a user H5 business page and was not used to judge the redesigned H5 surface.
- No write, payment, withdrawal, reward, upload, delete or messaging action was submitted.

## Cleanup

- Temporary mobile-UA mode: OFF
- Certification fixture: OFF
- Authentication bridge: OFF
- Staging POST guard: 405

## Production

- Deploy ID: `20260727T090142+0800`
- Files: 81
- Backup: `/www/staging/tg-h5-ui-r08/private/production-release-backups/20260727T090142+0800`
- Online smoke: home, login, help, about and App landing all HTTP 200; public UI CDN 0; old visible brand 0.
