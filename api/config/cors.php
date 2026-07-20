<?php
return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'api/auth/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'http://localhost:3000',
        'http://localhost:3001',
    ],
    'allowed_headers' => ['*'],
    'supports_credentials' => true,
];
