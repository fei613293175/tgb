# R09 v5 Server Hash Verification

- Date: `2026-07-27`
- Mode: read-only candidate and deployed-file verification
- Candidate: `deliverables/r09-production-candidate-v5.tar.gz`
- Candidate files: `78`
- Candidate SHA-256: `c87633b032784b7a634496c50f3bb424a6668271dd6b43e2b6ad561cc2410734`

## Result

```text
[R09-PRODUCTION] VERIFY PASS FILES=78 ARCHIVE_SHA256=c87633b032784b7a634496c50f3bb424a6668271dd6b43e2b6ad561cc2410734
R09_PRODUCTION_78_HASH_MATCH_PASS
R09_STAGING_78_HASH_MATCH_PASS
```

The server verifier unpacked the archive into an isolated `/tmp` directory, validated the archive SHA-256, exact file list, per-file manifest, PHP syntax, public-UI-CDN exclusion and dormant-plugin exclusion. It then compared the same normalized 78-file manifest against production and the isolated R08-derived staging tree.

No deployment mode was invoked. No production or staging file, cache, database, bridge, tunnel or request method was changed. The temporary verification directory was removed after PASS.
