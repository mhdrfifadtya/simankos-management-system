# SiManKos Design System (in-repo copy)

Extracted from `resources/views/layouts/app.blade.php`, `resources/views/auth/login.blade.php`
and `kosapp-mobile/src/constants/theme.ts` + `kosapp-mobile/src/components/`.
The browsable version (live previews, both themes) is a Claude design-system artifact.

| File | What |
| --- | --- |
| `tokens.json` | Source of truth: colours (themes `light` = admin, `dark` = tenant Night), type, spacing, radius, shadow, z-index |
| `tokens.css` | Generated CSS custom properties — rebuild with `python3 design-system/scripts/build-tokens-css.py design-system/tokens.json > design-system/tokens.css` |
| `components/bundle.css` | Admin component classes (`.btn`, `.badge-*`, `.card`, …) on the tokens, same class names as the Blade views; `.m-*` web renditions of the tenant-app components |
| `components/<Name>/` | Guidelines (`README.md`) and an HTML preview per component |
| `assets/` | Brand mark, Heroicons used by the panel, login skyline |
| `README.md` | Brand book: content, colour, type, spacing, iconography rules |

Adopting it in Blade: load `tokens.css` then `components/bundle.css` (e.g. via Vite) and remove the inline
`<style>` block in `layouts/app.blade.php`; class names already match.
