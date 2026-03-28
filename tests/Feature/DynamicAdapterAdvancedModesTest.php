<?php

namespace Tests\Feature;

use App\Adapters\DynamicAdapter;
use App\Models\ConnectorConfig;
use App\Models\System;
use App\Models\UcmUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests for DynamicAdapter advanced permission modes:
 *   Scenario I  — boolean_matrix
 *   Scenario J  — junction with soft-delete
 *   Scenario K  — group_inheritance
 *   Scenario L  — junction with time-bound validity
 *   Scenario M  — junction with insert metadata
 */
class DynamicAdapterAdvancedModesTest extends TestCase
{
    use RefreshDatabase;

    private const DB_HOST = 'ucm-db';
    private const DB_PORT = 3306;
    private const DB_USER = 'ucm_user';
    private const DB_PASS = 'ucm_password';

    // ── Helpers ────────────────────────────────────────────────────────────

    private function makeSystem(): System
    {
        return System::create([
            'name'          => 'Test System',
            'slug'          => 'test-'.uniqid(),
            'description'   => '',
            'is_active'     => true,
            'adapter_class' => DynamicAdapter::class,
        ]);
    }

    private function makeUcmUser(string $username): UcmUser
    {
        return UcmUser::create([
            'username'  => $username,
            'name'      => $username,
            'email'     => $username.'@test.com',
            'password'  => bcrypt('password'),
            'is_admin'  => 0,
            'is_active' => 1,
        ]);
    }

    /**
     * Build a ConnectorConfig for a given scenario DB and return it.
     *
     * @param  array<string, mixed>  $overrides
     */
    private function makeConfig(System $system, string $dbName, array $overrides): ConnectorConfig
    {
        $defaults = [
            'system_id'           => $system->id,
            'db_driver'           => 'mysql',
            'db_host'             => self::DB_HOST,
            'db_port'             => self::DB_PORT,
            'db_name'             => $dbName,
            'db_user'             => self::DB_USER,
            'db_password'         => self::DB_PASS,
            'user_ucm_identifier' => 'username',
        ];

        return ConnectorConfig::create(array_merge($defaults, $overrides));
    }

    private function adapter(System $system): DynamicAdapter
    {
        return new DynamicAdapter($system->load('connectorConfig'));
    }

    // ── Scenario I — boolean_matrix ────────────────────────────────────────

    #[Test]
    #[Group('scenario-i')]
    public function gets_available_permissions_for_boolean_matrix(): void
    {
        $system = $this->makeSystem();
        $this->makeConfig($system, 'sc_i', [
            'permission_mode'     => 'boolean_matrix',
            'user_table'          => 'cargo_users',
            'user_identifier_col' => 'username',
            'perm_bool_columns'   => [
                'can_view_manifest',
                'can_edit_manifest',
                'can_approve_manifest',
                'can_export_report',
                'can_manage_users',
            ],
        ]);

        $adapter = $this->adapter($system);
        $perms = $adapter->getAvailablePermissions();

        $keys = array_column($perms, 'key');
        $this->assertContains('can_view_manifest', $keys);
        $this->assertContains('can_manage_users', $keys);
        $this->assertCount(5, $perms);
    }

    #[Test]
    #[Group('scenario-i')]
    public function gets_current_permissions_for_boolean_matrix(): void
    {
        $system = $this->makeSystem();
        $this->makeConfig($system, 'sc_i', [
            'permission_mode'     => 'boolean_matrix',
            'user_table'          => 'cargo_users',
            'user_identifier_col' => 'username',
            'perm_bool_columns'   => [
                'can_view_manifest',
                'can_edit_manifest',
                'can_approve_manifest',
                'can_export_report',
                'can_manage_users',
            ],
        ]);

        // K21057 has: can_view=1, can_edit=1, can_approve=0, can_export=1, can_manage=0
        $user = $this->makeUcmUser('K21057');
        $adapter = $this->adapter($system);
        $current = $adapter->getCurrentPermissions($user);

        $this->assertContains('can_view_manifest', $current);
        $this->assertContains('can_edit_manifest', $current);
        $this->assertContains('can_export_report', $current);
        $this->assertNotContains('can_approve_manifest', $current);
        $this->assertNotContains('can_manage_users', $current);
    }

