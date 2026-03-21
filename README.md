# UCM — User Centralized Management

ระบบบริหารจัดการผู้ใช้และสิทธิ์การเข้าถึงแบบรวมศูนย์ (Centralized) สำหรับองค์กร
รองรับการซิงค์สิทธิ์กับระบบภายนอกหลายระบบผ่าน Adapter Pattern และ LDAP/Active Directory

> **Internal System** — Bangkok Flight Services IT Team · 2026

---

## ภาพรวม

UCM ทำหน้าที่เป็น **Single Source of Truth** สำหรับ User Account และ Permission ทั่วทั้งองค์กร
ผู้ดูแลระบบกำหนดสิทธิ์ผ่าน Web UI แล้ว UCM ซิงค์ไปยังระบบปลายทางโดยอัตโนมัติผ่าน Queue Worker

```
Active Directory ──► UCM (Web UI) ──► System A (ระบบซ่อมบำรุง)
                                  ──► System B (e-Filing)
                                  ──► System C (ระบบอื่นๆ)
                                  ──► REST API (ระบบที่ query เอง)
```

### ฟีเจอร์หลัก

| ฟีเจอร์ | รายละเอียด |
|---------|------------|
| **LDAP / Active Directory** | นำเข้าและยืนยันตัวตนผู้ใช้จาก AD ขององค์กร |
| **Adapter Pattern** | เชื่อมต่อระบบภายนอกได้ไม่จำกัดโดยไม่กระทบโค้ดหลัก |
| **Connector Wizard** | สร้าง Adapter แบบ No-Code ผ่าน UI Wizard — ไม่ต้องเขียนโค้ด PHP |
| **2-Way Permission Sync** | Sync สิทธิ์ทั้งขาเข้าและขาออกกับระบบที่รองรับ |
| **REST API + Sanctum** | ระบบภายนอก query สิทธิ์ผ่าน Bearer Token |
| **Queue-based Jobs** | Sync งานหนักผ่าน Job Queue — ไม่บล็อก UI |
| **Admin Level System** | ผู้ดูแล 3 ระดับ (ทั่วไป / L1 / L2) |
| **Audit Log** | บันทึก Sync Log ทุกครั้งที่มีการเปลี่ยนแปลงสิทธิ์ |

---

## Tech Stack

