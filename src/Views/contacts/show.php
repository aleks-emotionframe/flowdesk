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

<!-- Zurück + Aktionen -->
<div class="flex items-center justify-between mb-6">
    <a href="/contacts" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Alle Kontakte
    </a>
    <div class="flex items-center space-x-3">
        <a href="/contacts/<?= $contact['id'] ?>/edit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Bearbeiten
        </a>
        <form method="POST" action="/contacts/<?= $contact['id'] ?>/delete" onsubmit="return confirm('Kontakt wirklich löschen?')">
            <?= CsrfMiddleware::tokenField() ?>
            <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-red-700 bg-white border border-red-300 rounded-lg hover:bg-red-50 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Löschen
            </button>
        </form>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Hauptinfo -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center space-x-4">
                    <div class="w-14 h-14 bg-primary-100 rounded-full flex items-center justify-center">
                        <span class="text-xl font-bold text-primary-600"><?= e(mb_substr(contactName($contact), 0, 1)) ?></span>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900"><?= e(contactName($contact)) ?></h3>
                        <?php if ($contact['type'] === 'firma' && ($contact['first_name'] || $contact['last_name'])): ?>
                            <p class="text-sm text-gray-500"><?= e(trim($contact['first_name'] . ' ' . $contact['last_name'])) ?></p>
                        <?php endif; ?>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium mt-1 <?= $contact['type'] === 'firma' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' ?>">
                            <?= $contact['type'] === 'firma' ? 'Firma' : 'Kunde' ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6 mt-6">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">E-Mail</p>
                    <?php if ($contact['email']): ?>
                        <a href="mailto:<?= e($contact['email']) ?>" class="text-sm text-primary-600 hover:text-primary-700"><?= e($contact['email']) ?></a>
                    <?php else: ?>
                        <p class="text-sm text-gray-400">-</p>
                    <?php endif; ?>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Telefon</p>
                    <?php if ($contact['phone']): ?>
                        <a href="tel:<?= e($contact['phone']) ?>" class="text-sm text-gray-900"><?= e($contact['phone']) ?></a>
                    <?php else: ?>
                        <p class="text-sm text-gray-400">-</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Adresse -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-semibold text-gray-800 mb-3">Adresse</h3>
            <?php if ($contact['address'] || $contact['plz'] || $contact['city']): ?>
                <div class="text-sm text-gray-700 space-y-0.5">
                    <?php if ($contact['address']): ?>
                        <p><?= e($contact['address']) ?></p>
                    <?php endif; ?>
                    <?php if ($contact['plz'] || $contact['city']): ?>
                        <p><?= e(trim(($contact['plz'] ?? '') . ' ' . ($contact['city'] ?? ''))) ?></p>
                    <?php endif; ?>
                    <p><?= e($contact['country'] ?? 'CH') ?></p>
                </div>
            <?php else: ?>
                <p class="text-sm text-gray-400">Keine Adresse hinterlegt</p>
            <?php endif; ?>
        </div>

        <!-- Notizen -->
        <?php if ($contact['notes']): ?>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-semibold text-gray-800 mb-3">Notizen</h3>
                <p class="text-sm text-gray-700 whitespace-pre-line"><?= e($contact['notes']) ?></p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Rechte Spalte: Verknüpfte Dokumente -->
    <div class="space-y-6">
        <!-- Offerten -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-800">Offerten (<?= count($quotes) ?>)</h3>
                <a href="/quotes/create?contact_id=<?= $contact['id'] ?>" class="text-xs text-primary-600 hover:text-primary-700 font-medium">+ Neue Offerte</a>
            </div>
            <?php if (empty($quotes)): ?>
                <p class="text-sm text-gray-400">Noch keine Offerten</p>
            <?php else: ?>
                <div class="space-y-2">
                    <?php foreach ($quotes as $q): ?>
                        <a href="/quotes/<?= $q['id'] ?>" class="flex items-center justify-between py-1.5 hover:bg-gray-50 rounded px-2 -mx-2 transition-colors">
                            <div>
                                <span class="text-sm font-medium text-gray-900"><?= e($q['quote_number']) ?></span>
                                <span class="text-xs text-gray-500 ml-2"><?= e($q['title']) ?></span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <?= statusBadge($q['status']) ?>
                                <span class="text-xs font-medium text-gray-700"><?= formatCHF((float)$q['total']) ?></span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Rechnungen -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-800">Rechnungen (<?= count($invoices) ?>)</h3>
                <a href="/invoices/create?contact_id=<?= $contact['id'] ?>" class="text-xs text-primary-600 hover:text-primary-700 font-medium">+ Neue Rechnung</a>
            </div>
            <?php if (empty($invoices)): ?>
                <p class="text-sm text-gray-400">Noch keine Rechnungen</p>
            <?php else: ?>
                <div class="space-y-2">
                    <?php foreach ($invoices as $inv): ?>
                        <a href="/invoices/<?= $inv['id'] ?>" class="flex items-center justify-between py-1.5 hover:bg-gray-50 rounded px-2 -mx-2 transition-colors">
                            <div>
                                <span class="text-sm font-medium text-gray-900"><?= e($inv['invoice_number']) ?></span>
                                <span class="text-xs text-gray-500 ml-2"><?= e($inv['title']) ?></span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <?= statusBadge($inv['status']) ?>
                                <span class="text-xs font-medium text-gray-700"><?= formatCHF((float)$inv['total']) ?></span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Meta -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-semibold text-gray-800 mb-3">Details</h3>
            <dl class="text-sm space-y-2">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Erstellt</dt>
                    <dd class="text-gray-900"><?= formatDate($contact['created_at']) ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Aktualisiert</dt>
                    <dd class="text-gray-900"><?= formatDate($contact['updated_at']) ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">ID</dt>
                    <dd class="text-gray-900">#<?= $contact['id'] ?></dd>
                </div>
            </dl>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