    #[Test]
    #[Group('scenario-i')]
    public function syncs_boolean_matrix_permissions(): void
    {
        $system = $this->makeSystem();
        $this->makeConfig($system, 'sc_i', [
            'permission_mode'     => 'boolean_matrix',
            'user_table'          => 'cargo_users',
            'user_identifier_col' => 'username',
            'perm_bool_columns'   => [
                'can_view_manifest',
                'can_edit_manifest',
                'can_approve_manifest',
                'can_export_report',
                'can_manage_users',
            ],
        ]);

        $user = $this->makeUcmUser('K21057');
        $adapter = $this->adapter($system);

        // Grant only approve + manage
        $result = $adapter->syncPermissions($user, ['can_approve_manifest', 'can_manage_users']);
        $this->assertTrue($result);

        $current = $adapter->getCurrentPermissions($user);
        $this->assertContains('can_approve_manifest', $current);
        $this->assertContains('can_manage_users', $current);
        $this->assertNotContains('can_view_manifest', $current);
        $this->assertNotContains('can_edit_manifest', $current);

        // Restore original state: view + edit + export
        $adapter->syncPermissions($user, ['can_view_manifest', 'can_edit_manifest', 'can_export_report']);
    }

    // ── Scenario J — soft-delete junction ─────────────────────────────────

    #[Test]
    #[Group('scenario-j')]
    public function gets_current_permissions_with_soft_delete_junction(): void
    {
        $system = $this->makeSystem();
        $this->makeConfig($system, 'sc_j', [
            'permission_mode'            => 'junction',
            'user_table'                 => 'staff',
            'user_identifier_col'        => 'username',
            'perm_table'                 => 'staff_access',
            'perm_user_fk_col'           => 'username',
            'perm_value_col'             => 'access_code',
            'perm_junction_active_col'   => 'is_active',
            'perm_junction_active_val'   => '1',
            'perm_junction_inactive_val' => '0',
        ]);

        $user = $this->makeUcmUser('PatcharinW');
        $adapter = $this->adapter($system);
        $current = $adapter->getCurrentPermissions($user);

        $this->assertContains('SYS_LOGIN', $current);
        $this->assertContains('VIEW_REPORT', $current);
    }

    #[Test]
    #[Group('scenario-j')]
    public function soft_deletes_instead_of_hard_deletes_junction_rows(): void
    {
        $system = $this->makeSystem();
        $this->makeConfig($system, 'sc_j', [
            'permission_mode'            => 'junction',
            'user_table'                 => 'staff',
            'user_identifier_col'        => 'username',
            'perm_table'                 => 'staff_access',
            'perm_user_fk_col'           => 'username',
            'perm_value_col'             => 'access_code',
            'perm_junction_active_col'   => 'is_active',
            'perm_junction_active_val'   => '1',
            'perm_junction_inactive_val' => '0',
        ]);

        $user = $this->makeUcmUser('PatcharinW');
        $adapter = $this->adapter($system);

        // Keep only SYS_LOGIN — others should be deactivated
        $result = $adapter->syncPermissions($user, ['SYS_LOGIN']);
        $this->assertTrue($result);

        $current = $adapter->getCurrentPermissions($user);
        $this->assertContains('SYS_LOGIN', $current);
        $this->assertNotContains('VIEW_REPORT', $current);
        $this->assertNotContains('EDIT_RECORD', $current);

        // Rows should still exist in DB, just with is_active = 0
        $pdo = new \PDO(
            'mysql:host='.self::DB_HOST.';port='.self::DB_PORT.';dbname=sc_j;charset=utf8mb4',
            self::DB_USER,
            self::DB_PASS,
        );
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM staff_access WHERE username = ? AND is_active = 0');
        $stmt->execute(['PatcharinW']);
        $this->assertGreaterThan(0, (int) $stmt->fetchColumn(), 'Deactivated rows should remain in the table');

        // Restore all 3 permissions
        $adapter->syncPermissions($user, ['SYS_LOGIN', 'VIEW_REPORT', 'EDIT_RECORD']);
    }

    // ── Scenario K — group_inheritance ────────────────────────────────────

    #[Test]
    #[Group('scenario-k')]
    public function gets_available_permissions_for_group_inheritance(): void
    {
        $system = $this->makeSystem();
        $this->makeConfig($system, 'sc_k', [
            'permission_mode'       => 'group_inheritance',
            'user_table'            => 'employees',
            'user_identifier_col'   => 'username',
            'perm_via_table'        => 'employee_roles',
            'perm_via_user_fk_col'  => 'username',
            'perm_via_group_fk_col' => 'role_code',
            'perm_table'            => 'role_permissions',
            'perm_user_fk_col'      => 'role_code',
            'perm_value_col'        => 'perm_code',
        ]);

        $adapter = $this->adapter($system);
        $perms = $adapter->getAvailablePermissions();

        $this->assertNotEmpty($perms);
        $keys = array_column($perms, 'key');
        $this->assertContains('READ', $keys);
    }

