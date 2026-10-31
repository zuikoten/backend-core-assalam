# Master Context - Backend Sahabat Assalaam

## 📋 Overview

**Project:** Sistem Backend Assalaam (Modular: Agent Management System, Tour & Travel, Booking)
**Framework:** Laravel 12
**PHP Version:** 8.2
**Database:** MySQL (development) / MySQL (production)
**Authentication:** Laravel Sanctum (Token-based API Auth)

---

## 🎯 Main Features

### 1. User Management System

- **User Registration & Login** — Email/password authentication
- **Role-based Access** — 5 roles: super_admin, admin, staff, agent, jamaah
- **Multi-role per User** — One user can have multiple roles via user_roles pivot table
- **Profile Management** — User profile with complete biodata

### 2. Core Agent System (Sahabat Assalaam)

- **Agent Registration** — 5-step registration process
    - Step 1: Basic info (email, phone, password, full_name, NIK, birth_date, gender, job)
    - Step 2: Upload documents (KTP photo, selfie with KTP)
    - Step 3: Update address
    - Step 4: Bank account details
    - Step 5: Upload payment proof
- **Agent Levels** — cabang, premium
- **Upline System** — Hierarchical agent structure (referral)
- **Agent Status** — pending, active, inactive, rejected

### 3. Agent Payment System

- **Registration Fee** — Configurable per agent level
- **Payment Verification** — Admin verification of payment proof
- **Payment Status Tracking**

### 4. Agent Bonus System

- **Bonus Transactions** — Calculate and track agent bonuses (fixed_fee, coaching_fee, region_bonus)
- **Hierarchical Bonus** — Based on downline agents
- **Bonus Payouts** — Pencairan bonus ke rekening agen

### 5. Pilgrim Profile (Jamaah)

- **PilgrimProfile** — Data jamaah terpusat, linked ke user dan agen
- Dipakai sebagai sumber prefill data passenger saat booking
- Dikelola oleh agen (input_by) atau admin

### 6. Trip Management

- **Trip Catalog** — CRUD trip (fixed & recurring), kategori, destinasi, aktivitas
- **Trip Packages** — Paket kamar (single/double/triple/quad) dengan harga per pricing category
- **Trip Batches** — Jadwal keberangkatan untuk trip recurring
- **Master Data** — trip_categories, trip_category_services, destinations, activities, difficulties, pricing_categories

### 7. Booking System

- **Booking Mandiri** — Customer booking langsung (tanpa agen), multi-pax
- **Booking via Agen** — Agen booking untuk jamaah, selalu 1 pax, trigger bonus otomatis
- **Kuota Management** — Cek dan update kuota trip/batch secara dinamis
- **Payment Booking** — Upload bukti bayar, konfirmasi admin, trigger bonus agen
- **Document Tracking** — Status dokumen per passenger, sinkron ke booking induk

### 8. Region/Wilayah Data

- **Indonesian Regions** — Province, Regency/City, District (via tabel `regions` internal)

### 9. OTP Verification

- **Password Reset** — OTP via WhatsApp (simulated)
- **Email Verification** — OTP via email (simulated)

### 10. Admin Dashboard

- **Pending Agents** — List of agents waiting verification
- **Document Verification** — Verify agent documents
- **Payment Verification** — Verify payments
- **Statistics** — Dashboard stats

---

## 🗂️ Database Structure

### Main Tables

