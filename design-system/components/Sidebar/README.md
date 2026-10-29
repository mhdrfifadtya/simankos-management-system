# Sidebar

The admin panel's permanent navigation: a 256px navy column with the brand, grouped links and the signed-in user.

`.sidebar` > `.sidebar-brand` (the mark, "SiManKos", tagline "Kelola Kos, Lebih Mudah.") > `.sidebar-nav` with `.sidebar-item` links and `.sidebar-group` section labels. The current route takes `.active` (`sky` text on `sky-soft`). Groups in order: (none) Dashboard; Properti & Penyewa; Keuangan; Sistem.

**The consumer provides** the links (16px stroke icon + label) and the route check for `.active`.

- Below 1024px the sidebar becomes a drawer over a `bg-black/60` blurred overlay (`z-sidebar` over `z-overlay`).
- The sidebar is navy in both themes.
- Group labels in `sidebar-group` are 2.4:1 on navy in the source; they label, they don't instruct.
