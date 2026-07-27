#!/usr/bin/env python3
import hashlib
import pathlib
import sys

BASELINE_SHA256 = "ccae4d5f80d1c8f7ff71803c99ddd01644a76601c4c588acededdd78cd65fc82"
OUTPUT_SHA256 = "83661a7871894331bdf6f6543f792c236e871f3267ac787291eaab80e70abe86"

REPLACEMENTS = (
    ("<title>收银台 · 签米</title>", "<title>推广宝收银台</title>"),
    (
        "    </style>\n</head>",
        "    </style>\n"
        "    <link rel=\"stylesheet\" href=\"source/plugin/tb_pay/static/tgb-r09/cashier-light-grid-r09.css?{VERHASH}\">\n"
        "</head>",
    ),
    (
        '<div id="header" style="margin-top:0px;background-color: rgba(255,255,255,0.85)!important;backdrop-filter: blur(22px);">',
        '<div id="header">',
    ),
    (
        '<img style="margin-top: 0px;" src="https://img.imehui.com/20250919/175827202768cd1a1b5dc6e.png" alt="返回图标" width="20" height="20">',
        '<span class="cashier-back-icon" aria-hidden="true"></span>',
    ),
    ('<h1 id="header-title">收银台</h1>', '<h1 id="header-title">推广宝收银台</h1>'),
    (
        '<div class="weui-cell__hd"><img style="width:44px;height:44px;border-radius:50%;" src="$zfval1[1]"></div>',
        '<div class="weui-cell__hd"><img src="$zfval1[1]" alt="" onerror="this.hidden=true"></div>',
    ),
)


def digest(data: bytes) -> str:
    return hashlib.sha256(data).hexdigest()


def main() -> int:
    if len(sys.argv) != 3:
        raise SystemExit("usage: r09_transform_cashier_template.py INPUT OUTPUT")

    source = pathlib.Path(sys.argv[1])
    target = pathlib.Path(sys.argv[2])
    if source.resolve() == target.resolve():
        raise SystemExit("input and output must differ")

    source_bytes = source.read_bytes()
    if digest(source_bytes) != BASELINE_SHA256:
        raise SystemExit("cashier baseline SHA-256 drift")

    text = source_bytes.decode("utf-8")
    for old, new in REPLACEMENTS:
        if text.count(old) != 1:
            raise SystemExit("cashier visual anchor count drift")
        if new in text:
            raise SystemExit("cashier visual replacement already present")
        text = text.replace(old, new, 1)

    if not text.endswith("\n"):
        text += "\n"
    output_bytes = text.encode("utf-8")
    if digest(output_bytes) != OUTPUT_SHA256:
        raise SystemExit(
            "cashier transformed output SHA-256 drift: " + digest(output_bytes)
        )
    target.write_bytes(output_bytes)
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
