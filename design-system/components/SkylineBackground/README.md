# SkylineBackground

The night-city backdrop behind every tenant screen: a deep navy sky with 55 stars and fifteen buildings whose windows glow in the brand's accent colours.

No props; it fills the screen and ignores touches. The layout is deterministic: a seeded random function (`sin(seed + 1) × 10000`) decides star positions and which windows are lit, so the skyline is identical on every launch. Window colours cycle through `sky`, `warning`, `indigo`, `info`, `skyline-violet` and `skyline-mint`.

The admin login screen uses a flatter variant (two `navy-600` building silhouettes, see the Illustrations asset group). Source: `kosapp-mobile/src/components/SkylineBackground.tsx`.
