<?php

namespace App\Http\Controllers;

use App\Adapters\DynamicAdapter;
use App\Models\AuditLog;
use App\Models\ConnectorConfig;
use App\Models\System;
use App\Models\UcmUser;
use App\Services\AuditLogger;
use App\Services\Connector\AISchemaAnalyzer;
use App\Services\Connector\RuleBasedSuggester;
use App\Services\Connector\SchemaIntrospector;
use App\Services\Connector\ZipAnalyzer;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use PDO;
use PDOException;

class ConnectorWizardController extends Controller
{
    private function authUser(): ?UcmUser
    {
        /** @var UcmUser|null */
        return Auth::user();
    }

    private function requireSuperAdmin(): void
    {
        abort_unless($this->authUser()?->isSuperAdmin(), 403, 'เฉพาะ Admin ระดับ 2 เท่านั้นที่สามารถใช้งาน Connector Wizard ได้');
    }

    // ── Pages ──────────────────────────────────────────────────────────────

    public function index(): View
    {
        $this->requireSuperAdmin();

        $configs = ConnectorConfig::with('system')->latest()->get();

        return view('connectors.index', compact('configs'));
    }

    public function create(): View
    {
        $this->requireSuperAdmin();

        $aiAvailable = ! empty(config('services.anthropic.api_key'));

        return view('connectors.wizard', compact('aiAvailable'));
    }

    public function edit(ConnectorConfig $connectorConfig): View
    {
        $this->requireSuperAdmin();

        $aiAvailable = ! empty(config('services.anthropic.api_key'));

        return view('connectors.wizard', [
            'editConfig' => $connectorConfig->load('system'),
            'aiAvailable' => $aiAvailable,
        ]);
    }

    public function destroy(ConnectorConfig $connectorConfig): RedirectResponse
    {
        $this->requireSuperAdmin();

        $system = $connectorConfig->system;

        $connectorConfig->delete();

        // ถ้า system นี้ใช้ DynamicAdapter อยู่ และไม่มี config แล้ว ให้ล้าง adapter_class
        if ($system && $system->adapter_class === DynamicAdapter::class) {
            $system->update(['adapter_class' => null]);
        }

        AuditLogger::log(
            AuditLog::CATEGORY_CONNECTORS,
            AuditLog::EVENT_CONNECTOR_DELETED,
            'ลบ Connector ของระบบ '.($system?->name ?? 'ไม่ทราบ'),
            ['system_id' => $system?->id, 'system_name' => $system?->name],
            $this->authUser(),
            'system', $system?->id, $system?->name,
        );

        app(NotificationService::class)->dispatch('connector_deleted', [
            'system_id' => $system?->id,
            'system_name' => $system?->name ?? 'ไม่ทราบ',
            'description' => 'ลบ Connector ของระบบ '.($system?->name ?? 'ไม่ทราบ'),
        ]);

        return redirect()->route('connectors.index')
            ->with('success', 'ลบ Connector สำเร็จ');
    }

    // ── AJAX: Test Connection ──────────────────────────────────────────────