    #[Test]
    #[Group('scenario-k')]
    public function gets_current_permissions_via_group_inheritance(): void
    {
        $system = $this->makeSystem();
        $this->makeConfig($system, 'sc_k', [
            'permission_mode'       => 'group_inheritance',
            'user_table'            => 'employees',
            'user_identifier_col'   => 'username',
            'perm_via_table'        => 'employee_roles',
            'perm_via_user_fk_col'  => 'username',
            'perm_via_group_fk_col' => 'role_code',
            'perm_table'            => 'role_permissions',
            'perm_user_fk_col'      => 'role_code',
            'perm_value_col'        => 'perm_code',
        ]);

        // MalichaS has roles: APPROVER, VIEWER
        $user = $this->makeUcmUser('MalichaS');
        $adapter = $this->adapter($system);
        $current = $adapter->getCurrentPermissions($user);

        $this->assertNotEmpty($current, 'Should inherit permissions via group roles');
    }

    #[Test]
    #[Group('scenario-k')]
    public function syncs_group_inheritance_via_table(): void
    {
        $system = $this->makeSystem();
        $this->makeConfig($system, 'sc_k', [
            'permission_mode'       => 'group_inheritance',
            'user_table'            => 'employees',
            'user_identifier_col'   => 'username',
            'perm_via_table'        => 'employee_roles',
            'perm_via_user_fk_col'  => 'username',
            'perm_via_group_fk_col' => 'role_code',
            'perm_table'            => 'role_permissions',
            'perm_user_fk_col'      => 'role_code',
            'perm_value_col'        => 'perm_code',
        ]);

        $user = $this->makeUcmUser('SomsakC');
        $adapter = $this->adapter($system);

        // Assign VIEWER role
        $result = $adapter->syncPermissions($user, ['VIEWER']);
        $this->assertTrue($result);

        $pdo = new \PDO(
            'mysql:host='.self::DB_HOST.';port='.self::DB_PORT.';dbname=sc_k;charset=utf8mb4',
            self::DB_USER,
            self::DB_PASS,
        );
        $stmt = $pdo->prepare('SELECT role_code FROM employee_roles WHERE username = ?');
        $stmt->execute(['SomsakC']);
        $roles = array_column($stmt->fetchAll(\PDO::FETCH_ASSOC), 'role_code');
        $this->assertContains('VIEWER', $roles);
        $this->assertNotContains('ADMIN', $roles);

        // Assign ADMIN role instead
        $adapter->syncPermissions($user, ['ADMIN']);
        $stmt->execute(['SomsakC']);
        $roles = array_column($stmt->fetchAll(\PDO::FETCH_ASSOC), 'role_code');
        $this->assertContains('ADMIN', $roles);
        $this->assertNotContains('VIEWER', $roles);

        // Cleanup
        $pdo->prepare('DELETE FROM employee_roles WHERE username = ?')->execute(['SomsakC']);
    }

    // ── Scenario L — time-bounded junction ────────────────────────────────

    #[Test]
    #[Group('scenario-l')]
    public function filters_expired_junction_rows(): void
    {
        $system = $this->makeSystem();
        $this->makeConfig($system, 'sc_l', [
            'permission_mode'     => 'junction',
            'user_table'          => 'users',
            'user_identifier_col' => 'username',
            'perm_table'          => 'user_roles',
            'perm_user_fk_col'    => 'username',
            'perm_value_col'      => 'role_code',
            'perm_valid_from_col' => 'valid_from',
            'perm_valid_to_col'   => 'valid_to',
        ]);

        $pdo = new \PDO(
            'mysql:host='.self::DB_HOST.';port='.self::DB_PORT.';dbname=sc_l;charset=utf8mb4',
            self::DB_USER,
            self::DB_PASS,
        );

        // Insert one active row and one already-expired row
        $pdo->prepare("INSERT INTO users (username, full_name) VALUES (?, ?) ON DUPLICATE KEY UPDATE full_name = VALUES(full_name)")
            ->execute(['test_tl', 'Test TimeBound']);
        $pdo->prepare("DELETE FROM user_roles WHERE username = 'test_tl'")->execute();
        $pdo->prepare("INSERT INTO user_roles (username, role_code, valid_from, valid_to) VALUES (?, 'ROLE_ACTIVE', '2020-01-01', '2099-12-31')")->execute(['test_tl']);
        $pdo->prepare("INSERT INTO user_roles (username, role_code, valid_from, valid_to) VALUES (?, 'ROLE_EXPIRED', '2020-01-01', '2020-06-30')")->execute(['test_tl']);

        $user = $this->makeUcmUser('test_tl');
        $adapter = $this->adapter($system);
        $current = $adapter->getCurrentPermissions($user);

        $this->assertContains('ROLE_ACTIVE', $current);
        $this->assertNotContains('ROLE_EXPIRED', $current);

        // Cleanup
        $pdo->prepare("DELETE FROM user_roles WHERE username = 'test_tl'")->execute();
        $pdo->prepare("DELETE FROM users WHERE username = 'test_tl'")->execute();
    }

