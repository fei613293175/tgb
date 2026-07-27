# R09 Final Project Readiness

- H5 development closeout: `PASS`
- Final project readiness: `FAIL_PENDING_OWNER_AND_SECURITY`
- Formal gate: `scripts/test-final-project-readiness.ps1`

## Already Proven

- 39/39 non-Android visual entries are `REDESIGNED_VERIFIED`.
- The deterministic 79-file owner repair candidate SHA-256 `9deca6adaf7579bbbe8944d0725b3be61dbb6f3a1892fea07d339225f167ae15` is deployed to production with exact hash and online smoke PASS.
- The current signed replacement APK SHA-256 `5c69f3c4e64e214e901fae5574ec8b54c464e1cd19a907896742feb0327aa027` satisfies server unit-test, Lint, package, signature and size gates and is on the owner's Desktop.
- The R09 owner repair and global H5 closeout gates pass. The continuity manifest was rebuilt for 927 files and passed verification before the checkpoint commit.
- The current tree does not contain the previously hard-coded production database identifier.

## Required Before Final Project PASS

The owner verification matrix must contain PASS evidence files for:

1. Replacement APK installation and cold launch.
2. Status-bar and gesture-area geometry without duplicate blank space or overlap.
3. Gallery permission states and a real H5 image upload.
4. Approved Alipay checkout launch, return and server-side order refresh.
5. Trusted download notification and resulting file.
6. Offline light error state and successful retry.

Each PASS row must reference a JSON file conforming to `OWNER_DEVICE_EVIDENCE_SCHEMA.json` and record that JSON's SHA-256. The JSON must identify the current signed APK by SHA-256, record the owner device and Android version, contain at least one PASS observation, confirm redaction, and reference at least one repository attachment with a matching SHA-256. File existence alone is not acceptance evidence.

The security gate must also prove:

- the production database credential was rotated without storing the new value in the repository;
- the historical exposing commit is no longer reachable from local or remote branch refs;
- the rotation evidence path exists and the current-tree credential scan remains PASS.

Until every item is proven, `CURRENT_STATUS.release_status` remains `IN_PROGRESS`, the project must not receive a final PASS/tag, and owner feedback continues asynchronously without reopening hidden-page discovery.