| Table | Description |
|---|---|
| users | User accounts (email, phone, password, status) |
| roles | Available roles (super_admin, admin, staff, agent, jamaah) |
| user_roles | Pivot table for user-role many-to-many |
| user_profiles | Extended user biodata |
| staff | Staff internal data (position, department, work_status) |
| password_histories | Track password changes |
| regions | Wilayah Indonesia internal (province, regency, district) |
| otp_codes | OTP verification codes |
| personal_access_tokens | API tokens (Sanctum) |
| admin_actions | Admin activity log |
| settings | App configuration |
| **— AgentSystem —** | |
| agents | Agent data (linked to users, upline, level) |
| agent_addresses | Agent addresses (linked to regions) |
| agent_documents | KTP & payment documents |
| agent_bank_accounts | Bank accounts |
| agent_payments | Payment records for agent registration fee |
| agent_bonus_transactions | Bonus history (fixed_fee, coaching_fee, region_bonus) |
| agent_bonus_payouts | Pencairan bonus ke rekening agen |
| **— Pilgrim —** | |
| pilgrim_profiles | Data jamaah terpusat (NIK, passport, foto, linked ke agent & user) |
| **— Trip —** | |
| trip_categories | Kategori besar trip (Umroh, Haji, Wisata Halal) |
| trip_category_services | Variasi per kategori (Umroh VIP, Umroh Plus, Haji Furoda, dll) |
| destinations | Master destinasi (Arab Saudi, Turki, Bali, dll) |
| activities | Master aktivitas (City Tour, Ibadah, Snorkeling, dll) |
| difficulties | Master tingkat kesulitan (Easy, Moderate, Hard) |
| pricing_categories | Master kategori harga (Dewasa, Anak, Lansia, Infant) |
| trips | Entitas trip utama (fixed & recurring) |
| trip_itineraries | Rencana perjalanan per hari (rich text, sortable) |
| trip_packages | Paket kamar per trip (single/double/triple/quad) |
| trip_package_prices | Harga per paket per pricing category (dengan sale price) |
| trip_batches | Jadwal keberangkatan untuk trip recurring |
| trip_destination | Pivot trips ↔ destinations |
| trip_activity | Pivot trips ↔ activities |
| trip_difficulty | Pivot trips ↔ difficulties |
| **— Booking —** | |
| bookings | Header booking (linked ke trip, batch, package, user, agent) |
| booking_passengers | Detail passenger per booking (data diri + dokumen + price snapshot) |
| booking_payments | Pembayaran booking (1 booking = 1 payment, full payment di awal) |

### Relationships

```
users (1) ─── (1) user_profiles
     │
     ├── (1) staff
     │
     ├── (1) agents
     │         ├── (M) agent_addresses
     │         ├── (M) agent_documents
     │         ├── (M) agent_bank_accounts
     │         ├── (M) agent_payments
     │         ├── (M) agent_bonus_transactions
     │         └── (M) agent_bonus_payouts
     │
     ├── (1) pilgrim_profiles
     │
     └── (M) user_roles ─── (1) roles

trips (1) ─── (M) trip_itineraries
        │
        ├── (M) trip_packages ─── (M) trip_package_prices
        │
        ├── (M) trip_batches
        │
        ├── (M) trip_destination ─── destinations
        ├── (M) trip_activity    ─── activities
        └── (M) trip_difficulty  ─── difficulties

bookings (1) ─── (M) booking_passengers ─── (1) pilgrim_profiles [nullable]
          │
          └── (1) booking_payments

bookings ─── trips           [cross-module, no FK constraint]
bookings ─── trip_batches    [cross-module, no FK constraint]
bookings ─── trip_packages   [cross-module, no FK constraint]

booking_passengers ─── pricing_categories  [cross-module, no FK constraint]

agent_bonus_transactions ─── bookings      [cross-module, no FK constraint]
```

> **Catatan Cross-Module:** Relasi antara Booking ↔ Trip tidak menggunakan DB foreign key constraint. Validasi existence dilakukan di application layer (BookingService). Komunikasi antar modul menggunakan Laravel Events.

---

## 🔌 API Endpoints

### Public Routes (No Authentication)

| Method | Endpoint | Description |
|---|---|---|
| POST | /api/login | User login |
| POST | /api/forgot-password | Request password reset OTP |
| POST | /api/verify-otp | Verify OTP code |
| POST | /api/reset-password | Reset password with token |
| GET | /api/settings/registration-fee | Get registration fee |
| GET | /api/regions/provinces | Get all provinces |
| GET | /api/regions/regencies/{id} | Get cities by province |
| GET | /api/regions/districts/{id} | Get districts by city |
| POST | /api/agents/register | Register new agent (Step 1) |

### Protected Routes (auth:sanctum)

