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
                                  ──► System C (Earth FLIGHT OPS)
                                  ──► REST API (ระบบที่ query เอง)
```

### ฟีเจอร์หลัก

| ฟีเจอร์ | รายละเอียด |
|---------|------------|
| **LDAP / Active Directory** | นำเข้าและยืนยันตัวตนผู้ใช้จาก AD ขององค์กร (custom LdapService) |
| **Adapter Pattern** | เชื่อมต่อระบบภายนอกได้ไม่จำกัดโดยไม่กระทบโค้ดหลัก |
| **Connector Wizard** | สร้าง Adapter แบบ No-Code ผ่าน UI Wizard — ไม่ต้องเขียนโค้ด PHP |
| **2-Way Permission Sync** | Sync สิทธิ์ทั้งขาเข้าและขาออกกับระบบที่รองรับ |
| **REST API + Sanctum** | ระบบภายนอก query สิทธิ์ผ่าน Bearer Token |
| **Queue-based Jobs** | Sync งานหนักผ่าน Job Queue — ไม่บล็อก UI |
| **Queue Monitor** | หน้า Dashboard ตรวจสอบสถานะ Job + Retry/Flush Failed Jobs |
| **Admin Level System** | ผู้ดูแล 3 ระดับ (ทั่วไป / L1 / L2) |
| **Audit Log** | บันทึก Sync Log ทุกครั้งที่มีการเปลี่ยนแปลงสิทธิ์ |
| **Export CSV** | ส่งออกข้อมูลผู้ใช้พร้อม Permissions ทุกระบบเป็น CSV (UTF-8 BOM) |

---

## Tech Stack

| Layer | Technology | Version |
|-------|-----------|---------|
| Backend Framework | [Laravel](https://laravel.com) | v13.1 |
| Language | PHP | ^8.3 |
| Frontend CSS | [Tailwind CSS](https://tailwindcss.com) | ^4.0 (CDN) |
| Frontend JS | Vanilla JS | — |
| Build Tool | [Vite](https://vitejs.dev) | ^8.0 |
| HTTP Client (FE) | Axios | ^1.11 |
| API Auth | Laravel Sanctum | v4 |
| API Docs | [L5-Swagger](https://github.com/DarkaOnLine/L5-Swagger) | latest |
| Queue / Cache / Session | Database Driver | — |
| LDAP Integration | Custom `LdapService` (php-ldap extension) | — |
| Reactive UI | [Livewire](https://livewire.laravel.com) | v4 |
| Interactive Shell | Laravel Tinker | ^3.0 |
| Testing | PHPUnit | ^12.5 |
| Code Style | Laravel Pint | ^1.27 |

> Frontend ใช้ **Tailwind CSS + Vanilla JS** เป็นหลัก — ไม่มี Alpine.js / Vue / React

---

## โครงสร้างโปรเจค

```
app/
├── Adapters/                       # System adapters (Adapter Pattern)
│   ├── SystemAdapterInterface.php  # Contract ที่ทุก Adapter ต้อง implement
│   ├── BaseAdapter.php             # Abstract base พร้อม PDO helper
│   ├── AdapterFactory.php          # Factory: slug/class → Adapter instance
│   ├── DynamicAdapter.php          # No-Code adapter สำหรับ Connector Wizard
│   ├── EarthAdapter.php            # Adapter สำหรับระบบ Earth (FLIGHT OPS)
│   ├── EFilingAdapter.php          # Adapter สำหรับระบบ e-Filing
│   └── RepairSystemAdapter.php     # Adapter สำหรับระบบซ่อมบำรุง
│
├── Auth/
│   └── LdapUserProvider.php        # Custom Laravel Auth Guard ผ่าน LDAP
│
├── Http/Controllers/
│   ├── Auth/LoginController.php         # LDAP login / logout
│   ├── DashboardController.php          # Dashboard + stats
│   ├── UserController.php               # CRUD ผู้ใช้ + AD import + permissions + export CSV
│   ├── SystemController.php             # CRUD ระบบ + permission management
│   ├── ConnectorWizardController.php    # Connector Wizard + AJAX endpoints
│   ├── QueueMonitorController.php       # Queue monitor + retry/flush
│   ├── ApiDocsController.php            # API Documentation page
│   └── Api/
│       ├── AuthController.php           # API token issue / revoke / user-login
│       ├── PermissionController.php     # Permission query API
│       └── UserExportController.php     # CSV export API
│
├── Jobs/
│   └── SyncPermissionsJob.php      # Queue Job สำหรับ sync สิทธิ์ (tries=3, timeout=30s)
│
├── Models/
│   ├── UcmUser.php               # ผู้ใช้งาน (LDAP-based, is_admin 0/1/2)
│   ├── System.php                # ระบบที่เชื่อมต่อ
│   ├── ConnectorConfig.php       # Connector Wizard configuration
│   ├── SystemPermission.php      # Permission definition ของแต่ละระบบ
│   ├── UserSystemPermission.php  # User ↔ Permission mapping
│   └── SyncLog.php               # Audit trail ทุกการ sync
│
└── Services/
    └── LdapService.php           # LDAP search / bind / attribute mapping

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
| PHP Extensions | `php-ldap`, `php-pdo`, `php-pdo_mysql`, `php-mbstring`, `php-xml`, `php-zip`, `php-bcmath`, `php-curl` |
| Docker (แนะนำ) | Docker Engine 24+ + Compose v2 |

