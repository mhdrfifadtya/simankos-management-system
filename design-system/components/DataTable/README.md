# DataTable

The index table used on every list page (Kamar, Penyewa, Kontrak, Tagihan, Pembayaran).

Wrap the `<table>` in `.table-wrap` inside a `.card` so wide tables scroll sideways inside the card, never the page. Heads use the Overline style in `text-muted`; cells are 13px `text-body` with 14×16 padding; rows highlight on hover with `surface-row-hover`.

**The consumer provides** the columns and rows. Put the record's identifier (room number, invoice number) first in bold, status as a Badge, money right-aligned with `.num` and formatted "Rp 1.250.000", and row actions as `.btn-sm` buttons in the last column.
