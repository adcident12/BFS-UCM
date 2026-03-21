<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'event_category',
        'event_type',
        'actor_id',
        'actor_username',
        'subject_type',
        'subject_id',
        'subject_label',
        'description',
        'metadata',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    // Categories
    const CATEGORY_AUTH = 'auth';
    const CATEGORY_USERS = 'users';
    const CATEGORY_PERMISSIONS = 'permissions';
    const CATEGORY_SYSTEMS = 'systems';
    const CATEGORY_CONNECTORS = 'connectors';
    const CATEGORY_API = 'api';

    // Event types — Auth
    const EVENT_LOGIN = 'login';
    const EVENT_LOGOUT = 'logout';
    const EVENT_LOGIN_FAILED = 'login_failed';

    // Event types — Users
    const EVENT_USER_IMPORTED = 'user_imported';
    const EVENT_USER_BULK_IMPORTED = 'user_bulk_imported';
    const EVENT_USER_REMOVED = 'user_removed';
    const EVENT_USER_INFO_UPDATED = 'user_info_updated';
    const EVENT_ADMIN_LEVEL_UPDATED = 'admin_level_updated';

    // Event types — Permissions
    const EVENT_PERMISSIONS_UPDATED = 'permissions_updated';
    const EVENT_PERMISSIONS_DISCOVERED = 'permissions_discovered';
    const EVENT_ACCOUNT_STATUS_CHANGED = 'account_status_changed';

    // Event types — Systems
    const EVENT_SYSTEM_CREATED = 'system_created';
    const EVENT_SYSTEM_UPDATED = 'system_updated';
    const EVENT_SYSTEM_DELETED = 'system_deleted';
    const EVENT_SYSTEM_2WAY_TOGGLED = 'system_2way_toggled';
    const EVENT_PERM_DEF_CREATED = 'permission_def_created';
    const EVENT_PERM_DEF_UPDATED = 'permission_def_updated';
    const EVENT_PERM_DEF_DELETED = 'permission_def_deleted';
    const EVENT_PERM_DEF_DISCOVERED = 'permission_def_discovered';

    // Event types — Connectors
    const EVENT_CONNECTOR_CREATED = 'connector_created';
    const EVENT_CONNECTOR_UPDATED = 'connector_updated';
    const EVENT_CONNECTOR_DELETED = 'connector_deleted';

    // Event types — API
    const EVENT_API_TOKEN_ISSUED = 'api_token_issued';
    const EVENT_API_TOKEN_REVOKED = 'api_token_revoked';
    const EVENT_API_USER_LOGIN = 'api_user_login';

    public function actor(): BelongsTo
    {
        return $this->belongsTo(UcmUser::class, 'actor_id');
    }
}
