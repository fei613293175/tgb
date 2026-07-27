# Rejected screenshot evidence

`notice-after-final-bordered-preview.png` is retained only as evidence of a browser capture-coordinate failure. It must not be used for visual PASS.

The page reported a `360x800` CSS visual viewport and zero DOM offset, while the generic screenshot surface remained `450x1000`. The unprimed clip therefore enlarged the page by `1.25x` and falsely removed the right side of the modal. Final evidence uses `CDP_PRIMED_EXPLICIT_CSS_CLIP` and is checked by `scripts/test-r09-global-closeout.ps1`.
