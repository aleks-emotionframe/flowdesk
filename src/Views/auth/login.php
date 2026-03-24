<?php

declare(strict_types=1);

use App\Middleware\CsrfMiddleware;
?>
<!DOCTYPE html>
<html lang="de-CH">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FlowDesk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">
                <span class="text-indigo-600">Flow</span>Desk
            </h1>
            <p class="text-gray-500 mt-2">Agentur Management Tool</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-6">Anmelden</h2>

            <?php if (!empty($error)): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 text-sm">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/login">
                <?= CsrfMiddleware::tokenField() ?>

                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-Mail</label>
                    <input type="email" id="email" name="email" required autofocus
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                           placeholder="admin@flowdesk.ch">
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Passwort</label>
                    <input type="password" id="password" name="password" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                           placeholder="Passwort eingeben">
                </div>

                <button type="submit"
                        class="w-full bg-indigo-600 text-white py-2.5 px-4 rounded-lg font-medium hover:bg-indigo-700 focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors text-sm">
                    Anmelden
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-gray-400 mt-6">FlowDesk &copy; <?= date('Y') ?></p>
    </div>
</body>
</html>
