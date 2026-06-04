<?php
return [
    'pgsql' => [
        // Supabase dashboard > Project Settings > Database > Connection string.
        // Use the session pooler host when your network does not support IPv6.
        'host' => 'aws-0-ap-southeast-1.pooler.supabase.com',
        'port' => '6543',
        'database' => 'postgres',
        'username' => 'postgres.your-project-ref',
        'password' => 'your-supabase-database-password',
        'sslmode' => 'require',
        'emulate_prepares' => true,
    ],
    'mysql' => [
        'host' => '',
        'port' => '3306',
        'database' => '',
        'username' => '',
        'password' => '',
    ],
];
