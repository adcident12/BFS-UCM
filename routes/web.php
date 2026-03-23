<?php

use App\Http\Controllers\ApiDocsController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ConnectorWizardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\QueueMonitorController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SystemController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Auth
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Documentation
    Route::view('/manual', 'docs.manual')->name('docs.manual');
    Route::view('/install-guide', 'docs.install')->name('docs.install');

    // Downloads
    Route::get('/downloads/ucm-client', function () {
        return response()->download(
            storage_path('app/stubs/ucm_client.php'),
            'ucm_client.php',
            ['Content-Type' => 'text/plain; charset=utf-8']
        );
    })->name('downloads.ucm-client');

    Route::get('/downloads/ucm-token-cache', function () {
        return response('', 200)
            ->header('Content-Type', 'application/octet-stream')
            ->header('Content-Disposition', 'attachment; filename="ucm_token.cache"')
            ->header('Content-Length', '0');
    })->name('downloads.ucm-token-cache');

    // API Documentation
    Route::get('/api-docs', [ApiDocsController::class, 'index'])->name('api-docs');
    // Swagger UI ถูก register โดย l5-swagger package อัตโนมัติที่ /api-docs/swagger (config/l5-swagger.php)

    // Users
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/export', [UserController::class, 'export'])->name('users.export');
    Route::get('/users/inactive', [UserController::class, 'inactive'])->name('users.inactive');
    Route::get('/admin/levels', [UserController::class, 'adminLevels'])->name('admin.levels');
    Route::post('/users/{user}/admin-level', [UserController::class, 'updateAdminLevel'])->name('users.admin-level.update');
    Route::get('/users/search-ldap', [UserController::class, 'searchLdap'])->name('users.search-ldap');
    Route::get('/users/check-ad-status', [UserController::class, 'checkAdStatus'])->name('users.check-ad-status');
    Route::post('/users/import', [UserController::class, 'importFromLdap'])->name('users.import');
    Route::post('/users/import-bulk', [UserController::class, 'importBulkFromLdap'])->name('users.import-bulk');
    Route::post('/users/remove', [UserController::class, 'removeUsers'])->name('users.remove');
    Route::get('/users/{user}/permission-timeline', [UserController::class, 'permissionTimeline'])->name('users.permission-timeline');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::post('/users/{user}/permissions', [UserController::class, 'updatePermissions'])->name('users.permissions.update');
    Route::patch('/users/{user}/info', [UserController::class, 'updateInfo'])->name('users.info.update');
    Route::post('/users/{user}/systems/{system}/status', [UserController::class, 'setSystemStatus'])->name('users.system.status');
    Route::post('/users/{user}/systems/{system}/discover', [UserController::class, 'discoverFromRemote'])->name('users.system.discover');

    // Systems
    Route::resource('systems', SystemController::class);
    Route::post('/systems/{system}/health-check', [SystemController::class, 'healthCheck'])->name('systems.health-check');
    Route::post('/systems/{system}/permissions', [SystemController::class, 'storePermission'])->name('systems.permissions.store');
    Route::post('/systems/{system}/discover', [SystemController::class, 'discoverPermissions'])->name('systems.discover');
    Route::post('/systems/{system}/toggle-2way', [SystemController::class, 'toggle2WayPermissions'])->name('systems.toggle-2way');
    Route::get('/systems/{system}/users-for-import', [SystemController::class, 'usersForImport'])->name('systems.users-for-import');
    Route::put('/systems/{system}/permissions/{permission}', [SystemController::class, 'updatePermission'])->name('systems.permissions.update');
    Route::delete('/systems/{system}/permissions/{permission}', [SystemController::class, 'destroyPermission'])->name('systems.permissions.destroy');

    // Connector Wizard
    Route::get('/connectors', [ConnectorWizardController::class, 'index'])->name('connectors.index');
    Route::get('/connectors/wizard', [ConnectorWizardController::class, 'create'])->name('connectors.create');
    Route::get('/connectors/{connectorConfig}/edit', [ConnectorWizardController::class, 'edit'])->name('connectors.edit');
    Route::delete('/connectors/{connectorConfig}', [ConnectorWizardController::class, 'destroy'])->name('connectors.destroy');
    // Wizard AJAX
    Route::post('/connectors/ajax/test-connection', [ConnectorWizardController::class, 'testConnection'])->name('connectors.ajax.test-connection');
    Route::post('/connectors/ajax/fetch-tables', [ConnectorWizardController::class, 'fetchTables'])->name('connectors.ajax.fetch-tables');
    Route::post('/connectors/ajax/fetch-columns', [ConnectorWizardController::class, 'fetchColumns'])->name('connectors.ajax.fetch-columns');
    Route::post('/connectors/ajax/preview-users', [ConnectorWizardController::class, 'previewUsers'])->name('connectors.ajax.preview-users');
    Route::post('/connectors/ajax/preview-permissions', [ConnectorWizardController::class, 'previewPermissions'])->name('connectors.ajax.preview-permissions');
    Route::post('/connectors/ajax/analyze-schema', [ConnectorWizardController::class, 'analyzeSchema'])->name('connectors.ajax.analyze-schema');
    Route::post('/connectors/ajax/analyze-zip', [ConnectorWizardController::class, 'analyzeZip'])->name('connectors.ajax.analyze-zip');
    Route::post('/connectors', [ConnectorWizardController::class, 'store'])->name('connectors.store');
    Route::put('/connectors/{connectorConfig}', [ConnectorWizardController::class, 'update'])->name('connectors.update');

    // Audit Log
    Route::get('/audit-log', [AuditLogController::class, 'index'])->name('audit.index');

    // Notification Channels
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications', [NotificationController::class, 'store'])->name('notifications.store');
    Route::put('/notifications/{notificationChannel}', [NotificationController::class, 'update'])->name('notifications.update');
    Route::delete('/notifications/{notificationChannel}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Reports
    Route::get('/reports/permission-matrix', [ReportController::class, 'permissionMatrix'])->name('reports.permission-matrix');
    Route::get('/reports/permission-matrix/export', [ReportController::class, 'exportPermissionMatrix'])->name('reports.permission-matrix.export');

    // Queue Monitor
    Route::get('/queue/monitor', [QueueMonitorController::class, 'index'])->name('queue.monitor');
    Route::post('/queue/failed/retry-all', [QueueMonitorController::class, 'retryAll'])->name('queue.failed.retry-all');
    Route::delete('/queue/failed/flush', [QueueMonitorController::class, 'flushFailed'])->name('queue.failed.flush');
    Route::post('/queue/failed/{uuid}/retry', [QueueMonitorController::class, 'retryFailed'])->name('queue.failed.retry');
    Route::delete('/queue/failed/{uuid}', [QueueMonitorController::class, 'destroyFailed'])->name('queue.failed.destroy');

    // Managed Group CRUD (adapter-specific reference tables e.g. departments, document_categories)
    Route::get('/systems/{system}/group-records/{group}', [SystemController::class, 'groupRecords'])->name('systems.group-records.index');
    Route::post('/systems/{system}/group-records', [SystemController::class, 'storeGroupRecord'])->name('systems.group-records.store');
    Route::put('/systems/{system}/group-records/{group}/{recordId}', [SystemController::class, 'updateGroupRecord'])->name('systems.group-records.update');
    Route::delete('/systems/{system}/group-records/{group}/{recordId}', [SystemController::class, 'destroyGroupRecord'])->name('systems.group-records.destroy');
});
