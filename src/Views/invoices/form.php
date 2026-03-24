<?php

declare(strict_types=1);

use App\Middleware\CsrfMiddleware;

$isEdit = isset($invoice['id']);
$pageTitle = $isEdit ? 'Rechnung bearbeiten' : 'Neue Rechnung';
$action = $isEdit ? '/invoices/' . $invoice['id'] . '/update' : '/invoices';

ob_start();
?>

<div class="max-w-4xl">
    <a href="<?= $isEdit ? '/invoices/' . $invoice['id'] : '/invoices' ?>" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-6">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Zurück
    </a>

    <?php if (!empty($errors)): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 text-sm">
            <ul class="list-disc list-inside">
                <?php foreach ($errors as $error): ?>
                    <li><?= e($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= $action ?>" id="invoice-form" class="space-y-6">
        <?= CsrfMiddleware::tokenField() ?>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-semibold text-gray-800 mb-4">Grunddaten</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="contact_id" class="block text-sm font-medium text-gray-700 mb-1">Kontakt *</label>
                    <select id="contact_id" name="contact_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Bitte wählen...</option>
                        <?php foreach ($contacts as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= (int)($invoice['contact_id'] ?? 0) === (int)$c['id'] ? 'selected' : '' ?>>
                                <?= e(contactName($c)) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Titel *</label>
                    <input type="text" id="title" name="title" value="<?= e($invoice['title'] ?? '') ?>" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                           placeholder="z.B. Website Redesign">
                </div>
                <div>
                    <label for="due_date" class="block text-sm font-medium text-gray-700 mb-1">Fälligkeitsdatum</label>
                    <input type="date" id="due_date" name="due_date" value="<?= e($invoice['due_date'] ?? '') ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label for="mwst_satz" class="block text-sm font-medium text-gray-700 mb-1">MwSt-Satz (%)</label>
                    <input type="number" id="mwst_satz" name="mwst_satz" value="<?= e((string)($invoice['mwst_satz'] ?? '8.1')) ?>" step="0.1"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                           onchange="recalc()">
                </div>
            </div>
        </div>

        <!-- Positionen -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-800">Positionen</h3>
                <button type="button" onclick="addItem()" class="text-sm text-primary-600 hover:text-primary-700 font-medium">+ Position hinzufügen</button>
            </div>

            <div id="items-container">
                <div class="grid grid-cols-12 gap-2 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <div class="col-span-5 px-2">Beschreibung</div>
                    <div class="col-span-2 px-2">Menge</div>
                    <div class="col-span-1 px-2">Einheit</div>
                    <div class="col-span-2 px-2">Einzelpreis</div>
                    <div class="col-span-1 px-2 text-right">Total</div>
                    <div class="col-span-1"></div>
                </div>

                <?php foreach ($items as $i => $item): ?>
                    <div class="item-row grid grid-cols-12 gap-2 mb-2">
                        <div class="col-span-5">
                            <input type="text" name="item_description[]" value="<?= e($item['description'] ?? '') ?>" placeholder="Beschreibung"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div class="col-span-2">
                            <input type="number" name="item_quantity[]" value="<?= e((string)($item['quantity'] ?? '1')) ?>" step="0.25" min="0"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                   onchange="recalc()" oninput="recalc()">
                        </div>
                        <div class="col-span-1">
                            <select name="item_unit[]" class="w-full px-2 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="Std" <?= ($item['unit'] ?? '') === 'Std' ? 'selected' : '' ?>>Std</option>
                                <option value="Stk" <?= ($item['unit'] ?? '') === 'Stk' ? 'selected' : '' ?>>Stk</option>
                                <option value="Pauschal" <?= ($item['unit'] ?? '') === 'Pauschal' ? 'selected' : '' ?>>Pau.</option>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <input type="number" name="item_unit_price[]" value="<?= e((string)($item['unit_price'] ?? '')) ?>" step="0.05" min="0" placeholder="0.00"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                   onchange="recalc()" oninput="recalc()">
                        </div>
                        <div class="col-span-1 flex items-center justify-end">
                            <span class="item-total text-sm font-medium text-gray-700">CHF 0.00</span>
                        </div>
                        <div class="col-span-1 flex items-center justify-center">
                            <button type="button" onclick="removeItem(this)" class="text-gray-400 hover:text-red-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="border-t border-gray-200 mt-4 pt-4 space-y-2">
                <div class="flex justify-end text-sm">
                    <span class="text-gray-500 w-32">Zwischensumme</span>
                    <span id="subtotal" class="w-32 text-right font-medium text-gray-700">CHF 0.00</span>
                </div>
                <div class="flex justify-end text-sm">
                    <span class="text-gray-500 w-32">MwSt. (<span id="mwst-label">8.1</span>%)</span>
                    <span id="mwst-amount" class="w-32 text-right font-medium text-gray-700">CHF 0.00</span>
                </div>
                <div class="flex justify-end text-base border-t border-gray-200 pt-2">
                    <span class="text-gray-800 font-semibold w-32">Total</span>
                    <span id="total" class="w-32 text-right font-bold text-gray-900">CHF 0.00</span>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end space-x-3">
            <a href="<?= $isEdit ? '/invoices/' . $invoice['id'] : '/invoices' ?>" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">Abbrechen</a>
            <button type="submit" class="px-6 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors">
                <?= $isEdit ? 'Speichern' : 'Rechnung erstellen' ?>
            </button>
        </div>
    </form>
</div>

<script>
function addItem() {
    const container = document.getElementById('items-container');
    const rows = container.querySelectorAll('.item-row');
    const lastRow = rows[rows.length - 1];
    const newRow = lastRow.cloneNode(true);
    newRow.querySelectorAll('input[type="text"], input[type="number"]').forEach(el => { if(el.name !== 'item_quantity[]') el.value = ''; });
    newRow.querySelector('input[name="item_quantity[]"]').value = '1';
    newRow.querySelector('.item-total').textContent = 'CHF 0.00';
    container.appendChild(newRow);
}

function removeItem(btn) {
    const container = document.getElementById('items-container');
    if (container.querySelectorAll('.item-row').length <= 1) return;
    btn.closest('.item-row').remove();
    recalc();
}

function recalc() {
    const rows = document.querySelectorAll('.item-row');
    let subtotal = 0;
    rows.forEach(row => {
        const qty = parseFloat(row.querySelector('input[name="item_quantity[]"]').value) || 0;
        const price = parseFloat(row.querySelector('input[name="item_unit_price[]"]').value) || 0;
        const total = Math.round(qty * price * 100) / 100;
        row.querySelector('.item-total').textContent = 'CHF ' + total.toFixed(2);
        subtotal += total;
    });
    const mwstSatz = parseFloat(document.getElementById('mwst_satz').value) || 0;
    const mwst = Math.round(subtotal * mwstSatz) / 100;
    document.getElementById('subtotal').textContent = 'CHF ' + subtotal.toFixed(2);
    document.getElementById('mwst-label').textContent = mwstSatz.toFixed(1);
    document.getElementById('mwst-amount').textContent = 'CHF ' + mwst.toFixed(2);
    document.getElementById('total').textContent = 'CHF ' + (subtotal + mwst).toFixed(2);
}

document.addEventListener('DOMContentLoaded', recalc);
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
