# Sahabat Assalam - Backend Summary

> Dokumen ringkasan lengkap mengenai status pengembangan backend project **Sahabat Assalam** (Sistem Registrasi Agen Travel Haji/Umrah).
> Dibuat: Auto-generated dari analisis codebase.

---

## 1. Project Overview

| Item             | Detail                                                 |
| ---------------- | ------------------------------------------------------ |
| **Nama Project** | Sahabat Assalam                                        |
| **Tujuan**       | Sistem registrasi dan manajemen agen travel Haji/Umrah |
| **Framework**    | Laravel 12 (PHP ^8.2)                                  |
| **Auth**         | Laravel Sanctum (Token-based API untuk React SPA)      |
| **DB**           | MySQL                                                  |
| **Storage**      | Local `storage/app/public` (dokumen & bukti transfer)  |
| **Pattern**      | API-only backend dengan Resource/Request classes       |

---

## 2. Tech Stack & Dependencies

```json
"php": "^8.2"
"laravel/framework": "^12.0"
"laravel/sanctum": "^4.3"
"laravel/tinker": "^2.10.1"
"laravolt/indonesia": "^0.41.0"
```

```json
"@tailwindcss/vite": "^4.0.0"
"axios": "^1.11.0"
"laravel-vite-plugin": "^2.0.0"
"tailwindcss": "^4.0.0"
"vite": "^7.0.7"
```

- **Laravel 12** dengan struktur skeleton modern (`bootstrap/app.php` config-based)
- **Sanctum** untuk autentikasi API (pure Bearer token — **tanpa stateful CSRF session cookie**)
- **Laravolt Indonesia** untuk master data wilayah lengkap (provinsi, kabupaten, kecamatan)
- **Enum PHP 8.1+** untuk tipe region
- **Eloquent Resources** untuk transformasi response API
- **Form Requests** untuk validasi terpisah
- **Database Transactions** pada proses registrasi agen
- **Tailwind CSS v4** + **Vite 7** untuk frontend build tooling dengan proxy dev server

---

## 3. Database Schema (Migrations)

| No  | Tabel                    | Status | Keterangan                                                          |
| --- | ------------------------ | ------ | ------------------------------------------------------------------- |
| 1   | `cache`                  | ✅     | Laravel default                                                     |
| 2   | `jobs`                   | ✅     | Laravel default                                                     |
| 3   | `regions`                | ✅     | Master data wilayah (provinsi, kabupaten, kecamatan)                |
| 4   | `users`                  | ✅     | Data login: email, phone, whatsapp, password, role, status          |
| 5   | `agents`                 | ✅     | Data agen: level, kode agen, NIK, biodata, upline, biaya registrasi |
| 6   | `agent_addresses`        | ✅     | Alamat agen dengan relasi ke regions                                |
| 7   | `agent_documents`        | ✅     | Dokumen: KTP, selfie KTP, bukti transfer + status verifikasi        |
| 8   | `agent_bank_accounts`    | ✅     | Rekening bank agen                                                  |
| 9   | `payments`               | ✅     | Pembayaran: tipe, jumlah, metode, status, relasi dokumen            |
| 10  | `bonus_transactions`     | ✅     | Transaksi bonus per jamaah                                          |
| 11  | `pilgrims`               | ✅     | Data jamaah (relasi ke agent & branch)                              |
| 12  | `admin_actions`          | ✅     | Log aksi admin                                                      |
| 13  | `settings`               | ✅     | Key-value config dengan cache 1 jam                                 |
| 14  | `personal_access_tokens` | ✅     | Sanctum default                                                     |
| 15  | `sessions`               | ✅     | Laravel default (opsional untuk web)                                |
| 16  | `indonesia_provinces`    | ✅     | Laravolt Indonesia — master provinsi                                |
| 17  | `indonesia_cities`       | ✅     | Laravolt Indonesia — master kabupaten/kota                          |
| 18  | `indonesia_districts`    | ✅     | Laravolt Indonesia — master kecamatan                               |
| 19  | `indonesia_villages`     | ✅     | Laravolt Indonesia — master kelurahan/desa                          |

