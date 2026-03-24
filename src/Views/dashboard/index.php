<?php

declare(strict_types=1);

$pageTitle = 'Dashboard';

ob_start();
?>

<!-- Statistik-Karten -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Offene Rechnungen -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Offene Rechnungen</p>
                <p class="text-2xl font-bold text-gray-900 mt-1"><?= formatCHF($offeneRechnungen) ?></p>
            </div>
            <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Umsatz aktueller Monat -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Umsatz <?= date('F Y') ?></p>
                <p class="text-2xl font-bold text-gray-900 mt-1"><?= formatCHF($umsatzMonat) ?></p>
            </div>
            <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Offene Offerten -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Offene Offerten</p>
                <p class="text-2xl font-bold text-gray-900 mt-1"><?= $offeneOfferten ?></p>
            </div>
            <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Überfällige -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Überfällige</p>
                <p class="text-2xl font-bold <?= $ueberfaellige > 0 ? 'text-red-600' : 'text-gray-900' ?> mt-1"><?= $ueberfaellige ?></p>
            </div>
            <div class="w-12 h-12 <?= $ueberfaellige > 0 ? 'bg-red-50' : 'bg-gray-50' ?> rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 <?= $ueberfaellige > 0 ? 'text-red-600' : 'text-gray-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Linke Spalte: Letzte Rechnungen + Aktivitäten -->
    <div class="lg:col-span-2 space-y-8">
        <!-- Letzte Rechnungen -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-base font-semibold text-gray-800">Letzte Rechnungen</h3>
            </div>
            <div class="divide-y divide-gray-50">
                <?php if (empty($letzteRechnungen)): ?>
                    <div class="px-6 py-8 text-center text-gray-400 text-sm">
                        Noch keine Rechnungen vorhanden
                    </div>
                <?php else: ?>
                    <?php foreach ($letzteRechnungen as $rechnung): ?>
                        <div class="px-6 py-3.5 flex items-center justify-between hover:bg-gray-50 transition-colors">
                            <div class="flex items-center space-x-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-900"><?= e($rechnung['invoice_number']) ?></p>
                                    <p class="text-xs text-gray-500">
                                        <?= e(contactName($rechnung)) ?> &middot; <?= e($rechnung['title']) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <?= statusBadge($rechnung['status']) ?>
                                <span class="text-sm font-medium text-gray-900"><?= formatCHF((float) $rechnung['total']) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Letzte Aktivitäten -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-base font-semibold text-gray-800">Letzte Aktivitäten</h3>
            </div>
            <div class="divide-y divide-gray-50">
                <?php if (empty($letzteAktivitaeten)): ?>
                    <div class="px-6 py-8 text-center text-gray-400 text-sm">
                        Noch keine Aktivitäten vorhanden
                    </div>
                <?php else: ?>
                    <?php foreach ($letzteAktivitaeten as $aktivitaet): ?>
                        <div class="px-6 py-3 flex items-start space-x-3">
                            <div class="w-2 h-2 bg-primary-400 rounded-full mt-1.5 flex-shrink-0"></div>
                            <div>
                                <p class="text-sm text-gray-700"><?= e($aktivitaet['description']) ?></p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    <?= e($aktivitaet['user_name'] ?? 'System') ?> &middot; <?= formatDateTime($aktivitaet['created_at']) ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Rechte Spalte: Schnellaktionen -->
    <div class="space-y-8">
        <!-- Schnellaktionen -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-base font-semibold text-gray-800 mb-4">Schnellaktionen</h3>
            <div class="space-y-3">
                <a href="/quotes/create" class="flex items-center px-4 py-3 bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 transition-colors text-sm font-medium">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Neue Offerte
                </a>
                <a href="/invoices/create" class="flex items-center px-4 py-3 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors text-sm font-medium">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Neue Rechnung
                </a>
                <a href="/contacts/create" class="flex items-center px-4 py-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors text-sm font-medium">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    Neuer Kontakt
                </a>
            </div>
        </div>

        <!-- Umsatz-Chart Platzhalter -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-base font-semibold text-gray-800 mb-4">Umsatz <?= date('Y') ?></h3>
            <div class="space-y-2">
                <?php
                $monate = ['Jan', 'Feb', 'Mär', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dez'];
                $chartData = array_fill(1, 12, 0);
                foreach ($umsatzChart as $row) {
                    $chartData[(int) $row['monat']] = (float) $row['total'];
                }
                $maxUmsatz = max(1, max($chartData));
                $currentMonth = (int) date('n');
                for ($m = 1; $m <= $currentMonth; $m++):
                    $width = ($chartData[$m] / $maxUmsatz) * 100;
                ?>
                    <div class="flex items-center text-xs">
                        <span class="w-8 text-gray-500"><?= $monate[$m - 1] ?></span>
                        <div class="flex-1 mx-2">
                            <div class="bg-indigo-100 rounded-full h-4 overflow-hidden">
                                <div class="bg-indigo-500 h-4 rounded-full transition-all" style="width: <?= max(0, $width) ?>%"></div>
                            </div>
                        </div>
                        <span class="w-20 text-right text-gray-600 font-medium"><?= formatCHF($chartData[$m]) ?></span>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
