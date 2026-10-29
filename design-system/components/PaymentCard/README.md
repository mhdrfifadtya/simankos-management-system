# PaymentCard

One monthly bill in the tenant's Tagihan list: status disc, month, due or paid date, amount and StatusBadge.

Props: `payment` (`month_label`, `amount`, `status`, `due_date`, `payment_date`), optional `onPress` (opens the bill).

The 42px disc takes the status colour at 10% fill and 27% border with a glyph: ✓ paid, ⏳ pending, ! overdue, ₹ unpaid. The sub-line reads "Dibayar: 03 Apr", "Menunggu verifikasi admin" or "Jatuh tempo: 10 Jun".

The unpaid glyph ₹ is the Indian rupee sign. It is in the source; for Indonesian rupiah, "Rp" is the correct mark. Source: `kosapp-mobile/src/components/PaymentCard.tsx`.