**Relasi Utama:**

```
User 1:1 Agent
Agent 1:N AgentAddress, AgentDocument, AgentBankAccount, Payment, BonusTransaction
Agent N:1 Agent (upline/downline hierarchy)
Payment 1:1 AgentDocument (proof_document_id)
BonusTransaction N:1 Payment
Pilgrim N:1 Agent (agent_id), N:1 Agent (branch_id)
AgentAddress N:1 Region (province, regency, district)
```

---

## 4. Models (Eloquent)

### 4.1 Core Models

| Model              | Fillable                                                                                                    | Casts                                             | Scopes/Helpers                                                                                      |
| ------------------ | ----------------------------------------------------------------------------------------------------------- | ------------------------------------------------- | --------------------------------------------------------------------------------------------------- |
| `User`             | email, phone, whatsapp, password, role, status, ...                                                         | email_verified_at, email_verified, phone_verified | `isAdmin()`, `canRegisterAgent()`, `getAgentOrCreate()`                                             |
| `Agent`            | user*id, agent_level, full_name, nik, birth_date, gender, job, company_name, upline_id, registration_fee*\* | birth_date, registration_fee_amount               | Auto-generate `agent_code` (AGT+uniqid) on boot                                                     |
| `AgentAddress`     | agent_id, province_id, regency_id, district_id, village, full_address, postal_code, is_domicile             | is_domicile                                       | `complete_address` accessor                                                                         |
| `AgentDocument`    | agent_id, document_type, file_path, file_size, mime_type, verification_status, rejection_reason             | file_size                                         | `pending()`, `byType()`, `file_url`, `type_label`. **TYPES**: ktp_photo, selfie_ktp, transfer_proof |
| `AgentBankAccount` | agent_id, bank_name, account_number, account_holder, is_primary                                             | is_primary                                        | `primary()`, `display_account` (masked)                                                             |
| `Payment`          | agent_id, payment_type, amount, payment_method, bank_account, transaction_ref, status, proof_document_id    | amount                                            | `registration()`, `successful()`, `type_label`                                                      |
| `BonusTransaction` | agent_id, pilgrim_count, bonus_type, amount, description, status, payment_id                                | pilgrim_count, amount                             | `paid()`, `byType()`, `type_label`, `total_amount`                                                  |
| `Pilgrim`          | agent_id, branch_id, full_name, passport_number, departure_date, status                                     | departure_date                                    | `completed()`, `byAgent()`                                                                          |
| `Region`           | region_type, code, name, parent_id                                                                          | region_type (Enum)                                | `province()`, `regency()`, `district()`, `full_name`, `parent()`, `children()`                      |
| `Setting`          | setting_key, setting_value, setting_type, description                                                       | setting_value (array)                             | `Setting::get($key)`, `Setting::set($key, $value)` — **cached 1 jam**                               |
| `AdminAction`      | admin_id, agent_id, action_type, description, old_values, new_values                                        | old_values, new_values (array)                    | —                                                                                                   |

### 4.2 Enum

```php
enum RegionType: string {
    PROVINCE = 'province';    // Provinsi
    REGENCY = 'regency';      // Kabupaten/Kota
    DISTRICT = 'district';    // Kecamatan
}
```

---

## 5. API Endpoints (Routes)

Base: `/api/...`

### 5.1 Public

| Method | Endpoint                          | Controller                 | Keterangan                                              |
| ------ | --------------------------------- | -------------------------- | ------------------------------------------------------- |
| POST   | `/login`                          | `AuthController@login`     | Login dengan email + password, return token             |
| POST   | `/agents/register`                | `AgentController@register` | **Step 1**: Registrasi agen baru (public, return token) |
| GET    | `/regions/provinces`              | Closure                    | List semua provinsi                                     |
| GET    | `/regions/regencies/{provinceId}` | Closure                    | List kabupaten/kota berdasarkan provinsi                |
| GET    | `/regions/districts/{regencyId}`  | Closure                    | List kecamatan berdasarkan kabupaten                    |

### 5.2 Protected (`auth:sanctum`)

