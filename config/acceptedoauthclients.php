<?php

if (env('APP_ENV') === 'local') {
    return [
        'thisUrl' => env('APP_URL'),
        'clients' => [
            'http://127.0.0.1:8080/',
            'http://127.0.0.1:8080',
            'http://localhost:8080/',
            'http://localhost:8080',
        ],
        'self' => [
          'http://user.management.local',
          'http://sso.evenz.online'
        ],
        'http://127.0.0.1:8080/' => [
            'client_id' => 1,
            'client_secret' => 'dmSsT2bsZH8kyPiLa4x8SsFuIDXbM4wK6oZwI6S7',
            'grant_type' => 'password',
            'scope' => '*'
        ],
        'http://localhost:8080/' => [
            'client_id' => 1,
            'client_secret' => 'dmSsT2bsZH8kyPiLa4x8SsFuIDXbM4wK6oZwI6S7',
            'grant_type' => 'password',
            'scope' => '*'
        ],
        'http://127.0.0.1:8080' => [
            'client_id' => 1,
            'client_secret' => 'dmSsT2bsZH8kyPiLa4x8SsFuIDXbM4wK6oZwI6S7',
            'grant_type' => 'password',
            'scope' => '*'
        ],
        'http://localhost:8080' => [
            'client_id' => 1,
            'client_secret' => 'dmSsT2bsZH8kyPiLa4x8SsFuIDXbM4wK6oZwI6S7',
            'grant_type' => 'password',
            'scope' => '*'
        ],
    ];
}