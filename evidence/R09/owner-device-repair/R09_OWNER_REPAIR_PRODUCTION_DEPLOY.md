# R09 owner repair production deployment

- Date: 2026-07-27
- Candidate files: 79
- Candidate SHA-256: `9deca6adaf7579bbbe8944d0725b3be61dbb6f3a1892fea07d339225f167ae15`
- Pre-deploy stable production manifest SHA-256: `128aaead7304ae1aa39df5ef99a2f69d4606246c63597d1eebf047065bd44939`
- Production deploy ID: `20260727T233853+0800`
- Private rollback backup: `/www/staging/tg-h5-ui-r08/private/production-release-backups/20260727T233853+0800-owner-repair`

The production deploy script first verified the archive hash, exact 79-file manifest, PHP syntax, local-resource policy, out-of-scope plugin exclusions, and the frozen pre-deploy production hash. It then created a private per-file rollback backup, installed files as `www:www` with public read permissions, verified the installed files against the candidate manifest, and cleared generated template cache files.

Post-deploy read-only smoke results:

- Exact production candidate hash verification: `PASS_79`
- Home: HTTP 200
- Login: HTTP 200
- Headline: HTTP 200
- Sign-in: HTTP 200
- App landing: HTTP 200
- Local Light Grid CSS: HTTP 200
- Public UI CDN matches: 0

No purchase, payment, withdrawal, sign-in, publish, delete, upload, certification, or reward write was executed.
