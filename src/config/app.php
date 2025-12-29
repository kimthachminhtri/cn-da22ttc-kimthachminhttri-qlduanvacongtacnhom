<?php
/**
 * Application Configuration
 */

return [
    'name' => 'TaskFlow',
    'description' => 'Modern project management and team collaboration platform',
    'version' => '2.0.0',
    'url' => getenv('APP_URL') ?: 'http://localhost/php',
    'base_path' => '/php',
    'timezone' => 'Asia/Ho_Chi_Minh',
    'locale' => 'vi',
    'debug' => getenv('APP_DEBUG') ?: true,
    
    // Session settings
    'session' => [
        'lifetime' => 120, // minutes
        'expire_on_close' => false,
    ],
    
    // Upload settings
    'upload' => [
        'max_size' => 10 * 1024 * 1024, // 10MB
        'allowed_types' => [
            'image/jpeg', 'image/png', 'image/gif', 'image/webp',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/zip',
            'text/plain',
        ],
    ],
];
