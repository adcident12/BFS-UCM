<?php

use App\Http\Controllers\ApiDocsController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
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

    // API Documentation
    Route::get('/api-docs', [ApiDocsController::class, 'index'])->name('api-docs');
    // Swagger UI ถูก register โดย l5-swagger package อัตโนมัติที่ /api-docs/swagger (config/l5-swagger.php)

    // Users
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/search-ldap', [UserController::class, 'searchLdap'])->name('users.search-ldap');
    Route::get('/users/check-ad-status', [UserController::class, 'checkAdStatus'])->name('users.check-ad-status');
    Route::post('/users/import', [UserController::class, 'importFromLdap'])->name('users.import');
    Route::post('/users/import-bulk', [UserController::class, 'importBulkFromLdap'])->name('users.import-bulk');
    Route::post('/users/remove', [UserController::class, 'removeUsers'])->name('users.remove');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::post('/users/{user}/permissions', [UserController::class, 'updatePermissions'])->name('users.permissions.update');
    Route::patch('/users/{user}/info', [UserController::class, 'updateInfo'])->name('users.info.update');
    Route::post('/users/{user}/systems/{system}/status', [UserController::class, 'setSystemStatus'])->name('users.system.status');

    // Systems
    Route::resource('systems', SystemController::class);
    Route::post('/systems/{system}/permissions', [SystemController::class, 'storePermission'])->name('systems.permissions.store');
    Route::post('/systems/{system}/discover', [SystemController::class, 'discoverPermissions'])->name('systems.discover');
    Route::get('/systems/{system}/users-for-import', [SystemController::class, 'usersForImport'])->name('systems.users-for-import');
    Route::put('/systems/{system}/permissions/{permission}', [SystemController::class, 'updatePermission'])->name('systems.permissions.update');
    Route::delete('/systems/{system}/permissions/{permission}', [SystemController::class, 'destroyPermission'])->name('systems.permissions.destroy');
});
