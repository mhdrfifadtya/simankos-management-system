# RoomInfoCard

The tenant home screen's hero: the room they rent, their contract dates and how far through the contract they are.

Props: `tenant` (`start_date`, `contract_duration` in months), `room` (`room_number`, `type`, `facilities`, `price`).

The room number is the one Display element in the app (Room number style, white, 3px tracking). Below a hairline divider, three columns: start date, duration, monthly price (price in `sky`). A ProgressBar shows elapsed months with "83% — 10/12 bulan" right-aligned under it.

Source: `kosapp-mobile/src/components/RoomInfoCard.tsx`.
