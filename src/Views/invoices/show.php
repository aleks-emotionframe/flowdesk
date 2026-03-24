<?php

declare(strict_types=1);

use App\Middleware\CsrfMiddleware;

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
    <a href="/invoices" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Alle Rechnungen
    </a>
    <div class="flex items-center space-x-2">
        <?php if ($invoice['status'] === 'entwurf'): ?>
            <form method="POST" action="/invoices/<?= $invoice['id'] ?>/status" class="inline">
                <?= CsrfMiddleware::tokenField() ?>
                <input type="hidden" name="status" value="gesendet">
                <button type="submit" class="px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100">Als gesendet markieren</button>
            </form>
        <?php endif; ?>
        <?php if (in_array($invoice['status'], ['gesendet', 'offen', 'überfällig'], true)): ?>
            <form method="POST" action="/invoices/<?= $invoice['id'] ?>/status" class="inline">
                <?= CsrfMiddleware::tokenField() ?>
                <input type="hidden" name="status" value="bezahlt">
                <button type="submit" class="px-3 py-1.5 text-xs font-medium text-green-700 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100">Als bezahlt markieren</button>
            </form>
        <?php endif; ?>
        <?php if (in_array($invoice['status'], ['entwurf', 'gesendet', 'offen'], true)): ?>
            <form method="POST" action="/invoices/<?= $invoice['id'] ?>/status" class="inline">
                <?= CsrfMiddleware::tokenField() ?>
                <input type="hidden" name="status" value="storniert">
                <button type="submit" class="px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100">Stornieren</button>
            </form>
        <?php endif; ?>

        <a href="/invoices/<?= $invoice['id'] ?>/edit" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Bearbeiten
        </a>
        <form method="POST" action="/invoices/<?= $invoice['id'] ?>/delete" onsubmit="return confirm('Rechnung wirklich löschen?')">
            <?= CsrfMiddleware::tokenField() ?>
            <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-700 bg-white border border-red-300 rounded-lg hover:bg-red-50">Löschen</button>
        </form>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-900"><?= e($invoice['invoice_number']) ?></h3>
                    <p class="text-sm text-gray-600"><?= e($invoice['title']) ?></p>
                </div>
                <?= statusBadge($invoice['status']) ?>
            </div>

            <table class="w-full mb-6">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase py-2 w-8">Pos</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase py-2">Beschreibung</th>
                        <th class="text-right text-xs font-semibold text-gray-500 uppercase py-2 w-20">Menge</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase py-2 w-16">Einheit</th>
                        <th class="text-right text-xs font-semibold text-gray-500 uppercase py-2 w-28">Einzelpreis</th>
                        <th class="text-right text-xs font-semibold text-gray-500 uppercase py-2 w-28">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td class="py-2.5 text-sm text-gray-500"><?= $item['position'] ?></td>
                            <td class="py-2.5 text-sm text-gray-900"><?= e($item['description']) ?></td>
                            <td class="py-2.5 text-sm text-gray-700 text-right"><?= formatNumber((float)$item['quantity']) ?></td>
                            <td class="py-2.5 text-sm text-gray-500"><?= e($item['unit']) ?></td>
                            <td class="py-2.5 text-sm text-gray-700 text-right"><?= formatCHF((float)$item['unit_price']) ?></td>
                            <td class="py-2.5 text-sm font-medium text-gray-900 text-right"><?= formatCHF((float)$item['total']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="border-t border-gray-200 pt-3 space-y-1">
                <div class="flex justify-end text-sm">
                    <span class="text-gray-500 w-32">Zwischensumme</span>
                    <span class="w-32 text-right font-medium"><?= formatCHF((float)$invoice['subtotal']) ?></span>
                </div>
                <div class="flex justify-end text-sm">
                    <span class="text-gray-500 w-32">MwSt. (<?= formatNumber((float)$invoice['mwst_satz'], 1) ?>%)</span>
                    <span class="w-32 text-right font-medium"><?= formatCHF((float)$invoice['mwst_betrag']) ?></span>
                </div>
                <div class="flex justify-end text-lg border-t border-gray-200 pt-2">
                    <span class="text-gray-800 font-semibold w-32">Total</span>
                    <span class="w-32 text-right font-bold"><?= formatCHF((float)$invoice['total']) ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-semibold text-gray-800 mb-3">Kontakt</h3>
            <a href="/contacts/<?= $invoice['contact_id'] ?>" class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                <?= e(contactName($invoice)) ?>
            </a>
            <?php if ($invoice['contact_email']): ?>
                <p class="text-xs text-gray-500 mt-1"><?= e($invoice['contact_email']) ?></p>
            <?php endif; ?>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-semibold text-gray-800 mb-3">Details</h3>
            <dl class="text-sm space-y-2">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Erstellt</dt>
                    <dd class="text-gray-900"><?= formatDate($invoice['created_at']) ?></dd>
                </div>
                <?php if ($invoice['due_date']): ?>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Fällig am</dt>
                        <dd class="<?= (strtotime($invoice['due_date']) < time() && !in_array($invoice['status'], ['bezahlt', 'storniert'])) ? 'text-red-600 font-medium' : 'text-gray-900' ?>">
                            <?= formatDate($invoice['due_date']) ?>
                        </dd>
                    </div>
                <?php endif; ?>
                <?php if ($invoice['sent_at']): ?>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Gesendet</dt>
                        <dd class="text-gray-900"><?= formatDateTime($invoice['sent_at']) ?></dd>
                    </div>
                <?php endif; ?>
                <?php if ($invoice['paid_at']): ?>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Bezahlt</dt>
                        <dd class="text-green-600 font-medium"><?= formatDateTime($invoice['paid_at']) ?></dd>
                    </div>
                <?php endif; ?>
                <?php if ($invoice['quote_id']): ?>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Offerte</dt>
                        <dd><a href="/quotes/<?= $invoice['quote_id'] ?>" class="text-primary-600 hover:text-primary-700">Offerte anzeigen</a></dd>
                    </div>
                <?php endif; ?>
            </dl>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
