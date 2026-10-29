# Button

The action control for the admin panel: `.btn` plus one tone class and an optional size.

**Tones.** `.btn-primary` (`sky` fill, `on-sky` label) is the one main action on a screen: Simpan, Masuk, + Tambah. `.btn-secondary` is everything else: Batal, Detail, Kembali, filters; it is the most used tone in the panel. `.btn-success` confirms a payment. `.btn-danger` deletes or rejects and always sits behind a confirmation step.

**Sizes.** Default (13px, 8×16 padding). `.btn-sm` (12px) inside table rows and card headers. `.btn-lg` (15px) only for the login submit and full-width form submits.

**The consumer provides** the element (`<button>` for actions, `<a>` for navigation), the label in Indonesian sentence case, and an optional 16px stroke icon before the label.

- Never put white text on `sky`: the label is always `on-sky`.
- One primary button per view. Pair it with a secondary, never with a second primary.
- Disabled buttons drop to 50% opacity and keep their label ("Menyimpan…").
