# R09 Result

- Status: `H5 DEPLOYED / ASYNC OWNER FEEDBACK OPEN`
- Date: `2026-07-27`
- Scope: final real-reachable H5 visual candidate and production release
- Candidate files: 81
- SHA-256: `108ab6d6c10be84a892aec223e24bcd134f86be30bc48c33a60f4180fd99dd3e`
- Production deploy ID: `20260727T090142+0800`
- Backup: `/www/staging/tg-h5-ui-r08/private/production-release-backups/20260727T090142+0800`

## Outcome

- R02-R08 overlays and the eight-file R09 visible-brand overlay were assembled once and deployed.
- No hidden page, dormant plugin, business script, route, form protocol, payment protocol or Android source was added or changed.
- The final `390x844` quick pass found no H5 overflow, login redirect, public UI CDN or old visible brand residue.
- Production file hashes matched the candidate; home and App landing returned HTTP 200 during deployment.
- The five-page online smoke passed after deployment.

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
