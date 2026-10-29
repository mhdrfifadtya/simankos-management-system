# Toast

A transient notice stacked bottom-right that confirms an action without moving the page.

Call `siToast(message, type, title?, duration?)` from the layout. `type` is `success`, `error`, `warning` or `info`; the default titles are Berhasil, Gagal, Perhatian and Informasi. Default durations: 4.5 s success, 5 s info, 6 s warning, 7 s error. The bar at the bottom (`.toast-progress`) shows the time left.

**The consumer provides** the message: one sentence naming the record ("Pembayaran kamar A-204 dikonfirmasi."). Errors say how to fix the problem.

- Toasts slide in from the right with a slight overshoot (`cubic-bezier(.34,1.56,.64,1)`, 300 ms) and out in 250 ms. Honour reduced motion.
- `z-toast` keeps the stack above the sidebar and overlay.