| Method | Endpoint | Description |
|---|---|---|
| POST | /api/logout | Logout (delete token) |
| GET | /api/me | Get current user info |
| POST | /api/email/send-otp | Send email verification OTP |
| POST | /api/email/verify | Verify email with OTP |
| GET | /api/agents/{id}/status | Get agent status |
| POST | /api/agents/{id}/documents | Upload documents (Step 2) |
| PUT | /api/agents/{id}/address | Update address (Step 3) |
| PUT | /api/agents/{id}/bank | Update bank account (Step 4) |
| POST | /api/agents/{id}/payment | Upload payment proof (Step 5) |

### Admin Routes (auth:sanctum + admin middleware)

| Method | Endpoint | Description |
|---|---|---|
| GET | /api/admin/dashboard | Dashboard statistics |
| GET | /api/admin/pending-agents | List pending agents |
| PUT | /api/admin/documents/{id}/verify | Verify document |
| PUT | /api/admin/payments/{id}/verify | Verify payment |
| PUT | /api/admin/agents/{id}/status | Update agent status |

> **Catatan:** Routes untuk modul **Trip** dan **Booking** belum diimplementasikan (file routes masih kosong). Endpoint akan ditambahkan seiring pengembangan modul tersebut.

---

## 🔐 Authentication

- **Method:** Laravel Sanctum (Token-based)
- **Token Storage:** personal_access_tokens table
- **Middleware:** auth:sanctum
- **Admin Check:** `isAdmin()` method — checks user_roles for 'admin' or 'super_admin' role

---

## 👤 Default Users (Seeded)

| Role | Email | Password |
|---|---|---|
| Super Admin | superadmin@sahabatassalaam.com | superadmin123 |
| Admin | admin@sahabatassalaam.com | admin123 |

---

## 🏗️ Project Structure

```
backend/
├── app/
│   ├── Enums/
│   │   └── RegionType.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Controller.php
│   │   └── Middleware/
│   │       └── EnsureUserIsAdmin.php
│   ├── Models/                         ← Domain model utama (lintas modul)
│   │   ├── AdminAction.php
│   │   ├── OtpCode.php
│   │   ├── PasswordHistory.php
│   │   ├── PilgrimProfile.php          ← Model jamaah terpusat (menggantikan Pilgrim lama)
│   │   ├── Region.php
│   │   ├── Role.php
│   │   ├── Setting.php
│   │   ├── Staff.php
│   │   ├── User.php
│   │   └── UserProfile.php
│   └── Modules/
│       ├── AgentSystem/
│       │   ├── Controllers/
│       │   │   ├── AdminController.php
│       │   │   ├── AgentController.php
│       │   │   ├── AuthController.php
│       │   │   └── BonusController.php
│       │   ├── Models/
│       │   │   ├── Agent.php
│       │   │   ├── AgentAddress.php
│       │   │   ├── AgentBankAccount.php
│       │   │   ├── AgentBonusPayout.php
│       │   │   ├── AgentDocument.php
│       │   │   ├── BonusTransaction.php
│       │   │   └── Payment.php
│       │   │   ← Catatan: Model Pilgrim.php sudah dihapus.
│       │   │     Jamaah kini direpresentasikan oleh PilgrimProfile di app/Models/
│       │   ├── Providers/
│       │   │   └── AgentSystemServiceProvider.php
│       │   ├── Requests/
│       │   │   ├── RegisterAgentRequest.php
│       │   │   ├── UpdateAgentAddressRequest.php
│       │   │   ├── UpdateAgentBankRequest.php
│       │   │   ├── UploadDocumentsRequest.php
│       │   │   ├── VerifyDocumentRequest.php
│       │   │   └── VerifyPaymentRequest.php
│       │   ├── Resources/
│       │   │   ├── AgentAddressResource.php
│       │   │   └── AgentResource.php
│       │   ├── Routes/
│       │   │   └── api.php
│       │   └── Services/
│       │       └── GenerateBonusService.php    ← Dipindah dari app/Services/ ke sini
│       │
│       ├── Booking/
│       │   ├── Controllers/
│       │   │   ├── BookingController.php
│       │   │   └── BookingPaymentController.php
│       │   ├── Events/
│       │   │   ├── BookingConfirmed.php
│       │   │   ├── TripBatchQuotaFull.php
│       │   │   └── TripQuotaFull.php
│       │   ├── Listeners/
│       │   │   ├── OnBookingConfirmed.php
│       │   │   ├── OnTripBatchStatusChanged.php
│       │   │   └── OnTripStatusChanged.php
│       │   ├── Models/
│       │   │   ├── Booking.php
│       │   │   ├── BookingPassenger.php
│       │   │   └── BookingPayment.php
│       │   ├── Observers/
│       │   │   ├── BookingObserver.php
│       │   │   └── BookingPassengerObserver.php
│       │   ├── Providers/
│       │   │   └── BookingServiceProvider.php
│       │   ├── Routes/
│       │   │   └── api.php                     ← Belum diimplementasikan
│       │   └── Services/
│       │       ├── BookingPaymentService.php
│       │       ├── BookingService.php
│       │       └── KuotaCheckerService.php
│       │
│       └── Trip/
│           ├── Controllers/
│           │   ├── TripController.php
│           │   ├── TripBatchController.php
│           │   ├── TripPackageController.php
│           │   └── MasterData/
│           │       ├── TripCategoryController.php
│           │       ├── TripCategoryServiceController.php
│           │       ├── DestinationController.php
│           │       ├── ActivityController.php
│           │       ├── DifficultyController.php
│           │       └── PricingCategoryController.php
│           ├── Events/
│           │   ├── TripBatchStatusChanged.php
│           │   └── TripStatusChanged.php
│           ├── Listeners/
│           │   ├── OnTripBatchQuotaFull.php
│           │   └── OnTripQuotaFull.php
│           ├── Models/
│           │   ├── Activity.php
│           │   ├── Destination.php
│           │   ├── Difficulty.php
│           │   ├── PricingCategory.php
│           │   ├── Trip.php
│           │   ├── TripBatch.php
│           │   ├── TripCategory.php
│           │   ├── TripCategoryService.php     ← Model Eloquent untuk tabel trip_category_services
│           │   ├── TripItinerary.php
│           │   ├── TripPackage.php
│           │   └── TripPackagePrice.php
│           ├── Observers/
│           │   ├── TripBatchObserver.php
│           │   └── TripObserver.php
│           ├── Providers/
│           │   └── TripServiceProvider.php
│           ├── Routes/
│           │   └── api.php                     ← Belum diimplementasikan
│           └── Services/
│               ├── TripService.php
│               └── TripStatusService.php
│
├── bootstrap/
├── config/
├── database/
│   ├── migrations/
│   └── seeders/
├── public/
├── resources/
├── routes/
├── storage/
└── tests/
```

