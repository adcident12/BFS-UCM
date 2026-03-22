<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class LdapService
{
    protected string $host;
    protected int $port;
    protected string $baseDn;
    protected string $bindDn;
    protected string $bindPassword;
    protected string $userFilter;
    protected string $usernameAttribute;
    protected $connection = null;
    protected bool $bound = false;

    public function __construct()
    {
        $this->host              = config('ldap.host');
        $this->port              = config('ldap.port', 389);
        $this->baseDn            = config('ldap.base_dn');
        $this->bindDn            = config('ldap.bind_dn');
        $this->bindPassword      = config('ldap.bind_password');
        $this->userFilter        = config('ldap.user_filter', '(sAMAccountName={username})');
        $this->usernameAttribute = config('ldap.username_attribute', 'sAMAccountName');
    }

    public function connect(): bool
    {
        // reuse connection ถ้ายังอยู่ (ป้องกัน reconnect ซ้ำใน bulk import)
        if ($this->connection) {
            return true;
        }

        $this->connection = ldap_connect("ldap://{$this->host}", $this->port);

        if (! $this->connection) {
            return false;
        }

        ldap_set_option($this->connection, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($this->connection, LDAP_OPT_REFERRALS, 0);
        ldap_set_option($this->connection, LDAP_OPT_NETWORK_TIMEOUT, 5);

        return true;
    }

    public function bind(string $dn, string $password): bool
    {
        try {
            $ok = @ldap_bind($this->connection, $dn, $password);
            // reset bound flag เมื่อ bind ด้วย credentials อื่น (เช่น user auth)
            $this->bound = false;
            return $ok;
        } catch (\Exception $e) {
            Log::warning('LDAP bind failed: ' . $e->getMessage());
            return false;
        }
    }

    /** Bind ด้วย service account — reuse ถ้า bind ไว้แล้ว */
    protected function bindServiceAccount(): bool
    {
        if ($this->bound) {
            return true;
        }
        $ok = $this->bind($this->bindDn, $this->bindPassword);
        $this->bound = $ok;
        return $ok;
    }

    public function authenticate(string $username, string $password): array|false
    {
        if (! $this->connect()) {
            Log::error('LDAP: Cannot connect to server');
            return false;
        }

        // Bind ด้วย service account ก่อน เพื่อค้นหา user DN
        if (! $this->bind($this->bindDn, $this->bindPassword)) {
            Log::error('LDAP: Service account bind failed');
            return false;
        }

        // ค้นหา user
        $filter = str_replace('{username}', ldap_escape($username, '', LDAP_ESCAPE_FILTER), $this->userFilter);
        $search = @ldap_search($this->connection, $this->baseDn, $filter, [
            'dn', 'cn', 'sAMAccountName', 'mail', 'displayName',
            'department', 'title', 'memberOf', 'objectGUID', 'description',
        ]);

        if (! $search) {
            Log::warning("LDAP: User '{$username}' not found");
            return false;
        }

        $entries = ldap_get_entries($this->connection, $search);

        if ($entries['count'] === 0) {
            return false;
        }

        $userDn = $entries[0]['dn'];

        // Bind ด้วย password ของ user เพื่อยืนยัน
        if (! $this->bind($userDn, $password)) {
            return false;
        }

        return $this->parseEntry($entries[0]);
    }

    public function findUser(string $username): array|false
    {
        if (! $this->connect()) {
            return false;
        }

        if (! $this->bindServiceAccount()) {
            return false;
        }

        $filter = str_replace('{username}', ldap_escape($username, '', LDAP_ESCAPE_FILTER), $this->userFilter);
        $search = @ldap_search($this->connection, $this->baseDn, $filter, [
            'dn', 'cn', 'sAMAccountName', 'mail', 'displayName',
            'department', 'title', 'memberOf', 'objectGUID', 'description',
        ]);

        if (! $search) {
            return false;
        }

        $entries = ldap_get_entries($this->connection, $search);

        return $entries['count'] > 0 ? $this->parseEntry($entries[0]) : false;
    }

    public function searchUsers(string $query = '*', int $limit = 200): array
    {
        if (! $this->connect()) {
            return [];
        }

        if (! $this->bindServiceAccount()) {
            return [];
        }

        $escaped = ldap_escape($query, '', LDAP_ESCAPE_FILTER);
        $filter  = "(&(objectCategory=person)(objectClass=user)(|(sAMAccountName=*{$escaped}*)(displayName=*{$escaped}*)(mail=*{$escaped}*)))";

        $search = @ldap_search($this->connection, $this->baseDn, $filter, [
            'dn', 'cn', 'sAMAccountName', 'mail', 'displayName',
            'department', 'title', 'memberOf', 'objectGUID', 'description',
        ], 0, $limit);

        if (! $search) {
            return [];
        }

        $entries = ldap_get_entries($this->connection, $search);
        $users   = [];

        for ($i = 0; $i < $entries['count']; $i++) {
            $users[] = $this->parseEntry($entries[$i]);
        }

        return $users;
    }

    protected function parseEntry(array $entry): array
    {
        $groups = [];
        if (isset($entry['memberof'])) {
            for ($i = 0; $i < $entry['memberof']['count']; $i++) {
                // ดึงเฉพาะ CN จาก DN เช่น "CN=IT-Admin,OU=Groups,..."
                if (preg_match('/^CN=([^,]+)/i', $entry['memberof'][$i], $m)) {
                    $groups[] = $m[1];
                }
            }
        }

        return [
            'dn'              => $entry['dn'] ?? '',
            'username'        => $this->getValue($entry, 'samaccountname'),
            'employee_number' => $this->parseEmployeeNumber($entry),
            'name'            => $this->getValue($entry, 'displayname') ?: $this->getValue($entry, 'cn'),
            'email'           => $this->getValue($entry, 'mail'),
            'department'      => $this->getValue($entry, 'department'),
            'title'           => $this->getValue($entry, 'title'),
            'guid'            => $this->parseGuid($entry),
            'groups'          => $groups,
        ];
    }

    protected function parseGuid(array $entry): ?string
    {
        $raw = $entry['objectguid'][0] ?? null;

        if (! $raw || strlen($raw) !== 16) {
            return null;
        }

        // แปลง binary 16-byte objectGUID ของ AD เป็น UUID string
        $hex = bin2hex($raw);

        return sprintf(
            '%s-%s-%s-%s-%s',
            // AD เก็บ bytes 0-3 แบบ little-endian
            substr($hex, 6, 2) . substr($hex, 4, 2) . substr($hex, 2, 2) . substr($hex, 0, 2),
            substr($hex, 10, 2) . substr($hex, 8, 2),
            substr($hex, 14, 2) . substr($hex, 12, 2),
            substr($hex, 16, 4),
            substr($hex, 20, 12)
        );
    }

    protected function parseEmployeeNumber(array $entry): string
    {
        $description = $this->getValue($entry, 'description');
        if (empty($description)) {
            return '';
        }
        // รหัสพนักงานอยู่ที่คำแรกของ description เช่น "EMP001 John Doe"
        return explode(' ', trim($description))[0];
    }

    protected function getValue(array $entry, string $attribute): string
    {
        return isset($entry[$attribute][0]) ? $entry[$attribute][0] : '';
    }

    /**
     * ตรวจสอบ batch ว่า usernames เหล่านี้มีอยู่ใน AD หรือไม่
     * คืนค่า array ของ usernames (lowercase) ที่พบใน AD
     */
    public function findUsersExistence(array $usernames): array
    {
        if (empty($usernames) || ! $this->connect() || ! $this->bindServiceAccount()) {
            return [];
        }

        $found = [];

        // chunk เพื่อป้องกัน LDAP filter ยาวเกิน (50 ต่อ query)
        foreach (array_chunk($usernames, 50) as $chunk) {
            $parts  = array_map(
                fn ($u) => '(sAMAccountName=' . ldap_escape($u, '', LDAP_ESCAPE_FILTER) . ')',
                $chunk
            );
            $filter = '(&(objectCategory=person)(objectClass=user)(|' . implode('', $parts) . '))';

            $search = @ldap_search($this->connection, $this->baseDn, $filter, ['sAMAccountName'], 0, count($chunk) + 5);
            if (! $search) continue;

            $entries = ldap_get_entries($this->connection, $search);
            for ($i = 0; $i < $entries['count']; $i++) {
                $found[] = strtolower($this->getValue($entries[$i], 'samaccountname'));
            }
        }

        return $found;
    }

    public function __destruct()
    {
        if ($this->connection) {
            @ldap_close($this->connection);
        }
    }
}
