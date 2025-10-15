<?php
return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'register'],
    'allowed_methods' => ['*'],
    // Explicitly allow common dev origins and a localhost pattern so web dev servers
    // running on arbitrary ports (flutter web, Vite, etc.) are accepted.
    'allowed_origins' => ['*'],
    'allowed_origins_patterns' => ['/^http:\\/\\/localhost:\\d+$/'],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];