| Layer | Technology | Version |
|-------|-----------|---------|
| Backend Framework | [Laravel](https://laravel.com) | ^13.0 |
| Language | PHP | ^8.3 |
| Frontend CSS | [Tailwind CSS](https://tailwindcss.com) | ^4.0.0 (CDN) |
| Build Tool | [Vite](https://vitejs.dev) | ^8.0.0 |
| HTTP Client (FE) | Axios | ^1.11.0 |
| API Auth | Laravel Sanctum | latest |
| API Docs | [L5-Swagger](https://github.com/DarkaOnLine/L5-Swagger) | latest |
| Queue / Cache / Session | Database Driver | — |
| Interactive Shell | Laravel Tinker | ^3.0 |
| Testing | PHPUnit | ^12.5 |
| Code Style | Laravel Pint | ^1.27 |

> Frontend ใช้ **Tailwind CSS + Vanilla JS** เท่านั้น — ไม่มี Alpine.js / Vue / React

---

## โครงสร้างโปรเจค

```
app/
├── Adapters/                       # System adapters (Adapter Pattern)
│   ├── SystemAdapterInterface.php  # Contract ที่ทุก Adapter ต้อง implement
│   ├── BaseAdapter.php             # Abstract base พร้อม PDO helper
│   ├── AdapterFactory.php          # Factory: slug/class → Adapter instance
│   ├── DynamicAdapter.php          # No-Code adapter สำหรับ Connector Wizard
│   ├── EarthAdapter.php            # Adapter สำหรับระบบ Earth
│   ├── EFilingAdapter.php          # Adapter สำหรับระบบ e-Filing
│   └── RepairSystemAdapter.php     # Adapter สำหรับระบบซ่อมบำรุง
│
├── Http/Controllers/
│   ├── Auth/LoginController.php         # LDAP login / logout
│   ├── DashboardController.php          # Dashboard + stats
│   ├── UserController.php               # CRUD ผู้ใช้ + AD import + permissions
│   ├── SystemController.php             # CRUD ระบบ + permission management
│   ├── ConnectorWizardController.php    # Connector Wizard + AJAX endpoints
│   ├── QueueMonitorController.php       # Queue monitor + retry/flush
│   ├── ApiDocsController.php            # API Documentation page
│   └── Api/
│       ├── AuthController.php           # API token issue / revoke
│       ├── PermissionController.php     # Permission query API
│       └── UserExportController.php     # CSV export API
│
└── Models/
    ├── UcmUser.php               # ผู้ใช้งาน (LDAP-based, is_admin 0/1/2)
    ├── System.php                # ระบบที่เชื่อมต่อ
    ├── ConnectorConfig.php       # Connector Wizard configuration
    ├── SystemPermission.php      # Permission definition ของแต่ละระบบ
    ├── UserSystemPermission.php  # User ↔ Permission mapping
    └── SyncLog.php               # Audit trail ทุกการ sync

database/migrations/
    ├── ..._create_ucm_users_table.php
    ├── ..._create_systems_table.php
    ├── ..._create_system_permissions_table.php
    ├── ..._create_user_system_permissions_table.php
    ├── ..._create_sync_logs_table.php
    └── ..._create_connector_configs_table.php   ← Connector Wizard

resources/views/
    ├── layouts/app.blade.php       # Main layout + sidebar accordion nav
    ├── auth/login.blade.php
    ├── dashboard.blade.php
    ├── users/{index,show,admin-levels}.blade.php
    ├── systems/{index,show,create,edit}.blade.php
    ├── connectors/{index,wizard}.blade.php       ← Connector Wizard UI
    ├── queue/monitor.blade.php
    └── docs/{manual,install}.blade.php
```

---

## ความต้องการของระบบ

| Component | Version |
|-----------|---------|
| PHP | **8.3+** |
| Composer | **2.x** |
| Node.js | **18+** |
| MySQL | **8.0+** หรือ MariaDB **10.6+** |
| LDAP Server | Active Directory (Windows Server 2016+) |
| PHP Extensions | `php-ldap`, `php-pdo`, `php-pdo_mysql`, `php-mbstring`, `php-xml` |

---

## การติดตั้ง

### 1. Clone repository

```bash
git clone <repository-url>
cd user-centralized-managment
```

### 2. ติดตั้ง dependencies

```bash
composer install --optimize-autoloader
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
APP_ENV=production
APP_URL=https://your-domain.com

# ── Database (UCM) ────────────────────────────
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ucm_db
DB_USERNAME=ucm_user
DB_PASSWORD=your_secure_password

# ── Queue + Session (ใช้ database driver) ─────
QUEUE_CONNECTION=database
SESSION_DRIVER=database
SESSION_LIFETIME=120

# ── LDAP / Active Directory ───────────────────
LDAP_HOST=192.168.1.10
LDAP_PORT=389
LDAP_BASE_DN="DC=company,DC=local"
LDAP_USERNAME="CN=ldap-bind,OU=Service Accounts,DC=company,DC=local"
LDAP_PASSWORD=ldap_bind_password
LDAP_USE_SSL=false
LDAP_USE_TLS=false
```

### 4. Migrate ฐานข้อมูล

```bash
php artisan migrate
```

### 5. Build Frontend Assets

```bash
# Development (hot-reload)
npm run dev

# Production
npm run build
```

### 6. รัน Queue Worker

```bash
# Development
php artisan queue:work --tries=3

# Production (แนะนำใช้ Supervisor)
php artisan queue:work --daemon --tries=3 --sleep=3
```

#### ตัวอย่าง Supervisor config

```ini
[program:ucm-queue]
command=php /var/www/ucm/artisan queue:work --tries=3 --sleep=3
directory=/var/www/ucm
autostart=true
autorestart=true
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/ucm-queue.log
```

---

## Web Routes

| Method | Path | Description | สิทธิ์ |
|--------|------|-------------|--------|
| `GET/POST` | `/login` | เข้าสู่ระบบ (LDAP) | ทุกคน |
| `GET` | `/` | Dashboard | ทุกคน |
| `GET` | `/users` | รายการผู้ใช้ | ทุกคน |
| `POST` | `/users/import` | นำเข้าผู้ใช้จาก AD | L1+ |
| `GET` | `/users/{id}` | จัดการสิทธิ์ผู้ใช้ | L1+ |
| `GET` | `/systems` | ระบบที่เชื่อมต่อทั้งหมด | ทุกคน |
| `GET` | `/systems/{id}` | รายละเอียดระบบ + Permissions | ทุกคน |
| `GET` | `/connectors` | รายการ Connector Wizard | L2 |
| `GET` | `/connectors/wizard` | สร้าง Connector ใหม่ | L2 |
| `GET` | `/connectors/{id}/edit` | แก้ไข Connector | L2 |
| `GET` | `/admin/levels` | จัดการระดับ Admin | L2 |
| `GET` | `/queue/monitor` | Queue Monitor | L2 |
| `GET` | `/manual` | คู่มือผู้ใช้งาน | ทุกคน |
| `GET` | `/install-guide` | Install Guide สำหรับนักพัฒนา | ทุกคน |
| `GET` | `/api-docs` | API Documentation | ทุกคน |

### Connector Wizard AJAX Endpoints

| Method | Path | Description |
|--------|------|-------------|
| `POST` | `/connectors/ajax/test-connection` | ทดสอบการเชื่อมต่อ DB |
| `POST` | `/connectors/ajax/fetch-tables` | ดึงรายการตาราง |
| `POST` | `/connectors/ajax/fetch-columns` | ดึงรายการคอลัมน์ของตาราง |
| `POST` | `/connectors/ajax/preview-users` | ดูตัวอย่างข้อมูล Users 10 รายการ |
| `POST` | `/connectors/ajax/preview-permissions` | ดูตัวอย่าง Permissions 20 รายการ |

---

## REST API

Base URL: `/api`
Authentication: `Authorization: Bearer <token>` (Laravel Sanctum)

| Method | Endpoint | Description |
|--------|----------|-------------|
| `POST` | `/auth/token` | ออก API Token สำหรับ Admin L2 |
| `POST` | `/auth/user-login` | Login + รับ Token + ข้อมูล Permissions ทุกระบบ |
| `DELETE` | `/auth/token` | Revoke Token ปัจจุบัน |
| `GET` | `/users/{username}/permissions` | สิทธิ์ทั้งหมดของ User ใน System ที่ token นั้นเป็นเจ้าของ |
| `GET` | `/users/{username}/permissions/all` | สิทธิ์ทุก System ของ User |
| `POST` | `/permissions/check` | ตรวจสอบสิทธิ์เฉพาะรายการ |
| `GET` | `/users/export` | Export ผู้ใช้ทั้งหมด (CSV) |

> Rate limit: **10 requests/min** สำหรับ endpoint สาธารณะ

### ตัวอย่างการใช้ API

```bash
# 1. รับ Token
curl -X POST https://ucm.example.com/api/auth/token \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"password","system_slug":"repair-system"}'

# 2. ตรวจสอบสิทธิ์
curl https://ucm.example.com/api/users/john.doe/permissions \
  -H "Authorization: Bearer <token>"

# 3. ตรวจสอบสิทธิ์เฉพาะรายการ
curl -X POST https://ucm.example.com/api/permissions/check \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{"username":"john.doe","permissions":["role_admin","dept_1"]}'
```

---

## Adapter Pattern

UCM ใช้ Adapter Pattern ในการเชื่อมต่อกับระบบภายนอก มี 2 วิธี:

### วิธีที่ 1 — Connector Wizard (No-Code) ✨

สำหรับระบบที่เชื่อมต่อผ่านฐานข้อมูลโดยตรง ไม่ต้องเขียนโค้ด:

1. ไปที่ **ผู้ดูแลระบบ → Connector Wizard**
2. กรอกข้อมูล DB Connection (host, port, database, credentials)
3. เลือก mapping ตาราง Users และ Permissions
4. ยืนยัน — ระบบสร้าง `DynamicAdapter` และ `System` ให้อัตโนมัติ

`DynamicAdapter` รองรับ MySQL/MariaDB, PostgreSQL, SQL Server และ Permission Mode แบบ Junction Table, Single Column หรือ Manual

### วิธีที่ 2 — Custom Adapter (Code-based)

สำหรับระบบที่ต้องการ logic พิเศษ implement `SystemAdapterInterface`:

```php
<?php

namespace App\Adapters;

use App\Models\UcmUser;

class MySystemAdapter extends BaseAdapter implements SystemAdapterInterface
{
    // ── บังคับ implement ──────────────────────────────

    public function syncPermissions(UcmUser $user, array $permissions): bool
    {
        $pdo = $this->getConnection(); // MySQL/MSSQL PDO จาก System.db_*
        // ... write permissions to remote DB
        return true;
    }

    public function getAvailablePermissions(): array
    {
        return [
            ['key' => 'role_admin', 'label' => 'ผู้ดูแลระบบ', 'group' => 'Role'],
            ['key' => 'role_user',  'label' => 'ผู้ใช้ทั่วไป',  'group' => 'Role'],
        ];
    }

    public function getCurrentPermissions(UcmUser $user): array
    {
        // ดึง permissions ปัจจุบันจากระบบเดิม
        return [];
    }

    public function revokeAll(UcmUser $user): bool
    {
        return $this->syncPermissions($user, []);
    }

    // ── Optional overrides ────────────────────────────

    public function getSystemUsers(): array { return []; }
    public function testConnection(): array
    {
        try {
            $this->getConnection()->query('SELECT 1');
            return ['ok' => true, 'message' => 'เชื่อมต่อสำเร็จ'];
        } catch (\PDOException $e) {
            return ['ok' => false, 'message' => $e->getMessage()];
        }
    }
}
```

จากนั้นลงทะเบียน class ใน `AdapterFactory::$map`:

```php
protected static array $map = [
    'my-system' => MySystemAdapter::class,
    // ...
];
```

หรือตั้งค่า `adapter_class` บน System record ผ่าน Web UI

---

## Connector Wizard

ฟีเจอร์สร้าง Adapter แบบ No-Code ผ่าน Web UI 5 ขั้นตอน:

| ขั้นตอน | หัวข้อ | รายละเอียด |
|---------|--------|------------|
| 1 | ข้อมูลระบบ | ชื่อ, Slug, สี, ไอคอน หรือเลือกระบบที่มีอยู่แล้ว |
| 2 | การเชื่อมต่อ DB | Driver (MySQL/PostgreSQL/SQL Server), Host, Port, Database, Credentials + Test |
| 3 | ตาราง Users | เลือกตาราง, map คอลัมน์ Identifier/ชื่อ/อีเมล/แผนก/สถานะ |
| 4 | Permission Mode | Junction Table / Single Column / Manual |
| 5 | ยืนยัน | Review สรุปทั้งหมด → สร้าง System + ConnectorConfig |

### Permission Modes

```
Junction Table:   users ─── user_roles(user_id, role) ──► sync ทุก row
Single Column:    users.role ──► sync ค่าเดียว
Manual:           กำหนด permissions เองใน UCM ไม่มี remote sync
```

### การทำงานของ DynamicAdapter

```
UCM Admin กด Sync
    │
    ▼
DynamicAdapter.syncPermissions()
    │  อ่าน ConnectorConfig จาก DB
    │
    ├─ Junction mode: DELETE WHERE user_id=? → INSERT ทุก permission
    ├─ Column mode:   UPDATE users SET role=? WHERE id=?
    └─ Manual mode:   return true (ไม่ sync)
```

---

## Admin Levels

| Level | บทบาท | ความสามารถ |
|-------|-------|------------|
| `0` | ผู้ใช้ทั่วไป | ดูสิทธิ์ของตนเองเท่านั้น |
| `1` | Admin L1 | จัดการสิทธิ์ผู้ใช้, นำเข้า AD, ดู Queue Monitor |
| `2` | Admin L2 (Super Admin) | ทุกอย่าง + จัดการระบบ, Connector Wizard, กำหนดระดับ Admin |

---

## Queue Worker

UCM ใช้ Database Queue สำหรับ sync งานหนักโดยไม่บล็อก UI

```bash
# รัน worker (production)
php artisan queue:work --tries=3 --sleep=3 --max-time=3600

# ดู failed jobs
php artisan queue:failed

# Retry failed job
php artisan queue:retry <uuid>

# Flush failed jobs
php artisan queue:flush
```

Queue Monitor อยู่ที่ `/queue/monitor` — แสดงสถานะ queue แบบ real-time พร้อมปุ่ม Retry/Delete

---

## Security Notes

- รหัสผ่านฐานข้อมูลปลายทางใน `connector_configs` ถูกเก็บใน plaintext — แนะนำให้ใช้ DB user ที่มีสิทธิ์ขั้นต่ำ (SELECT บน users table, INSERT/DELETE บน permissions table)
- CSRF protection เปิดใช้งานบนทุก POST/PUT/DELETE route
- API rate limited 10 req/min บน public endpoints
- Session timeout 120 นาที (ปรับได้ใน `.env SESSION_LIFETIME`)
- ข้อมูล `db_password` และ `api_token` ถูก hidden ใน Model `$hidden`

---

## Development

```bash
# Code style fix
./vendor/bin/pint

# Run tests
php artisan test

# Generate API docs (Swagger)
php artisan l5-swagger:generate

# Clear all cache
php artisan optimize:clear
```

---

## เครื่องมือที่ใช้พัฒนา

โปรเจคนี้พัฒนาด้วยความช่วยเหลือของ:

- **[Claude Code](https://claude.ai/claude-code)** by Anthropic — AI coding assistant (Claude Sonnet 4.6)
  ช่วยในการออกแบบ Architecture, เขียนโค้ด, Code Review, Refactoring และสร้างฟีเจอร์ Connector Wizard

---

## License

Internal use only — Bangkok Flight Services
&copy; 2026 BFS IT Team
