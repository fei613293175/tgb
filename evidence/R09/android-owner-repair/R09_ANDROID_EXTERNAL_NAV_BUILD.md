# R09 Android external navigation server build

- Date: 2026-07-27
- Result: PASS
- Build ID: `20260727T235000+0800`
- Build root: `/opt/tg-android-r01/builds/20260727T235000+0800`
- Build image: `hhy-android-toolchain:r08-api36-cache`
- Source archive SHA-256: `58cd915afd798815ac54ae853c6e11616a49fc254812ef242f0657328e4a291d`
- APK bytes: `18154509`
- APK SHA-256: `5c69f3c4e64e214e901fae5574ec8b54c464e1cd19a907896742feb0327aa027`
- Certificate SHA-256: `622faaef8c659efafcf67bf7ddd9fdd8409186a5f4043d33de368db7cb668854`
- Repository APK: `deliverables/android/R09/tuiguangbao-1.0.0-R09-owner-repair-external-nav.apk`
- Desktop APK: `C:\Users\小白\Desktop\推广宝-1.0.0-R09-页面返修与外链修复.apk`

Server tasks were `testDebugUnitTest lintRelease assembleRelease`. The build completed successfully; the new `ExternalNavigationPolicyTest` ran 2 tests with 0 failures, 0 errors, and 0 skipped. Release Lint completed with 0 errors. The build script also rejected visible native page-loading progress code before compilation.

Independent artifact checks passed:

- Package: `com.suewammes.tuiguangbao`
- Version: `1.0.0` / versionCode `1`
- minSdk / targetSdk / compileSdk: `23 / 36 / 37`
- App label: `推广宝`
- APK size: greater than 10 MiB
- Signing: v1, v2, and v3 PASS
- Embedded start URL: `https://tg.suewammes.com/`
- Server, repository, and Desktop APK SHA-256: exact match

An earlier immutable build ID `20260727T234800+0800` compiled the APK and produced passing test and Lint reports, but the controlling SSH command timed out before the script archived and verified artifacts. It was not accepted as a build PASS. The complete canonical script was rerun under the new build ID above.

The APK was not installed, launched, or function-tested by Codex. Installation, safe-area behavior, gallery upload, third-party payment launch/return, trusted download, and offline retry remain owner-only asynchronous verification items.
