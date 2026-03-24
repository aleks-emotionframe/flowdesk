<?php

declare(strict_types=1);

return [
    'name' => $_ENV['APP_NAME'] ?? 'FlowDesk',
    'env' => $_ENV['APP_ENV'] ?? 'production',
    'url' => $_ENV['APP_URL'] ?? 'http://localhost',
    'debug' => ($_ENV['APP_ENV'] ?? 'production') === 'development',

    'db' => [
        'host' => $_ENV['DB_HOST'] ?? '127.0.0.1',
        'port' => (int) ($_ENV['DB_PORT'] ?? 3306),
        'database' => $_ENV['DB_DATABASE'] ?? 'flowdesk',
        'username' => $_ENV['DB_USERNAME'] ?? 'root',
        'password' => $_ENV['DB_PASSWORD'] ?? '',
        'charset' => 'utf8mb4',
    ],

    'smtp' => [
        'host' => $_ENV['SMTP_HOST'] ?? '',
        'port' => (int) ($_ENV['SMTP_PORT'] ?? 587),
        'user' => $_ENV['SMTP_USER'] ?? '',
        'pass' => $_ENV['SMTP_PASS'] ?? '',
        'from' => $_ENV['SMTP_FROM'] ?? 'info@deineagentur.ch',
    ],

    'company' => [
        'name' => $_ENV['COMPANY_NAME'] ?? 'Deine Agentur GmbH',
        'address' => $_ENV['COMPANY_ADDRESS'] ?? '',
        'phone' => $_ENV['COMPANY_PHONE'] ?? '',
        'email' => $_ENV['COMPANY_EMAIL'] ?? '',
        'uid' => $_ENV['COMPANY_UID'] ?? '',
        'iban' => $_ENV['COMPANY_IBAN'] ?? '',
        'bank' => $_ENV['COMPANY_BANK'] ?? '',
        'mwst_nr' => $_ENV['COMPANY_MWST_NR'] ?? '',
        'mwst_satz' => (float) ($_ENV['COMPANY_MWST_SATZ'] ?? 8.1),
    ],

    'mwst_satz' => (float) ($_ENV['COMPANY_MWST_SATZ'] ?? 8.1),
];
