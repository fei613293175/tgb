#!/usr/bin/env bash
set -Eeuo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
APK="$ROOT_DIR/android-app/app/build/outputs/apk/debug/app-debug.apk"
OUT="$ROOT_DIR/ci-artifacts/emulator"
PACKAGE="com.suewammes.tuiguangbao.debug"
COMPONENT="$PACKAGE/com.suewammes.tuiguangbao.MainActivity"

mkdir -p "$OUT"
adb wait-for-device
adb logcat -c

adb install -r -t "$APK" | tee "$OUT/adb-install.txt"
adb shell pm list packages "$PACKAGE" | tee "$OUT/package-installed.txt"
grep -Fq "package:$PACKAGE" "$OUT/package-installed.txt"

adb shell am force-stop "$PACKAGE"
adb shell am start -W -n "$COMPONENT" | tee "$OUT/activity-start.txt"
grep -Fq "Status: ok" "$OUT/activity-start.txt"

PID=""
for _ in $(seq 1 30); do
  PID="$(adb shell pidof "$PACKAGE" | tr -d '\r' || true)"
  if [[ -n "$PID" ]]; then
    break
  fi
  sleep 1
done

if [[ -z "$PID" ]]; then
  echo "App process did not stay alive." >&2
  exit 1
fi

# Allow the production H5 landing page and its WebView render to settle.
sleep 15

adb shell dumpsys activity activities > "$OUT/dumpsys-activity.txt"
adb shell dumpsys window windows > "$OUT/dumpsys-window.txt"
adb shell dumpsys package "$PACKAGE" > "$OUT/dumpsys-package.txt"
adb shell uiautomator dump /sdcard/tuiguangbao-ui.xml >/dev/null
adb pull /sdcard/tuiguangbao-ui.xml "$OUT/ui-hierarchy.xml" >/dev/null
adb exec-out screencap -p > "$OUT/launch-1080x2340.png"
adb logcat -d -v threadtime > "$OUT/logcat-full.txt"
adb logcat -d -v threadtime --pid="$PID" > "$OUT/logcat-app.txt" || true

grep -Fq "$COMPONENT" "$OUT/dumpsys-activity.txt"

if [[ ! -s "$OUT/launch-1080x2340.png" ]]; then
  echo "Launch screenshot is empty." >&2
  exit 1
fi

if grep -E \
  'FATAL EXCEPTION:|ANR in com\.suewammes\.tuiguangbao|Process com\.suewammes\.tuiguangbao.*has died' \
  "$OUT/logcat-full.txt"; then
  echo "Crash or ANR signature found in emulator log." >&2
  exit 1
fi

curl --fail --silent --show-error --location \
  --max-time 30 \
  --user-agent 'Mozilla/5.0 (Linux; Android 16) AppleWebKit/537.36 Chrome/140 Mobile Safari/537.36 TuiGuangBaoAndroid-CI' \
  --output "$OUT/public-entry.html" \
  --write-out '%{http_code}\n' \
  'https://tg.suewammes.com/' \
  | tee "$OUT/public-entry-status.txt"
grep -Eq '^200$' "$OUT/public-entry-status.txt"

echo "Emulator smoke passed: pid=$PID screenshot=$OUT/launch-1080x2340.png"

