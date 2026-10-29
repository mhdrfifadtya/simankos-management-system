# Card

The white container every admin page is built from: tables, forms, charts and lists all sit in a `.card`.

Structure: `.card` > optional `.card-header` (title + subtitle on the left, one action or link on the right) > `.card-body`. A card that holds only a chart may skip the header and use `p-5` padding.

**The consumer provides** the title (`.card-title`, Title style), an optional subtitle (`.card-subtitle`, Caption), the right-side action, and the body.

- Cards rest on `page`, never on another card.
- Radius `radius-xl`, border `border`, shadow `shadow-flat`. Don't add accent stripes.
- Header links use `link` colour.
