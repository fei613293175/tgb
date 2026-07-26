# R06 v7 scope checkpoint

- Scope remains limited to 8 click-proven pages.
- The contact entry really renders the Discuz mobile fallback prompt. The hidden `userext` form was restored to its exact baseline SHA-256 and removed from the overlay.
- Overlay: `deliverables/r06-account-communication-overlay-v7.tar.gz`
- SHA-256: `b5c50e99b7040d75ddad0bc64c3d29e63dce74f9e12f127134f62d9165989a0d`
- Deploy ID: `20260727T061010+0800`
- Files: 13
- Static gate: PASS; controls, template flow, business URLs and scripts unchanged.
- Runtime contact prompt at 390x844: visible 推广宝 brand, light background, 8px card, no horizontal overflow, no public UI CSS CDN.
- Evidence: `evidence/R06/after/R06-CONTACT-PROMPT-390x844-V7.png`
- Temporary auth bridge, browser-origin bridge and local SSH tunnel are OFF.
- R06 remains `IN_PROGRESS` only because the corrected contact prompt still needs honest 360x800 and 430x932 evidence.