| Method | Endpoint                    | Controller                           | Keterangan                        |
| ------ | --------------------------- | ------------------------------------ | --------------------------------- |
| POST   | `/logout`                   | `AuthController@logout`              | Hapus current token               |
| GET    | `/me`                       | `AuthController@me`                  | Data user + agent                 |
| GET    | `/agents/{agent}/status`    | `AgentController@status`             | Cek status lengkap agen           |
| POST   | `/agents/{agent}/documents` | `AgentController@uploadDocuments`    | **Step 2**: Upload KTP + Selfie   |
| PUT    | `/agents/{agent}/address`   | `AgentController@updateAddress`      | **Step 3**: Update alamat         |
| PUT    | `/agents/{agent}/bank`      | `AgentController@updateBank`         | **Step 4**: Update rekening bank  |
| POST   | `/agents/{agent}/payment`   | `AgentController@uploadPaymentProof` | **Step 5**: Upload bukti transfer |

> **Flow Auth Baru:** Step 1 (register) tidak memerlukan auth. Setelah register, backend mengembalikan `token` Sanctum. Token disimpan di `localStorage` frontend dan digunakan sebagai Bearer token untuk Steps 2–5.

### 5.3 Admin Only (`auth:sanctum` + `admin` middleware)

| Method | Endpoint                             | Controller                          | Keterangan                              |
| ------ | ------------------------------------ | ----------------------------------- | --------------------------------------- |
| GET    | `/admin/dashboard`                   | `AdminController@dashboard`         | Statistik dashboard                     |
| GET    | `/admin/pending-agents`              | `AdminController@pendingAgents`     | List agen pending + pagination + search |
| PUT    | `/admin/documents/{document}/verify` | `AdminController@verifyDocuments`   | Verifikasi dokumen (approve/reject)     |
| PUT    | `/admin/payments/{payment}/verify`   | `AdminController@verifyPayment`     | Verifikasi pembayaran (success/failed)  |
| PUT    | `/admin/agents/{agent}/status`       | `AdminController@updateAgentStatus` | Approve/reject agen manual              |

> **Catatan:** `agentReports` ada di `AdminController` tapi **belum di-route**.

---

## 6. Controllers & Business Logic

### 6.1 `AuthController`

- ✅ `login()` — Validasi email/password, generate Sanctum token
- ✅ `me()` — Return user dengan relasi agent
- ✅ `logout()` — Delete current access token

### 6.2 `AgentController` — 5 Step Registration Flow

- ✅ `register(RegisterAgentRequest)` — Step 1 (**PUBLIC — no auth required**)
    - Buat User (role: pending, status: pending)
    - Generate agent code otomatis (`AGT` + uniqid)
    - Support upline via `upline_agent_code`
    - Ambil biaya registrasi dari Setting (`registration_fee_{level}`)
    - Wrap dalam DB transaction
    - **Return Sanctum token** agar user bisa lanjut Steps 2–5 tanpa login ulang
- ✅ `uploadDocuments(UploadDocumentsRequest, Agent)` — Step 2
    - Upload KTP & Selfie ke `storage/app/public/agents/{agent_code}`
    - UpdateOrCreate per document_type
- ✅ `updateAddress(UpdateAgentAddressRequest, Agent)` — Step 3
    - Simpan alamat dengan relasi province/regency/district
- ✅ `updateBank(UpdateAgentBankRequest, Agent)` — Step 4
    - Simpan rekening bank (is_primary = true)
- ✅ `uploadPaymentProof(Request, Agent)` — Step 5
    - Upload bukti transfer ke `storage/app/public/payments/{agent_code}`
    - Buat record Payment (status: pending)
    - Update agent `registration_fee_status` → `paid`
- ✅ `status(Agent)` — Return full agent data dengan semua relasi
- ✅ `authorizeForUser()` — Private method untuk memastikan user hanya akses data sendiri (kecuali admin)

### 6.3 `AdminController`

- ✅ `dashboard()` — Statistik:
    - pending_agents, approved_agents
    - pending_payments, pending_documents
    - total_revenue (sum successful registration fees)
    - recent_agents (10 terbaru)
    - recent_payments (5 pending terbaru)
