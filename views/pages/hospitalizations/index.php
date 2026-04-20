<?php
/**
 * Module Hospitalisation - IlaraNet Bénin
 */
?>
<div class="space-y-6" x-data="hospitalizations()">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Hospitalisations</h1>
            <p class="text-gray-600 mt-1">Gestion des lits et patients hospitalisés</p>
        </div>
        <button @click="showAdmissionModal = true" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Nouvelle admission</span>
        </button>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Carte des lits</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <template x-for="bed in beds" :key="bed.id">
                <div :class="{'bg-green-50 border-green-500': bed.status === 'libre', 'bg-red-50 border-red-500': bed.status === 'occupé', 'bg-yellow-50 border-yellow-500': bed.status === 'nettoyage'}" class="border-2 rounded-lg p-4 cursor-pointer hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-semibold text-gray-800" x-text="bed.number"></span>
                        <span :class="{'bg-green-500': bed.status === 'libre', 'bg-red-500': bed.status === 'occupé', 'bg-yellow-500': bed.status === 'nettoyage'}" class="w-3 h-3 rounded-full"></span>
                    </div>
                    <p class="text-xs text-gray-600" x-text="bed.service"></p>
                    <template x-if="bed.patient"><p class="text-xs text-gray-800 mt-2 font-medium truncate" x-text="bed.patient"></p></template>
                </div>
            </template>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200"><h2 class="text-lg font-semibold text-gray-800">Patients hospitalisés</h2></div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50"><tr><th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Patient</th><th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th><th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lit</th><th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date admission</th><th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th></tr></thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <template x-for="patient in hospitalizedPatients" :key="patient.id">
                    <tr><td class="px-4 py-3"><p class="font-medium text-gray-900" x-text="patient.name"></p><p class="text-xs text-gray-500" x-text="patient.npi"></p></td><td class="px-4 py-3 text-sm" x-text="patient.service"></td><td class="px-4 py-3"><span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded" x-text="patient.bed"></span></td><td class="px-4 py-3 text-sm" x-text="patient.admissionDate"></td><td class="px-4 py-3"><a :href="'/hospitalizations/'+patient.id" class="text-blue-600 hover:text-blue-700 text-sm">Voir</a></td></tr>
                </template>
            </tbody>
        </table>
    </div>
</div>
<script>
function hospitalizations() {
    return {
        showAdmissionModal: false,
        beds: [
            { id: 1, number: 'LIT-001', service: 'Médecine', status: 'occupé', patient: 'KOUASSI J.' },
            { id: 2, number: 'LIT-002', service: 'Médecine', status: 'libre', patient: null },
            { id: 3, number: 'LIT-003', service: 'Chirurgie', status: 'occupé', patient: 'ADJOVI M.' },
            { id: 4, number: 'LIT-004', service: 'Chirurgie', status: 'libre', patient: null },
            { id: 5, number: 'LIT-005', service: 'Pédiatrie', status: 'nettoyage', patient: null },
            { id: 6, number: 'LIT-006', service: 'Maternité', status: 'occupé', patient: 'DANFOSSO L.' }
        ],
        hospitalizedPatients: [
            { id: 1, name: 'KOUASSI Jean', npi: 'NPI-2024-001234', service: 'Médecine', bed: 'LIT-001', admissionDate: '13/01/2026' },
            { id: 2, name: 'ADJOVI Michel', npi: 'NPI-2024-005678', service: 'Chirurgie', bed: 'LIT-003', admissionDate: '15/01/2026' },
            { id: 3, name: 'DANFOSSO Laure', npi: 'NPI-2024-003456', service: 'Maternité', bed: 'LIT-006', admissionDate: '15/01/2026' }
        ]
    };
}
</script>
