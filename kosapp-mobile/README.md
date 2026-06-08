# KosApp Mobile — Tenant Dashboard

React Native + Expo mobile app for the **Sistem Informasi Manajemen Kos** tenant portal.  
Visual theme: **Skyline Night City** (deep navy, glassmorphism cards, SVG city silhouette, star particles).

---

## Screens

| Screen | Route | Description |
|--------|-------|-------------|
| Login | `Login` | Room number + password login |
| Dashboard | `Home` | Room info, latest payments, latest notification |
| Tagihan | `Payment` | Full payment history + detail modal |
| Notifikasi | `Notification` | Grouped notification list with read/unread state |
| Profil | `Profile` | Tenant info, contact owner, logout |

---

## Quick Start

### Prerequisites

- Node 18+
- Expo CLI: `npm install -g expo-cli`
- Expo Go app on your phone **or** Android/iOS emulator

### Installation

```bash
cd kosapp-mobile
npm install
```

### Run

```bash
# Start Metro bundler
npm start

# Open on Android emulator
npm run android

# Open on iOS simulator (macOS only)
npm run ios

# Open in browser (limited)
npm run web
```

Scan the QR code with **Expo Go** (Android) or the Camera app (iOS).

---

## Demo Credentials

| Field | Value |
|-------|-------|
| Nomor Kamar | `205` |
| Kata Sandi | `password` |

When the Laravel backend (`http://localhost:8000`) is unreachable, the app automatically falls back to mock data so the UI is always functional during development.

---

## Project Structure

```
kosapp-mobile/
├── App.tsx                       ← Entry point
├── app.json                      ← Expo config
├── package.json
├── tsconfig.json
└── src/
    ├── constants/theme.ts        ← Color tokens, spacing, typography
    ├── types/index.ts            ← TypeScript interfaces
    ├── utils/
    │   ├── formatCurrency.ts     ← "Rp 750.000" formatter
    │   ├── formatDate.ts         ← Indonesian date formatter (date-fns)
    │   └── getDueDiff.ts         ← H-7/H-3/H-1/overdue calculator
    ├── components/
    │   ├── SkylineBackground.tsx ← SVG city silhouette + star particles
    │   ├── GlassCard.tsx         ← Glassmorphism card wrapper (expo-blur)
    │   ├── StatusBadge.tsx       ← Color-coded payment status pill
    │   ├── ProgressBar.tsx       ← Animated contract progress bar
    │   ├── PaymentCard.tsx       ← Single payment row
    │   ├── NotificationCard.tsx  ← Single notification card
    │   ├── RoomInfoCard.tsx      ← Tenant room info + progress bar
    │   └── BottomNavBar.tsx      ← Bottom navigation with badge
    ├── services/
    │   ├── api.ts                ← Axios instance + JWT interceptors
    │   ├── authService.ts        ← Login / logout / token storage
    │   ├── paymentService.ts     ← Payment API calls
    │   └── notificationService.ts← Notification API calls
    ├── store/
    │   ├── authStore.ts          ← Zustand auth state
    │   ├── paymentStore.ts       ← Zustand payment state
    │   └── notificationStore.ts  ← Zustand notification state
    ├── navigation/
    │   └── AppNavigator.tsx      ← React Navigation stack
    └── screens/
        ├── LoginScreen.tsx
        ├── HomeScreen.tsx
        ├── PaymentScreen.tsx
        ├── NotificationScreen.tsx
        └── ProfileScreen.tsx
```

---

## API Integration

Base URL is `http://localhost:8000/api`.  
Change it in [src/services/api.ts](src/services/api.ts) — look for `BASE_URL`.

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/auth/login` | Login → `{ token, tenant, room }` |
| POST | `/auth/logout` | Logout |
| GET  | `/tenant/profile` | Tenant + Room info |
| GET  | `/tenant/payments` | Payment list |
| GET  | `/tenant/payments/:id` | Payment detail |
| GET  | `/tenant/notifications` | Notification list |
| PATCH | `/tenant/notifications/:id/read` | Mark single read |
| PATCH | `/tenant/notifications/read-all` | Mark all read |

Authentication: JWT Bearer token stored in AsyncStorage, attached via Axios request interceptor. Auto-logout on 401.

---

## Tech Stack

| Package | Purpose |
|---------|---------|
| `expo ~51` | Build toolchain |
| `react-native 0.74` | Core framework |
| `@react-navigation/stack` | Screen navigation |
| `react-native-svg` | SVG skyline background |
| `expo-blur` | Glassmorphism blur effect |
| `expo-linear-gradient` | Gradient fills |
| `zustand ^4` | Lightweight global state |
| `axios ^1` | HTTP client |
| `@react-native-async-storage/async-storage` | JWT persistence |
| `date-fns ^3` | Indonesian date formatting |

---

## Theme

All color tokens live in [src/constants/theme.ts](src/constants/theme.ts).

| Token | Value | Use |
|-------|-------|-----|
| `background` | `#0a0e1a` | Screen background |
| `accent` | `#7dd3fc` | Primary highlight, active nav |
| `indigo` | `#818cf8` | Secondary highlight |
| `success` | `#34d399` | Paid status |
| `warning` | `#fbbf24` | Pending / notification badge |
| `danger` | `#f87171` | Overdue |
| `textPrimary` | `#e8eaf0` | Main text |
| `textSecondary` | `#94a3b8` | Labels |
| `textHint` | `#64748b` | Placeholders, metadata |