---

## 🔧 Key Models

### User Model (`app/Models/User.php`)

```php
// Relationships
$user->agent()       → HasOne(Agent::class)
$user->roles()       → BelongsToMany(Role::class)
$user->profile()     → HasOne(UserProfile::class)
$user->staff()       → HasOne(Staff::class)
$user->pilgrimProfile() → HasOne(PilgrimProfile::class)

// Methods
$user->isAdmin()        → bool
$user->isSuperAdmin()   → bool
$user->isStaff()        → bool
$user->isAgent()        → bool
$user->hasRole('admin') → bool
$user->getRoleNames()   → array
$user->assignRole()     → self
$user->removeRole()     → self
```

### PilgrimProfile Model (`app/Models/PilgrimProfile.php`)

```php
// Model jamaah terpusat — menggantikan model Pilgrim lama di AgentSystem
// Relationships
$profile->user()   → BelongsTo(User::class)
$profile->agent()  → BelongsTo(Agent::class)   // agen yang mendaftarkan
```

### Agent Model (`app/Modules/AgentSystem/Models/Agent.php`)

```php
$agent->user()              → BelongsTo(User::class)
$agent->upline()            → BelongsTo(Agent::class)
$agent->downlines()         → HasMany(Agent::class)
$agent->addresses()         → HasMany(AgentAddress::class)
$agent->documents()         → HasMany(AgentDocument::class)
$agent->bankAccounts()      → HasMany(AgentBankAccount::class)
$agent->payments()          → HasMany(Payment::class)
$agent->bonusTransactions() → HasMany(BonusTransaction::class)
$agent->bonusPayouts()      → HasMany(AgentBonusPayout::class)
```

### Trip Model (`app/Modules/Trip/Models/Trip.php`)