    // ── Scenario M — insert metadata ──────────────────────────────────────

    #[Test]
    #[Group('scenario-m')]
    public function inserts_metadata_columns_when_adding_junction_row(): void
    {
        $system = $this->makeSystem();
        $this->makeConfig($system, 'sc_m', [
            'permission_mode'       => 'junction',
            'user_table'            => 'users',
            'user_identifier_col'   => 'username',
            'perm_table'            => 'user_grants',
            'perm_user_fk_col'      => 'username',
            'perm_value_col'        => 'grant_code',
            'perm_insert_meta_cols' => [
                'granted_by' => '__ucm_admin__',
                'granted_at' => '__now__',
            ],
        ]);

        $pdo = new \PDO(
            'mysql:host='.self::DB_HOST.';port='.self::DB_PORT.';dbname=sc_m;charset=utf8mb4',
            self::DB_USER,
            self::DB_PASS,
        );

        // Ensure user exists
        $pdo->prepare("INSERT INTO users (username, full_name) VALUES (?, ?) ON DUPLICATE KEY UPDATE full_name = VALUES(full_name)")
            ->execute(['test_meta', 'Test Meta']);
        $pdo->prepare("DELETE FROM user_grants WHERE username = 'test_meta'")->execute();

        $user = $this->makeUcmUser('test_meta');
        $adapter = $this->adapter($system);
        $result = $adapter->syncPermissions($user, ['GRANT_A']);

        $this->assertTrue($result);

        $stmt = $pdo->prepare('SELECT granted_by, granted_at FROM user_grants WHERE username = ? AND grant_code = ?');
        $stmt->execute(['test_meta', 'GRANT_A']);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertNotNull($row, 'Inserted row should exist');
        $this->assertNotEmpty($row['granted_at'], 'granted_at should be filled by __now__ token');

        // Cleanup
        $pdo->prepare("DELETE FROM user_grants WHERE username = 'test_meta'")->execute();
        $pdo->prepare("DELETE FROM users WHERE username = 'test_meta'")->execute();
    }

    #[Test]
    #[Group('scenario-m')]
    public function syncs_and_re_inserts_with_metadata_on_second_sync(): void
    {
        $system = $this->makeSystem();
        $this->makeConfig($system, 'sc_m', [
            'permission_mode'       => 'junction',
            'user_table'            => 'users',
            'user_identifier_col'   => 'username',
            'perm_table'            => 'user_grants',
            'perm_user_fk_col'      => 'username',
            'perm_value_col'        => 'grant_code',
            'perm_insert_meta_cols' => [
                'granted_by' => '__ucm_admin__',
                'granted_at' => '__now__',
            ],
        ]);

        $pdo = new \PDO(
            'mysql:host='.self::DB_HOST.';port='.self::DB_PORT.';dbname=sc_m;charset=utf8mb4',
            self::DB_USER,
            self::DB_PASS,
        );

        $pdo->prepare("INSERT INTO users (username, full_name) VALUES (?, ?) ON DUPLICATE KEY UPDATE full_name = VALUES(full_name)")
            ->execute(['test_meta2', 'Test Meta 2']);
        $pdo->prepare("DELETE FROM user_grants WHERE username = 'test_meta2'")->execute();

        $user = $this->makeUcmUser('test_meta2');
        $adapter = $this->adapter($system);

        $adapter->syncPermissions($user, ['GRANT_A']);
        // Second sync changes grants
        $result = $adapter->syncPermissions($user, ['GRANT_B']);
        $this->assertTrue($result);

        $current = $adapter->getCurrentPermissions($user);
        $this->assertContains('GRANT_B', $current);
        $this->assertNotContains('GRANT_A', $current);

        // Cleanup
        $pdo->prepare("DELETE FROM user_grants WHERE username = 'test_meta2'")->execute();
        $pdo->prepare("DELETE FROM users WHERE username = 'test_meta2'")->execute();
    }
}
