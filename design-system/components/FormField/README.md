# FormField

Label, control and help text for every admin form (create and edit pages).

`.form-label` above a `.form-input` (works on `input`, `select` and `textarea`), followed by either `.form-hint` or `.form-error`. Add `.is-invalid` to the control when Laravel returns an error for it. Focus shows a `sky` border with the `shadow-focus` halo.

**The consumer provides** a label in sentence case tied to the control with `for`/`id`, an optional placeholder that shows an example value, and the error text from `$errors->first('field')`.

- Lay fields in a two-column grid on desktop, one column on phones, gap `space-4`.
- Placeholders are examples, never instructions; put instructions in `.form-hint`.
- The input border (`border-input`) is fainter than 3:1 in the source; the label and focus state carry the affordance.
