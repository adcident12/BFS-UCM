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
| **Audit Log** | บันทึกการกระทำทุกจุด (Auth / Users / Permissions / Systems / Connectors / API) พร้อมหน้าดูข้อมูลสำหรับ Admin และแผนก SQA/QA |
| **Export CSV** | ส่งออกข้อมูลผู้ใช้พร้อม Permissions ทุกระบบเป็น CSV (UTF-8 BOM) |
| **Dashboard Charts** | กราฟ Audit Activity / Sync Activity (7 วันล่าสุด) และ Permissions per System (Top 8) ขับเคลื่อนด้วย Chart.js |
| **Notification Channels** | แจ้งเตือน Admin อัตโนมัติผ่าน Email หรือ Webhook เมื่อเกิด event สำคัญ รองรับ HMAC-SHA256 signature |
| **Permission Matrix Report** | รายงานตารางครอสแทบ User × Permission พร้อม sticky headers, system color bands, Export CSV |

---

## Tech Stack

| Layer | Technology | Version |
|-------|-----------|---------|
| Backend Framework | [Laravel](https://laravel.com) | v13.1 |
| Language | PHP | ^8.3 |
| Frontend CSS | [Tailwind CSS](https://tailwindcss.com) | ^4.0 (CDN) |
| Frontend JS | Vanilla JS | — |
| Build Tool | [Vite](https://vitejs.dev) | ^8.0 |
| Charts | [Chart.js](https://www.chartjs.org) | ^4.x (auto) |
| HTTP Client (FE) | Axios | ^1.11 |
| Date Picker | [Flatpickr](https://flatpickr.js.org) | ^4.6 |
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
├── Adapters/                            # System adapters (Adapter Pattern)
│   ├── SystemAdapterInterface.php       # Contract ที่ทุก Adapter ต้อง implement
│   ├── BaseAdapter.php                  # Abstract base พร้อม PDO helper + quoteIdentifier()
│   ├── AdapterFactory.php               # Factory: slug/class → Adapter instance
│   ├── DynamicAdapter.php               # No-Code adapter สำหรับ Connector Wizard
│   ├── EarthAdapter.php                 # Adapter สำหรับระบบ Earth (FLIGHT OPS)
│   ├── EFilingAdapter.php               # Adapter สำหรับระบบ e-Filing
│   └── RepairSystemAdapter.php          # Adapter สำหรับระบบซ่อมบำรุง
│
├── Auth/
│   └── LdapUserProvider.php             # Custom Laravel Auth Guard ผ่าน LDAP
│
├── Http/
│   ├── Controllers/
│   │   ├── Controller.php               # Base controller (Laravel default)
│   │   ├── Auth/LoginController.php     # LDAP login / logout
│   │   ├── DashboardController.php      # Dashboard + stat cards + chart data
│   │   ├── UserController.php           # CRUD ผู้ใช้ + AD import + permissions + export CSV
│   │   ├── SystemController.php         # CRUD ระบบ + permission management + 2-way toggle
│   │   ├── AuditLogController.php       # Audit Log — filter, paginate, display
│   │   ├── ConnectorWizardController.php # Connector Wizard + AJAX endpoints (5 routes)
│   │   ├── QueueMonitorController.php   # Queue monitor + retry/flush failed jobs
│   │   ├── NotificationController.php   # Notification Channel CRUD
│   │   ├── ReportController.php         # Permission Matrix report + CSV Export
│   │   ├── ApiDocsController.php        # API Documentation page (custom UI)
│   │   └── Api/
│   │       ├── ApiAnnotations.php       # Swagger/OpenAPI annotation stubs (L5-Swagger)
│   │       ├── AuthController.php       # API token issue / revoke / user-login
│   │       ├── PermissionController.php # Permission query API (single / all / batch check)
│   │       └── UserExportController.php # CSV export API
│   └── Requests/
│       └── StoreNotificationChannelRequest.php  # Validation + authorization (Admin L2)
│
├── Jobs/
│   └── SyncPermissionsJob.php           # Queue Job — sync สิทธิ์ไประบบปลายทาง (tries=3, timeout=30s)
│
├── Models/
│   ├── User.php                         # Laravel default User model (unused — UCM ใช้ UcmUser)
│   ├── UcmUser.php                      # ผู้ใช้งาน (LDAP-based, is_admin 0/1/2)
│   ├── System.php                       # ระบบที่เชื่อมต่อ + db credentials + adapter config
│   ├── ConnectorConfig.php              # Connector Wizard configuration (JSON columns)
│   ├── SystemPermission.php             # Permission definition ของแต่ละระบบ
│   ├── UserSystemPermission.php         # User ↔ Permission mapping (pivot)
│   ├── SyncLog.php                      # Audit trail ทุกการ sync (status, error_message)
│   ├── AuditLog.php                     # Audit event log (6 หมวด, immutable)
│   └── NotificationChannel.php         # Notification channel (Email/Webhook, config JSON)
│
├── Providers/
│   └── AppServiceProvider.php          # Service provider หลัก (LDAP auth binding)
│
└── Services/
    ├── AuditLogger.php                  # Static helper — บันทึก AuditLog ทุกจุดในระบบ
    ├── LdapService.php                  # LDAP search / bind / attribute mapping
    └── NotificationService.php         # Dispatch notifications ไปยัง active channels

database/migrations/
    │
    │  ── Laravel default ─────────────────────────────────────────────────────
    ├── 0001_01_01_000000_create_users_table.php             # Laravel users table (default)
    ├── 0001_01_01_000001_create_cache_table.php             # Cache driver: database
    ├── 0001_01_01_000002_create_jobs_table.php              # Queue jobs + failed_jobs tables
    │
    │  ── UCM Core ────────────────────────────────────────────────────────────
    ├── 2026_03_19_..._create_ucm_users_table.php
    ├── 2026_03_19_..._create_systems_table.php
    ├── 2026_03_19_..._create_system_permissions_table.php
    ├── 2026_03_19_..._create_user_system_permissions_table.php
    ├── 2026_03_19_..._create_sync_logs_table.php
    ├── 2026_03_19_..._create_personal_access_tokens_table.php  # Sanctum
    │
    │  ── Alter migrations ────────────────────────────────────────────────────
    ├── 2026_03_19_..._add_is_exclusive_to_system_permissions_table.php
    ├── 2026_03_19_..._add_employee_number_to_ucm_users_table.php
    ├── 2026_03_19_..._add_remote_value_to_system_permissions_table.php
    ├── 2026_03_20_..._add_two_way_permissions_to_systems_table.php
    ├── 2026_03_20_..._change_is_admin_to_tinyint_in_ucm_users_table.php
    │
    │  ── Feature migrations ──────────────────────────────────────────────────
    ├── 2026_03_21_..._create_connector_configs_table.php       ← Connector Wizard
    ├── 2026_03_21_..._create_audit_logs_table.php              ← Audit Log
    └── 2026_03_22_..._create_notification_channels_table.php  ← Notification Channels

resources/views/
    ├── layouts/app.blade.php               # Main layout + sidebar accordion nav
    ├── welcome.blade.php                   # Laravel default (redirect ไปยัง login)
    ├── auth/login.blade.php                # หน้า Login (AD credentials)
    ├── dashboard.blade.php                 # Stats cards + Activity Charts (Chart.js)
    ├── users/
    │   ├── index.blade.php                 # รายการผู้ใช้ + search + AD import modal
    │   ├── show.blade.php                  # จัดการสิทธิ์ผู้ใช้ (per-system toggles)
    │   └── admin-levels.blade.php          # จัดการระดับ Admin ทั้งหมด
    ├── systems/
    │   ├── index.blade.php
    │   ├── show.blade.php                  # Permission definitions + Discover + 2-way toggle
    │   ├── create.blade.php
    │   └── edit.blade.php
    ├── connectors/
    │   ├── index.blade.php                 # รายการ Connector configs
    │   └── wizard.blade.php                # Multi-step Wizard UI (5 steps, vanilla JS)
    ├── audit/index.blade.php               # Audit Log (flatpickr date range filter)
    ├── queue/monitor.blade.php             # Queue Monitor + Retry/Flush actions
    ├── notifications/
    │   ├── index.blade.php                 # Notification Channels list + modals
    │   └── _form.blade.php                 # Reusable form partial (create/edit)
    ├── reports/
    │   └── permission-matrix.blade.php     # Permission Matrix (sticky headers, color bands)
    ├── api-docs/index.blade.php            # Custom API Documentation UI
    ├── components/
    │   ├── api-code-block.blade.php        # Reusable code block สำหรับ API docs
    │   ├── api-endpoint.blade.php          # Reusable endpoint card สำหรับ API docs
    │   └── api-group.blade.php             # Reusable endpoint group สำหรับ API docs
    └── docs/
        ├── manual.blade.php                # คู่มือผู้ใช้งาน (17 sections, sticky TOC)
        └── install.blade.php               # Install Guide สำหรับนักพัฒนา (11 sections)
```

---

## ความต้องการของระบบ

| Component | Version |
|-----------|---------|
| PHP | **8.3+** |
| Composer | **2.x** |
| Node.js | **20+ (LTS)** — Vite 8 ต้องการ Node 20 ขึ้นไป (แนะนำ `nvm install 22`) |
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

# 7. Install JS dependencies + build frontend assets (Node.js ≥ 20 required on HOST)
cd www/user-centralized-managment && npm install && npm run build
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

#### 2. ติดตั้ง Node.js 20+ ด้วย nvm

```bash
# ติดตั้ง nvm
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.40.1/install.sh | bash
source ~/.bashrc

# ติดตั้ง Node.js 22 (LTS)
nvm install 22
nvm use 22
node -v   # ควรแสดง v22.x.x
```

#### 3. Clone & Setup

```bash
cd /var/www
git clone <repo-url> user-centralized-managment
cd user-centralized-managment
cp .env.example .env
composer install --no-dev --optimize-autoloader
php artisan key:generate
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Build frontend assets
npm install && npm run build
```

#### 4. Nginx Virtual Host

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

#### 5. Queue Worker ด้วย Supervisor

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

#### 6. Migration & Seed

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

# ── Mail (สำหรับ Notification Channels ประเภท Email) ──────
MAIL_MAILER=smtp
MAIL_HOST=smtp.your-domain.com
MAIL_PORT=587
MAIL_USERNAME=ucm@your-domain.com
MAIL_PASSWORD=your_mail_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=ucm@your-domain.com
MAIL_FROM_NAME="UCM Notification"

# ── UCM Settings ─────────────────────────────────────────
UCM_ALLOWED_DEPARTMENT="Systems Development and IT"  # เว้นว่างเพื่ออนุญาตทุกแผนก
UCM_AUDIT_DEPARTMENTS="Safety,Quality Assurance"     # แผนกที่ดู Audit Log ได้ (Read-Only) คั่นด้วย ,

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
| `GET` | `/` | Dashboard + Charts | ทุกคน |
| `GET` | `/users` | รายการผู้ใช้ | ทุกคน |
| `GET` | `/users/export` | Export CSV ผู้ใช้ทั้งหมด | ทุกคน |
| `POST` | `/users/import` | นำเข้าผู้ใช้จาก AD | L1+ |
| `POST` | `/users/import-bulk` | นำเข้าผู้ใช้จาก AD (Bulk) | L1+ |
| `GET` | `/users/{id}` | จัดการสิทธิ์ผู้ใช้ | L1+ |
| `PATCH` | `/users/{id}/permissions` | บันทึกสิทธิ์ผู้ใช้ | L1+ |
| `PATCH` | `/users/{id}/status` | เปิด/ปิดใช้งาน Account | L1+ |
| `DELETE` | `/users/{id}` | ลบผู้ใช้ | L2 |
| `POST` | `/users/{id}/check-ad` | ตรวจสอบข้อมูลผู้ใช้ใน AD | L1+ |
| `GET` | `/systems` | ระบบที่เชื่อมต่อทั้งหมด | ทุกคน |
| `GET/POST` | `/systems/create` | สร้างระบบใหม่ | L2 |
| `GET` | `/systems/{id}` | รายละเอียดระบบ + Permissions | ทุกคน |
| `GET/PATCH` | `/systems/{id}/edit` | แก้ไขระบบ | L2 |
| `DELETE` | `/systems/{id}` | ลบระบบ | L2 |
| `POST` | `/systems/{id}/permissions` | เพิ่ม Permission | L1+ |
| `PATCH` | `/systems/{id}/permissions/{pid}` | แก้ไข Permission | L1+ |
| `DELETE` | `/systems/{id}/permissions/{pid}` | ลบ Permission | L2 |
| `POST` | `/systems/{id}/discover` | Discover Permissions จากระบบปลายทาง | L1+ |
| `POST` | `/systems/{id}/toggle-2way` | เปิด/ปิด 2-Way Sync | L2 |
| `GET` | `/connectors` | รายการ Connector Wizard | L2 |
| `GET` | `/connectors/wizard` | สร้าง Connector ใหม่ | L2 |
| `GET` | `/connectors/{id}/edit` | แก้ไข Connector | L2 |
| `DELETE` | `/connectors/{id}` | ลบ Connector | L2 |
| `GET` | `/admin/levels` | จัดการระดับ Admin | L2 |
| `POST` | `/admin/levels/{id}` | อัปเดตระดับ Admin ของผู้ใช้ | L2 |
| `GET` | `/audit-log` | Audit Log (filter ได้ตาม category/actor/date) | Admin ทุกระดับ / SQA / QA |
| `GET` | `/queue/monitor` | Queue Monitor | L1+ |
| `POST` | `/queue/monitor/retry/{uuid}` | Retry Failed Job | L2 |
| `POST` | `/queue/monitor/retry-all` | Retry ทุก Failed Job | L2 |
| `DELETE` | `/queue/monitor/failed/{uuid}` | ลบ Failed Job | L2 |
| `POST` | `/queue/monitor/flush` | ล้าง Failed Jobs ทั้งหมด | L2 |
| `GET` | `/notifications` | Notification Channels | L2 |
| `POST` | `/notifications` | สร้าง Notification Channel | L2 |
| `PUT` | `/notifications/{id}` | แก้ไข Notification Channel | L2 |
| `DELETE` | `/notifications/{id}` | ลบ Notification Channel | L2 |
| `GET` | `/reports/permission-matrix` | Permission Matrix Report | L2 |
| `GET` | `/reports/permission-matrix/export` | Export Permission Matrix เป็น CSV | L2 |
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

## Dashboard Charts

หน้า Dashboard แสดงกราฟสถิติ 3 ชุด ขับเคลื่อนด้วย **Chart.js** (bundled ผ่าน Vite):

| กราฟ | ประเภท | ข้อมูล |
|------|--------|--------|
| **Audit Activity (7 วัน)** | Stacked Bar | จำนวน Audit Event แยกตาม Category ใน 7 วันล่าสุด |
| **Sync Activity (7 วัน)** | Stacked Bar | จำนวน Sync สำเร็จ / ล้มเหลว ใน 7 วันล่าสุด |
| **Permissions per System** | Horizontal Bar | จำนวน Permission ที่ Active ในแต่ละระบบ (Top 8) |

กราฟใช้สี Indigo/Violet ตาม design system ของ UCM และฟอนต์ Inter ทั้งหมด

---

## Notification Channels

ระบบแจ้งเตือนอัตโนมัติเมื่อเกิด event สำคัญใน UCM รองรับ 2 ช่องทาง:

### Email

ส่งอีเมลหาผู้รับหลายคน (คั่นด้วย comma) ผ่าน SMTP ที่กำหนดใน `.env`

```
Subject: [UCM] อัปเดตสิทธิ์ john.doe
Body:
  **Event:** `permissions_updated`
  **user_id:** 42
  **username:** john.doe
  ...
  เวลา: 22/03/2026 10:00:00
```

### Webhook

ส่ง HTTP POST พร้อม JSON payload ไปยัง URL ที่กำหนด

```json
{
  "event": "permissions_updated",
  "payload": {
    "user_id": 42,
    "username": "john.doe",
    "description": "อัปเดตสิทธิ์ john.doe"
  },
  "timestamp": "2026-03-22T10:00:00+07:00",
  "source": "UCM"
}
```

เมื่อตั้งค่า **Secret** ระบบจะเพิ่ม Header `X-UCM-Signature` สำหรับตรวจสอบความถูกต้อง:

```
X-UCM-Signature: <HMAC-SHA256(json_body, secret)>
```

### Events ที่รองรับ

| Event Key | เกิดขึ้นเมื่อ |
|-----------|--------------|
| `permissions_updated` | Admin บันทึกการเปลี่ยนแปลงสิทธิ์ผู้ใช้ |
| `user_imported` | นำเข้าผู้ใช้รายคนจาก Active Directory |
| `user_bulk_imported` | นำเข้าผู้ใช้แบบ Bulk จาก Active Directory |
| `user_removed` | ลบผู้ใช้ออกจากระบบ UCM |
| `admin_level_updated` | เปลี่ยนระดับ Admin ของผู้ใช้ |
| `system_created` | เพิ่มระบบที่เชื่อมต่อใหม่ |
| `system_updated` | แก้ไขข้อมูลระบบที่เชื่อมต่อ |
| `system_deleted` | ลบระบบที่เชื่อมต่อ |
| `login_failed` | Login ล้มเหลว (รหัสผ่านผิด หรือแผนกไม่มีสิทธิ์) |
| `*` | Wildcard — รับแจ้งเตือนทุก event |

### การจัดการ Channel

ไปที่ **ผู้ดูแลระบบ → Notifications** (ต้องการ Admin L2):
- **เพิ่ม** — กดปุ่ม "+ เพิ่ม Channel" มุมขวาบน
- **เปิด/ปิด** — Toggle `is_active` บน Channel ได้โดยไม่ต้องลบ
- **แก้ไข** — กดไอคอน Edit บน Channel card
- **ลบ** — กดไอคอน Delete พร้อม Confirm dialog

---

## Permission Matrix Report

รายงานตารางครอสแทบแสดง **ใครมีสิทธิ์อะไรในระบบไหน** ในมุมมองเดียว

เข้าถึงได้ที่ **ผู้ดูแลระบบ → Permission Matrix** (ต้องการ Admin L2)

### คุณสมบัติของตาราง

| คุณสมบัติ | รายละเอียด |
|-----------|------------|
| **Sticky Row Headers** | แถวชื่อระบบและแถว Permission label ยึดอยู่กับที่ขณะ Scroll แนวตั้ง |
| **Sticky Column** | คอลัมน์ Username ยึดอยู่กับที่ขณะ Scroll แนวนอน |
| **Rotated Labels** | ชื่อ Permission แสดงแนวตั้ง (`writing-mode: vertical-rl`) ประหยัดพื้นที่แนวนอน |
| **System Color Bands** | แต่ละระบบมีสีพื้นหลังแยกกัน ทำให้แยกกลุ่มสิทธิ์ได้ชัดเจน |
| **Permission Count Badge** | แต่ละแถวแสดงจำนวนสิทธิ์รวมทุกระบบ |
| **Column Hover Highlight** | วาง cursor บนคอลัมน์ใดจะ highlight ทั้งคอลัมน์นั้น |
| **Scroll Container** | ตาราง scroll แบบ `overflow: auto` ในกรอบของตัวเอง — ไม่กระทบ scroll ของหน้า |

### ตัวกรอง

- **ระบบ** — เลือกดูเฉพาะระบบที่สนใจ (หรือเว้นว่างเพื่อดูทุกระบบ)
- **ค้นหา** — กรองตาม Username, ชื่อ, แผนก, หรือตำแหน่ง

### Export CSV

กด **Export CSV** เพื่อดาวน์โหลดตารางทั้งหมด (ตัวกรองที่เลือกอยู่จะถูก apply ใน export ด้วย)

- รูปแบบ UTF-8 BOM — เปิดใน Excel ได้ทันทีโดยไม่มี encoding ผิดพลาด
- Header row: `Username`, `ชื่อ`, `แผนก`, `ตำแหน่ง`, `{ระบบ} — {Permission}`, ..., `Total`
- ✓ = มีสิทธิ์, ว่าง = ไม่มีสิทธิ์

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

> **Security:** identifier ชื่อตาราง/คอลัมน์ทุกตัวผ่าน `qi()` helper ที่มี whitelist regex `[\w.]+` + backtick/double-quote quoting ตาม driver ก่อน execute — รองรับ schema-qualified names (`schema.table`) อย่างถูกต้อง

---

## Admin Levels

| Level | บทบาท | ความสามารถ |
|-------|-------|------------|
| `0` | ผู้ใช้ทั่วไป | ดูข้อมูลและสิทธิ์ได้ (read-only) — ไม่สามารถแก้ไขได้ |
| `1` | Admin L1 | จัดการสิทธิ์ผู้ใช้, นำเข้า AD, Discover Permissions, เพิ่ม Reference Data, ดู Queue Monitor |
| `2` | Admin L2 (Super Admin) | ทุกอย่าง + จัดการระบบ, Connector Wizard, Toggle 2-way, แก้ไข/ลบ Reference Data, กำหนดระดับ Admin, Notifications, Reports |

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

## Audit Log

ระบบบันทึก Audit Event ทุกการกระทำที่สำคัญโดยอัตโนมัติ ดูได้ที่ `/audit-log`

### หมวดเหตุการณ์ (Category)

| หมวด | เหตุการณ์ที่บันทึก |
|---|---|
| `auth` | Login, Login Failed (รวมถึงกรณีแผนกไม่ได้รับอนุญาต), Logout |
| `users` | Import, Bulk Import, Update Info, Remove, Admin Level Changed |
| `permissions` | Update Permissions, Account Status Changed, Discover Permissions |
| `systems` | Create/Update/Delete System, Toggle 2-Way, Create/Update/Delete Permission Definition, Discover Permission Definitions |
| `connectors` | Create/Update/Delete Connector Config |
| `api` | Issue Token, Revoke Token, User Login via API |

### สิทธิ์การเข้าถึง

| บทบาท | สิทธิ์ |
|---|---|
| Admin (ทุกระดับ) | ดูบันทึกทั้งหมดได้ |
| แผนกที่กำหนดใน `UCM_AUDIT_DEPARTMENTS` | ดูได้อย่างเดียว (Read-Only) ไม่สามารถแก้ไขหรือลบ |

```env
# กำหนดแผนกที่ดู Audit Log ได้ (คั่นด้วย , เพิ่มได้ในอนาคต)
UCM_AUDIT_DEPARTMENTS="Safety,Quality Assurance"
```

### ข้อมูลที่บันทึกในแต่ละ Event

- **actor** — Username ผู้ดำเนินการ + IP Address + User Agent
- **subject** — เป้าหมาย (ชื่อผู้ใช้, ชื่อระบบ ฯลฯ)
- **description** — คำอธิบายเหตุการณ์ภาษาไทย
- **metadata** — JSON รายละเอียดเพิ่มเติม (เช่น permission ก่อน/หลัง)
- **created_at** — เวลาที่เกิดเหตุการณ์ (ไม่มี updated_at — Immutable)

---

## Security Notes

- รหัสผ่านฐานข้อมูลปลายทางใน `connector_configs.db_password` ถูกเก็บใน plaintext — แนะนำให้ใช้ DB user ที่มีสิทธิ์ขั้นต่ำ (SELECT บน users table, INSERT/DELETE บน permissions table เท่านั้น)
- CSRF protection เปิดใช้งานบนทุก POST/PUT/DELETE route
- SQL Injection prevention: identifier ชื่อตาราง/คอลัมน์ผ่าน `qi()` helper + whitelist regex `[\w.]+`; ค่า parameter ทุกตัวผ่าน PDO prepared statement / `$pdo->quote()`
- Session timeout 120 นาที (ปรับได้ใน `.env SESSION_LIFETIME`)
- ข้อมูล `db_password` และ `api_token` ถูก hidden ใน Model `$hidden` — ไม่ถูกส่งออกใน JSON response
- Authorization ทุก mutation route ใช้ `abort_unless()` ตามระดับที่เหมาะสม: `isAdmin()` สำหรับ L1 ขึ้นไป, `isSuperAdmin()` สำหรับ L2 (เช่น CRUD System, Notification Channels, Connector Wizard, จัดการ Admin)
- UUID ของ Failed Job ถูก validate ด้วย `Str::isUuid()` ก่อนส่งให้ Artisan command
- Webhook Secret ใช้ HMAC-SHA256 — ระบบปลายทางควรตรวจสอบ `X-UCM-Signature` header ก่อนประมวลผล payload

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

# Build frontend assets (production)
cd www/user-centralized-managment && npm run build

# Start Vite dev server (hot reload สำหรับ development)
cd www/user-centralized-managment && npm run dev
```

> **หมายเหตุ Node.js:** Vite 8 ต้องการ Node.js **20+** บน host machine
> ถ้า `node -v` แสดงเวอร์ชันต่ำกว่านั้น ให้ใช้ nvm: `nvm install 22 && nvm use 22`

---

## เครื่องมือที่ใช้พัฒนา

โปรเจคนี้พัฒนาด้วยความช่วยเหลือของ:

- **[Claude Code](https://claude.ai/claude-code)** by Anthropic — AI coding assistant (Claude Sonnet 4.6)
  ช่วยในการออกแบบ Architecture, เขียนโค้ด, Code Review, Refactoring และสร้างฟีเจอร์ Connector Wizard

---

## License

Internal use only — Bangkok Flight Services
&copy; 2026 BFS IT Team