---

## การติดตั้ง

### วิธีที่ 1 — Docker (แนะนำ)

โปรเจคใช้ Docker Compose ที่ root directory (`nginx-proxy/docker-compose.yml`) ประกอบด้วย containers:

| Container | Image | หน้าที่ |
|-----------|-------|---------|
| `ucm-db` | MySQL 8.0 | ฐานข้อมูลหลัก UCM |
| `php83` | PHP 8.3-FPM | รัน Laravel application |
| `ucm-queue` | php83 (same) | Queue Worker — รัน `queue:work` ตลอดเวลา |
| `web-router` | Nginx alpine | Reverse proxy → php83 |
| `phpmyadmin` | phpMyAdmin | จัดการ DB ผ่าน port 8181 |
| `nginx-proxy-manager` | NPM | SSL termination + domain routing |

```bash
# 1. Clone โปรเจคเข้า www/
git clone <repo-url> www/user-centralized-managment

# 2. Copy .env และแก้ไขค่าให้ครบ (ดูหัวข้อ Environment Config)
cp www/user-centralized-managment/.env.example www/user-centralized-managment/.env

# 3. Start containers ทั้งหมด
docker compose up -d

# 4. Install PHP dependencies
docker exec -w /var/www/html/user-centralized-managment php83 composer install --no-dev --optimize-autoloader

# 5. Generate app key
docker exec -w /var/www/html/user-centralized-managment php83 php artisan key:generate

# 6. Run migrations + seed
docker exec -w /var/www/html/user-centralized-managment php83 php artisan migrate --seed
```

> **หมายเหตุ:** เมื่อแก้ไขไฟล์ Adapter หรือ Job ต้อง `docker restart ucm-queue` ทุกครั้ง เพราะ PHP process โหลด code ไว้ใน memory แล้ว

---

### วิธีที่ 2 — Bare Server (Non-Docker)

#### 1. ติดตั้ง PHP 8.3 + Extensions

```bash
# Ubuntu / Debian
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install php8.3 php8.3-fpm php8.3-cli php8.3-curl php8.3-mbstring \
  php8.3-xml php8.3-zip php8.3-ldap php8.3-bcmath php8.3-intl php8.3-mysql

# Optional: pdo_sqlsrv (ถ้าต้องเชื่อมต่อ SQL Server ปลายทาง)
curl https://packages.microsoft.com/keys/microsoft.asc | sudo apt-key add -
curl https://packages.microsoft.com/config/ubuntu/$(lsb_release -rs)/prod.list \
  | sudo tee /etc/apt/sources.list.d/mssql-release.list
sudo apt update && sudo ACCEPT_EULA=Y apt install msodbcsql18 unixodbc-dev
sudo pecl install sqlsrv pdo_sqlsrv
sudo phpenmod sqlsrv pdo_sqlsrv && sudo systemctl restart php8.3-fpm
```

#### 2. Clone & Setup

```bash
cd /var/www
git clone <repo-url> user-centralized-managment
cd user-centralized-managment
cp .env.example .env
composer install --no-dev --optimize-autoloader
php artisan key:generate
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

#### 3. Nginx Virtual Host

```nginx
# /etc/nginx/sites-available/ucm.conf
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/user-centralized-managment/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

```bash
sudo ln -s /etc/nginx/sites-available/ucm.conf /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
```

#### 4. Queue Worker ด้วย Supervisor

```bash
sudo apt install supervisor
```

```ini
# /etc/supervisor/conf.d/ucm-queue.conf
[program:ucm-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/user-centralized-managment/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/log/ucm-queue.log
```

