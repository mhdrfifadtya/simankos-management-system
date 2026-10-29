The Heroicons v1 outline icons the admin panel draws inline, copied from `resources/views/layouts/app.blade.php`.

- 24×24 viewBox, stroke width 2 (toast icons 2.5), round caps and joins.
- Ink in these files is `text-tertiary` #64748B so they read on white. In the app they are inline SVG with `stroke="currentColor"` and inherit the text colour; do that rather than using the files as `<img>`.
- Nav: dashboard, rooms, facilities, tenants, rentals, invoices, payments, notifications, reports, settings. Chrome: menu, chevron-right, logout. Feedback: toast-success, toast-error, toast-warning, toast-info.
