# SiManKos - Class Diagram

```mermaid
classDiagram
    %% User Management
    class User {
        -id: int
        -name: string
        -email: string
        -password: string
        -phone: string
        -whatsappNumber: string
        +isAdmin() bool
        +hasRole(role) bool
        +hasPermission(perm) bool
    }
    
    class Role {
        -id: int
        -name: string
        +assignPermission() void
        +revokePermission() void
    }
    
    class Permission {
        -id: int
        -name: string
    }
    
    %% Room Management
    class Room {
        -id: int
        -roomNumber: string
        -type: string
        -price: decimal
        -area: decimal
        -floor: int
        -status: string
        +isAvailable() bool
        +getCurrentTenant() Tenant
    }
    
    class Facility {
        -id: int
        -name: string
        -icon: string
    }
    
    class RoomPhoto {
        -id: int
        -photoPath: string
        -isPrimary: bool
        +getUrl() string
    }
    
    %% Tenant Management
    class Tenant {
        -id: int
        -fullName: string
        -nik: string
        -whatsappNumber: string
        -address: string
        -status: string
        +getActiveRental() Rental
        +isActive() bool
        +getOutstandingInvoices() list
    }
    
    %% Contract Management
    class Rental {
        -id: int
        -startDate: date
        -endDate: date
        -monthlyRent: decimal
        -deposit: decimal
        -status: string
        +isActive() bool
        +calculateRemainingDays() int
        +generateMonthlyInvoice() Invoice
    }
    
    %% Financial Management
    class Invoice {
        -id: int
        -invoiceNumber: string
        -year: int
        -month: int
        -rentAmount: decimal
        -total: decimal
        -dueDate: date
        -status: string
        +isPaid() bool
        +isOverdue() bool
        +markAsPaid() void
    }
    
    class Payment {
        -id: int
        -amount: decimal
        -paymentDate: date
        -paymentMethod: string
        -status: string
        -verifiedAt: datetime
        +isPending() bool
        +verify() void
    }
    
    %% Communication Management
    class CommunicationLog {
        -status: string
        -message: string
        +send()* void
        +markAsSent() void
    }
    
    class NotificationLog {
        -id: int
        -whatsappNumber: string
        -type: string
        -sentAt: datetime
        -isRead: bool
        +send() void
        +resend() void
    }
    
    class ReminderLog {
        -id: int
        -channel: string
        -type: string
        +send() void
    }
    
    %% System
    class Setting {
        -id: int
        -key: string
        -value: string
        +get(key) mixed
        +set(key, value) void
    }
    
    %% Relationships
    User "0..*" --> "0..*" Role : has
    Role "0..*" --> "0..*" Permission : grants
    User "1" --> "0..1" Tenant : has
    
    Room "1" --> "0..*" RoomPhoto : contains
    Room "0..*" --> "0..*" Facility : equipped
    
    Tenant "1" --> "0..*" Rental : signs
    Room "1" --> "0..*" Rental : occupied
    
    Rental "1" --> "0..*" Invoice : generates
    Tenant "1" --> "0..*" Invoice : billed
    Invoice "1" --> "0..*" Payment : settled
    
    CommunicationLog <|-- NotificationLog
    CommunicationLog <|-- ReminderLog
    
    Tenant "1" --> "0..*" NotificationLog : receives
    Tenant "1" --> "0..*" ReminderLog : receives
    Invoice "1" --> "0..*" ReminderLog : triggers
```

## 📋 Rincian Domain

### 1. Domain Manajemen Pengguna
- **User**: Admin, Staff, Penghuni
- **Role**: Admin, Staff, Tenant (RBAC Spatie)
- **Permission**: Buat, Edit, Hapus, Lihat

### 2. Domain Manajemen Kamar
- **Room**: Kamar kos dengan tipe (Basic, Standard, Deluxe)
- **Facility**: Fasilitas kamar (AC, WiFi, Kamar Mandi Pribadi, dll)
- **RoomPhoto**: Foto kamar dengan penunjukan utama

### 3. Domain Manajemen Penyewa
- **Tenant**: Profil penyewa dengan NIK, kontak darurat, preferensi notifikasi

### 4. Domain Manajemen Kontrak
- **Rental**: Kontrak sewa dengan durasi, sewa bulanan, deposit

### 5. Domain Manajemen Keuangan
- **Invoice**: Tagihan bulanan dengan pelacakan pembayaran
- **Payment**: Transaksi pembayaran dengan verifikasi admin

### 6. Domain Komunikasi
- **CommunicationLog**: Kelas dasar abstrak
- **NotificationLog**: Notifikasi WhatsApp real-time
- **ReminderLog**: Pengingat tagihan terjadwal

---

## 🔄 Alur Bisnis Utama

```
Pengguna (Akun)
    ↓
Penyewa (Profil)
    ↓
Kontrak Sewa (Rental)
    ↓
Tagihan (Invoice)
    ↓
Pembayaran (Payment)
    ↓
Log Komunikasi
├─ Notifikasi
└─ Pengingat
```

---

## 📊 Hubungan Kunci

| Dari | Ke | Tipe | Kardinalitas |
|------|-----|------|-------------|
| User | Role | Asosiasi | M:M |
| Role | Permission | Asosiasi | M:M |
| Room | RoomPhoto | Komposisi | 1:N |
| Room | Facility | Asosiasi | M:M |
| Tenant | Rental | Asosiasi | 1:N |
| Room | Rental | Asosiasi | 1:N |
| Rental | Invoice | Asosiasi | 1:N |
| Invoice | Payment | Asosiasi | 1:N |
| CommunicationLog | NotificationLog | Inheritance | - |
| CommunicationLog | ReminderLog | Inheritance | - |
