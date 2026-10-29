SiManKos (Sistem Manajemen Kos) runs boarding houses: rooms, tenants, contracts, invoices, payments and WhatsApp reminders. It has two faces that share one palette: the **admin panel** (Laravel + Blade, light, theme `light` "Admin") and the **tenant app** (Expo / React Native, dark, theme `dark` "Tenant Night"). Tagline: *Kelola Kos, Lebih Mudah.*

## Content fundamentals

- **Language.** All UI copy is Bahasa Indonesia. Keep domain words as owners say them: kos, kamar, penyewa, kontrak sewa, tagihan, pembayaran, jatuh tempo, lunas.
- **Voice.** Plain and polite, never chatty. The admin panel addresses an operator (imperative labels: "Simpan Data", "Tambah Kamar"); the tenant app and WhatsApp messages address the tenant by name ("Halo Budi, tagihan kos …").
- **Casing.** Title Case for navigation and page titles ("Manajemen Kamar", "Kontrak Sewa"); sentence case for messages and hints. Uppercase only in the Overline style.
- **Status words are fixed.** Lunas (paid), Belum Bayar (unpaid), Menunggu Konfirmasi (pending verification), Terlambat (overdue). Reminder codes H-7, H-3, H-1 count days before the due date.
- **Numbers.** Money as "Rp 1.250.000" (dot thousands); shortened on stat cards as "Rp 12,4jt". Dates in Indonesian locale: "29 Okt 2026", "Kam, 29 Okt 2026". Room numbers are block letter + number: "A-204".
- **No emoji** in the admin panel. The tenant app uses a few Unicode glyphs as icons (see Iconography).

## Visual foundations

**Colour.** One accent, `sky`, on slate neutrals and a deep `navy`.
- Admin: content sits in white `surface` cards on a `page` ground; the `navy` sidebar frames it. Set text in `ink`, headings in `ink-strong`, table cells in `text-body`, secondary copy in `text-secondary`.
- Night: `page` is near-black blue; cards are translucent `surface` over the SkylineBackground. `sky` lightens to #7dd3fc.
- `sky` is a fill and an indicator, not a text colour on white (2.1:1). For text links on light grounds use `link`. Text on a `sky` fill is always `on-sky`, never white.
- `indigo` is the second accent: tenant counts in admin, "pending verification" in the app, and the end of the ProgressBar gradient.
- Status colours come in families: `<status>` for text, `<status>-soft` for badge fills, `<status>-wash` for alert fills, `<status>-border` for outlines, `<status>-solid` for bars and dots. Success, warning, danger and info always appear with a word; never rely on hue alone.
- `whatsapp` green marks a message that was sent over WhatsApp and nothing else.

**Type.** One family, Plus Jakarta Sans (Google Fonts, 300–800), for everything. The admin panel's workhorse size is 13px (`body-ui`, `button`, `label`), not 14. Weight does the hierarchy: 500 for UI text, 600 for labels and buttons, 700 for titles, 800 for figures and the wordmark. Use `tabular-nums` for money and counts. The tenant app currently renders in the platform system font; load Plus Jakarta Sans there to match.

**Spacing.** A 4px base with half steps the panel actually uses (`space-1-5` 6px, `space-2-5` 10px, `space-3-5` 14px). Card body `space-6`, card header and stat card `space-5`, grid gaps `space-4`, page padding `space-5` on phones and `space-6` on desktop. The tenant app uses `space-1/2/4/6/8/12` only.

**Radii.** Rounded but not pill-soft: controls `radius-md` (10px), alerts and icon tiles `radius-lg` (12px), admin cards `radius-xl` (14px), tenant cards `radius-2xl` (16px). Badges, status pills and progress bars are `radius-full`.

**Borders and shadows.** Borders do the separating; shadows are barely there. Cards take a 1px `border` and `shadow-flat`; toasts are the only elevated element (`shadow-toast`). The brand mark carries a `sky` glow (`shadow-brand`). In Night, borders are 0.5px white at 8%.

**Gradients.** Only three, all from the source: the brand mark and avatar (`sky` → `sky-deep`, 135°), the revenue stat card (`navy` → `navy-600`), and the ProgressBar (#7dd3fc → #818cf8). Don't add others.

**Imagery.** The brand's one illustration is a city skyline at night: building silhouettes with lit windows in `sky`, `warning`, `indigo`, `info`, `skyline-violet` and `skyline-mint`. It sits behind the login screen and every tenant screen, always at the bottom edge. No photos of people, no stock art.

**Motion.** Short and functional: 150 ms colour and border transitions on controls, 100 ms row hover, 200/150 ms overlay fade for the mobile drawer, 300 ms drawer slide. Toasts enter with a slight overshoot (`cubic-bezier(.34,1.56,.64,1)`, 300 ms). The ProgressBar fills over 900 ms. Respect `prefers-reduced-motion`.

**States.** Hover darkens fills one step (`sky` → `sky-hover`, `surface-sunken` → `surface-hover`). Focus on inputs is a `sky` border plus `shadow-focus`; on other controls use a 2px `sky` outline offset 2px. Disabled is 50% opacity. Selected nav is `sky` text on `sky-soft`.

**Layout.** Admin: fixed 256px navy sidebar, sticky white top bar with breadcrumb, content scrolls in `page`. Below 1024px the sidebar becomes a drawer. Dashboards open with a row of StatCards (six across on wide screens, two on phones), then charts and lists in Cards. Tenant app: one column, cards 8px apart, a bottom tab bar.

**Accessibility gaps in the source** (kept exact here, fix in a later pass): `text-muted` on white is 2.6:1 and on Night surface 3.8:1; `sidebar-group` on navy is 2.4:1; `danger` on `danger-soft` is 4.4:1; `border-input` is 1.2:1; white "WA" on `whatsapp` is 2:1. `link` was added because the source used `sky` for text links.

## Iconography

- **Admin panel:** Heroicons v1 outline, drawn inline as SVG at 16px (nav, buttons) or 20px (stat cards), `stroke="currentColor"`, stroke width 2 (2.5 in toasts and the brand mark). The full set the panel uses is in the Icons group. Icons inherit the text colour of their container.
- **Tenant app:** Unicode glyphs, not an icon font: ⌂ Beranda, ◈ Tagihan, ◉ Notifikasi, ◎ Profil in the tab bar; ✓ ⏳ ! ₹ inside PaymentCard. The ₹ (Indian rupee) for an unpaid rupiah bill is a source quirk; prefer "Rp".
- **Brand mark:** the Heroicons "home" glyph in white on a 36px `sky` → `sky-deep` tile with `radius-lg` corners. There is no drawn logo or wordmark file in the codebase; set "SiManKos" in Plus Jakarta Sans ExtraBold (800), tracking −0.025em, beside the mark.

## Using the files

- `tokens.css` declares every token as a CSS custom property; `components/bundle.css` holds the admin classes (`.btn`, `.badge-*`, `.card`, `.stat-card`, `.table-wrap`, `.form-input`, `.alert-*`, `.page-link`, `.toast-*`, `.sidebar-*`, `.topbar`) with the same names as the Blade views, plus `.m-*` web renditions of the tenant components.
- Wrap tenant-app UI in an element with `data-theme="dark"`; it is Night-only.
- For React Native, the same values live in `kosapp-mobile/src/constants/theme.ts` (`COLORS`, `SPACING`, `FONT_SIZE`, `RADIUS`, `FONT_WEIGHT`); the Night theme maps to it one for one.
