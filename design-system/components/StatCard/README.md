# StatCard

A KPI tile in the dashboard's top row: icon, figure, label.

`.stat-card` > `.stat-icon` (40px tile, tinted fill, 20px stroke icon) > `.stat-value` (Stat value style) > `.stat-label` (Caption). The figure takes the colour of its meaning: neutral `ink-strong`, available `success-strong`, tenants `indigo`, unpaid invoices `warning`. Revenue is always the last tile and uses `.is-dark` (navy gradient, white figure).

**The consumer provides** the icon, the figure (rupiah shortened as "Rp 12,4jt") and a two- or three-word label.

- Six tiles across on wide screens, two on phones (`grid-cols-2 xl:grid-cols-6`, gap `space-4`).
- Only one `.is-dark` tile per row.
