<?php
/**
 * Mail Configuration
 * 
 * Drivers available:
 * - 'mail': PHP mail() function (requires mail server configured)
 * - 'smtp': Direct SMTP connection
 * - 'log': Log emails instead of sending (for development)
 */

return [
    // Mail driver: mail, smtp, log
    'driver' => getenv('MAIL_DRIVER') ?: 'log',
    
    // Default sender
    'from_email' => getenv('MAIL_FROM_ADDRESS') ?: 'noreply@taskflow.local',
    'from_name' => getenv('MAIL_FROM_NAME') ?: 'TaskFlow',
    
    // SMTP Configuration (used when driver = 'smtp')
    'smtp' => [
        'host' => getenv('MAIL_HOST') ?: 'smtp.gmail.com',
        'port' => (int)(getenv('MAIL_PORT') ?: 587),
        'username' => getenv('MAIL_USERNAME') ?: '',
        'password' => getenv('MAIL_PASSWORD') ?: '',
        'encryption' => getenv('MAIL_ENCRYPTION') ?: 'tls', // tls, ssl, none
    ],
];
