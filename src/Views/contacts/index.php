<?php

declare(strict_types=1);

use App\Middleware\CsrfMiddleware;

$pageTitle = 'Kontakte';
$flashSuccess = $_SESSION['flash_success'] ?? null;
unset($_SESSION['flash_success']);

ob_start();
?>

<?php if ($flashSuccess): ?>
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 text-sm">
        <?= e($flashSuccess) ?>
    </div>
<?php endif; ?>

<!-- Header mit Aktion -->
<div class="flex items-center justify-between mb-6">
    <div>
        <p class="text-sm text-gray-500"><?= count($contacts) ?> von <?= $totalCount ?> Kontakten</p>
    </div>
    <a href="/contacts/create" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Neuer Kontakt
    </a>
</div>

<!-- Filter & Suche -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
    <form method="GET" action="/contacts" class="flex items-center gap-4">
        <div class="flex-1">
            <input type="text" name="search" value="<?= e($search) ?>" placeholder="Suche nach Name, Firma oder E-Mail..."
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
        </div>
        <select name="type" class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            <option value="">Alle Typen</option>
            <option value="kunde" <?= $type === 'kunde' ? 'selected' : '' ?>>Kunden</option>
            <option value="firma" <?= $type === 'firma' ? 'selected' : '' ?>>Firmen</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
            Filtern
        </button>
        <?php if ($search !== '' || $type !== ''): ?>
            <a href="/contacts" class="text-sm text-gray-500 hover:text-gray-700">Zurücksetzen</a>
        <?php endif; ?>
    </form>
</div>

<!-- Kontakt-Liste -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <?php if (empty($contacts)): ?>
        <div class="px-6 py-12 text-center">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <p class="text-gray-500 text-sm">Keine Kontakte gefunden.</p>
            <a href="/contacts/create" class="inline-block mt-4 text-sm text-primary-600 hover:text-primary-700 font-medium">Ersten Kontakt erstellen</a>
        </div>
    <?php else: ?>
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">Name</th>
                    <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">Typ</th>
                    <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">E-Mail</th>
                    <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">Telefon</th>
                    <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">Ort</th>
                    <th class="text-right text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">Aktionen</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php foreach ($contacts as $c): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-3.5">
                            <a href="/contacts/<?= $c['id'] ?>" class="text-sm font-medium text-gray-900 hover:text-primary-600">
                                <?= e(contactName($c)) ?>
                            </a>
                            <?php if ($c['type'] === 'firma' && $c['first_name']): ?>
                                <p class="text-xs text-gray-500"><?= e($c['first_name'] . ' ' . $c['last_name']) ?></p>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-3.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium <?= $c['type'] === 'firma' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' ?>">
                                <?= $c['type'] === 'firma' ? 'Firma' : 'Kunde' ?>
                            </span>
                        </td>
                        <td class="px-6 py-3.5 text-sm text-gray-600"><?= e($c['email'] ?? '-') ?></td>
                        <td class="px-6 py-3.5 text-sm text-gray-600"><?= e($c['phone'] ?? '-') ?></td>
                        <td class="px-6 py-3.5 text-sm text-gray-600">
                            <?= $c['plz'] || $c['city'] ? e(trim(($c['plz'] ?? '') . ' ' . ($c['city'] ?? ''))) : '-' ?>
                        </td>
                        <td class="px-6 py-3.5 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="/contacts/<?= $c['id'] ?>/edit" class="text-gray-400 hover:text-primary-600" title="Bearbeiten">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form method="POST" action="/contacts/<?= $c['id'] ?>/delete" class="inline" onsubmit="return confirm('Kontakt wirklich löschen?')">
                                    <?= CsrfMiddleware::tokenField() ?>
                                    <button type="submit" class="text-gray-400 hover:text-red-600" title="Löschen">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