```bash
sudo supervisorctl reread && sudo supervisorctl update
sudo supervisorctl start ucm-queue:*
# Reload หลังแก้ไข code
sudo supervisorctl restart ucm-queue:*
```

#### 5. Migration & Seed

```bash
php artisan migrate
# Seed ตามระบบที่ใช้งาน
php artisan db:seed --class=EarthSeeder
php artisan db:seed --class=EFilingSeeder
php artisan db:seed --class=RepairSystemSeeder
```

---

## Environment Config (.env)

```env
# ── App ──────────────────────────────────────────────────
APP_NAME="User Centralized Management"
APP_ENV=production
APP_KEY=base64:...           # สร้างด้วย php artisan key:generate
APP_DEBUG=false
APP_URL=https://your-domain.com/user-centralized-managment

# ── Database (UCM — MySQL 8.0) ────────────────────────────
DB_CONNECTION=mysql
DB_HOST=ucm-db               # Docker container name (หรือ 127.0.0.1 สำหรับ bare server)
DB_PORT=3306
DB_DATABASE=ucm_db
DB_USERNAME=ucm_user
DB_PASSWORD=your_secure_password

# ── Queue & Session & Cache ──────────────────────────────
QUEUE_CONNECTION=database
SESSION_DRIVER=database
SESSION_LIFETIME=120
CACHE_STORE=database

# ── LDAP / Active Directory ──────────────────────────────
LDAP_HOST=DOMAIN.COM                        # AD domain หรือ DC IP
LDAP_PORT=389                               # 636 = LDAPS
LDAP_BASE_DN="OU=User,DC=DOMAIN,DC=COM"
LDAP_BIND_DN="DOMAIN\service_account"       # Service account สำหรับ bind
LDAP_BIND_PASSWORD="password"
LDAP_USER_FILTER=(sAMAccountName={username})
LDAP_USERNAME_ATTRIBUTE=sAMAccountName

# ── UCM Settings ─────────────────────────────────────────
UCM_ALLOWED_DEPARTMENT="Systems Development and IT"  # เว้นว่างเพื่ออนุญาตทุกแผนก

# ── Swagger / API Docs ───────────────────────────────────
SWAGGER_GENERATE_ALWAYS=false              # false = ใช้ cached JSON (แนะนำใน production)
L5_SWAGGER_UI_DOC_EXPANSION=full
```

---

## ตั้งค่า Super Admin คนแรก

หลัง migrate ผู้ใช้ทุกคนจะมี `is_admin = 0` (ทั่วไป) ต้องยกระดับผู้ดูแลคนแรกด้วย tinker:

```bash
# Docker
docker exec -it -w /var/www/html/user-centralized-managment php83 php artisan tinker

# Non-Docker
php artisan tinker
```

```php
// ใน tinker shell — ต้อง import ผู้ใช้จาก AD ผ่านหน้าเว็บก่อน
App\Models\UcmUser::where('username', 'firstname.lastname')->update(['is_admin' => 2]);
exit
```

หลังตั้งค่าแล้ว Admin ระดับ 2 สามารถยกระดับผู้ใช้คนอื่นได้ผ่านหน้า **ผู้ดูแลระบบ → จัดการสิทธิ์ Admin** โดยไม่ต้องใช้ tinker อีก

---

## Web Routes

| Method | Path | Description | สิทธิ์ |
|--------|------|-------------|--------|
| `GET/POST` | `/login` | เข้าสู่ระบบ (LDAP) | ทุกคน |
| `GET` | `/` | Dashboard | ทุกคน |
| `GET` | `/users` | รายการผู้ใช้ + Export CSV | ทุกคน |
| `POST` | `/users/import` | นำเข้าผู้ใช้จาก AD | L1+ |
| `GET` | `/users/{id}` | จัดการสิทธิ์ผู้ใช้ | L1+ |
| `PATCH` | `/users/{id}/permissions` | บันทึกสิทธิ์ผู้ใช้ | L1+ |
| `GET` | `/systems` | ระบบที่เชื่อมต่อทั้งหมด | ทุกคน |
| `GET` | `/systems/{id}` | รายละเอียดระบบ + Permissions | ทุกคน |
| `POST` | `/systems/{id}/permissions` | เพิ่ม Permission | L1+ |
| `GET` | `/connectors` | รายการ Connector Wizard | L2 |
| `GET` | `/connectors/wizard` | สร้าง Connector ใหม่ | L2 |
| `GET` | `/connectors/{id}/edit` | แก้ไข Connector | L2 |
| `GET` | `/admin/levels` | จัดการระดับ Admin | L2 |
| `GET` | `/queue/monitor` | Queue Monitor | **L1+** |
| `POST` | `/queue/monitor/retry/{uuid}` | Retry Failed Job | L2 |
| `POST` | `/queue/monitor/retry-all` | Retry ทุก Failed Job | L2 |
| `DELETE` | `/queue/monitor/failed/{uuid}` | ลบ Failed Job | L2 |
| `POST` | `/queue/monitor/flush` | ล้าง Failed Jobs ทั้งหมด | L2 |
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

