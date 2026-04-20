<?php
$pageTitle = 'Service des Urgences';
ob_start();
?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">🚨 Urgences</h1>
            <p class="text-gray-600">Triage ESI et prise en charge urgente</p>
        </div>
        <button class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center space-x-2">
            <span>⚡</span><span>Nouveau Patient Urgence</span>
        </button>
    </div>
    
    <!-- Triage Board -->
    <div class="grid grid-cols-5 gap-4">
        <div class="bg-red-50 border-2 border-red-500 rounded-xl p-4">
            <h3 class="font-bold text-red-700 mb-2">ESI 1 - Immédiat</h3>
            <div class="space-y-2">
                <div class="bg-white p-3 rounded-lg shadow-sm">
                    <p class="font-semibold">INCONNU-001</p>
                    <p class="text-xs text-gray-600">Arrêt cardiaque</p>
                    <p class="text-xs text-red-600 mt-1">Box 1 • Dr. AKOTEGNON</p>
                </div>
            </div>
        </div>
        <div class="bg-orange-50 border-2 border-orange-500 rounded-xl p-4">
            <h3 class="font-bold text-orange-700 mb-2">ESI 2 - Très urgent</h3>
            <div class="space-y-2">
                <div class="bg-white p-3 rounded-lg shadow-sm">
                    <p class="font-semibold">DAVOGBE Marie</p>
                    <p class="text-xs text-gray-600">Douleur thoracique</p>
                    <p class="text-xs text-orange-600 mt-1">Box 3 • En attente ECG</p>
                </div>
            </div>
        </div>
        <div class="bg-yellow-50 border-2 border-yellow-500 rounded-xl p-4">
            <h3 class="font-bold text-yellow-700 mb-2">ESI 3 - Urgent</h3>
            <div class="space-y-2">
                <div class="bg-white p-3 rounded-lg shadow-sm">
                    <p class="font-semibold">HOUNKPE Pierre</p>
                    <p class="text-xs text-gray-600">Fracture fermée</p>
                </div>
            </div>
        </div>
        <div class="bg-green-50 border-2 border-green-500 rounded-xl p-4">
            <h3 class="font-bold text-green-700 mb-2">ESI 4 - Peu urgent</h3>
            <div class="space-y-2"></div>
        </div>
        <div class="bg-blue-50 border-2 border-blue-500 rounded-xl p-4">
            <h3 class="font-bold text-blue-700 mb-2">ESI 5 - Non urgent</h3>
            <div class="space-y-2"></div>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../../layouts/app.php'; ?>
