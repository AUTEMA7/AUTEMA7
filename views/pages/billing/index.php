<?php
$pageTitle = 'Facturation & Caisse';
ob_start();
?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">💰 Facturation</h1>
            <p class="text-gray-600">Gestion des factures et encaissements</p>
        </div>
        <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Nouvelle Facture</button>
    </div>
    
    <div class="grid grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm">
            <p class="text-sm text-gray-600">Recettes du jour</p>
            <p class="text-2xl font-bold text-green-600">1,250,000 FCFA</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm">
            <p class="text-sm text-gray-600">En attente</p>
            <p class="text-2xl font-bold text-yellow-600">450,000 FCFA</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm">
            <p class="text-sm text-gray-600">Créances >30j</p>
            <p class="text-2xl font-bold text-red-600">1,890,000 FCFA</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm">
            <p class="text-sm text-gray-600">Factures émises</p>
            <p class="text-2xl font-bold text-blue-600">47</p>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../../layouts/app.php'; ?>
