# StatusBadge

The tenant app's payment status pill: a dot plus the status word on a 15% tint of its colour.

Props: `status` (`paid` · `unpaid` · `overdue` · `pending`), optional `dueDate` (an `unpaid` bill past its due date shows as Terlambat), optional `small`.

| status | Label | Colour |
| --- | --- | --- |
| paid | Lunas | `success` |
| pending | Menunggu Konfirmasi | `indigo` |
| overdue / past due | Terlambat | `danger` |
| unpaid | Belum Bayar | `warning` |

Source: `kosapp-mobile/src/components/StatusBadge.tsx`. Web rendition: `.m-status .m-status-<status>`.
