# Topbar

The sticky white bar above every admin page: breadcrumb on the left; date, notifications and profile on the right.

`.topbar` > `.breadcrumb` ("SiManKos › page title") and `.topbar-actions` (`.date-chip` in `ddd, D MMM YYYY` Indonesian locale, an `.icon-btn` to notifications, the `.avatar` initial). On phones a menu `.icon-btn` opens the sidebar drawer and the date chip hides.

**The consumer provides** the page title (`@section('page-title')`).
