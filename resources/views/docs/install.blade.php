@extends('layouts.app')

@section('title', 'Install Guide')
@section('header', 'Install Guide')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-slate-600 font-medium transition-colors whitespace-nowrap">Dashboard</a>
    <svg class="w-3.5 h-3.5 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="font-semibold text-slate-800 truncate">Install Guide</span>
@endsection

@section('content')

@php
$sections = [
    ['id' => 'prerequisites', 'label' => 'Prerequisites'],
    ['id' => 'docker',        'label' => 'Docker Setup'],
    ['id' => 'non-docker',    'label' => 'Non-Docker Setup'],
    ['id' => 'env',           'label' => 'Environment Config'],
    ['id' => 'migrate',       'label' => 'Migration & Seeder'],
    ['id' => 'first-admin',   'label' => 'ตั้งค่า Super Admin'],
    ['id' => 'ldap',          'label' => 'LDAP / AD Config'],
    ['id' => 'queue',         'label' => 'Queue Worker'],
    ['id' => 'connector',     'label' => 'Connector Wizard'],
    ['id' => 'client',        'label' => 'UCM Client (Legacy)'],
    ['id' => 'api',           'label' => 'API Authentication'],
];
@endphp

<div class="flex gap-8 items-start">

    {{-- Sticky TOC --}}
    <aside class="hidden xl:block w-56 flex-shrink-0 sticky top-24 self-start">
        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 p-4">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-3">สารบัญ</p>
            <nav class="space-y-0.5" id="toc-nav">
                @foreach ($sections as $s)
                    <a href="#{{ $s['id'] }}"
                       class="toc-link flex items-center gap-2 px-3 py-2 rounded-xl text-sm text-slate-500 hover:text-slate-800 hover:bg-slate-50 transition-colors">
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-300 flex-shrink-0"></span>
                        {{ $s['label'] }}
                    </a>
                @endforeach
            </nav>
        </div>
    </aside>

    {{-- Content --}}
    <div class="flex-1 min-w-0 space-y-6">

        {{-- Header --}}
        <div class="relative overflow-hidden rounded-2xl p-7"
             style="background:linear-gradient(135deg,#0f172a 0%,#1e293b 50%,#0f172a 100%)">
            <div class="absolute inset-0 pointer-events-none"
                 style="background-image:linear-gradient(rgba(99,102,241,0.08) 1px,transparent 1px),linear-gradient(90deg,rgba(99,102,241,0.08) 1px,transparent 1px);background-size:28px 28px"></div>
            <div class="absolute -top-8 -right-8 w-48 h-48 bg-indigo-600/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="relative flex items-center gap-4">
                <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white">UCM Install Guide</h1>
                    <p class="text-slate-400 text-sm mt-1">Developer documentation — v1.0 — Laravel 13 / PHP 8.3</p>
                </div>
            </div>
        </div>

        {{-- ── Prerequisites ── --}}
        <div id="prerequisites" class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <div class="w-8 h-8 bg-indigo-100 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
                <h2 class="font-bold text-slate-800">Prerequisites</h2>
            </div>
            <div class="px-6 py-5 text-sm text-slate-700">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach ([
                        ['label' => 'PHP 8.3', 'note' => 'pdo_mysql, ldap, curl, mbstring, zip, bcmath, intl extensions', 'color' => 'violet'],
                        ['label' => 'MySQL 8.0', 'note' => 'ฐานข้อมูลหลัก UCM (รันใน Docker container ucm-db)', 'color' => 'sky'],
                        ['label' => 'Composer ≥ 2', 'note' => 'PHP package manager (built-in ใน Docker image)', 'color' => 'emerald'],
                        ['label' => 'Node.js ≥ 20 (LTS)', 'note' => 'สำหรับ build frontend assets ด้วย Vite 8 — แนะนำ nvm install 22', 'color' => 'lime'],
                        ['label' => 'Active Directory / LDAP', 'note' => 'Authentication + import users', 'color' => 'amber'],
                        ['label' => 'Docker & Compose', 'note' => 'สำหรับ Docker deployment (แนะนำ)', 'color' => 'indigo'],
                        ['label' => 'pdo_sqlsrv (optional)', 'note' => 'เฉพาะระบบที่ต้องเชื่อมต่อ SQL Server ปลายทาง (built-in ใน Docker image)', 'color' => 'slate'],
                    ] as $req)
                        <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <div class="w-2 h-2 rounded-full bg-{{ $req['color'] }}-500 flex-shrink-0"></div>
                            <div>
                                <div class="font-semibold text-slate-900 text-xs">{{ $req['label'] }}</div>
                                <div class="text-slate-500 text-xs">{{ $req['note'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ── Docker Setup ── --}}
        <div id="docker" class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <div class="w-8 h-8 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <h2 class="font-bold text-slate-800">Docker Setup</h2>
                <span class="ml-auto text-xs font-semibold text-emerald-700 bg-emerald-100 px-2.5 py-1 rounded-full">แนะนำ</span>
            </div>
            <div class="px-6 py-5 space-y-4 text-sm">
                <p class="text-slate-600">โปรเจคใช้ Docker Compose ซึ่งอยู่ที่ root directory ของ nginx-proxy (<code class="font-mono bg-slate-100 px-1.5 py-0.5 rounded text-xs">docker-compose.yml</code>) มี containers ที่เกี่ยวข้อง:</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach ([
                        ['name' => 'ucm-db', 'color' => 'sky', 'desc' => 'MySQL 8.0 — ฐานข้อมูลหลัก UCM (database: ucm_db)'],
                        ['name' => 'php83', 'color' => 'indigo', 'desc' => 'PHP 8.3-FPM — รัน Laravel application'],
                        ['name' => 'ucm-queue', 'color' => 'violet', 'desc' => 'Queue Worker — ใช้ image php83 เดียวกัน รัน queue:work'],
                        ['name' => 'web-router', 'color' => 'emerald', 'desc' => 'Nginx alpine — reverse proxy, route /user-centralized-managment → php83'],
                        ['name' => 'phpmyadmin', 'color' => 'amber', 'desc' => 'phpMyAdmin — จัดการ DB ผ่าน port 8181'],
                        ['name' => 'nginx-proxy-manager', 'color' => 'slate', 'desc' => 'Nginx Proxy Manager — SSL termination + domain routing'],
                    ] as $c)
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <code class="text-xs font-mono font-bold text-{{ $c['color'] }}-700">{{ $c['name'] }}</code>
                            <p class="text-xs text-slate-600 mt-1">{{ $c['desc'] }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="bg-slate-50 rounded-xl border border-slate-200 p-4 text-xs space-y-1 text-slate-600">
                    <p class="font-bold text-slate-700 mb-2">โครงสร้าง Directory</p>
                    <div class="font-mono space-y-0.5">
                        <div><span class="text-slate-400">nginx-proxy/</span></div>
                        <div class="pl-4"><span class="text-slate-400">├── docker-compose.yml</span></div>
                        <div class="pl-4"><span class="text-slate-400">├── php83/</span> <span class="text-slate-500 font-sans">← Dockerfile + custom.ini</span></div>
                        <div class="pl-4"><span class="text-slate-400">├── web-router/default.conf</span> <span class="text-slate-500 font-sans">← Nginx config</span></div>
                        <div class="pl-4"><span class="text-indigo-600 font-semibold">└── www/user-centralized-managment/</span> <span class="text-slate-500 font-sans">← Laravel app root</span></div>
                    </div>
                </div>

                @foreach ([
                    '# 1. Clone โปรเจคเข้า www/',
                    'git clone <repo-url> www/user-centralized-managment',
                    '# 2. Copy .env และแก้ไขค่าให้ครบ (ดูหัวข้อ Environment Config)',
                    'cp www/user-centralized-managment/.env.example www/user-centralized-managment/.env',
                    '# 3. Start containers ทั้งหมด',
                    'docker compose up -d',
                    '# 4. Install PHP dependencies',
                    'docker exec -w /var/www/html/user-centralized-managment php83 composer install --no-dev --optimize-autoloader',
                    '# 5. Generate app key',
                    'docker exec -w /var/www/html/user-centralized-managment php83 php artisan key:generate',
                    '# 6. Run migrations + seed',
                    'docker exec -w /var/www/html/user-centralized-managment php83 php artisan migrate --seed',
                    '# 7. Install JS dependencies + build frontend assets (requires Node.js ≥ 20 on HOST)',
                    'cd www/user-centralized-managment && npm install && npm run build',
                ] as $cmd)
                    <div class="bg-slate-900 rounded-xl px-4 py-2.5 font-mono text-xs {{ str_starts_with($cmd, '#') ? 'text-slate-500' : 'text-slate-300' }} overflow-x-auto">{{ $cmd }}</div>
                @endforeach

                <div class="flex items-start gap-3 p-3.5 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800">
                    <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    <span>เมื่อแก้ไขไฟล์ Adapter หรือ Job ต้อง <code class="font-mono bg-amber-100 px-1 rounded">docker restart ucm-queue</code> ทุกครั้ง เพราะ PHP process โหลด code ไว้ใน memory แล้ว</span>
                </div>
            </div>
        </div>

        {{-- ── Non-Docker Setup ── --}}
        <div id="non-docker" class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <div class="w-8 h-8 bg-sky-100 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/>
                    </svg>
                </div>
                <h2 class="font-bold text-slate-800">Non-Docker Setup (Bare Server)</h2>
            </div>
            <div class="px-6 py-5 space-y-5 text-sm">

                {{-- 1. System Requirements --}}
                <div>
                    <h3 class="font-bold text-slate-900 mb-2 flex items-center gap-2">
                        <span class="w-5 h-5 bg-sky-600 text-white text-xs font-bold rounded-full flex items-center justify-center flex-shrink-0">1</span>
                        ติดตั้ง PHP 8.3 + Extensions
                    </h3>
                    @foreach ([
                        '# Ubuntu / Debian',
                        'sudo add-apt-repository ppa:ondrej/php',
                        'sudo apt update',
                        'sudo apt install php8.3 php8.3-fpm php8.3-cli php8.3-curl php8.3-mbstring \\',
                        '  php8.3-xml php8.3-zip php8.3-ldap php8.3-bcmath php8.3-intl',
                        '',
                        '# ติดตั้ง Microsoft ODBC Driver + pdo_sqlsrv',
                        'curl https://packages.microsoft.com/keys/microsoft.asc | sudo apt-key add -',
                        'curl https://packages.microsoft.com/config/ubuntu/$(lsb_release -rs)/prod.list \\',
                        '  | sudo tee /etc/apt/sources.list.d/mssql-release.list',
                        'sudo apt update && sudo ACCEPT_EULA=Y apt install msodbcsql18 unixodbc-dev',
                        'sudo pecl install sqlsrv pdo_sqlsrv',
                        'echo "extension=sqlsrv.so" | sudo tee /etc/php/8.3/mods-available/sqlsrv.ini',
                        'echo "extension=pdo_sqlsrv.so" | sudo tee /etc/php/8.3/mods-available/pdo_sqlsrv.ini',
                        'sudo phpenmod sqlsrv pdo_sqlsrv && sudo systemctl restart php8.3-fpm',
                    ] as $cmd)
                        @if($cmd === '')
                            <div class="h-1"></div>
                        @else
                            <div class="bg-slate-900 rounded-xl px-4 py-2 font-mono text-xs {{ str_starts_with($cmd, '#') ? 'text-slate-500' : 'text-slate-300' }} overflow-x-auto mt-1">{{ $cmd }}</div>
                        @endif
                    @endforeach
                </div>

                {{-- 2. Composer --}}
                <div>
                    <h3 class="font-bold text-slate-900 mb-2 flex items-center gap-2">
                        <span class="w-5 h-5 bg-sky-600 text-white text-xs font-bold rounded-full flex items-center justify-center flex-shrink-0">2</span>
                        ติดตั้ง Composer
                    </h3>
                    @foreach ([
                        'curl -sS https://getcomposer.org/installer | php',
                        'sudo mv composer.phar /usr/local/bin/composer',
                    ] as $cmd)
                        <div class="bg-slate-900 rounded-xl px-4 py-2 font-mono text-xs text-slate-300 overflow-x-auto mb-1">{{ $cmd }}</div>
                    @endforeach
                </div>

                {{-- 3. Clone & Setup --}}
                <div>
                    <h3 class="font-bold text-slate-900 mb-2 flex items-center gap-2">
                        <span class="w-5 h-5 bg-sky-600 text-white text-xs font-bold rounded-full flex items-center justify-center flex-shrink-0">3</span>
                        Clone โปรเจคและติดตั้ง
                    </h3>
                    @foreach ([
                        'cd /var/www',
                        'git clone <repo-url> user-centralized-managment',
                        'cd user-centralized-managment',
                        'cp .env.example .env',
                        'composer install --no-dev --optimize-autoloader',
                        'php artisan key:generate',
                        '# ตั้งสิทธิ์ directory',
                        'sudo chown -R www-data:www-data storage bootstrap/cache',
                        'sudo chmod -R 775 storage bootstrap/cache',
                    ] as $cmd)
                        <div class="bg-slate-900 rounded-xl px-4 py-2 font-mono text-xs {{ str_starts_with($cmd, '#') ? 'text-slate-500' : 'text-slate-300' }} overflow-x-auto mb-1">{{ $cmd }}</div>
                    @endforeach
                </div>

                {{-- 4. Nginx Config --}}
                <div>
                    <h3 class="font-bold text-slate-900 mb-2 flex items-center gap-2">
                        <span class="w-5 h-5 bg-sky-600 text-white text-xs font-bold rounded-full flex items-center justify-center flex-shrink-0">4</span>
                        Nginx Virtual Host
                    </h3>
                    <div class="bg-slate-900 rounded-xl px-4 py-4 font-mono text-xs text-slate-300 overflow-x-auto space-y-0.5">
                        <div class="text-slate-500"># /etc/nginx/sites-available/ucm.conf</div>
                        <div>server &#123;</div>
                        <div class="pl-4">listen <span class="text-amber-300">80</span>;</div>
                        <div class="pl-4">server_name <span class="text-green-300">your-domain.com</span>;</div>
                        <div class="pl-4">root <span class="text-green-300">/var/www/user-centralized-managment/public</span>;</div>
                        <div class="pl-4">index index.php;</div>
                        <div class="pl-4 mt-2">location / &#123;</div>
                        <div class="pl-8">try_files $uri $uri/ /index.php?$query_string;</div>
                        <div class="pl-4">&#125;</div>
                        <div class="pl-4 mt-2">location ~ \.php$ &#123;</div>
                        <div class="pl-8">fastcgi_pass unix:/run/php/php8.3-fpm.sock;</div>
                        <div class="pl-8">fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;</div>
                        <div class="pl-8">include fastcgi_params;</div>
                        <div class="pl-4">&#125;</div>
                        <div>&#125;</div>
                    </div>
                    @foreach ([
                        'sudo ln -s /etc/nginx/sites-available/ucm.conf /etc/nginx/sites-enabled/',
                        'sudo nginx -t && sudo systemctl reload nginx',
                    ] as $cmd)
                        <div class="bg-slate-900 rounded-xl px-4 py-2 font-mono text-xs text-slate-300 overflow-x-auto mt-2">{{ $cmd }}</div>
                    @endforeach
                </div>

                {{-- 5. Queue Worker via Supervisor --}}
                <div>
                    <h3 class="font-bold text-slate-900 mb-2 flex items-center gap-2">
                        <span class="w-5 h-5 bg-sky-600 text-white text-xs font-bold rounded-full flex items-center justify-center flex-shrink-0">5</span>
                        Queue Worker ด้วย Supervisor
                    </h3>
                    <p class="text-slate-600 text-xs mb-3">ใช้ Supervisor เพื่อให้ Queue Worker รันตลอดเวลาและ restart อัตโนมัติเมื่อ crash</p>
                    @foreach ([
                        'sudo apt install supervisor',
                    ] as $cmd)
                        <div class="bg-slate-900 rounded-xl px-4 py-2 font-mono text-xs text-slate-300 overflow-x-auto mb-2">{{ $cmd }}</div>
                    @endforeach
                    <div class="bg-slate-900 rounded-xl px-4 py-4 font-mono text-xs text-slate-300 overflow-x-auto space-y-0.5">
                        <div class="text-slate-500"># /etc/supervisor/conf.d/ucm-queue.conf</div>
                        <div>[program:<span class="text-amber-300">ucm-queue</span>]</div>
                        <div>process_name=%(program_name)s_%(process_num)02d</div>
                        <div>command=<span class="text-green-300">php /var/www/user-centralized-managment/artisan queue:work --sleep=3 --tries=3 --max-time=3600</span></div>
                        <div>autostart=true</div>
                        <div>autorestart=true</div>
                        <div>stopasgroup=true</div>
                        <div>killasgroup=true</div>
                        <div>user=<span class="text-amber-300">www-data</span></div>
                        <div>numprocs=1</div>
                        <div>redirect_stderr=true</div>
                        <div>stdout_logfile=<span class="text-green-300">/var/log/ucm-queue.log</span></div>
                    </div>
                    @foreach ([
                        'sudo supervisorctl reread && sudo supervisorctl update',
                        'sudo supervisorctl start ucm-queue:*',
                        '# ดู status',
                        'sudo supervisorctl status ucm-queue:*',
                        '# Reload หลังแก้ไข code (แทน docker restart)',
                        'sudo supervisorctl restart ucm-queue:*',
                    ] as $cmd)
                        <div class="bg-slate-900 rounded-xl px-4 py-2 font-mono text-xs {{ str_starts_with($cmd, '#') ? 'text-slate-500' : 'text-slate-300' }} overflow-x-auto mt-1">{{ $cmd }}</div>
                    @endforeach
                </div>

                {{-- 6. Migration --}}
                <div>
                    <h3 class="font-bold text-slate-900 mb-2 flex items-center gap-2">
                        <span class="w-5 h-5 bg-sky-600 text-white text-xs font-bold rounded-full flex items-center justify-center flex-shrink-0">6</span>
                        Migration & Seed
                    </h3>
                    @foreach ([
                        'php artisan migrate',
                        '# Seed ตามระบบที่ใช้งาน',
                        'php artisan db:seed --class=EarthSeeder',
                        'php artisan db:seed --class=EFilingSeeder',
                        'php artisan db:seed --class=RepairSystemSeeder',
                        '# หรือ seed ทั้งหมด',
                        'php artisan migrate --seed',
                    ] as $cmd)
                        <div class="bg-slate-900 rounded-xl px-4 py-2 font-mono text-xs {{ str_starts_with($cmd, '#') ? 'text-slate-500' : 'text-slate-300' }} overflow-x-auto mb-1">{{ $cmd }}</div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ── Environment Config ── --}}
        <div id="env" class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <div class="w-8 h-8 bg-amber-100 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h2 class="font-bold text-slate-800">Environment Config (.env)</h2>
            </div>
            <div class="px-6 py-5 space-y-4 text-sm">
                @foreach ([
                    ['section' => 'App', 'color' => 'indigo', 'vars' => [
                        ['key' => 'APP_NAME', 'val' => '"User Centralized Management"'],
                        ['key' => 'APP_ENV', 'val' => 'local', 'note' => 'local | production'],
                        ['key' => 'APP_KEY', 'val' => 'base64:...', 'note' => 'สร้างด้วย php artisan key:generate'],
                        ['key' => 'APP_DEBUG', 'val' => 'true', 'note' => 'ปิดใน production'],
                        ['key' => 'APP_URL', 'val' => 'https://your-domain.com/user-centralized-managment'],
                    ]],
                    ['section' => 'Database (UCM — MySQL 8.0)', 'color' => 'sky', 'vars' => [
                        ['key' => 'DB_CONNECTION', 'val' => 'mysql'],
                        ['key' => 'DB_HOST', 'val' => 'ucm-db', 'note' => 'Docker container name'],
                        ['key' => 'DB_PORT', 'val' => '3306'],
                        ['key' => 'DB_DATABASE', 'val' => 'ucm_db'],
                        ['key' => 'DB_USERNAME', 'val' => 'ucm_user'],
                        ['key' => 'DB_PASSWORD', 'val' => 'ucm_password'],
                    ]],
                    ['section' => 'Queue & Session & Cache', 'color' => 'violet', 'vars' => [
                        ['key' => 'QUEUE_CONNECTION', 'val' => 'database', 'note' => 'ใช้ jobs table ใน DB'],
                        ['key' => 'SESSION_DRIVER', 'val' => 'database'],
                        ['key' => 'CACHE_STORE', 'val' => 'database'],
                    ]],
                    ['section' => 'LDAP / Active Directory', 'color' => 'emerald', 'vars' => [
                        ['key' => 'LDAP_HOST', 'val' => 'DOMAIN.COM', 'note' => 'AD domain หรือ DC IP'],
                        ['key' => 'LDAP_PORT', 'val' => '389', 'note' => '636 = LDAPS'],
                        ['key' => 'LDAP_BASE_DN', 'val' => '"OU=User,DC=DOMAIN,DC=COM"'],
                        ['key' => 'LDAP_BIND_DN', 'val' => '"DOMAIN\\service_account"', 'note' => 'service account'],
                        ['key' => 'LDAP_BIND_PASSWORD', 'val' => '"password"'],
                        ['key' => 'LDAP_USER_FILTER', 'val' => '(sAMAccountName={username})'],
                        ['key' => 'LDAP_USERNAME_ATTRIBUTE', 'val' => 'sAMAccountName'],
                    ]],
                    ['section' => 'UCM Settings', 'color' => 'amber', 'vars' => [
                        ['key' => 'UCM_ALLOWED_DEPARTMENT', 'val' => '"Systems Development and IT"', 'note' => 'แผนกที่อนุญาต Login (เว้นว่างเพื่ออนุญาตทุกแผนก)'],
                        ['key' => 'UCM_AUDIT_DEPARTMENTS', 'val' => '"Safety,Quality Assurance"', 'note' => 'แผนกที่ดู Audit Log ได้ (Read-Only) คั่นด้วย , เพิ่มแผนกได้ในอนาคต'],
                    ]],
                    ['section' => 'Swagger / API Docs', 'color' => 'rose', 'vars' => [
                        ['key' => 'SWAGGER_GENERATE_ALWAYS', 'val' => 'false', 'note' => 'false = ใช้ cached JSON (แนะนำ)'],
                        ['key' => 'L5_SWAGGER_UI_DOC_EXPANSION', 'val' => 'full'],
                    ]],
                ] as $group)
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-2 h-2 rounded-full bg-{{ $group['color'] }}-500"></div>
                            <span class="text-xs font-bold text-slate-600 uppercase tracking-wide">{{ $group['section'] }}</span>
                        </div>
                        <div class="bg-slate-900 rounded-xl overflow-hidden">
                            @foreach ($group['vars'] as $v)
                                <div class="flex items-baseline gap-3 px-4 py-2 border-b border-white/5 last:border-0 flex-wrap">
                                    <code class="text-{{ $group['color'] }}-400 font-mono text-xs flex-shrink-0">{{ $v['key'] }}</code>
                                    <code class="text-slate-300 font-mono text-xs">= {{ $v['val'] }}</code>
                                    @if(!empty($v['note']))
                                        <span class="text-slate-600 text-xs ml-auto flex-shrink-0"># {{ $v['note'] }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ── Migration & Seeder ── --}}
        <div id="migrate" class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <div class="w-8 h-8 bg-rose-100 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                    </svg>
                </div>
                <h2 class="font-bold text-slate-800">Migration & Seeder</h2>
            </div>
            <div class="px-6 py-5 space-y-4 text-sm">
                <div class="space-y-2">
                    @foreach ([
                        '# Docker: รัน migrations ทั้งหมด',
                        'docker exec -w /var/www/html/user-centralized-managment php83 php artisan migrate',
                        '# Seed permissions ของแต่ละระบบ (เลือก seed ตามระบบที่ใช้)',
                        'docker exec -w /var/www/html/user-centralized-managment php83 php artisan db:seed --class=EarthSeeder',
                        'docker exec -w /var/www/html/user-centralized-managment php83 php artisan db:seed --class=EFilingSeeder',
                        'docker exec -w /var/www/html/user-centralized-managment php83 php artisan db:seed --class=RepairSystemSeeder',
                        '# หรือ seed ทั้งหมดในคราวเดียว (DatabaseSeeder)',
                        'docker exec -w /var/www/html/user-centralized-managment php83 php artisan migrate --seed',
                        '# Development: reset ทั้งหมดและ seed ใหม่',
                        'docker exec -w /var/www/html/user-centralized-managment php83 php artisan migrate:fresh --seed',
                    ] as $cmd)
                        <div class="bg-slate-900 rounded-xl px-4 py-2 font-mono text-xs {{ str_starts_with($cmd, '#') ? 'text-slate-500' : 'text-slate-300' }} overflow-x-auto">{{ $cmd }}</div>
                    @endforeach
                </div>
                <div class="flex items-start gap-3 p-3.5 bg-blue-50 border border-blue-100 rounded-xl text-xs text-blue-800">
                    <svg class="w-4 h-4 text-blue-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                    <span>คอลัมน์ <code class="font-mono bg-blue-100 px-1 rounded">is_admin</code> เป็น <code class="font-mono bg-blue-100 px-1 rounded">tinyInteger</code> ที่รองรับค่า 0 (ทั่วไป), 1 (Admin L1), 2 (Admin L2) — หลัง migrate ต้องตั้งค่า Super Admin คนแรกด้วยตนเอง (ดูหัวข้อถัดไป)</span>
                </div>
                <div class="flex items-start gap-3 p-3.5 bg-emerald-50 border border-emerald-100 rounded-xl text-xs text-emerald-800">
                    <svg class="w-4 h-4 text-emerald-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                    <span>Migration รวม <code class="font-mono bg-emerald-100 px-1 rounded">notification_channels</code> table (ใช้โดย Notification Channels feature) — ไม่ต้องรันคำสั่งพิเศษเพิ่มเติม <code class="font-mono bg-emerald-100 px-1 rounded">php artisan migrate</code> จัดการให้ครบอัตโนมัติ</span>
                </div>
                <div class="flex items-start gap-3 p-3.5 bg-emerald-50 border border-emerald-100 rounded-xl text-xs text-emerald-800">
                    <svg class="w-4 h-4 text-emerald-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                    <span>Migration รวมคอลัมน์ <code class="font-mono bg-emerald-100 px-1 rounded">last_login_at</code> ใน <code class="font-mono bg-emerald-100 px-1 rounded">ucm_users</code> (ใช้โดย Inactive User Detection feature) — บันทึกเวลา Login ล่าสุดอัตโนมัติทุกครั้งที่ผู้ใช้เข้าสู่ระบบ</span>
                </div>
                <div class="flex items-start gap-3 p-3.5 bg-red-50 border border-red-200 rounded-xl text-xs text-red-800">
                    <svg class="w-4 h-4 text-red-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    <span><strong>ห้ามรัน</strong> <code class="font-mono bg-red-100 px-1 rounded">php artisan route:cache</code> — เนื่องจาก UCM รันอยู่ภายใต้ sub-path (<code class="font-mono bg-red-100 px-1 rounded">/user-centralized-managment/</code>) route cache จะทำให้ homepage ตอบกลับ HTTP 405 แทนที่จะเป็น 302 เนื่องจาก Symfony routing คำนวณ base path ผิดพลาดเมื่อ trailing slash ถูกตัดออก ใช้ <code class="font-mono bg-red-100 px-1 rounded">php artisan route:clear</code> เพื่อล้าง cache แทน</span>
                </div>
            </div>
        </div>

        {{-- ── First Super Admin ── --}}
        <div id="first-admin" class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <div class="w-8 h-8 bg-amber-100 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <h2 class="font-bold text-slate-800">ตั้งค่า Super Admin คนแรก</h2>
            </div>
            <div class="px-6 py-5 space-y-4 text-sm text-slate-700">
                <p>หลัง migrate ผู้ใช้ทุกคนจะมี <code class="font-mono bg-slate-100 px-1.5 py-0.5 rounded text-xs">is_admin = 0</code> (ทั่วไป) ต้องยกระดับผู้ดูแลคนแรกเป็น Admin ระดับ 2 ด้วย tinker</p>

                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Docker</p>
                    @foreach ([
                        '# 1. Import ผู้ใช้จาก AD ผ่านหน้าเว็บก่อน แล้วรัน tinker',
                        'docker exec -it -w /var/www/html/user-centralized-managment php83 php artisan tinker',
                        '# 2. ใน tinker shell — แทน \'username\' ด้วย AD username ของ admin',
                        'App\Models\UcmUser::where(\'username\', \'firstname.lastname\')->update([\'is_admin\' => 2]);',
                        'exit',
                    ] as $cmd)
                        <div class="bg-slate-900 rounded-xl px-4 py-2 font-mono text-xs {{ str_starts_with($cmd, '#') ? 'text-slate-500' : 'text-slate-300' }} overflow-x-auto mb-1">{{ $cmd }}</div>
                    @endforeach
                </div>

                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Non-Docker</p>
                    @foreach ([
                        'cd /var/www/user-centralized-managment',
                        'php artisan tinker',
                        'App\Models\UcmUser::where(\'username\', \'firstname.lastname\')->update([\'is_admin\' => 2]);',
                        'exit',
                    ] as $cmd)
                        <div class="bg-slate-900 rounded-xl px-4 py-2 font-mono text-xs {{ str_starts_with($cmd, '#') ? 'text-slate-500' : 'text-slate-300' }} overflow-x-auto mb-1">{{ $cmd }}</div>
                    @endforeach
                </div>

                <div class="overflow-hidden rounded-xl border border-slate-200 text-xs">
                    <div class="grid grid-cols-3 bg-slate-50 font-bold text-slate-500 px-3 py-2 border-b border-slate-200">
                        <div>ค่า is_admin</div><div>ระดับ</div><div>สิทธิ์หลัก</div>
                    </div>
                    @foreach ([
                        ['0', 'ทั่วไป', 'ดูข้อมูลได้อย่างเดียว', 'text-slate-600'],
                        ['1', 'Admin ระดับ 1', 'แก้ไขสิทธิ์ผู้ใช้ + นำเข้า AD', 'text-indigo-700'],
                        ['2', 'Admin ระดับ 2', 'ทุกอย่าง + จัดการระบบ/Admin', 'text-amber-700'],
                    ] as [$val, $level, $perm, $cls])
                    <div class="grid grid-cols-3 px-3 py-2 border-b border-slate-100 last:border-0">
                        <div class="font-mono font-bold text-slate-700">{{ $val }}</div>
                        <div class="font-semibold {{ $cls }}">{{ $level }}</div>
                        <div class="text-slate-500">{{ $perm }}</div>
                    </div>
                    @endforeach
                </div>

                <div class="flex items-start gap-3 p-3.5 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800">
                    <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    <span>หลังตั้งค่าแล้ว Admin ระดับ 2 สามารถยกระดับผู้ใช้คนอื่นได้ผ่านหน้า <strong>ผู้ดูแลระบบ → จัดการสิทธิ์ Admin</strong> ในเว็บ UCM โดยไม่ต้องใช้ tinker อีก</span>
                </div>
            </div>
        </div>

        {{-- ── LDAP / AD ── --}}
        <div id="ldap" class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <div class="w-8 h-8 bg-teal-100 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h2 class="font-bold text-slate-800">LDAP / Active Directory</h2>
            </div>
            <div class="px-6 py-5 space-y-3 text-sm text-slate-700">
                <p class="text-slate-600">UCM ใช้ <strong>LdapRecord</strong> สำหรับ Login และ Import ผู้ใช้ ค่า LDAP config อยู่ใน <code class="font-mono bg-slate-100 px-1.5 py-0.5 rounded text-xs">config/ldap.php</code> ซึ่งดึงค่าจาก .env อัตโนมัติ</p>
                <div class="flex items-start gap-3 p-3.5 bg-blue-50 border border-blue-200 rounded-xl text-xs text-blue-800">
                    <svg class="w-4 h-4 text-blue-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                    <span>ผู้ใช้ต้องถูก import เข้า <code class="font-mono bg-blue-100 px-1 rounded">ucm_users</code> table ก่อน (ผ่านหน้า "จัดการผู้ใช้") จึงจะ Login ได้</span>
                </div>
                <div class="bg-slate-900 rounded-xl px-4 py-2 font-mono text-xs text-slate-500 overflow-x-auto"># ทดสอบ LDAP connection</div>
                <div class="bg-slate-900 rounded-xl px-4 py-2 font-mono text-xs text-slate-300 overflow-x-auto">php artisan ldap:test</div>
            </div>
        </div>

        {{-- ── Queue Worker ── --}}
        <div id="queue" class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <div class="w-8 h-8 bg-violet-100 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>
                <h2 class="font-bold text-slate-800">Queue Worker — คำสั่งที่ใช้บ่อย</h2>
            </div>
            <div class="px-6 py-5 space-y-2 text-sm">
                <div class="mb-3 bg-slate-50 rounded-xl border border-slate-200 p-3 text-xs text-slate-600">
                    <span class="font-bold text-slate-700">Docker queue command:</span>
                    <code class="font-mono ml-2 text-violet-700">php artisan queue:work --sleep=3 --tries=3 --max-time=3600</code>
                    <p class="mt-1 text-slate-500">Container <code class="font-mono">ucm-queue</code> รัน command นี้อัตโนมัติ และ restart ตัวเองทุก 3600 วินาที (1 ชั่วโมง) เพื่อป้องกัน memory leak</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @foreach ([
                        ['label' => 'Docker — ดู logs', 'cmd' => 'docker logs ucm-queue --tail 50 -f'],
                        ['label' => 'Docker — restart', 'cmd' => 'docker restart ucm-queue'],
                        ['label' => 'Supervisor — restart', 'cmd' => 'sudo supervisorctl restart ucm-queue:*'],
                        ['label' => 'Supervisor — status', 'cmd' => 'sudo supervisorctl status'],
                        ['label' => 'Failed jobs (Docker)', 'cmd' => 'docker exec -w /var/www/html/user-centralized-managment php83 php artisan queue:failed'],
                        ['label' => 'Retry all failed', 'cmd' => 'docker exec -w /var/www/html/user-centralized-managment php83 php artisan queue:retry all'],
                    ] as $item)
                        <div class="bg-slate-900 rounded-xl px-4 py-3 overflow-x-auto">
                            <div class="text-slate-500 text-[10px] mb-1">{{ $item['label'] }}</div>
                            <code class="text-slate-300 font-mono text-xs">{{ $item['cmd'] }}</code>
                        </div>
                    @endforeach
                </div>
                <div class="flex items-start gap-3 p-3.5 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-800 mt-2">
                    <svg class="w-4 h-4 text-rose-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                    <span>ทุกครั้งที่แก้ไข Adapter หรือ Job — <strong>ต้อง restart queue worker</strong> เพราะ PHP process โหลด code ไว้ใน memory แล้ว</span>
                </div>
            </div>
        </div>

        {{-- ── Connector Wizard ── --}}
        <div id="connector" class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <div class="w-8 h-8 bg-indigo-100 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <h2 class="font-bold text-slate-800">Connector Wizard — เชื่อมต่อฐานข้อมูลภายนอก</h2>
            </div>
            <div class="px-6 py-5 space-y-4 text-sm text-slate-700">
                <p class="text-slate-600">Connector Wizard ช่วยสร้าง Dynamic Adapter สำหรับเชื่อมต่อฐานข้อมูลภายนอก (MySQL, PostgreSQL, SQL Server) โดยไม่ต้องเขียน PHP Adapter เอง เข้าใช้งานได้ที่ <strong>เมนู Connector Wizard</strong> (Admin L1+ เท่านั้น)</p>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    @foreach ([
                        ['driver' => 'MySQL', 'color' => 'sky', 'ext' => 'pdo_mysql', 'note' => 'built-in ใน Docker image'],
                        ['driver' => 'PostgreSQL', 'color' => 'indigo', 'ext' => 'pdo_pgsql', 'note' => 'ต้องติดตั้งเพิ่มใน Dockerfile'],
                        ['driver' => 'SQL Server', 'color' => 'violet', 'ext' => 'pdo_sqlsrv', 'note' => 'built-in ใน Docker image'],
                    ] as $d)
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <div class="flex items-center gap-2 mb-1">
                                <div class="w-2 h-2 rounded-full bg-{{ $d['color'] }}-500"></div>
                                <span class="font-bold text-slate-800 text-xs">{{ $d['driver'] }}</span>
                            </div>
                            <code class="text-xs font-mono text-{{ $d['color'] }}-700">{{ $d['ext'] }}</code>
                            <p class="text-xs text-slate-500 mt-1">{{ $d['note'] }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="border-t border-slate-100 pt-3">
                    <h3 class="font-bold text-slate-900 mb-2">ข้อมูลที่ต้องเตรียม</h3>
                    <ul class="space-y-1 text-slate-600 text-xs list-disc list-inside">
                        <li><strong>DB Host / Port / Database / Username / Password</strong> ของฐานข้อมูลปลายทาง</li>
                        <li><strong>ตาราง Users</strong> — ระบุชื่อตารางและคอลัมน์ที่ map กับ username ของ UCM</li>
                        <li><strong>ตาราง Permissions</strong> — ระบุตารางและคอลัมน์ที่เก็บสิทธิ์ รวมถึง UCM Permission key mapping</li>
                        <li><strong>Permission Mode</strong> — เลือกว่า permission เก็บแบบ column-based หรือ manual-defined</li>
                    </ul>
                </div>

                <div class="border-t border-slate-100 pt-3">
                    <h3 class="font-bold text-slate-900 mb-2">ข้อจำกัด</h3>
                    <ul class="space-y-1 text-slate-600 text-xs list-disc list-inside">
                        <li>รองรับ <strong>1-Way Sync เท่านั้น</strong> — UCM อ่านสิทธิ์จากระบบปลายทาง แต่ไม่สามารถ Push กลับได้</li>
                        <li>ไม่รองรับ 2-Way Sync — ต้องพัฒนา Custom Adapter (PHP) เพื่อรองรับ</li>
                        <li>ระบบปลายทางต้องเปิด network port ให้ container <code class="font-mono bg-slate-100 px-1 rounded">php83</code> เข้าถึงได้</li>
                    </ul>
                </div>

                <div class="flex items-start gap-3 p-3.5 bg-blue-50 border border-blue-200 rounded-xl text-xs text-blue-800">
                    <svg class="w-4 h-4 text-blue-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                    <span>Connector config ถูกเก็บใน table <code class="font-mono bg-blue-100 px-1 rounded">connector_configs</code> และสร้าง DynamicAdapter instance ที่ runtime — ไม่ต้อง deploy code ใหม่เมื่อเพิ่ม connector</span>
                </div>
            </div>
        </div>

        {{-- ── UCM Client (Legacy) ── --}}
        <div id="client" class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <div class="w-8 h-8 bg-orange-100 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h2 class="font-bold text-slate-800">UCM Client — Legacy Integration</h2>
            </div>
            <div class="px-6 py-5 space-y-5 text-sm text-slate-700">

                {{-- Download card --}}
                <div class="rounded-2xl border-2 border-dashed border-indigo-200 bg-indigo-50/50 p-5">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-9 h-9 bg-indigo-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-bold text-slate-900 text-sm">ดาวน์โหลดไฟล์ Integration</p>
                            <p class="text-slate-500 text-xs">วางไฟล์ทั้งสองในโฟลเดอร์เดียวกัน เช่น <code class="font-mono bg-white px-1.5 py-0.5 rounded border border-slate-200 text-xs">inc/</code></p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        {{-- ucm_client.php --}}
                        <a href="{{ route('downloads.ucm-client') }}"
                           class="flex items-center gap-3 bg-white hover:bg-indigo-50 rounded-xl p-4 border border-indigo-200 hover:border-indigo-400 transition-all duration-150 group shadow-sm hover:shadow-md">
                            <div class="w-10 h-10 bg-indigo-100 group-hover:bg-indigo-200 rounded-xl flex items-center justify-center flex-shrink-0 transition-colors">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-bold text-slate-900 text-sm">ucm_client.php</div>
                                <div class="text-slate-500 text-xs">PHP client — ตรวจสอบสิทธิ์ผ่าน UCM API</div>
                            </div>
                            <svg class="w-4 h-4 text-indigo-400 group-hover:text-indigo-600 transition-colors flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                        </a>
                        {{-- ucm_token.cache --}}
                        <a href="{{ route('downloads.ucm-token-cache') }}"
                           class="flex items-center gap-3 bg-white hover:bg-slate-50 rounded-xl p-4 border border-slate-200 hover:border-slate-400 transition-all duration-150 group shadow-sm hover:shadow-md">
                            <div class="w-10 h-10 bg-slate-100 group-hover:bg-slate-200 rounded-xl flex items-center justify-center flex-shrink-0 transition-colors">
                                <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-bold text-slate-900 text-sm">ucm_token.cache</div>
                                <div class="text-slate-500 text-xs">ไฟล์ว่าง — web server ต้องมีสิทธิ์เขียน</div>
                            </div>
                            <svg class="w-4 h-4 text-slate-400 group-hover:text-slate-600 transition-colors flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                        </a>
                    </div>
                    <div class="mt-3 flex items-start gap-2 text-xs text-amber-800 bg-amber-50 border border-amber-200 rounded-xl p-3">
                        <svg class="w-3.5 h-3.5 text-amber-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        <span>ตั้งค่าคงที่ใน <code class="font-mono bg-amber-100 px-1 rounded">ucm_client.php</code> ให้ถูกต้อง (UCM_URL, SYSTEM_SLUG, ADMIN_USER, ADMIN_PASS) ก่อนนำไปใช้งาน และตั้งสิทธิ์ไฟล์ <code class="font-mono bg-amber-100 px-1 rounded">ucm_token.cache</code> ให้ web server เขียนได้: <code class="font-mono bg-amber-100 px-1 rounded">chmod 600 ucm_token.cache</code></span>
                    </div>
                </div>

                {{-- Usage example --}}
                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">ตัวอย่างการใช้งาน</p>
                    <div class="bg-slate-900 rounded-xl px-4 py-4 font-mono text-xs overflow-x-auto space-y-1">
                        <div class="text-slate-500">// require ไฟล์</div>
                        <div><span class="text-rose-400">require_once</span> <span class="text-green-300">__DIR__ . '/inc/ucm_client.php'</span>;</div>
                        <div class="mt-2 text-slate-500">// ดึง permissions ทั้งหมด</div>
                        <div><span class="text-sky-400">$ucm</span> = <span class="text-rose-400">new</span> <span class="text-amber-300">UcmClient</span>();</div>
                        <div><span class="text-sky-400">$perms</span> = <span class="text-sky-400">$ucm</span>-><span class="text-amber-300">getPermissions</span>(<span class="text-green-300">'firstname.lastname'</span>);</div>
                        <div class="text-slate-500">// => ['daily_edit', 'pax_read', 'ramp_deny']</div>
                        <div class="mt-2 text-slate-500">// ตรวจสอบ permission เดียว</div>
                        <div><span class="text-rose-400">if</span> (<span class="text-sky-400">$ucm</span>-><span class="text-amber-300">hasPermission</span>(<span class="text-green-300">'firstname.lastname'</span>, <span class="text-green-300">'pax_edit'</span>)) {</div>
                        <div class="pl-4 text-slate-400">// แสดงเมนู PAX</div>
                        <div>}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── API Authentication ── --}}
        <div id="api" class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <div class="w-8 h-8 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </div>
                <h2 class="font-bold text-slate-800">API Authentication</h2>
            </div>
            <div class="px-6 py-5 space-y-4 text-sm text-slate-700">
                <p class="text-slate-600">UCM API ใช้ Bearer Token (Laravel Sanctum) สำหรับ server-to-server authentication</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="flex flex-col">
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">1. ขอ Token</p>
                        <div class="bg-slate-900 rounded-xl px-4 py-4 font-mono text-xs space-y-1 overflow-x-auto flex-1">
                            <div class="text-sky-400">POST /api/auth/token</div>
                            <div class="mt-2">{</div>
                            <div class="pl-4"><span class="text-green-300">"username"</span>: <span class="text-amber-300">"admin"</span>,</div>
                            <div class="pl-4"><span class="text-green-300">"password"</span>: <span class="text-amber-300">"pass"</span>,</div>
                            <div class="pl-4"><span class="text-green-300">"token_name"</span>: <span class="text-amber-300">"my-system"</span></div>
                            <div>}</div>
                            <div class="mt-2 text-slate-500">// Response</div>
                            <div>{ <span class="text-green-300">"token"</span>: <span class="text-amber-300">"1|abc..."</span> }</div>
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">2. ดึง Permissions</p>
                        <div class="bg-slate-900 rounded-xl px-4 py-4 font-mono text-xs space-y-1 overflow-x-auto flex-1">
                            <div class="text-sky-400">GET /api/users/{username}/permissions</div>
                            <div class="text-slate-500">    ?system=earth</div>
                            <div class="mt-2"><span class="text-amber-300">Authorization</span>: Bearer 1|abc...</div>
                            <div class="mt-2 text-slate-500">// Response</div>
                            <div>{</div>
                            <div class="pl-4"><span class="text-green-300">"permissions"</span>: [</div>
                            <div class="pl-8"><span class="text-amber-300">"daily_edit"</span>,</div>
                            <div class="pl-8"><span class="text-amber-300">"pax_read"</span></div>
                            <div class="pl-4">]</div>
                            <div>}</div>
                        </div>
                    </div>
                </div>
                <p class="text-xs text-slate-500">
                    ดู endpoints ทั้งหมดได้ที่
                    <a href="{{ route('api-docs') }}" class="text-indigo-600 hover:underline font-semibold">API Docs</a> หรือ
                    <a href="{{ url('api-docs/swagger') }}" class="text-indigo-600 hover:underline font-semibold">Swagger UI</a>
                </p>
            </div>
        </div>

    </div>
</div>

<script>
(function () {
    var links = document.querySelectorAll('.toc-link');
    var ids   = Array.from(links).map(function (l) { return l.getAttribute('href').slice(1); });

    function setActive(id) {
        links.forEach(function (l) {
            var isCur = l.getAttribute('href') === '#' + id;
            l.className = 'toc-link flex items-center gap-2 px-3 py-2 rounded-xl text-sm transition-colors ' +
                (isCur ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50');
            l.querySelector('span').className = 'w-1.5 h-1.5 rounded-full flex-shrink-0 ' +
                (isCur ? 'bg-indigo-500' : 'bg-slate-300');
        });
    }

    function onScroll() {
        var threshold = 140;
        var active    = ids[0];
        ids.forEach(function (id) {
            var el = document.getElementById(id);
            if (el && el.getBoundingClientRect().top <= threshold) active = id;
        });
        setActive(active);
    }

    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
})();
</script>

@endsection
