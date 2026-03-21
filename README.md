# UCM — User Centralized Management

ระบบจัดการผู้ใช้และสิทธิ์การเข้าถึงแบบรวมศูนย์ (Centralized) สำหรับองค์กร
รองรับการซิงค์สิทธิ์กับระบบภายนอกหลายระบบผ่าน Adapter Pattern และ LDAP/Active Directory

---

## ภาพรวม

UCM ทำหน้าที่เป็น **Single Source of Truth** สำหรับ User Account และ Permission
ผู้ดูแลระบบสามารถกำหนดสิทธิ์ผ่าน Web UI แล้ว UCM จะ sync ไปยังระบบปลายทางโดยอัตโนมัติ

### ฟีเจอร์หลัก

- **LDAP / Active Directory Integration** — นำเข้าและตรวจสอบผู้ใช้จาก AD
- **Adapter Pattern** — เชื่อมต่อระบบภายนอกได้หลายระบบโดยไม่กระทบโค้ดหลัก
- **2-Way Permission Sync** — sync สิทธิ์ทั้งขาเข้าและขาออกกับระบบที่รองรับ
- **REST API + Sanctum** — ระบบภายนอกสามารถ query สิทธิ์ผ่าน API Token
- **Queue-based Background Jobs** — sync งานหนักผ่าน Job Queue ไม่บล็อก UI
- **Admin Level System** — ระดับผู้ดูแล 3 ระดับ (ผู้ใช้ทั่วไป / Admin L1 / Admin L2)
- **Audit Log** — บันทึก Sync Log ทุกครั้งที่มีการเปลี่ยนแปลงสิทธิ์

---

## Tech Stack

