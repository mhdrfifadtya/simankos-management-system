# Alert

An inline message at the top of a page or form that reports the result of the last action or a condition the admin should know.

Use `.alert` with `.alert-success`, `.alert-error`, `.alert-warning` or `.alert-info`. Laravel flash messages (`session('success')`, `$errors`) render as alerts at the top of the content area.

**The consumer provides** a 16px stroke icon (the toast icons) and one or two sentences that say what happened and, for errors, how to fix it.

- Keep alerts in the page flow. For transient confirmations use Toast.
- One alert per outcome; list multiple validation errors inside one `.alert-error`.
