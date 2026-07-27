# R09 Global Closeout Audit

- Date: `2026-07-27`
- Release: `R09`
- Scope: all H5 pages proven reachable by the production Android-H5-UA click graph
- Development result: `PASS`
- Owner-device cumulative result: `PENDING_OWNER_VERIFICATION`

## Scope And Evidence

- `03_PAGE_LEDGER.csv` contains 39 non-Android visual `IN_SCOPE` rows: 38 business H5 pages plus the desktop compatibility entry.
- All 39 non-Android visual rows are `REDESIGNED_VERIFIED`.
- `R09_GLOBAL_VISUAL_EVIDENCE_MAP.csv` contains exactly one PASS evidence row for each of those 39 page IDs and no out-of-scope page.
- Hidden, direct-URL and dormant routes remain outside visual scope. The five explicitly excluded dormant plugins remain unchanged.
- All H5 conclusions use an Android mobile UA with `TuiGuangBaoAndroid/1.0.0`; desktop-UA observations are not acceptance evidence.

## Final Visual Recheck

- R08 invite, team, sign-in, dividend and App-download pages were rechecked at `360x800`, `390x844` and `430x932`.
- The sign-in promotion badge was corrected for the 360px viewport. The notice dialog was corrected for long titles, links, body scrolling, images and 44px navigation/close controls.
- The real content-publish route is `/plugin.php?id=xigua_hb&ac=pub&step=3&catid=31`. Historical evidence for `/ac=pub` was rejected because that route is not the clicked publish form.
- The publish header was reduced from 92px to 60px, the submit control is a complete `64x44` target, and two empty 50px placeholders were removed.
- All final recheck results have no horizontal overflow, visible out-of-bounds element, broken visible image, public UI CDN or old visible brand.
- Rejected coordinate-space captures are retained only under `rejected-capture/` and are excluded from final evidence.

## Business And Safety Boundary

- No publish, sign-in, reward, withdrawal, payment or other side-effect action was submitted during the global visual closeout.
- The publish page retains both POST forms, field names, hidden fields, routes and business JavaScript.
- The historical dividend-page `Unexpected token '}'` remains an unchanged baseline business-script error and was not removed or hidden to manufacture a PASS.
- Production deployment remained backup-first and hash-verified. Staging returned HTTP 405 after fixture and bridge cleanup.

## Production Corrections

- Sign-in template/CSS deployment: `20260727T181342+0800`.
- Sign-in backup: `/www/staging/tg-h5-ui-r08/private/production-sign-badge-backups/20260727T181342+0800`.
- Publish template deployment: `20260727T181343+0800`.
- Publish backup: `/www/staging/tg-h5-ui-r08/private/production-publish-visual-backups/20260727T181343+0800`.
- Auth bridge: `OFF`.
- Browser-origin bridge: `OFF`.
- Local tunnel `127.0.0.1:28088`: `OFF`.
- Staging POST status: `405`.

## Candidate

- Archive: `deliverables/r09-production-candidate-v5.tar.gz`.
- Site files: `78`.
- SHA-256: `c87633b032784b7a634496c50f3bb424a6668271dd6b43e2b6ad561cc2410734`.
- Two consecutive builds produced the same SHA-256.
- Server verify-only passed the 78-file archive contract, and the normalized manifest matched both production and staging 78/78 without deploying. See `R09_V5_SERVER_HASH_VERIFY.md`.

## Gate Result

`scripts/test-r09-global-closeout.ps1` returned:

```text
[R09-GLOBAL-CLOSEOUT] PASS
[R09-GLOBAL-CLOSEOUT] non_android_in_scope=39 business_h5=38 desktop_entry=1 redesigned_verified=39 evidence_map=39 r08_results=15 notice_viewports=3 publish_viewports=3
```

## Remaining Cumulative Owner Verification

- Replacement APK status-bar and bottom-safe-area screenshot/runtime confirmation.
- Real-device gallery permission and image upload.
- Alipay launch, cancellation/completion return and order refresh using an approved real or sandbox checkout sample.
- Any owner-reported H5 defect is handled as a targeted R09 repair and does not reopen hidden-page discovery.
- Final security signoff also requires owner-side database credential rotation and a decision on Git-history remediation. HEAD/current-tree credential scanning is PASS, but one historical commit contains a production database identifier that may equal the password.

These items are not H5 development omissions and do not wait-block repository closeout work. They remain explicit cumulative release gates and must not be represented as PASS without owner-device evidence.
