<?php

declare(strict_types=1);

use App\Middleware\CsrfMiddleware;

$isEdit = isset($contact['id']);
$pageTitle = $isEdit ? 'Kontakt bearbeiten' : 'Neuer Kontakt';
$action = $isEdit ? '/contacts/' . $contact['id'] . '/update' : '/contacts';

ob_start();
?>

<div class="max-w-3xl">
    <!-- Zurück-Link -->
    <a href="<?= $isEdit ? '/contacts/' . $contact['id'] : '/contacts' ?>" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-6">
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

    <form method="POST" action="<?= $action ?>" class="space-y-6">
        <?= CsrfMiddleware::tokenField() ?>

        <!-- Typ -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-semibold text-gray-800 mb-4">Kontakttyp</h3>
            <div class="flex gap-4">
                <label class="flex items-center cursor-pointer">
                    <input type="radio" name="type" value="kunde" <?= ($contact['type'] ?? 'kunde') === 'kunde' ? 'checked' : '' ?>
                           class="w-4 h-4 text-primary-600 border-gray-300 focus:ring-primary-500" onchange="toggleType()">
                    <span class="ml-2 text-sm text-gray-700">Kunde (Privatperson)</span>
                </label>
                <label class="flex items-center cursor-pointer">
                    <input type="radio" name="type" value="firma" <?= ($contact['type'] ?? 'kunde') === 'firma' ? 'checked' : '' ?>
                           class="w-4 h-4 text-primary-600 border-gray-300 focus:ring-primary-500" onchange="toggleType()">
                    <span class="ml-2 text-sm text-gray-700">Firma</span>
                </label>
            </div>
        </div>

        <!-- Kontaktdaten -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-semibold text-gray-800 mb-4">Kontaktdaten</h3>
            <div class="space-y-4">
                <!-- Firmenname -->
                <div id="company-field" class="<?= ($contact['type'] ?? 'kunde') === 'kunde' ? 'hidden' : '' ?>">
                    <label for="company_name" class="block text-sm font-medium text-gray-700 mb-1">Firmenname *</label>
                    <input type="text" id="company_name" name="company_name" value="<?= e($contact['company_name'] ?? '') ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">Vorname</label>
                        <input type="text" id="first_name" name="first_name" value="<?= e($contact['first_name'] ?? '') ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Nachname <span id="lastname-required">*</span></label>
                        <input type="text" id="last_name" name="last_name" value="<?= e($contact['last_name'] ?? '') ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-Mail</label>
                        <input type="email" id="email" name="email" value="<?= e($contact['email'] ?? '') ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Telefon</label>
                        <input type="text" id="phone" name="phone" value="<?= e($contact['phone'] ?? '') ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                </div>
            </div>
        </div>

        <!-- Adresse -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-semibold text-gray-800 mb-4">Adresse</h3>
            <div class="space-y-4">
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Strasse / Nr.</label>
                    <input type="text" id="address" name="address" value="<?= e($contact['address'] ?? '') ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label for="plz" class="block text-sm font-medium text-gray-700 mb-1">PLZ</label>
                        <input type="text" id="plz" name="plz" value="<?= e($contact['plz'] ?? '') ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div class="col-span-2">
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-1">Ort</label>
                        <input type="text" id="city" name="city" value="<?= e($contact['city'] ?? '') ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                </div>
                <div>
                    <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Land</label>
                    <input type="text" id="country" name="country" value="<?= e($contact['country'] ?? 'CH') ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
            </div>
        </div>

        <!-- Notizen -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-semibold text-gray-800 mb-4">Notizen</h3>
            <textarea id="notes" name="notes" rows="4"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                      placeholder="Interne Notizen..."><?= e($contact['notes'] ?? '') ?></textarea>
        </div>

        <!-- Aktionen -->
        <div class="flex items-center justify-end space-x-3">
            <a href="<?= $isEdit ? '/contacts/' . $contact['id'] : '/contacts' ?>" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                Abbrechen
            </a>
            <button type="submit" class="px-6 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors">
                <?= $isEdit ? 'Speichern' : 'Kontakt erstellen' ?>
            </button>
        </div>
    </form>
</div>

<script>
function toggleType() {
    const isFirma = document.querySelector('input[name="type"][value="firma"]').checked;
    document.getElementById('company-field').classList.toggle('hidden', !isFirma);
    document.getElementById('lastname-required').textContent = isFirma ? '' : '*';
}
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
