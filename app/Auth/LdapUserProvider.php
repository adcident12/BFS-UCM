<?php

namespace App\Auth;

use App\Models\UcmUser;
use App\Services\LdapService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;

class LdapUserProvider implements UserProvider
{
    /** เก็บข้อมูล LDAP ชั่วคราวระหว่าง retrieveByCredentials → validateCredentials */
    private ?array $pendingLdapUser = null;

    public function __construct(protected LdapService $ldap) {}

    public function retrieveById($identifier): ?Authenticatable
    {
        return UcmUser::find($identifier);
    }

    public function retrieveByToken($identifier, $token): ?Authenticatable
    {
        return UcmUser::where('id', $identifier)
            ->where('remember_token', $token)
            ->first();
    }

    public function updateRememberToken(Authenticatable $user, $token): void
    {
        $user->forceFill(['remember_token' => $token])->save();
    }

    public function isServerUnreachable(): bool
    {
        return $this->ldap->isServerUnreachable();
    }

    public function retrieveByCredentials(array $credentials): ?Authenticatable
    {
        if (empty($credentials['username'])) {
            return null;
        }

        $ldapUser = $this->ldap->findUser($credentials['username']);
        if (! $ldapUser) {
            return null;
        }

        // เก็บข้อมูล LDAP ไว้ก่อน — ยังไม่บันทึก DB (รอให้ password ถูกต้องก่อน)
        $this->pendingLdapUser = $ldapUser;

        // คืน user จาก DB ถ้ามีอยู่แล้ว หรือ instance ว่างๆ ถ้ายังไม่มี
        return UcmUser::where('username', $ldapUser['username'])->first()
            ?? (new UcmUser)->forceFill(['username' => $ldapUser['username']]);
    }

    public function validateCredentials(Authenticatable $user, array $credentials): bool
    {
        $ok = $this->ldap->authenticate($credentials['username'], $credentials['password']) !== false;

        if ($ok && $this->pendingLdapUser) {
            // Password ถูกต้อง → บันทึก/อัปเดต DB
            $saved = UcmUser::updateOrCreate(
                ['username' => $this->pendingLdapUser['username']],
                [
                    'employee_number' => $this->pendingLdapUser['employee_number'] ?: null,
                    'name' => $this->pendingLdapUser['name'],
                    'email' => $this->pendingLdapUser['email'],
                    'department' => $this->pendingLdapUser['department'],
                    'title' => $this->pendingLdapUser['title'],
                    'ldap_dn' => $this->pendingLdapUser['dn'],
                    'ldap_guid' => $this->pendingLdapUser['guid'],
                    'is_active' => true,
                ]
            );

            // Sync attributes กลับมายัง $user instance เพื่อให้ Auth::login() ได้ ID ที่ถูกต้อง
            $user->forceFill($saved->getAttributes());
            $user->exists = true;
        }

        $this->pendingLdapUser = null;

        return $ok;
    }

    public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false): void
    {
        // LDAP ไม่ต้องการ rehash
    }
}
