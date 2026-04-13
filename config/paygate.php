<?php

return [
    'auth_token' => env('PAYGATE_AUTH_TOKEN'),
    'base_url' => 'https://paygateglobal.com/api/v1',
    'callback_url' => env('PAYGATE_CALLBACK_URL'),
    'admin_email' => env('ADMIN_EMAIL', 'admin@mrepublique.com'),
];