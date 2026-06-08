# SiManKos - Class Diagram

## Mermaid Class Diagram

```mermaid
classDiagram
    %% ============ USER MANAGEMENT DOMAIN ============
    class User {
        -id: int
        -name: string
        -email: string
        -password: string
        -phone: string
        -whatsappNumber: string
        -status: UserStatus
        +isAdmin() bool
        +isPenghuni() bool
        +hasPermission(name: string) bool
    }
    
    class Role {
        -id: int
        -name: string
        -guardName: string
        +assignPermission(p: Permission) void
        +revokePermission(p: Permission) void
    }
    
    class Permission {
        -id: int
        -name: string
        -guardName: string
    }
    
    %% ============ ROOM MANAGEMENT DOMAIN ============
    class Room {
        -id: int
        -roomNumber: string
        -type: RoomType
        -price: decimal
        -area: decimal
        -floor: int
        -status: RoomStatus
        -description: string
        +isAvailable() bool
        +getPrimaryPhoto() RoomPhoto
    }
    
    class Facility {
        -id: int
        -name: string
        -icon: string
    }
    
    class RoomFacility {
        -id: int
        -quantity: int
        -installDate: date
    }
    
    class RoomPhoto {
        -id: int
        -photoPath: string
        -isPrimary: bool
        +getUrl() string
        +setPrimary() void
    }
    
    %% ============ TENANT MANAGEMENT DOMAIN ============
    class Tenant {
        -id: int
        -fullName: string
        -nik: string
        -whatsappNumber: string
        -address: string
        -emergencyContact: string
        -emergencyPhone: string
        -notifyViaWhatsapp: bool
        -notifyViaEmail: bool
        -status: TenantStatus
        -notes: string
        +getActiveRental() Rental
        +isActive() bool
    }
    
    %% ============ CONTRACT MANAGEMENT DOMAIN ============
    class Rental {
        -id: int
        -startDate: date
        -endDate: date
        -monthlyRent: decimal
        -deposit: decimal
        -method: RentalMethod
        -status: RentalStatus
        -dueDateDay: int
        -notes: string
        +isActive() bool
        +terminate() void
        +generateMonthlyInvoice() Invoice
    }
    
    %% ============ FINANCIAL MANAGEMENT DOMAIN ============
    class Invoice {
        -id: int
        -invoiceNumber: string
        -year: int
        -month: int
        -rentAmount: decimal
        -extraCharges: decimal
        -total: decimal
        -dueDate: date
        -paidDate: date
        -status: InvoiceStatus
        -paymentGateway: string
        +isPaid() bool
        +isOverdue() bool
        +sendReminder() void
        +markAsPaid() void
    }
    
    class Payment {
        -id: int
        -amount: decimal
        -paymentDate: date
        -paymentMethod: string
        -proofOfPayment: string
        -status: PaymentStatus
        -notes: string
        -verifiedAt: datetime
        +verify(verifiedBy: User) void
        +reject() void
    }
    
    %% ============ COMMUNICATION DOMAIN ============
    class CommunicationLog {
        <<abstract>>
        -status: CommunicationStatus
        -message: string
        +send()* void
    }
    
    class NotificationLog {
        -id: int
        -whatsappNumber: string
        -type: string
        -gatewayResponse: string
        -sentAt: datetime
        -isRead: bool
        +send() void
    }
    
    class ReminderLog {
        -id: int
        -channel: string
        -type: string
        -recipient: string
        -response: string
        +send() void
    }
    
    %% ============ SYSTEM DOMAIN ============
    class Setting {
        -id: int
        -key: string
        -value: string
        -type: SettingType
        -group: string
        -description: string
    }
    
    %% ============ ENUMS / VALUE OBJECTS ============
    class UserStatus {
        <<enumeration>>
        ACTIVE
        INACTIVE
        SUSPENDED
    }
    
    class RoomType {
        <<enumeration>>
        BASIC
        STANDARD
        DELUXE
    }
    
    class RoomStatus {
        <<enumeration>>
        AVAILABLE
        OCCUPIED
        MAINTENANCE
        RESERVED
    }
    
    class TenantStatus {
        <<enumeration>>
        ACTIVE
        INACTIVE
        BLACKLISTED
    }
    
    class RentalMethod {
        <<enumeration>>
        MONTHLY
        QUARTERLY
        ANNUAL
    }
    
    class RentalStatus {
        <<enumeration>>
        ACTIVE
        EXPIRED
        TERMINATED
        CANCELLED
    }
    
    class InvoiceStatus {
        <<enumeration>>
        DRAFT
        SENT
        PAID
        OVERDUE
        PARTIAL
    }
    
    class PaymentStatus {
        <<enumeration>>
        PENDING
        VERIFIED
        REJECTED
    }
    
    class CommunicationStatus {
        <<enumeration>>
        PENDING
        SENT
        FAILED
        DELIVERED
    }
    
    class SettingType {
        <<enumeration>>
        STRING
        INTEGER
        DECIMAL
        BOOLEAN
    }
    
    %% ============ RELATIONSHIPS ============
    
    %% User - Role - Permission (RBAC Spatie)
    User "0..*" --> "0..*" Role : has
    Role "0..*" --> "0..*" Permission : grants
    User --> UserStatus : "has"
    
    %% User - Tenant
    User "1" --> "0..1" Tenant : "has profile"
    
    %% Room Management
    Room "1" *-- "0..*" RoomPhoto : "contains"
    Room "0..*" --> "0..*" Facility : "0..*" RoomFacility : "equipped with"
    Room --> RoomType : "is"
    Room --> RoomStatus : "has"
    
    %% Contract Management
    Tenant "1" --> "0..*" Rental : "signs"
    Room "1" --> "0..*" Rental : "occupied by"
    Rental --> RentalMethod : "uses"
    Rental --> RentalStatus : "has"
    
    %% Financial Management
    Rental "1" --> "0..*" Invoice : "generates"
    Invoice "1" --> "0..*" Payment : "settled by"
    User "1" --> "0..*" Payment : "verifies"
    Invoice --> InvoiceStatus : "has"
    Payment --> PaymentStatus : "has"
    
    %% Communication Management - Inheritance
    CommunicationLog <|-- NotificationLog : extends
    CommunicationLog <|-- ReminderLog : extends
    CommunicationLog --> CommunicationStatus : "has"
    
    %% Tenant - Communication Logs
    Tenant "1" --> "0..*" NotificationLog : "receives"
    Tenant "1" --> "0..*" ReminderLog : "receives"
    Invoice "1" --> "0..*" ReminderLog : "triggers"
    Payment "0..1" --> "0..*" NotificationLog : "referenced by"
    
    %% System
    Setting --> SettingType : "is"
```

