# NotificationCard

A WhatsApp reminder the tenant received, as listed on the Notifikasi screen.

Props: `notification` (`type`, `message`, `sent_at`, `is_read`).

Reminder types and colours: H-7 `sky`, H-3 `warning`, H-1 `danger`, Terlambat `danger`, Lunas `success`. Unread cards carry a 3px `sky` left edge and a `sky` dot. The message shows at most three lines in the Body relaxed style.

Source: `kosapp-mobile/src/components/NotificationCard.tsx`.