| Method | Endpoint | Description | สิทธิ์ |
|--------|----------|-------------|--------|
| `POST` | `/auth/token` | ออก API Token (LDAP credentials) | — |
| `POST` | `/auth/user-login` | Login + รับ Token + Permissions ทุกระบบ | — |
| `DELETE` | `/auth/token` | Revoke Token ปัจจุบัน | — |
| `GET` | `/users/{username}/permissions` | สิทธิ์ของ User ในระบบที่ Token นั้นเชื่อมกับ | Bearer |
| `GET` | `/users/{username}/permissions/all` | สิทธิ์ทุก System ของ User | Bearer |
| `POST` | `/permissions/check` | ตรวจสอบสิทธิ์เฉพาะรายการ (batch) | Bearer |
| `GET` | `/users/export` | Export ผู้ใช้ทั้งหมด (CSV) | Bearer |

### ตัวอย่างการใช้ API

```bash
# 1. รับ Token
curl -X POST https://ucm.example.com/api/auth/token \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"password","token_name":"repair-system"}'
# Response: { "token": "1|abc...", "type": "Bearer" }

# 2. ตรวจสอบสิทธิ์ทั้งหมดของ user
curl https://ucm.example.com/api/users/john.doe/permissions \
  -H "Authorization: Bearer 1|abc..."
# Response: { "permissions": ["daily_edit", "pax_read"] }

# 3. ตรวจสอบสิทธิ์เฉพาะรายการ
curl -X POST https://ucm.example.com/api/permissions/check \
  -H "Authorization: Bearer 1|abc..." \
  -H "Content-Type: application/json" \
  -d '{"username":"john.doe","permissions":["pax_edit","ramp_deny"]}'
# Response: { "pax_edit": true, "ramp_deny": false }
```

> ดู endpoints ทั้งหมดพร้อม schema ได้ที่ `/api-docs` (Swagger UI)

---

## Adapter Pattern

UCM ใช้ Adapter Pattern ในการเชื่อมต่อกับระบบภายนอก มี 2 วิธี:

### วิธีที่ 1 — Connector Wizard (No-Code) ✨

สำหรับระบบที่เชื่อมต่อผ่านฐานข้อมูลโดยตรง ไม่ต้องเขียนโค้ด:

1. ไปที่ **ผู้ดูแลระบบ → Connector Wizard**
2. กรอกข้อมูล DB Connection (host, port, database, credentials) + ทดสอบการเชื่อมต่อ
3. เลือก mapping ตาราง Users และ Permissions
4. เลือก Permission Mode (Junction Table / Single Column / Manual)
5. ยืนยัน — ระบบสร้าง `DynamicAdapter` และ `System` ให้อัตโนมัติ

`DynamicAdapter` รองรับ MySQL/MariaDB, PostgreSQL, SQL Server และ Permission Mode แบบ Junction Table, Single Column หรือ Manual

### วิธีที่ 2 — Custom Adapter (Code-based)

สำหรับระบบที่ต้องการ logic พิเศษ หรือรองรับ 2-Way Permission Sync implement `SystemAdapterInterface`:

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
        // ดึง permissions ปัจจุบันจากระบบเดิม (สำหรับ Discover + Out-of-Sync detection)
        return [];
    }

    public function revokeAll(UcmUser $user): bool
    {
        return $this->syncPermissions($user, []);
    }

    // ── Optional overrides ────────────────────────────

    public function supports2WayPermissions(): bool { return true; } // เปิด 2-way toggle ใน UI

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
Junction Table:  users ─── user_roles(user_id, role_code) ──► DELETE+INSERT on sync
Single Column:   users.role_col ──► UPDATE ค่าเดียวต่อ user
Manual:          กำหนด permission list ใน UCM เอง — ไม่ sync ไปยัง DB ปลายทาง
                 (ระบบปลายทาง query UCM API โดยตรง)