## 📊 Struktur Domain

```
SiManKos System Architecture
│
├─ User Management
│  ├─ User (dengan RBAC)
│  ├─ Role (Spatie Permission)
│  └─ Permission
│
├─ Room Management
│  ├─ Room (Kamar kos)
│  ├─ Facility (Fasilitas)
│  └─ RoomPhoto (Foto kamar)
│
├─ Tenant Management
│  └─ Tenant (Profil penyewa)
│
├─ Contract Management
│  └─ Rental (Kontrak sewa)
│
├─ Financial Management
│  ├─ Invoice (Tagihan)
│  └─ Payment (Pembayaran)
│
└─ Communication Management
   ├─ CommunicationLog (Abstract)
   ├─ NotificationLog (WhatsApp)
   └─ ReminderLog (Reminder)
```

## 🔄 Alur Bisnis Utama

```
User (Penghuni)
    ↓
Tenant (Profil Penyewa)
    ↓
Rental (Kontrak Sewa)
    ↓
Invoice (Tagihan Bulanan)
    ↓
Payment (Pembayaran)
    ↓
CommunicationLog
├─ NotificationLog (Konfirmasi pembayaran)
└─ ReminderLog (Pengingat tagihan)
```

## 📋 Penjelasan Relasi Kunci

### 1. **User - Role - Permission** (RBAC Spatie)
- **Kardinalitas**: Many-to-Many
- **Tujuan**: Implementasi role-based access control
- **Contoh**: Admin memiliki role ADMIN dengan permission CREATE_USER, DELETE_USER, dll

### 2. **Room - RoomPhoto** (Composition)
- **Kardinalitas**: 1 to Many
- **Tujuan**: Foto kamar merupakan bagian integral dari room
- **Behavior**: Jika room dihapus, semua foto-nya dihapus otomatis

### 3. **Rental - Invoice** (Aggregation)
- **Kardinalitas**: 1 to Many
- **Tujuan**: Satu kontrak menghasilkan multiple invoice (bulanan)
- **Method**: `Rental.generateMonthlyInvoice()` membuat invoice baru setiap bulan

### 4. **CommunicationLog (Inheritance)** ⭐
- **NotificationLog extends CommunicationLog**: Untuk notifikasi WhatsApp real-time
- **ReminderLog extends CommunicationLog**: Untuk pengingat tagihan terjadwal
- **Polymorphic Behavior**: Method `send()` di-override dengan logic berbeda untuk setiap subclass

### 5. **Invoice - Payment** (Partial Payment)
- **Kardinalitas**: 1 to Many
- **Tujuan**: Satu invoice bisa dibayar dalam beberapa transaksi
- **Contoh**: Invoice 500K bisa dibayar 250K + 250K

## 🎯 Desain Pattern yang Diterapkan

| Pattern | Implementasi |
|---------|-------------|
| **Inheritance** | CommunicationLog ← NotificationLog, ReminderLog |
| **Polymorphism** | Method `send()` di-override per subclass |
| **Composition** | Room ← RoomPhoto (strong aggregation) |
| **Aggregation** | Rental ← Invoice, Payment |
| **Repository** | User, Tenant, Room, Invoice, Payment (via Laravel ORM) |

---

**Diagram dibuat dengan Mermaid.js** - Dapat dilihat di:
- GitHub (preview otomatis)
- VS Code (dengan extension Markdown Preview Mermaid)
- Online: https://mermaid.live