    public function testConnection(Request $request): JsonResponse
    {
        $this->requireSuperAdmin();

        $data = $request->validate([
            'db_driver' => 'required|in:mysql,pgsql,sqlsrv',
            'db_host' => 'required|string|max:255',
            'db_port' => 'required|integer|min:1|max:65535',
            'db_name' => 'required|string|max:100',
            'db_user' => 'required|string|max:100',
            'db_password' => 'nullable|string|max:255',
        ]);

        try {
            $pdo = $this->makePdo($data);
            $pdo->query('SELECT 1');

            return response()->json(['ok' => true, 'message' => 'เชื่อมต่อสำเร็จ']);
        } catch (PDOException $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()]);
        }
    }

    // ── AJAX: Fetch Tables ─────────────────────────────────────────────────

    public function fetchTables(Request $request): JsonResponse
    {
        $this->requireSuperAdmin();

        $data = $request->validate([
            'db_driver' => 'required|in:mysql,pgsql,sqlsrv',
            'db_host' => 'required|string|max:255',
            'db_port' => 'required|integer|min:1|max:65535',
            'db_name' => 'required|string|max:100',
            'db_user' => 'required|string|max:100',
            'db_password' => 'nullable|string|max:255',
        ]);

        try {
            $pdo = $this->makePdo($data);

            $tables = match ($data['db_driver']) {
                'pgsql' => array_column($pdo->query("SELECT tablename FROM pg_tables WHERE schemaname='public'")->fetchAll(), 'tablename'),
                'sqlsrv' => array_column($pdo->query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE='BASE TABLE'")->fetchAll(), 'TABLE_NAME'),
                default => array_column($pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_NUM), 0),
            };

            return response()->json(['ok' => true, 'tables' => $tables]);
        } catch (PDOException $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()]);
        }
    }

    // ── AJAX: Fetch Columns ────────────────────────────────────────────────

    public function fetchColumns(Request $request): JsonResponse
    {
        $this->requireSuperAdmin();

        $data = $request->validate([
            'db_driver' => 'required|in:mysql,pgsql,sqlsrv',
            'db_host' => 'required|string|max:255',
            'db_port' => 'required|integer|min:1|max:65535',
            'db_name' => 'required|string|max:100',
            'db_user' => 'required|string|max:100',
            'db_password' => 'nullable|string|max:255',
            'table' => 'required|string|regex:/^[\w.]+$/',
        ]);

        try {
            $pdo = $this->makePdo($data);
            $table = $data['table'];

            $columns = match ($data['db_driver']) {
                'pgsql' => array_column(
                    $pdo->query('SELECT column_name FROM information_schema.columns WHERE table_name = '.$pdo->quote($table)." AND table_schema = 'public' ORDER BY ordinal_position")->fetchAll(),
                    'column_name'
                ),
                'sqlsrv' => array_column(
                    $pdo->query('SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '.$pdo->quote($table).' ORDER BY ORDINAL_POSITION')->fetchAll(),
                    'COLUMN_NAME'
                ),
                default => array_column(
                    $pdo->query('DESCRIBE '.$this->qi($table, 'mysql'))->fetchAll(),
                    'Field'
                ),
            };

            return response()->json(['ok' => true, 'columns' => $columns]);
        } catch (PDOException $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()]);
        }
    }

    // ── AJAX: Preview Users ────────────────────────────────────────────────

    public function previewUsers(Request $request): JsonResponse
    {
        $this->requireSuperAdmin();

        $data = $request->validate([
            'db_driver' => 'required|in:mysql,pgsql,sqlsrv',
            'db_host' => 'required|string|max:255',
            'db_port' => 'required|integer|min:1|max:65535',
            'db_name' => 'required|string|max:100',
            'db_user' => 'required|string|max:100',
            'db_password' => 'nullable|string|max:255',
            'user_table' => 'required|string|regex:/^[\w.]+$/',
            'user_identifier_col' => 'required|string|regex:/^[\w.]+$/',
            'user_name_col' => 'nullable|string|regex:/^[\w.]+$/',
        ]);

        try {
            $pdo = $this->makePdo($data);
            $driver = $data['db_driver'];
            $table = $this->qi($data['user_table'], $driver);
            $idCol = $this->qi($data['user_identifier_col'], $driver);

            $cols = [$idCol];
            if (! empty($data['user_name_col'])) {
                $cols[] = $this->qi($data['user_name_col'], $driver);
            }

            $colSql = implode(', ', $cols);
            $sql = $driver === 'sqlsrv'
                ? "SELECT TOP 10 {$colSql} FROM {$table}"
                : "SELECT {$colSql} FROM {$table} LIMIT 10";
            $rows = $pdo->query($sql)->fetchAll();

            return response()->json(['ok' => true, 'rows' => $rows]);
        } catch (PDOException $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()]);
        }
    }

    // ── AJAX: Preview Permissions ──────────────────────────────────────────

    public function previewPermissions(Request $request): JsonResponse
    {
        $this->requireSuperAdmin();

        $data = $request->validate([
            'db_driver' => 'required|in:mysql,pgsql,sqlsrv',
            'db_host' => 'required|string|max:255',
            'db_port' => 'required|integer|min:1|max:65535',
            'db_name' => 'required|string|max:100',
            'db_user' => 'required|string|max:100',
            'db_password' => 'nullable|string|max:255',
            'perm_table' => 'required|string|regex:/^[\w.]+$/',
            'perm_value_col' => 'required|string|regex:/^[\w.]+$/',
            'perm_label_col' => 'nullable|string|regex:/^[\w.]+$/',
            'perm_group_col' => 'nullable|string|regex:/^[\w.]+$/',
        ]);

        try {
            $pdo = $this->makePdo($data);
            $driver = $data['db_driver'];
            $table = $this->qi($data['perm_table'], $driver);
            $valCol = $this->qi($data['perm_value_col'], $driver);

            $cols = [$valCol];
            if (! empty($data['perm_label_col'])) {
                $cols[] = $this->qi($data['perm_label_col'], $driver);
            }
            if (! empty($data['perm_group_col'])) {
                $cols[] = $this->qi($data['perm_group_col'], $driver);
            }

            $colSql = implode(', ', $cols);
            $sql = $driver === 'sqlsrv'
                ? "SELECT DISTINCT TOP 20 {$colSql} FROM {$table}"
                : "SELECT DISTINCT {$colSql} FROM {$table} LIMIT 20";
            $rows = $pdo->query($sql)->fetchAll();

            return response()->json(['ok' => true, 'rows' => $rows]);
        } catch (PDOException $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()]);
        }
    }

    // ── AJAX: Analyze Schema ───────────────────────────────────────────────

    public function analyzeSchema(Request $request): JsonResponse
    {
        $this->requireSuperAdmin();

        $data = $request->validate([
            'db_driver' => 'required|in:mysql,pgsql,sqlsrv',
            'db_host' => 'required|string|max:255',
            'db_port' => 'required|integer|min:1|max:65535',
            'db_name' => 'required|string|max:100',
            'db_user' => 'required|string|max:100',
            'db_password' => 'nullable|string|max:255',
            'use_ai' => 'nullable|boolean',
        ]);

        try {
            $introspector = new SchemaIntrospector($data['db_driver'], $data);
            $schema = $introspector->introspect();

            $suggester = new RuleBasedSuggester($schema);
            $ruleSuggest = $suggester->suggest();

            $aiSuggest = null;
            if ($data['use_ai'] ?? false) {
                $analyzer = new AISchemaAnalyzer(
                    config('services.anthropic.api_key', ''),
                    config('services.anthropic.model', 'claude-opus-4-6')
                );
                if ($analyzer->isAvailable()) {
                    $aiSuggest = $analyzer->analyze($schema, [], $ruleSuggest);
                }
            }

            return response()->json([
                'ok' => true,
                'rule' => $ruleSuggest,
                'ai' => $aiSuggest,
                'schema_keys' => array_keys($schema),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()]);
        }
    }

    // ── AJAX: Analyze ZIP ──────────────────────────────────────────────────

    public function analyzeZip(Request $request): JsonResponse
    {
        $this->requireSuperAdmin();

        $data = $request->validate([
            'db_driver' => 'required|in:mysql,pgsql,sqlsrv',
            'db_host' => 'required|string|max:255',
            'db_port' => 'required|integer|min:1|max:65535',
            'db_name' => 'required|string|max:100',
            'db_user' => 'required|string|max:100',
            'db_password' => 'nullable|string|max:255',
            'source_zip' => 'required|file|mimes:zip|max:51200',
        ]);

        try {
            $introspector = new SchemaIntrospector($data['db_driver'], $data);
            $schema = $introspector->introspect();

            $zipResult = (new ZipAnalyzer)->analyze($request->file('source_zip'));
            $suggester = new RuleBasedSuggester($schema);
            $ruleSuggest = $suggester->suggest();

            $analyzer = new AISchemaAnalyzer(
                config('services.anthropic.api_key', ''),
                config('services.anthropic.model', 'claude-opus-4-6')
            );

            if (! $analyzer->isAvailable()) {
                return response()->json(['ok' => false, 'message' => 'ยังไม่ได้ตั้งค่า ANTHROPIC_API_KEY']);
            }

            $aiSuggest = $analyzer->analyze($schema, $zipResult['files'], $ruleSuggest);

            return response()->json([
                'ok' => true,
                'rule' => $ruleSuggest,
                'ai' => $aiSuggest,
                'framework' => $zipResult['framework'],
            ]);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()]);
        }
    }

    // ── Store (Final Submit) ───────────────────────────────────────────────

    public function store(Request $request): JsonResponse
    {
        $this->requireSuperAdmin();

        $data = $request->validate([
            // System info
            'system_id' => 'nullable|exists:systems,id',
            'system_name' => 'required_without:system_id|string|max:100',
            'system_slug' => 'required_without:system_id|string|max:50|alpha_dash',
            'system_description' => 'nullable|string|max:500',
            'system_color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'system_icon' => 'nullable|string|max:10',

            // DB Connection
            'db_driver' => 'required|in:mysql,pgsql,sqlsrv',
            'db_host' => 'required|string|max:255',
            'db_port' => 'required|integer|min:1|max:65535',
            'db_name' => 'required|string|max:100',
            'db_user' => 'required|string|max:100',
            'db_password' => 'nullable|string|max:255',

            // User Table
            'user_table' => 'required|string|max:100|regex:/^[\w.]+$/',
            'user_ucm_identifier' => 'required|in:username,employee_number',
            'user_identifier_col' => 'required|string|max:100|regex:/^[\w.]+$/',
            'user_name_col' => 'nullable|string|max:100|regex:/^[\w.]+$/',
            'user_email_col' => 'nullable|string|max:100|regex:/^[\w.]+$/',
            'user_dept_col' => 'nullable|string|max:100|regex:/^[\w.]+$/',
            'user_status_col' => 'nullable|string|max:100|regex:/^[\w.]+$/',
            'user_status_active_val' => 'nullable|string|max:100',

            // Permission
            'permission_mode' => 'required|in:junction,column,manual',
            'perm_table' => 'nullable|string|max:100|regex:/^[\w.]+$/',
            'perm_user_fk_col' => 'nullable|string|max:100|regex:/^[\w.]+$/',
            'perm_value_col' => 'nullable|string|max:100|regex:/^[\w.]+$/',
            'perm_label_col' => 'nullable|string|max:100|regex:/^[\w.]+$/',
            'perm_group_col' => 'nullable|string|max:100|regex:/^[\w.]+$/',
            'perm_composite_cols' => 'nullable|json',
            'manual_permissions' => 'nullable|json',

            // 2-Way Sync
            'perm_def_table' => 'nullable|string|max:100|regex:/^[\w.]+$/',
            'perm_def_value_col' => 'nullable|string|max:100|regex:/^[\w.]+$/',
            'perm_def_pk_col' => 'nullable|string|max:100|regex:/^[\w.]+$/',
            'perm_def_label_col' => 'nullable|string|max:100|regex:/^[\w.]+$/',
            'perm_def_group_col' => 'nullable|string|max:100|regex:/^[\w.]+$/',
            'perm_delete_mode' => 'nullable|in:hard,soft,detach_only',
            'perm_def_soft_delete_col' => 'nullable|string|max:100|regex:/^[\w.]+$/',
            'perm_def_soft_delete_val' => 'nullable|string|max:100',
        ]);

        $isNew = ! isset($data['system_id']);

        $system = DB::transaction(function () use ($data) {
            // สร้างหรือใช้ System ที่มีอยู่
            if (! empty($data['system_id'])) {
                $system = System::findOrFail($data['system_id']);
            } else {
                $slug = $data['system_slug'];
                abort_if(
                    System::where('slug', $slug)->exists(),
                    422,
                    "Slug '{$slug}' มีอยู่แล้วในระบบ"
                );

                $system = System::create([
                    'name' => $data['system_name'],
                    'slug' => $slug,
                    'description' => $data['system_description'] ?? null,
                    'color' => $data['system_color'] ?? '#6366f1',
                    'icon' => $data['system_icon'] ?? null,
                    'adapter_class' => DynamicAdapter::class,
                    'db_host' => $data['db_host'],
                    'db_port' => $data['db_port'],
                    'db_name' => $data['db_name'],
                    'db_user' => $data['db_user'],
                    'db_password' => $data['db_password'] ?? null,
                    'is_active' => true,
                ]);
            }

            // สร้างหรืออัปเดต ConnectorConfig
            $configData = [
                'system_id' => $system->id,
                'db_driver' => $data['db_driver'],
                'db_host' => $data['db_host'],
                'db_port' => $data['db_port'],
                'db_name' => $data['db_name'],
                'db_user' => $data['db_user'],
                'db_password' => $data['db_password'] ?? '',
                'user_table' => $data['user_table'],
                'user_ucm_identifier' => $data['user_ucm_identifier'],
                'user_identifier_col' => $data['user_identifier_col'],
                'user_name_col' => $data['user_name_col'] ?? null,
                'user_email_col' => $data['user_email_col'] ?? null,
                'user_dept_col' => $data['user_dept_col'] ?? null,
                'user_status_col' => $data['user_status_col'] ?? null,
                'user_status_active_val' => $data['user_status_active_val'] ?? null,
                'permission_mode' => $data['permission_mode'],
                'perm_table' => $data['perm_table'] ?? null,
                'perm_user_fk_col' => $data['perm_user_fk_col'] ?? null,
                'perm_value_col' => $data['perm_value_col'] ?? null,
                'perm_label_col' => $data['perm_label_col'] ?? null,
                'perm_group_col' => $data['perm_group_col'] ?? null,
                'perm_composite_cols' => isset($data['perm_composite_cols'])
                    ? (is_array($decoded = json_decode($data['perm_composite_cols'], true)) ? $decoded : null)
                    : null,
                'manual_permissions' => isset($data['manual_permissions'])
                    ? (is_array($decoded = json_decode($data['manual_permissions'], true)) ? $decoded : null)
                    : null,
                'perm_def_table' => $data['perm_def_table'] ?? null,
                'perm_def_value_col' => $data['perm_def_value_col'] ?? null,
                'perm_def_pk_col' => $data['perm_def_pk_col'] ?? null,
                'perm_def_label_col' => $data['perm_def_label_col'] ?? null,
                'perm_def_group_col' => $data['perm_def_group_col'] ?? null,
                'perm_delete_mode' => $data['perm_delete_mode'] ?? null,
                'perm_def_soft_delete_col' => $data['perm_def_soft_delete_col'] ?? null,
                'perm_def_soft_delete_val' => $data['perm_def_soft_delete_val'] ?? null,
            ];

            ConnectorConfig::updateOrCreate(
                ['system_id' => $system->id],
                $configData
            );

            // ตั้งค่า adapter_class ให้ system ถ้ายังไม่ได้เซ็ต
            if ($system->adapter_class !== DynamicAdapter::class) {
                $system->update(['adapter_class' => DynamicAdapter::class]);
            }

            return $system;
        });

        AuditLogger::log(
            AuditLog::CATEGORY_CONNECTORS,
            $isNew ? AuditLog::EVENT_CONNECTOR_CREATED : AuditLog::EVENT_CONNECTOR_UPDATED,
            ($isNew ? 'สร้าง' : 'อัปเดต')." Connector สำหรับระบบ {$system->name} ({$data['db_driver']}://{$data['db_host']}/{$data['db_name']})",
            ['system_id' => $system->id, 'system_name' => $system->name, 'db_driver' => $data['db_driver'], 'db_host' => $data['db_host'], 'db_name' => $data['db_name'], 'permission_mode' => $data['permission_mode']],
            $this->authUser(),
            'system', $system->id, $system->name,
        );

        app(NotificationService::class)->dispatch($isNew ? 'connector_created' : 'connector_updated', [
            'system_id' => $system->id,
            'system_name' => $system->name,
            'db_driver' => $data['db_driver'],
            'db_host' => $data['db_host'],
            'db_name' => $data['db_name'],
            'permission_mode' => $data['permission_mode'],
            'description' => ($isNew ? 'สร้าง' : 'อัปเดต')." Connector สำหรับระบบ {$system->name}",
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'สร้าง Connector สำเร็จ',
            'system_id' => $system->id,
            'redirect' => route('systems.show', $system),
        ]);
    }

    // ── Update (Edit Existing) ─────────────────────────────────────────────

    public function update(Request $request, ConnectorConfig $connectorConfig): JsonResponse
    {
        $this->requireSuperAdmin();

        $request->merge(['system_id' => $connectorConfig->system_id]);

        return $this->store($request);
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    private function makePdo(array $data): PDO
    {
        $driver = $data['db_driver'];
        $host = $data['db_host'];
        $port = $data['db_port'];
        $dbname = $data['db_name'];
        $user = $data['db_user'];
        $password = $data['db_password'] ?? '';

        $dsn = match ($driver) {
            'sqlsrv' => "sqlsrv:Server={$host},{$port};Database={$dbname};TrustServerCertificate=1;Encrypt=0",
            'pgsql' => "pgsql:host={$host};port={$port};dbname={$dbname}",
            default => "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4",
        };

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        if ($driver !== 'sqlsrv') {
            $options[PDO::ATTR_TIMEOUT] = 8;
        }

        return new PDO($dsn, $user, $password, $options);
    }

    /** Quote Identifier */
    private function qi(string $name, string $driver): string
    {
        if (! preg_match('/^[\w.]+$/', $name)) {
            throw new \InvalidArgumentException("Invalid identifier: {$name}");
        }
        // MySQL → backtick  |  PostgreSQL + SQL Server → double-quote
        $q = $driver === 'mysql' ? '`' : '"';

        return implode('.', array_map(
            fn ($p) => $q.str_replace($q, '', $p).$q,
            explode('.', $name)
        ));
    }
}
