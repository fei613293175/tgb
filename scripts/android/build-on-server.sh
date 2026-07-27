#!/usr/bin/env bash
set -Eeuo pipefail

if [[ $# -ne 3 ]]; then
  echo "Usage: build-on-server.sh <build-id> <source.tar.gz> <existing-private-signing-dir>" >&2
  exit 2
fi

BUILD_ID="$1"
SOURCE_ARCHIVE="$2"
SIGNING_SOURCE="$3"
BASE="/opt/tg-android-r01/builds"
BUILD_ROOT="$BASE/$BUILD_ID"
SOURCE_ROOT="$BUILD_ROOT/source"
PRIVATE_ROOT="$BUILD_ROOT/private"
ARTIFACT_ROOT="$BUILD_ROOT/artifacts"
IMAGE="hhy-android-toolchain:r08-api36-cache"

if [[ ! "$BUILD_ID" =~ ^[0-9TZ+_-]+$ ]]; then
  echo "Invalid build id." >&2
  exit 2
fi
if [[ "$BUILD_ROOT" != "$BASE/"* ]]; then
  echo "Resolved build path escaped the Android build base." >&2
  exit 2
fi
if [[ ! -f "$SOURCE_ARCHIVE" ]]; then
  echo "Source archive missing." >&2
  exit 2
fi
for file in signing.env keystore.properties tuiguangbao-release.jks; do
  if [[ ! -f "$SIGNING_SOURCE/$file" ]]; then
    echo "Required private signing file missing: $file" >&2
    exit 2
  fi
done
if [[ -e "$BUILD_ROOT" ]]; then
  echo "Build root already exists; use a new immutable build id." >&2
  exit 2
fi

install -d -m 755 "$BUILD_ROOT" "$SOURCE_ROOT" "$ARTIFACT_ROOT"
install -d -m 700 "$PRIVATE_ROOT"
install -m 600 "$SIGNING_SOURCE/signing.env" "$PRIVATE_ROOT/signing.env"
install -m 600 "$SIGNING_SOURCE/keystore.properties" "$PRIVATE_ROOT/keystore.properties"
install -m 600 "$SIGNING_SOURCE/tuiguangbao-release.jks" "$PRIVATE_ROOT/tuiguangbao-release.jks"
install -m 644 "$SOURCE_ARCHIVE" "$BUILD_ROOT/source.tar.gz"
tar -xzf "$BUILD_ROOT/source.tar.gz" -C "$SOURCE_ROOT"

if grep -R -n -E 'onProgressChanged|progressBarStyleHorizontal' \
  "$SOURCE_ROOT/app/src/main"; then
  echo "Visible native page-loading progress UI is forbidden." >&2
  exit 1
fi

docker run --rm \
  --env-file "$PRIVATE_ROOT/signing.env" \
  -v "$SOURCE_ROOT:/workspace" \
  -v "$PRIVATE_ROOT:/run/tgb-signing:ro" \
  -v /root/.gradle:/root/.gradle \
  "$IMAGE" \
  -lc '
    set -Eeuo pipefail
    cd /workspace
    chmod +x gradlew
    ./gradlew --no-daemon --stacktrace testDebugUnitTest lintRelease assembleRelease
  '

APK_SOURCE="$SOURCE_ROOT/app/build/outputs/apk/release/app-release.apk"
APK_FINAL="$ARTIFACT_ROOT/tuiguangbao-1.0.2-release.apk"
if [[ ! -f "$APK_SOURCE" ]]; then
  echo "Release APK missing after build." >&2
  exit 1
fi

install -m 644 "$APK_SOURCE" "$APK_FINAL"
APK_BYTES="$(stat -c %s "$APK_FINAL")"
if (( APK_BYTES < 10485760 )); then
  echo "Signed Release APK is below 10 MiB: $APK_BYTES" >&2
  exit 1
fi

BUILD_TOOLS="/opt/android-sdk/build-tools/36.0.0"
docker run --rm \
  -v "$ARTIFACT_ROOT:/artifacts:ro" \
  --entrypoint /opt/android-sdk/build-tools/36.0.0/apksigner \
  "$IMAGE" \
  verify --verbose --print-certs \
  /artifacts/tuiguangbao-1.0.2-release.apk \
  > "$ARTIFACT_ROOT/apksigner-report.txt"
docker run --rm \
  -v "$ARTIFACT_ROOT:/artifacts:ro" \
  --entrypoint "$BUILD_TOOLS/aapt" \
  "$IMAGE" \
  dump badging \
  /artifacts/tuiguangbao-1.0.2-release.apk \
  > "$ARTIFACT_ROOT/aapt-badging.txt"

grep -Fq "package: name='com.suewammes.tuiguangbao'" "$ARTIFACT_ROOT/aapt-badging.txt"
grep -Fq "versionCode='3' versionName='1.0.2'" "$ARTIFACT_ROOT/aapt-badging.txt"
grep -Fq "sdkVersion:'23'" "$ARTIFACT_ROOT/aapt-badging.txt"
grep -Fq "targetSdkVersion:'36'" "$ARTIFACT_ROOT/aapt-badging.txt"
grep -Fq "application-label:'推广宝'" "$ARTIFACT_ROOT/aapt-badging.txt"
grep -Fq "Verified using v2 scheme (APK Signature Scheme v2): true" "$ARTIFACT_ROOT/apksigner-report.txt"
grep -Fq "Verified using v3 scheme (APK Signature Scheme v3): true" "$ARTIFACT_ROOT/apksigner-report.txt"

sha256sum "$APK_FINAL" > "$ARTIFACT_ROOT/tuiguangbao-1.0.2-release.apk.sha256"
sha256sum "$BUILD_ROOT/source.tar.gz" > "$BUILD_ROOT/source.tar.gz.sha256"
printf '%s\n' "$APK_BYTES" > "$ARTIFACT_ROOT/tuiguangbao-1.0.2-release.apk.bytes"

echo "SERVER_BUILD_PASS"
echo "BUILD_ROOT=$BUILD_ROOT"
echo "APK_BYTES=$APK_BYTES"
cat "$ARTIFACT_ROOT/tuiguangbao-1.0.2-release.apk.sha256"
