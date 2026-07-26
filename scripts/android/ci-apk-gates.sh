#!/usr/bin/env bash
set -Eeuo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
APK="$ROOT_DIR/android-app/app/build/outputs/apk/debug/app-debug.apk"
OUT="$ROOT_DIR/ci-artifacts/build"
MIN_BYTES=10485760
EXPECTED_PACKAGE="com.suewammes.tuiguangbao.debug"

mkdir -p "$OUT"

if [[ ! -f "$APK" ]]; then
  echo "APK missing: $APK" >&2
  exit 1
fi

APK_BYTES="$(stat -c %s "$APK")"
if (( APK_BYTES < MIN_BYTES )); then
  echo "APK size $APK_BYTES is below the 10 MiB gate $MIN_BYTES." >&2
  exit 1
fi

BUILD_TOOLS_DIR="$ANDROID_HOME/build-tools/36.0.0"
AAPT="$BUILD_TOOLS_DIR/aapt"
APKSIGNER="$BUILD_TOOLS_DIR/apksigner"

"$AAPT" dump badging "$APK" > "$OUT/aapt-badging.txt"
"$AAPT" dump permissions "$APK" > "$OUT/aapt-permissions.txt"
"$APKSIGNER" verify --verbose --print-certs "$APK" > "$OUT/apksigner-debug.txt"
sha256sum "$APK" > "$OUT/app-debug.apk.sha256"
printf '%s\n' "$APK_BYTES" > "$OUT/app-debug.apk.bytes"

grep -Fq "package: name='$EXPECTED_PACKAGE'" "$OUT/aapt-badging.txt"
grep -Fq "sdkVersion:'23'" "$OUT/aapt-badging.txt"
grep -Fq "targetSdkVersion:'36'" "$OUT/aapt-badging.txt"
grep -Fq "application-label:'推广宝'" "$OUT/aapt-badging.txt"

if grep -Eq \
  'android.permission.(MANAGE_EXTERNAL_STORAGE|READ_EXTERNAL_STORAGE|WRITE_EXTERNAL_STORAGE|READ_MEDIA_IMAGES|CAMERA)' \
  "$OUT/aapt-permissions.txt"; then
  echo "Forbidden broad media/storage/camera permission detected." >&2
  exit 1
fi

FORBIDDEN_DB_ID="$(printf '\164\147\137\163\165\145\167\141\155\155\145\163\137\143\157\155')"
if grep -aFq "$FORBIDDEN_DB_ID" "$APK"; then
  echo "Forbidden database identifier found in APK." >&2
  exit 1
fi

if grep -R -n -E 'ProgressBar|onProgressChanged|progressBarStyleHorizontal' \
  "$ROOT_DIR/android-app/app/src/main"; then
  echo "Visible native page-loading progress UI is forbidden by the R01 device feedback." >&2
  exit 1
fi

git -C "$ROOT_DIR" grep -IlE \
  '(BEGIN (RSA |EC |OPENSSH )?PRIVATE KEY|AKIA[0-9A-Z]{16}|gh[pousr]_[A-Za-z0-9_]{20,})' \
  -- ':!MANIFEST_SHA256.txt' ':!scripts/android/ci-apk-gates.sh' \
  > "$OUT/source-secret-scan.txt" || true

if [[ -s "$OUT/source-secret-scan.txt" ]]; then
  echo "Potential plaintext credential material detected:" >&2
  cat "$OUT/source-secret-scan.txt" >&2
  exit 1
fi

echo "APK gates passed: package=$EXPECTED_PACKAGE bytes=$APK_BYTES"
