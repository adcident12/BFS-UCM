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
        'user_tables',
        'user_ucm_identifier',
        'user_identifier_col',
        'user_pk_col',
        'user_name_col',
        'user_email_col',
        'user_dept_col',
        'user_status_col',
        'user_status_active_val',
        'user_status_inactive_val',
        'permission_mode',
        'perm_table',
        'perm_user_fk_col',
        'perm_value_col',
        'perm_label_col',
        'perm_group_col',
        'perm_composite_cols',
        'manual_permissions',
        'perm_col_table',
        'perm_col_identifier',
        'perm_col_value_col',
        'perm_col_options',
        'perm_def_table',
        'perm_def_value_col',
        'perm_def_pk_col',
        'perm_def_label_col',
        'perm_def_group_col',
        'perm_delete_mode',
        'perm_def_soft_delete_col',
        'perm_def_soft_delete_val',
        'master_tables',
        'perm_bool_columns',
        'perm_junction_active_col',
        'perm_junction_active_val',
        'perm_junction_inactive_val',
        'perm_via_table',
        'perm_via_user_fk_col',
        'perm_via_group_fk_col',
        'perm_valid_from_col',
        'perm_valid_to_col',
        'perm_insert_meta_cols',
        // Mode: json_column
        'perm_json_col',
        'perm_json_available',
        // Mode: delimited_column
        'perm_delimited_col',
        'perm_delimiter',
        'perm_delimited_available',
        // Mode: bitmask
        'perm_bitmask_col',
        'perm_bitmask_map',
        // Mode: multi_level_hierarchy
        'perm_hier_user_role_table',
        'perm_hier_user_fk_col',
        'perm_hier_role_fk_col',
        'perm_hier_role_table',
        'perm_hier_role_pk_col',
        'perm_hier_role_name_col',
        'perm_hier_role_parent_col',
        'perm_hier_perm_table',
        'perm_hier_perm_role_fk_col',
        'perm_hier_perm_value_col',
        'dept_map',
    ];

    protected $hidden = ['db_password'];

    protected $casts = [
        'db_port' => 'integer',
        'db_password' => 'encrypted',
        'user_tables' => 'array',
        'manual_permissions' => 'array',
        'perm_composite_cols' => 'array',
        'perm_col_options' => 'array',
        'master_tables' => 'array',
        'perm_bool_columns' => 'array',
        'perm_insert_meta_cols' => 'array',
        'perm_json_available' => 'array',
        'perm_delimited_available' => 'array',
        'perm_bitmask_map' => 'array',
        'dept_map' => 'array',
    ];

    public function system(): BelongsTo
    {
        return $this->belongsTo(System::class);
    }
}
