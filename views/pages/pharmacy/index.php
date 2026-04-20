<?php
/**
 * Module Pharmacie - IlaraNet Bénin
 */
?>
<div class="space-y-6" x-data="pharmacy()">
    <div class="flex items-center justify-between">
        <div><h1 class="text-2xl font-bold text-gray-800">Pharmacie</h1><p class="text-gray-600 mt-1">Gestion des stocks et dispensation</p></div>
        <button @click="showDispenseModal = true" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center space-x-2"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg><span>Dispenser</span></button>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-red-50 border border-red-200 rounded-xl p-4"><p class="font-semibold text-red-800">Ruptures de stock</p><p class="text-2xl font-bold text-red-600" x-text="alerts.stockouts"></p></div>
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4"><p class="font-semibold text-yellow-800">Péremptions proches</p><p class="text-2xl font-bold text-yellow-600" x-text="alerts.expirations"></p></div>
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4"><p class="font-semibold text-blue-800">Ordonnances en attente</p><p class="text-2xl font-bold text-blue-600" x-text="alerts.pending"></p></div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <input type="text" x-model="search" placeholder="Rechercher un médicament..." class="w-full px-4 py-2 border border-gray-300 rounded-lg mb-4 focus:ring-2 focus:ring-blue-500">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50"><tr><th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Médicament</th><th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">DCI</th><th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th><th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prix</th><th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th></tr></thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <template x-for="med in filteredMedications" :key="med.id">
                    <tr><td class="px-4 py-3"><p class="font-medium" x-text="med.name"></p></td><td class="px-4 py-3 text-sm" x-text="med.dci"></td><td class="px-4 py-3"><span :class="med.stock <= 10 ? 'text-red-600 font-bold' : ''" x-text="med.stock + ' ' + med.unit"></span></td><td class="px-4 py-3 text-sm" x-text="formatMoney(med.price)"></td><td class="px-4 py-3"><span :class="{'bg-green-100 text-green-800': med.stock > 20, 'bg-yellow-100 text-yellow-800': med.stock > 10 && med.stock <= 20, 'bg-red-100 text-red-800': med.stock <= 10}" class="px-2 py-1 rounded-full text-xs" x-text="getStatus(med.stock)"></span></td></tr>
                </template>
            </tbody>
        </table>
    </div>
</div>
<script>
function pharmacy() {
    return {
        search: '',
        showDispenseModal: false,
        alerts: { stockouts: 3, expirations: 7, pending: 12 },
        medications: [
            { id: 1, name: 'PARACÉTAMOL 1000mg', dci: 'Paracétamol', stock: 150, unit: 'boîtes', price: 500 },
            { id: 2, name: 'AMOXICILLINE 500mg', dci: 'Amoxicilline', stock: 8, unit: 'boîtes', price: 1500 },
            { id: 3, name: 'ARTÉMÉTHÈRE-LUMEFANTRINE', dci: 'Artéméther+Luméfantrine', stock: 45, unit: 'boîtes', price: 2000 },
            { id: 4, name: 'METFORMINE 500mg', dci: 'Metformine', stock: 5, unit: 'boîtes', price: 1000 }
        ],
        get filteredMedications() { return this.search === '' ? this.medications : this.medications.filter(m => m.name.toLowerCase().includes(this.search.toLowerCase()) || m.dci.toLowerCase().includes(this.search.toLowerCase())); },
        formatMoney(amount) { return new Intl.NumberFormat('fr-FR').format(amount) + ' FCFA'; },
        getStatus(stock) { if (stock > 20) return 'En stock'; if (stock > 10) return 'Stock bas'; return 'Rupture'; }
    };
}
</script>
