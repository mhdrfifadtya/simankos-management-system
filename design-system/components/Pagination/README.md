# Pagination

Page links under every index table.

`.pagination` wraps `.page-link` items; the current page takes `.active` (`sky` fill, `on-sky` text) and `aria-current="page"`; unavailable arrows take `.disabled`.

**The consumer provides** the Laravel paginator links, ten records per page by default. Place it in the card footer, right-aligned, below the table.