```

### การทำงานของ DynamicAdapter

```
UCM Admin กด "บันทึกสิทธิ์"
    │
    ▼
SyncPermissionsJob (Queue Worker)
    │
    ▼
DynamicAdapter.syncPermissions()
    │  อ่าน ConnectorConfig จาก DB
    │
    ├─ Junction mode: BEGIN TX → DELETE WHERE fk=? → INSERT ทุก permission → COMMIT
    ├─ Column mode:   UPDATE users SET perm_col=? WHERE id_col=?
    └─ Manual mode:   return true (ไม่ sync ไปยัง DB ปลายทาง)
```

> **Security:** identifier ชื่อตาราง/คอลัมน์ทุกตัวผ่าน `quoteIdentifier()` ที่มี whitelist regex `[\w.]+` ก่อน execute

---

## Admin Levels

| Level | บทบาท | ความสามารถ |
|-------|-------|------------|
| `0` | ผู้ใช้ทั่วไป | ดูข้อมูลและสิทธิ์ได้ (read-only) — ไม่สามารถแก้ไขได้ |
| `1` | Admin L1 | จัดการสิทธิ์ผู้ใช้, นำเข้า AD, Discover Permissions, เพิ่ม Reference Data, ดู Queue Monitor |
| `2` | Admin L2 (Super Admin) | ทุกอย่าง + จัดการระบบ, Connector Wizard, Toggle 2-way, แก้ไข/ลบ Reference Data, กำหนดระดับ Admin |

> Admin ระดับ 2 **ไม่สามารถลดระดับตัวเองได้** เพื่อป้องกันระบบไม่มีผู้ดูแล

---

## Queue Worker

UCM ใช้ Database Queue สำหรับ sync งานหนักโดยไม่บล็อก UI

```bash
# Docker — ดู logs
docker logs ucm-queue --tail 50 -f

# Docker — restart (จำเป็นหลังแก้ไข Adapter/Job)
docker restart ucm-queue

# Supervisor — restart
sudo supervisorctl restart ucm-queue:*

# Artisan — ดู failed jobs
docker exec -w /var/www/html/user-centralized-managment php83 php artisan queue:failed

# Artisan — retry failed job
docker exec -w /var/www/html/user-centralized-managment php83 php artisan queue:retry <uuid>

# Artisan — retry all failed
docker exec -w /var/www/html/user-centralized-managment php83 php artisan queue:retry all

# Artisan — flush failed jobs
docker exec -w /var/www/html/user-centralized-managment php83 php artisan queue:flush
```

Queue Monitor อยู่ที่ `/queue/monitor` — แสดงสถานะ queue แบบ real-time พร้อมปุ่ม Retry/Delete (ต้องการ Admin L1+)

Job config: `tries=3`, `timeout=30s`, `backoff=10s`

---

## Security Notes

- รหัสผ่านฐานข้อมูลปลายทางใน `connector_configs.db_password` ถูกเก็บใน plaintext — แนะนำให้ใช้ DB user ที่มีสิทธิ์ขั้นต่ำ (SELECT บน users table, INSERT/DELETE บน permissions table เท่านั้น)
- CSRF protection เปิดใช้งานบนทุก POST/PUT/DELETE route
- SQL Injection prevention: identifier ชื่อตาราง/คอลัมน์ผ่าน `quoteIdentifier()` + whitelist regex; ค่า parameter ทุกตัวผ่าน PDO prepared statement
- Session timeout 120 นาที (ปรับได้ใน `.env SESSION_LIFETIME`)
- ข้อมูล `db_password` และ `api_token` ถูก hidden ใน Model `$hidden` — ไม่ถูกส่งออกใน JSON response
- Authorization ทุก mutation route ใช้ `abort_unless($user->isAdmin(), 403)`

---

## Development

```bash
# Code style fix (ควรรันก่อน commit ทุกครั้ง)
docker exec -w /var/www/html/user-centralized-managment php83 vendor/bin/pint --dirty

# Run all tests
docker exec -w /var/www/html/user-centralized-managment php83 php artisan test --compact

# Run specific test
docker exec -w /var/www/html/user-centralized-managment php83 php artisan test --compact --filter=testName

# Generate API docs (Swagger)
docker exec -w /var/www/html/user-centralized-managment php83 php artisan l5-swagger:generate

# Clear all cache
docker exec -w /var/www/html/user-centralized-managment php83 php artisan optimize:clear

# View routes
docker exec -w /var/www/html/user-centralized-managment php83 php artisan route:list
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
