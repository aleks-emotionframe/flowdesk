<?php

declare(strict_types=1);

use App\Middleware\CsrfMiddleware;

$pageTitle = 'Rechnungen';
$flashSuccess = $_SESSION['flash_success'] ?? null;
unset($_SESSION['flash_success']);

ob_start();
?>

<?php if ($flashSuccess): ?>
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 text-sm">
        <?= e($flashSuccess) ?>
    </div>
<?php endif; ?>

<div class="flex items-center justify-between mb-6">
    <div>
        <p class="text-sm text-gray-500"><?= count($invoices) ?> Rechnungen</p>
    </div>
    <a href="/invoices/create" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Neue Rechnung
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
    <form method="GET" action="/invoices" class="flex items-center gap-4">
        <div class="flex-1">
            <input type="text" name="search" value="<?= e($search) ?>" placeholder="Suche nach Nummer, Titel oder Kontakt..."
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
        </div>
        <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            <option value="">Alle Status</option>
            <option value="entwurf" <?= $status === 'entwurf' ? 'selected' : '' ?>>Entwurf</option>
            <option value="gesendet" <?= $status === 'gesendet' ? 'selected' : '' ?>>Gesendet</option>
            <option value="offen" <?= $status === 'offen' ? 'selected' : '' ?>>Offen</option>
            <option value="bezahlt" <?= $status === 'bezahlt' ? 'selected' : '' ?>>Bezahlt</option>
            <option value="überfällig" <?= $status === 'überfällig' ? 'selected' : '' ?>>Überfällig</option>
            <option value="storniert" <?= $status === 'storniert' ? 'selected' : '' ?>>Storniert</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">Filtern</button>
        <?php if ($search !== '' || $status !== ''): ?>
            <a href="/invoices" class="text-sm text-gray-500 hover:text-gray-700">Zurücksetzen</a>
        <?php endif; ?>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <?php if (empty($invoices)): ?>
        <div class="px-6 py-12 text-center">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
            </svg>
            <p class="text-gray-500 text-sm">Keine Rechnungen gefunden.</p>
            <a href="/invoices/create" class="inline-block mt-4 text-sm text-primary-600 hover:text-primary-700 font-medium">Erste Rechnung erstellen</a>
        </div>
    <?php else: ?>
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">Nummer</th>
                    <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">Kontakt</th>
                    <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">Titel</th>
                    <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">Status</th>
                    <th class="text-right text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">Total</th>
                    <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">Fällig</th>
                    <th class="text-right text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">Aktionen</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php foreach ($invoices as $inv): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-3.5">
                            <a href="/invoices/<?= $inv['id'] ?>" class="text-sm font-medium text-primary-600 hover:text-primary-700"><?= e($inv['invoice_number']) ?></a>
                        </td>
                        <td class="px-6 py-3.5 text-sm text-gray-900"><?= e(contactName($inv)) ?></td>
                        <td class="px-6 py-3.5 text-sm text-gray-600"><?= e($inv['title']) ?></td>
                        <td class="px-6 py-3.5"><?= statusBadge($inv['status']) ?></td>
                        <td class="px-6 py-3.5 text-sm font-medium text-gray-900 text-right"><?= formatCHF((float) $inv['total']) ?></td>
                        <td class="px-6 py-3.5 text-sm text-gray-500"><?= $inv['due_date'] ? formatDate($inv['due_date']) : '-' ?></td>
                        <td class="px-6 py-3.5 text-right">
                            <a href="/invoices/<?= $inv['id'] ?>/edit" class="text-gray-400 hover:text-primary-600" title="Bearbeiten">
                                <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
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