```php
$trip->category()       → BelongsTo(TripCategory::class)
$trip->categoryService()→ BelongsTo(TripCategoryService::class)
$trip->teamLeader()     → BelongsTo(Staff::class)
$trip->itineraries()    → HasMany(TripItinerary::class)   // ordered by sort_order
$trip->packages()       → HasMany(TripPackage::class)
$trip->batches()        → HasMany(TripBatch::class)
$trip->destinations()   → BelongsToMany(Destination::class)
$trip->activities()     → BelongsToMany(Activity::class)
$trip->difficulties()   → BelongsToMany(Difficulty::class)

// Scopes
Trip::published()
Trip::byCategory($categoryId)
Trip::bySchedule('fixed'|'recurring'|'other')
Trip::available()
```

### Booking Model (`app/Modules/Booking/Models/Booking.php`)

```php
$booking->user()       → BelongsTo(User::class)
$booking->agent()      → BelongsTo(Agent::class)   // nullable
$booking->passengers() → HasMany(BookingPassenger::class)
$booking->payment()    → HasOne(BookingPayment::class)

// Scopes
Booking::byTrip($tripId)
Booking::byBatch($batchId)
Booking::active()

// Catatan: trip_id, trip_batch_id, package_id adalah integer biasa
// tanpa FK constraint — validasi di BookingService
```

---

## ⚡ Event-Driven Architecture (Cross-Module)

Komunikasi antar modul menggunakan Laravel Events — tidak ada import model langsung antar modul.

```
EVENT                    │ DIKIRIM OLEH  │ DIDENGAR OLEH
─────────────────────────┼───────────────┼──────────────────────────────────
BookingConfirmed         │ Booking       │ AgentSystem → GenerateBonusService
                         │               │ Booking     → KuotaCheckerService
TripQuotaFull            │ Booking       │ Trip        → set trips.status = 'full'
TripBatchQuotaFull       │ Booking       │ Trip        → set trip_batches.status = 'full'
TripStatusChanged        │ Trip          │ Booking     → cascade status ke bookings
TripBatchStatusChanged   │ Trip          │ Booking     → cascade status ke bookings
```

**Aturan cross-module:**
- ✅ Simpan `trip_id`, `trip_batch_id`, `package_id` sebagai integer (tanpa DB FK constraint)
- ✅ Validasi existence via service layer sebelum insert
- ✅ Komunikasi antar modul via Events & Listeners
- ❌ Dilarang: `use App\Modules\Trip\Models\Trip` di dalam Booking module
- ❌ Dilarang: Query langsung ke tabel modul lain dari controller/service modul berbeda

---

## 📦 Packages Used

- **laravel/framework:** ^12.0
- **laravel/sanctum:** ^4.0
- **laravolt/indonesia:** ^0.41 — data wilayah (digunakan sebagai referensi, akan dihapus)
- **phpunit/phpunit:** ^12.0

---

## 🚀 Running the Application

```bash
composer install
php artisan migrate
php artisan db:seed
php artisan serve
php artisan storage:link
```

---

## 📝 Notes

- OTP untuk password reset dan email verification masih disimulasikan (logged ke console)
- Data wilayah Indonesia menggunakan tabel `regions` internal (`app/Models/Region.php`). Package `laravolt/indonesia` masih terpasang namun akan dihapus ke depannya.
- Registrasi agen adalah proses 5 langkah berurutan
- Routes modul Trip dan Booking belum diimplementasikan; endpoint akan ditambahkan bertahap
- Model `Pilgrim` di AgentSystem sudah dihapus; jamaah direpresentasikan sepenuhnya oleh `PilgrimProfile` di `app/Models/`
- `GenerateBonusService` sudah dipindah dari `app/Services/` ke `app/Modules/AgentSystem/Services/` karena logika bisnisnya milik AgentSystem
- `PilgrimProfileObserver` di `app/Observers/` sudah dihapus dari direktori

---

## 📅 Last Updated

Diperbarui untuk mencerminkan kondisi aktual project: modul Trip dan Booking sudah aktif (bukan lagi "future"), struktur direktori final, dan konsolidasi model Jamaah ke PilgrimProfile.
