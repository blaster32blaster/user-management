<?php

if (env('APP_ENV') === 'local') {
    return [
        'clients' => [
            'http://127.0.0.1:8080/',
            'http://127.0.0.1:8080',
            'http://localhost:8080/',
            'http://localhost:8080',
        ],
        'http://127.0.0.1:8080/' => [
            'client_id' => 10,
            'client_secret' => 'Ds4Wxl23YFo9MOOjlhxroHCbmHlHl7rkc2vS6kj3',
            'grant_type' => 'password',
            'scope' => '*'
        ],
        'http://localhost:8080/' => [
            'client_id' => 10,
            'client_secret' => 'Ds4Wxl23YFo9MOOjlhxroHCbmHlHl7rkc2vS6kj3',
            'grant_type' => 'password',
            'scope' => '*'
        ],
        'http://127.0.0.1:8080' => [
            'client_id' => 10,
            'client_secret' => 'Ds4Wxl23YFo9MOOjlhxroHCbmHlHl7rkc2vS6kj3',
            'grant_type' => 'password',
            'scope' => '*'
        ],
        'http://localhost:8080' => [
            'client_id' => 10,
            'client_secret' => 'Ds4Wxl23YFo9MOOjlhxroHCbmHlHl7rkc2vS6kj3',
            'grant_type' => 'password',
            'scope' => '*'
        ],
    ];
}