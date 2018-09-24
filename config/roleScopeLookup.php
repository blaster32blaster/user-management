<?php

    return [
        'PlatformSuperAdmin' => [
            'manage-admins',
            'manage-client-admins',
            'manage-users',
            'manage-access',
            'view-public-content',
            'view-private-content',
            'view-public-client-content'
        ],
        'PlatformAdmin' => [
            'manage-client-admins',
            'manage-users',
            'manage-access',
            'view-public-content',
            'view-private-content',
            'view-public-client-content'
        ],
        'ClientAdmin' => [
            'manage-client-users',
            'manage-client-access',
            'view-public-content',
            'view-public-client-content',
            'view-private-client-content'
        ],
        'PlatformSuperUser' => [
            'view-public-content',
            'view-private-content',
            'view-public-client-content'
        ],
        'PlatformUser' => [
            'view-public-content',
            'view-public-client-content'
        ],
        'ClientSuperUser' => [
            'view-public-content',
            'view-public-client-content',
            'view-private-client-content'
        ],
        'ClientUser' => [
            'view-public-content',
            'view-public-client-content'
        ],
        ];