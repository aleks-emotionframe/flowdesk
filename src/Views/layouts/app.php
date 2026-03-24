<?php

declare(strict_types=1);

use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;

$user = AuthMiddleware::user();
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
?>
<!DOCTYPE html>
<html lang="de-CH">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Dashboard') ?> - FlowDesk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4F46E5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-900 text-white flex flex-col fixed h-full z-30">
            <!-- Logo -->
            <div class="px-6 py-5 border-b border-gray-800">
                <h1 class="text-xl font-bold tracking-tight">
                    <span class="text-primary-400">Flow</span>Desk
                </h1>
                <p class="text-xs text-gray-400 mt-1">Agentur Management</p>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                <a href="/" class="flex items-center px-3 py-2.5 text-sm rounded-lg transition-colors <?= $currentPath === '/' ? 'bg-primary-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>

                <a href="/contacts" class="flex items-center px-3 py-2.5 text-sm rounded-lg transition-colors <?= str_starts_with($currentPath, '/contacts') ? 'bg-primary-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Kontakte
                </a>

                <a href="/quotes" class="flex items-center px-3 py-2.5 text-sm rounded-lg transition-colors <?= str_starts_with($currentPath, '/quotes') ? 'bg-primary-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Offerten
                </a>

                <a href="/invoices" class="flex items-center px-3 py-2.5 text-sm rounded-lg transition-colors <?= str_starts_with($currentPath, '/invoices') ? 'bg-primary-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                    </svg>
                    Rechnungen
                </a>

                <div class="pt-4 mt-4 border-t border-gray-800">
                    <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">System</p>

                    <a href="/settings" class="flex items-center px-3 py-2.5 text-sm rounded-lg transition-colors <?= str_starts_with($currentPath, '/settings') ? 'bg-primary-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' ?>">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Einstellungen
                    </a>
                </div>
            </nav>

            <!-- User Info -->
            <div class="px-4 py-3 border-t border-gray-800">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-primary-600 rounded-full flex items-center justify-center text-sm font-medium">
                        <?= e(mb_substr($user['name'] ?? 'U', 0, 1)) ?>
                    </div>
                    <div class="ml-3 flex-1 min-w-0">
                        <p class="text-sm font-medium truncate"><?= e($user['name'] ?? '') ?></p>
                        <p class="text-xs text-gray-400 truncate"><?= e($user['email'] ?? '') ?></p>
                    </div>
                    <a href="/logout" class="text-gray-400 hover:text-white ml-2" title="Abmelden">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </a>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 ml-64">
            <!-- Top Header -->
            <header class="bg-white border-b border-gray-200 px-8 py-4 sticky top-0 z-20">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-800"><?= e($pageTitle ?? 'Dashboard') ?></h2>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-500"><?= date('d.m.Y') ?></span>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="p-8">
                <?php if (isset($content)): ?>
                    <?= $content ?>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
