<?php

return [
    'host'               => env('LDAP_HOST', ''),
    'port'               => env('LDAP_PORT', 389),
    'base_dn'            => env('LDAP_BASE_DN', ''),
    'bind_dn'            => env('LDAP_BIND_DN', ''),
    'bind_password'      => env('LDAP_BIND_PASSWORD', ''),
    'user_filter'        => env('LDAP_USER_FILTER', '(sAMAccountName={username})'),
    'username_attribute' => env('LDAP_USERNAME_ATTRIBUTE', 'sAMAccountName'),
];
