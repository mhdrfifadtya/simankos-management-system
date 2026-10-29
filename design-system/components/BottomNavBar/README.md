# BottomNavBar

The tenant app's four-tab bar: Beranda, Tagihan, Notifikasi, Profil.

Props: `activeTab`, `onTabPress(key)`, optional `unreadCount` (shows a `warning-solid` bubble on Notifikasi; "9+" above nine).

72px tall (85px on iOS, with 16px bottom inset), `nav-bar` fill, hairline top border. The active tab turns `sky` with a 36×3px bar above it and a bold label. Icons are Unicode glyphs (⌂ ◈ ◉ ◎) at 22px. Source: `kosapp-mobile/src/components/BottomNavBar.tsx`.
