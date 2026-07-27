# R09 Android Inset H5 Browser Check

- Date: `2026-07-27`
- Viewport: `390x844`
- Browser UA: Android mobile with `TuiGuangBaoAndroid/1.0.0`
- Scope: read-only visual and DOM geometry check after the native-only inset ownership change

## Home

- URL: `https://tg.suewammes.com/plugin.php?id=xigua_hb`
- Header: `top=0`, `height=68.8px`
- Document scroll width: `380px` within `390px` viewport
- Screenshot: `R09-ANDROID-INSET-HOME-390x844.png`, verified output `390x844`

## Sign

- URL: `https://tg.suewammes.com/plugin.php?id=view&modac=sign&idu=8888888`
- Header: `top=0`, `height=56px`
- Document scroll width: `375px` within `390px` viewport
- The existing read-only notice overlay was closed before the geometry capture; no sign, reward, payment or other write action was triggered.
- Screenshot: `R09-ANDROID-INSET-SIGN-390x844.png`, verified output `390x844`

## Result

The standalone H5 headers are compact and have no horizontal overflow. The owner screenshots therefore isolate the excessive top blank to the native root WindowInsets padding. This browser check does not claim APK runtime verification; device validation remains owner-only under D-036.