| Layer | Technology | Version |
|-------|-----------|---------|
| Backend Framework | [Laravel](https://laravel.com) | ^13.0 |
| Language | PHP | ^8.3 |
| Frontend CSS | [Tailwind CSS](https://tailwindcss.com) | ^4.0.0 |
| Build Tool | [Vite](https://vitejs.dev) | ^8.0.0 |
| HTTP Client (FE) | Axios | ^1.11.0 |
| API Auth | Laravel Sanctum | latest |
| API Docs | [L5-Swagger](https://github.com/DarkaOnLine/L5-Swagger) | latest |
| Queue / Cache | Database Driver | — |
| Interactive Shell | Laravel Tinker | ^3.0 |
| Testing | PHPUnit | ^12.5 |
| Code Style | Laravel Pint | ^1.27 |

> Frontend ใช้ **Tailwind CSS + Vanilla JS** เท่านั้น (ไม่มี Alpine.js / Vue / React)

---

## โครงสร้างโปรเจค

```
app/
├── Adapters/               # System adapters (Adapter Pattern)
│   ├── SystemAdapterInterface.php
│   ├── BaseAdapter.php
│   ├── AdapterFactory.php
│   ├── EarthAdapter.php
│   ├── EFilingAdapter.php
│   └── RepairSystemAdapter.php
├── Http/Controllers/
│   ├── Auth/LoginController.php
│   ├── DashboardController.php
│   ├── UserController.php
│   ├── SystemController.php
│   ├── QueueMonitorController.php
│   └── Api/
│       ├── AuthController.php
│       ├── PermissionController.php
│       └── UserExportController.php
└── Models/
    ├── UcmUser.php               # ผู้ใช้งาน (LDAP)
    ├── System.php                # ระบบที่เชื่อมต่อ
    ├── SystemPermission.php      # Permission definition
    ├── UserSystemPermission.php  # User ↔ Permission mapping
    └── SyncLog.php               # Audit trail
```

---

## ความต้องการของระบบ

- PHP **8.3+**
- Composer **2.x**
- Node.js **18+** และ npm
- MySQL **8.0+** หรือ MariaDB **10.6+**
- LDAP / Active Directory server

---

## การติดตั้ง

### 1. Clone repository

```bash
git clone <repository-url>
cd user-centralized-managment
```

### 2. ติดตั้ง dependencies

```bash
composer install
npm install
```

### 3. ตั้งค่า Environment

```bash
cp .env.example .env
php artisan key:generate
```

แก้ไขค่าใน `.env`:

```env
APP_NAME="UCM"
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ucm
DB_USERNAME=your_user
DB_PASSWORD=your_password

# Queue (ใช้ database driver)
QUEUE_CONNECTION=database

# Session
SESSION_DRIVER=database
SESSION_LIFETIME=120
```

### 4. Migrate และ Seed

```bash
php artisan migrate
```

### 5. Build Frontend

```bash
# Development
npm run dev

# Production
npm run build
```

### 6. รัน Queue Worker

```bash
php artisan queue:work --tries=3
```

---

## Web Routes

| Method | Path | Description |
|--------|------|-------------|
| `GET` | `/` | Dashboard |
| `GET/POST` | `/login` | เข้าสู่ระบบ (LDAP) |
| `GET` | `/users` | รายการผู้ใช้ |
| `POST` | `/users/import` | นำเข้าผู้ใช้จาก AD |
| `GET` | `/users/{id}` | จัดการสิทธิ์ผู้ใช้ |
| `GET` | `/systems` | ระบบที่เชื่อมต่อ |
| `GET` | `/systems/{id}` | รายละเอียด + Permission |
| `GET` | `/admin/levels` | จัดการระดับ Admin |
| `GET` | `/queue/monitor` | Queue Monitor |
| `GET` | `/manual` | คู่มือผู้ใช้งาน |
| `GET` | `/api-docs` | API Documentation |

---

## REST API

Base URL: `/api`
Authentication: `Bearer <token>` (Laravel Sanctum)

| Method | Endpoint | Description |
|--------|----------|-------------|
| `POST` | `/auth/token` | ออก Token สำหรับ Admin |
| `POST` | `/auth/user-login` | Login + รับ Token + Permissions |
| `DELETE` | `/auth/token` | Revoke Token |
| `GET` | `/users/{username}/permissions` | สิทธิ์ของ User ใน System นั้น |
| `GET` | `/users/{username}/permissions/all` | สิทธิ์ทุก System |
| `POST` | `/permissions/check` | ตรวจสอบสิทธิ์เฉพาะรายการ |
| `GET` | `/users/export` | Export ผู้ใช้ทั้งหมด (CSV) |

> Rate limit: 10 requests/min สำหรับ endpoint สาธารณะ

---

## Adapter Pattern

การเพิ่มระบบใหม่ทำโดย implement `SystemAdapterInterface`:

```php
class MySystemAdapter extends BaseAdapter implements SystemAdapterInterface
{
    public function syncPermissions(UcmUser $user, array $permissions): bool { ... }
    public function getAvailablePermissions(): array { ... }
    public function getCurrentPermissions(UcmUser $user): array { ... }
    public function testConnection(): bool { ... }
}
```

จากนั้นลงทะเบียน class ใน `AdapterFactory` และสร้าง System record ผ่าน Web UI

---

## Admin Levels

| Level | ความสามารถ |
|-------|-----------|
| `0` — ผู้ใช้ทั่วไป | ดูสิทธิ์ของตัวเองเท่านั้น |
| `1` — Admin L1 | จัดการสิทธิ์ผู้ใช้ได้ |
| `2` — Admin L2 (Super Admin) | จัดการทุกอย่าง รวมถึง System และ Admin อื่น |

---

## เครื่องมือที่ใช้พัฒนา

โปรเจคนี้พัฒนาด้วยความช่วยเหลือของ:

- **[Claude Code](https://claude.ai/claude-code)** by Anthropic — AI coding assistant (Claude Sonnet 4.6)
  ช่วยในการออกแบบ Architecture, เขียนโค้ด, Code Review และ Refactoring

---

## License

Internal use only — Bangkok Flight Services
&copy; 2026 BFS IT Team