- ✅ `pendingAgents(Request)` — List agen pending dengan:
    - Eager load: user, documents, payments, addresses, upline.user
    - Search by full_name / NIK
    - Pagination 15 per page
- ✅ `verifyDocuments(VerifyDocumentRequest, AgentDocument)` — Verifikasi dokumen
    - Update status + rejection_reason
    - **Auto-activation**: Jika semua dokumen approved + payment verified → user status = active
- ✅ `verifyPayment(VerifyPaymentRequest, Payment)` — Verifikasi pembayaran
    - Update payment status
    - Jika success → update agent fee status = verified
    - **Auto-activation**: Jika semua dokumen approved → user status = active
- ✅ `updateAgentStatus(Request, Agent)` — Manual approve/reject agen
- ⚠️ `agentReports(Request)` — Sudah diimplementasikan tapi **BELUM DI-ROUTE**

### 6.4 `BonusController`

- ⚠️ `calculatePilgrimBonus(Request, Pilgrim)` — **Partially implemented**
    - Fixed fee untuk agent premium (dari Setting)
    - Coaching fee untuk cabang/upline
    - Region bonus: logic placeholder (belum lengkap)
- ✅ `agentBonuses(Agent)` — List bonus transaksi per agen (paginated)

---

## 7. Form Requests (Validation)

| Request                     | Rules                                                                                                                                                                                                               | Keterangan                                                      |
| --------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | --------------------------------------------------------------- |
| `RegisterAgentRequest`      | agent_level (cabang/premium), full_name, nik (16 digit, unique), phone/whatsapp (regex 08...), email (unique), birth_date, gender (L/P), job, company_name, upline_agent_code (exists), password (min 8, confirmed) | Validasi registrasi lengkap dengan pesan error Bahasa Indonesia |
| `UpdateAgentAddressRequest` | province_id, regency_id, district_id (exists di regions dengan tipe sesuai), village, full_address, postal_code (5 digit)                                                                                           | Validasi alamat dengan relasi region                            |
| `UpdateAgentBankRequest`    | bank_name, account_number, account_holder                                                                                                                                                                           | Validasi rekening bank                                          |
| `UploadDocumentsRequest`    | ktp_photo, selfie_ktp (required, image, jpg/png, max 2MB)                                                                                                                                                           | Upload dokumen identitas                                        |
| `VerifyDocumentRequest`     | status (approved/rejected), reason (required if rejected)                                                                                                                                                           | Hanya admin yang bisa authorize                                 |
| `VerifyPaymentRequest`      | status (success/failed)                                                                                                                                                                                             | Hanya admin yang bisa authorize                                 |

---

## 8. API Resources

| Resource               | Field yang Di-transform                                                                                                                                                                                                  |
| ---------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| `AgentResource`        | id, agent_code, agent_id, level, full_name, nik, status, registration_fee_status, registration_fee_amount, upline (nested), downlines_count, address (nested), bank_accounts (when loaded), documents_status, created_at |
| `AgentAddressResource` | complete_address, province, regency, district, village, postal_code                                                                                                                                                      |

---

## 9. Middleware & Security

| Middleware      | Fungsi                                                              |
| --------------- | ------------------------------------------------------------------- |
| `auth:sanctum`  | Proteksi semua route API (kecuali public routes)                    |
| `admin` (alias) | `EnsureUserIsAdmin` — cek `$user->isAdmin()`, return 403 jika bukan |

> **Perubahan Auth:** `EnsureFrontendRequestsAreStateful` **dihapus** dari middleware API global. Tidak ada lagi validasi CSRF session cookie. API menggunakan pure Bearer token.

**Keamanan yang sudah diimplementasikan:**

- ✅ Password hashing dengan `Hash::make()`
- ✅ Authorization per-agent (user hanya bisa akses data sendiri)
- ✅ Admin-only routes dengan double middleware
- ✅ File upload: validasi mime type (jpg/png), max 2MB
- ✅ DB Transaction pada registrasi
- ✅ Form Request validation terpisah
- ✅ Bearer token auth (tidak ada CSRF/session cookie vulnerability)

