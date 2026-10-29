# ProgressBar

A pill-shaped meter for contract progress, filled with a left-to-right `sky` → `indigo` gradient (#7dd3fc → #818cf8).

Props: `progress` (0–1, clamped), optional `height` (default 8), optional `backgroundColor` (default `surface-hover` in Night). The fill animates to its value over 900 ms.

Always pair it with a text label stating the value; the bar alone carries no number. Source: `kosapp-mobile/src/components/ProgressBar.tsx`.
