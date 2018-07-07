<?php

if (env('APP_ENV') === 'local') {
    return [
        'thisUrl' => env('APP_URL'),
        'clients' => [
            'http://localhost:8080/'  => [
                'client_id' => 1,
                'client_secret' => 'dmSsT2bsZH8kyPiLa4x8SsFuIDXbM4wK6oZwI6S7',
                'grant_type' => 'password',
                'scope' => 'view-public-content view-public-client-content',
                'url' => 'http://localhost:8080/'
            ],
            'http://localhost:8080/#/'  => [
                'client_id' => 1,
                'client_secret' => 'dmSsT2bsZH8kyPiLa4x8SsFuIDXbM4wK6oZwI6S7',
                'grant_type' => 'password',
                'scope' => 'view-public-content view-public-client-content',
                'url' => 'http://localhost:8080/'
            ],
            'http://localhost:8080'  => [
                'client_id' => 1,
                'client_secret' => 'dmSsT2bsZH8kyPiLa4x8SsFuIDXbM4wK6oZwI6S7',
                'grant_type' => 'password',
                'scope' => 'view-public-content view-public-client-content',
                'url' => 'http://localhost:8080/'
            ],
            'http://evenz.frontend/#/'  => [
                'client_id' => 6,
                'client_secret' => 'wFrRLwpnCged56YQFOeDB2nPLIe3z62L5Sp5GUYG',
                'grant_type' => 'password',
                'scope' => 'view-public-content view-public-client-content',
                'url' => 'http://evenz.frontend/'
            ],
            'http://evenz.frontend/'  => [
                'client_id' => 6,
                'client_secret' => 'wFrRLwpnCged56YQFOeDB2nPLIe3z62L5Sp5GUYG',
                'grant_type' => 'password',
                'scope' => 'view-public-content view-public-client-content',
                'url' => 'http://evenz.frontend/'
            ],
            'http://evenz.frontend'  => [
                'client_id' => 6,
                'client_secret' => 'wFrRLwpnCged56YQFOeDB2nPLIe3z62L5Sp5GUYG',
                'grant_type' => 'password',
                'scope' => 'view-public-content view-public-client-content',
                'url' => 'http://evenz.frontend/'
            ]
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
        'http://evenz.online' => [
            'client_id' => 1,
            'client_secret' => 'OxjUy30LQGHpsr8hsU2EXnfaqMLqZ223nWfG4P1k',
            'grant_type' => 'password',
            'scope' => '*'
        ],
        'http://evenz.online/' => [
            'client_id' => 1,
            'client_secret' => 'OxjUy30LQGHpsr8hsU2EXnfaqMLqZ223nWfG4P1k',
            'grant_type' => 'password',
            'scope' => '*'
        ],
        'http://evenz.frontend/'  => [
            'client_id' => 6,
            'client_secret' => 'wFrRLwpnCged56YQFOeDB2nPLIe3z62L5Sp5GUYG',
            'grant_type' => 'password',
            'scope' => 'view-public-content view-public-client-content',
            'url' => 'http://evenz.frontend/'
        ],
        'http://evenz.frontend'  => [
            'client_id' => 6,
            'client_secret' => 'wFrRLwpnCged56YQFOeDB2nPLIe3z62L5Sp5GUYG',
            'grant_type' => 'password',
            'scope' => 'view-public-content view-public-client-content',
            'url' => 'http://evenz.frontend/'
        ],
    ];
}
if (env('APP_ENV') === 'production') {
    return [
        'thisUrl' => env('APP_URL'),
        'clients' => [
            'https://evenz.online/' => [
                'client_id' => 1,
                'client_secret' => 'OxjUy30LQGHpsr8hsU2EXnfaqMLqZ223nWfG4P1k',
                'grant_type' => 'password',
                'scope' => '*',
                'url' => 'https://evenz.online/'
            ],
            'https://evenz.online/#/' => [
                'client_id' => 1,
                'client_secret' => 'OxjUy30LQGHpsr8hsU2EXnfaqMLqZ223nWfG4P1k',
                'grant_type' => 'password',
                'scope' => '*',
                'url' => 'https://evenz.online/'
            ]
        ],
        'self' => [
            'http://user.management.local',
            'https://sso.evenz.online'
        ],
        'https://evenz.online' => [
            'client_id' => 1,
            'client_secret' => 'OxjUy30LQGHpsr8hsU2EXnfaqMLqZ223nWfG4P1k',
            'grant_type' => 'password',
            'scope' => '*'
        ],
        'https://evenz.online/' => [
            'client_id' => 1,
            'client_secret' => 'OxjUy30LQGHpsr8hsU2EXnfaqMLqZ223nWfG4P1k',
            'grant_type' => 'password',
            'scope' => '*'
        ],
        ];
}