---

## 10. Seeders

| Seeder             | Data                                                                               |
| ------------------ | ---------------------------------------------------------------------------------- |
| `AdminSeeder`      | User admin: `admin@sahabatassalaam.com` / `admin123`, role=admin, status=active    |
| `RegionSeeder`     | Sample data: Jawa Barat → Bandung → Cibeunying (untuk development/testing)         |
| `RegionFullSeeder` | Import data wilayah Indonesia lengkap dari tabel `laravolt/indonesia` ke `regions` |
| `DatabaseSeeder`   | Memanggil `AdminSeeder`                                                            |

---

## 11. Settings System (Dynamic Config)

Settings disimpan di DB dengan caching 1 jam:

| Key                        | Default   | Digunakan di                                        |
| -------------------------- | --------- | --------------------------------------------------- |
| `registration_fee_cabang`  | 0         | AgentController@register                            |
| `registration_fee_premium` | 0         | AgentController@register                            |
| `fixed_fee_per_pilgrim`    | 2.000.000 | BonusController@calculatePilgrimBonus               |
| `coaching_fee_per_pilgrim` | 100.000   | BonusController@calculatePilgrimBonus               |
| `region_bonus_per_pilgrim` | 25.000    | BonusController@calculatePilgrimBonus (placeholder) |

---

## 12. Status Checklist: Apa yang Sudah Selesai

### ✅ COMPLETED (100%)

- [x] **Autentikasi API** — Login, logout, me dengan Sanctum (pure Bearer token, tanpa CSRF session)
- [x] **Self-Registration Flow** — Register public → return token → Steps 2-5 dengan Bearer token dari `localStorage`
- [x] **Registrasi Agen 5 Step** — Register → Dokumen → Alamat → Bank → Pembayaran
- [x] **Upload File System** — KTP, Selfie, Bukti Transfer dengan storage public
- [x] **Master Data Wilayah** — Region hierarchy (provinsi, kabupaten, kecamatan) dengan Enum
- [x] **Data Wilayah Lengkap** — `RegionFullSeeder` import dari `laravolt/indonesia`
- [x] **Public Region API** — Endpoint provinsi, kabupaten, kecamatan untuk frontend dropdown
- [x] **Verifikasi Admin** — Dokumen & Pembayaran (approve/reject dengan alasan)
- [x] **Auto-activation Agent** — Jika dokumen + payment terverifikasi → status active
- [x] **Dashboard Admin** — Statistik lengkap dengan recent data
- [x] **List Pending Agents** — Dengan search, eager load, pagination
- [x] **Agent Hierarchy** — Upline/downline support
- [x] **Dynamic Settings** — Key-value dengan cache
- [x] **Form Request Validation** — Semua endpoint tervalidasi dengan pesan BI
- [x] **API Resources** — Transformasi response terstruktur (termasuk `bank_accounts`)
- [x] **Middleware Admin** — Proteksi route admin
- [x] **Database Migrations** — 19 tabel lengkap (termasuk laravolt & sessions)
- [x] **Seeders** — Admin + sample region + RegionFullSeeder
- [x] **Frontend Dev Setup** — Vite proxy `/api` & `/storage`, Axios dengan auto Bearer token

### ⚠️ PARTIAL / NEEDS ATTENTION

- [ ] **BonusController::calculatePilgrimBonus** — Region bonus logic masih placeholder
- [ ] **AdminController::agentReports** — Sudah diimplementasikan tapi **belum ada route**
- [ ] **AdminAction logging** — Model sudah ada tapi **belum digunakan** di controller (tidak ada audit trail saat admin melakukan verifikasi)
- [ ] **Email/Phone verification** — Field `email_verified`, `phone_verified` ada di User tapi tidak ada logic/proses verifikasinya
- [ ] **Branch concept** — Pilgrim punya `branch_id` tapi tidak ada definisi jelas branch vs agent biasa

### ❌ NOT YET IMPLEMENTED

