<?php
/**
 * Module Laboratoire - IlaraNet Bénin
 * Gestion des analyses médicales
 */
?>
<div class="space-y-6" x-data="laboratory()">
    <!-- En-tête -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Laboratoire d'Analyses</h1>
            <p class="text-gray-600 mt-1">Gestion des demandes et résultats d'analyses</p>
        </div>
        <div class="flex space-x-3">
            <button class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <span>Synchroniser</span>
            </button>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">En attente</p>
                    <p class="text-2xl font-bold text-yellow-600 mt-1" x-text="stats.pending"></p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">En cours</p>
                    <p class="text-2xl font-bold text-blue-600 mt-1" x-text="stats.processing"></p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Terminés aujourd'hui</p>
                    <p class="text-2xl font-bold text-green-600 mt-1" x-text="stats.completed"></p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Critiques</p>
                    <p class="text-2xl font-bold text-red-600 mt-1" x-text="stats.critical"></p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des demandes -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-800">Demandes d'analyses</h2>
            <div class="flex space-x-2">
                <select x-model="filterStatus" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="all">Tous les statuts</option>
                    <option value="pending">En attente</option>
                    <option value="processing">En cours</option>
                    <option value="completed">Terminé</option>
                </select>
                <input type="text" x-model="search" placeholder="Rechercher..." 
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Patient</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type d'analyse</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date demande</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Médecin</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="request in filteredRequests" :key="request.id">
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div>
                                    <p class="font-medium text-gray-900" x-text="request.patient"></p>
                                    <p class="text-xs text-gray-500" x-text="request.npi"></p>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm text-gray-900" x-text="request.type"></span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900" x-text="request.date"></td>
                            <td class="px-4 py-3 text-sm text-gray-900" x-text="request.doctor"></td>
                            <td class="px-4 py-3">
                                <span :class="{
                                    'bg-yellow-100 text-yellow-800': request.status === 'pending',
                                    'bg-blue-100 text-blue-800': request.status === 'processing',
                                    'bg-green-100 text-green-800': request.status === 'completed',
                                    'bg-red-100 text-red-800': request.status === 'critical'
                                }" class="px-2 py-1 rounded-full text-xs font-medium capitalize" x-text="request.status"></span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center space-x-2">
                                    <button @click="processRequest(request.id)" 
                                        x-show="request.status === 'pending' || request.status === 'processing'"
                                        class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                        Saisir
                                    </button>
                                    <button @click="viewResult(request.id)" 
                                        x-show="request.status === 'completed' || request.status === 'critical'"
                                        class="text-green-600 hover:text-green-700 text-sm font-medium">
                                        Voir
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Saisie Résultats -->
    <div x-show="showResultModal" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
        style="display: none;">
        <div class="bg-white rounded-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto" @click.away="showResultModal = false">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-white">
                <h3 class="text-lg font-semibold">Saisie des résultats</h3>
                <button @click="showResultModal = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <p class="font-medium text-blue-800" x-text="currentRequest?.patient"></p>
                    <p class="text-sm text-blue-600" x-text="currentRequest?.type"></p>
                </div>
                
                <div class="space-y-3">
                    <h4 class="font-medium text-gray-800">Paramètres à saisir</h4>
                    <template x-for="(param, index) in resultParams" :key="index">
                        <div class="grid grid-cols-3 gap-4 items-center">
                            <label class="text-sm text-gray-700" x-text="param.name"></label>
                            <input type="text" x-model="param.value" 
                                :placeholder="param.unit ? 'Valeur...' : ''"
                                class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <div class="text-sm text-gray-500" x-text="param.unit"></div>
                        </div>
                    </template>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Commentaires / Interprétation</label>
                    <textarea rows="3" x-model="comments" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <div class="flex items-center space-x-2">
                    <input type="checkbox" id="critical" x-model="isCritical" class="w-4 h-4 text-red-600 rounded focus:ring-red-500">
                    <label for="critical" class="text-sm text-gray-700">Résultat critique - Alerter le médecin immédiatement</label>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 sticky bottom-0 bg-white">
                <button @click="showResultModal = false" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Annuler
                </button>
                <button @click="saveResults()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Enregistrer et envoyer au dossier patient
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function laboratory() {
    return {
        filterStatus: 'all',
        search: '',
        showResultModal: false,
        currentRequest: null,
        comments: '',
        isCritical: false,
        stats: {
            pending: 8,
            processing: 5,
            completed: 23,
            critical: 2
        },
        requests: [
            { id: 1, patient: 'KOUASSI Jean', npi: 'NPI-2024-001234', type: 'NFS - Hémogramme complet', date: '16/01/2026 08:30', doctor: 'Dr. AKOTEGNON', status: 'pending' },
            { id: 2, patient: 'ADJOVI Marie', npi: 'NPI-2024-005678', type: 'Glycémie à jeun', date: '16/01/2026 09:15', doctor: 'Dr. SOSSOU', status: 'processing' },
            { id: 3, patient: 'DANFOSSO Pierre', npi: 'NPI-2024-009012', type: 'Bilan hépatique', date: '16/01/2026 07:45', doctor: 'Dr. HOUNKPE', status: 'completed' },
            { id: 4, patient: 'GBAGUIDI Laure', npi: 'NPI-2024-003456', type: 'Créatinine + Urée', date: '16/01/2026 10:00', doctor: 'Dr. AKOTEGNON', status: 'pending' },
            { id: 5, patient: 'AMOUSSOU Michel', npi: 'NPI-2024-007890', type: 'Goutte épaisse', date: '16/01/2026 08:00', doctor: 'Dr. SOSSOU', status: 'critical' },
            { id: 6, patient: 'HOUNTOKEDE Sarah', npi: 'NPI-2024-002345', type: 'ECBU', date: '16/01/2026 09:30', doctor: 'Dr. GBAGBO', status: 'processing' }
        ],
        resultParams: [
            { name: 'Hémoglobine', value: '', unit: 'g/dL' },
            { name: 'Globules rouges', value: '', unit: 'T/L' },
            { name: 'Globules blancs', value: '', unit: 'G/L' },
            { name: 'Plaquettes', value: '', unit: 'G/L' }
        ],

        get filteredRequests() {
            return this.requests.filter(req => {
                const matchStatus = this.filterStatus === 'all' || req.status === this.filterStatus;
                const matchSearch = this.search === '' || 
                    req.patient.toLowerCase().includes(this.search.toLowerCase()) ||
                    req.type.toLowerCase().includes(this.search.toLowerCase());
                return matchStatus && matchSearch;
            });
        },

        processRequest(id) {
            this.currentRequest = this.requests.find(r => r.id === id);
            this.showResultModal = true;
            this.comments = '';
            this.isCritical = false;
        },

        viewResult(id) {
            alert('Affichage du résultat...');
        },

        saveResults() {
            if (confirm('Confirmer l\'enregistrement des résultats ?')) {
                alert('Résultats enregistrés et envoyés au dossier patient !');
                this.showResultModal = false;
            }
        }
    }
}
</script>
