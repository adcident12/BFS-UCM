<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConnectorConfig extends Model
{
    protected $fillable = [
        'system_id',
        'db_driver',
        'db_host',
        'db_port',
        'db_name',
        'db_user',
        'db_password',
        'user_table',
        'user_ucm_identifier',
        'user_identifier_col',
        'user_name_col',
        'user_email_col',
        'user_dept_col',
        'user_status_col',
        'user_status_active_val',
        'permission_mode',
        'perm_table',
        'perm_user_fk_col',
        'perm_value_col',
        'perm_label_col',
        'perm_group_col',
        'perm_composite_cols',
        'manual_permissions',
        'perm_def_table',
        'perm_def_value_col',
        'perm_def_pk_col',
        'perm_def_label_col',
        'perm_def_group_col',
        'perm_delete_mode',
        'perm_def_soft_delete_col',
        'perm_def_soft_delete_val',
    ];

    protected $hidden = ['db_password'];

    protected $casts = [
        'db_port' => 'integer',
        'manual_permissions' => 'array',
        'perm_composite_cols' => 'array',
    ];

    public function system(): BelongsTo
    {
        return $this->belongsTo(System::class);
    }
}