- [ ] **Forgot/Reset Password**
- [ ] **Email/OTP Verification**
- [ ] **Queue/Job processing** (tabel jobs ada tapi belum digunakan)
- [ ] **Notification system** (email/SMS ke agen saat status berubah)
- [ ] **Advanced search/filter** pada list agen
- [ ] **Export data** (PDF/Excel)
- [ ] **Rate limiting** pada API
- [ ] **Unit/Feature Tests** (hanya skeleton default)
- [ ] **API Documentation** (Swagger/OpenAPI)

---

## 13. Architecture Notes

```
┌─────────────────────────────────────────────────────────────┐
│              React SPA (Frontend)                           │
│  - Axios: baseURL=/api, withCredentials=false               │
│  - Auto-attach Bearer token dari localStorage               │
│  - Vite proxy: /api → localhost:8000, /storage → localhost  │
└──────────────────────┬──────────────────────────────────────┘
                       │ API (Sanctum Bearer Token)
┌──────────────────────▼──────────────────────────────────────┐
│  Public Routes       │  Protected Routes (auth:sanctum)     │
│  - POST /login       │  - POST /logout                      │
│  - POST /agents/     │  - GET  /me                          │
│    register (return  │  - Agent steps 2-5                   │
│    token)            │  - Admin routes                      │
│  - GET /regions/*    │                                      │
├─────────────────────────────────────────────────────────────┤
│  AuthController  │  AgentController     │  AdminController  │
│  - login         │  - register (public) │  - dashboard      │
│  - logout        │  - steps 2-5         │  - verify         │
│  - me            │  - status            │  - reports        │
├─────────────────────────────────────────────────────────────┤
│  BonusController     │  Models │ Requests │ Resources       │
│  - calculate bonus   │  Middleware (admin alias)             │
│  - list bonuses      │                                       │
├─────────────────────────────────────────────────────────────┤
│  MySQL │ Storage (public) │ Cache (Settings) │ Laravolt     │
└─────────────────────────────────────────────────────────────┘
```

**Flow Registrasi Baru:**

1. User isi form Step 1 → POST `/api/agents/register` (public)
2. Backend return `{ agent, token }`
3. Frontend simpan `token` ke `localStorage`
4. Steps 2-5 menggunakan `Authorization: Bearer <token>`

---

## 14. Quick Reference: File Structure

```
app/
├── Enums/RegionType.php
├── Http/
│   ├── Controllers/Api/
│   │   ├── AuthController.php
│   │   ├── AgentController.php
│   │   ├── AdminController.php
│   │   └── BonusController.php
│   ├── Middleware/EnsureUserIsAdmin.php
│   ├── Requests/
│   │   ├── RegisterAgentRequest.php
│   │   ├── UpdateAgentAddressRequest.php
│   │   ├── UpdateAgentBankRequest.php
│   │   ├── UploadDocumentsRequest.php
│   │   ├── VerifyDocumentRequest.php
│   │   └── VerifyPaymentRequest.php
│   └── Resources/
│       ├── AgentResource.php
│       └── AgentAddressResource.php
├── Models/
│   ├── User.php
│   ├── Agent.php
│   ├── AgentAddress.php
│   ├── AgentBankAccount.php
│   ├── AgentDocument.php
│   ├── Payment.php
│   ├── BonusTransaction.php
│   ├── Pilgrim.php
│   ├── Region.php
│   ├── Setting.php
│   └── AdminAction.php
├── Providers/AppServiceProvider.php
bootstrap/app.php
config/
├── laravolt/
│   └── indonesia.php
database/
├── migrations/ (19 files — includes laravolt indonesia + sessions)
└── seeders/
    ├── DatabaseSeeder.php
    ├── AdminSeeder.php
    ├── RegionSeeder.php
    └── RegionFullSeeder.php
resources/
├── js/
│   ├── app.js
│   └── bootstrap.js        # Axios config: baseURL, Bearer token, withCredentials=false
├── css/
│   └── app.css
routes/api.php
vite.config.js              # Vite 7 + Tailwind v4 + proxy /api & /storage
```

---

_End of Summary_
