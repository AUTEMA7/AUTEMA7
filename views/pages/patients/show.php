<?php
/**
 * Page de détails patient - IlaraNet Bénin
 * Affiche le dossier médical complet d'un patient
 */
?>
<div class="space-y-6" x-data="patientDetails()">
    <!-- En-tête Patient -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center overflow-hidden">
                        <template x-if="patient.photo">
                            <img :src="patient.photo" alt="Photo patient" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!patient.photo">
                            <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </template>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white" x-text="patient.name"></h1>
                        <p class="text-blue-100">
                            NPI: <span x-text="patient.npi"></span> • 
                            <span x-text="patient.age"></span> ans • 
                            <span x-text="patient.gender === 'M' ? 'Homme' : 'Femme'"></span>
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <button @click="printQRCode()" class="px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-lg transition flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                        </svg>
                        <span>QR Code</span>
                    </button>
                    <a href="/patients" class="px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-lg transition">
                        ← Retour
                    </a>
                </div>
            </div>
        </div>

        <!-- Informations -->
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Téléphone</p>
                    <p class="font-medium" x-text="patient.phone"></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Groupe sanguin</p>
                    <p class="font-medium" x-text="patient.bloodType || 'Non renseigné'"></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Médecin traitant</p>
                    <p class="font-medium" x-text="patient.doctor || 'Non désigné'"></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Dernière visite</p>
                    <p class="font-medium" x-text="patient.lastVisit"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Onglets -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <template x-for="tab in tabs" :key="tab.id">
                    <button @click="activeTab = tab.id"
                        :class="activeTab === tab.id ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="py-4 px-6 border-b-2 font-medium text-sm transition"
                        x-text="tab.label">
                    </button>
                </template>
            </nav>
        </div>

        <div class="p-6">
            <!-- Consultations -->
            <div x-show="activeTab === 'consultations'" class="space-y-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Historique des consultations</h3>
                    <a href="/consultations/new?patient_id=<?= $_GET['id'] ?? '' ?>" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Nouvelle consultation</span>
                    </a>
                </div>

                <div class="space-y-3">
                    <template x-for="consultation in consultations" :key="consultation.id">
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                            <div class="flex items-start justify-between">
                                <div>
                                    <p class="font-medium text-gray-800" x-text="consultation.date"></p>
                                    <p class="text-sm text-gray-600 mt-1">Dr. <span x-text="consultation.doctor"></span></p>
                                    <p class="text-sm text-blue-600 mt-2" x-text="consultation.diagnosis"></p>
                                </div>
                                <a :href="`/consultations/${consultation.id}`" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                    Voir →
                                </a>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Ordonnances -->
            <div x-show="activeTab === 'prescriptions'" class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Ordonnances</h3>
                <div class="space-y-3">
                    <template x-for="prescription in prescriptions" :key="prescription.id">
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                            <div class="flex items-start justify-between">
                                <div>
                                    <p class="font-medium text-gray-800" x-text="prescription.date"></p>
                                    <p class="text-sm text-gray-600 mt-1">Dr. <span x-text="prescription.doctor"></span></p>
                                    <div class="mt-2 flex items-center space-x-2">
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full" x-text="prescription.status"></span>
                                        <span class="text-sm text-gray-500" x-text="prescription.pharmacy"></span>
                                    </div>
                                </div>
                                <button @click="viewPrescription(prescription.id)" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                    Voir →
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Résultats Labo -->
            <div x-show="activeTab === 'lab'" class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Résultats de laboratoire</h3>
                <div class="space-y-3">
                    <template x-for="result in labResults" :key="result.id">
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                            <div class="flex items-start justify-between">
                                <div>
                                    <p class="font-medium text-gray-800" x-text="result.date"></p>
                                    <p class="text-sm text-gray-600 mt-1" x-text="result.type"></p>
                                    <p class="text-sm mt-2">
                                        <span :class="result.status === 'normal' ? 'text-green-600' : 'text-red-600'" 
                                            class="font-medium" x-text="result.status === 'normal' ? '✓ Normal' : '⚠ Anormal'"></span>
                                    </p>
                                </div>
                                <button @click="viewLabResult(result.id)" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                    Télécharger PDF
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Imagerie -->
            <div x-show="activeTab === 'imaging'" class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Examens d'imagerie</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <template x-for="image in imaging" :key="image.id">
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                            <div class="aspect-video bg-gray-100 rounded-lg mb-3 flex items-center justify-center">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <p class="font-medium text-gray-800" x-text="image.type"></p>
                            <p class="text-sm text-gray-600" x-text="image.date"></p>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Hospitalisations -->
            <div x-show="activeTab === 'hospitalizations'" class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Hospitalisations</h3>
                <div class="space-y-3">
                    <template x-for="hosp in hospitalizations" :key="hosp.id">
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                            <div class="flex items-start justify-between">
                                <div>
                                    <p class="font-medium text-gray-800" x-text="hosp.service"></p>
                                    <p class="text-sm text-gray-600 mt-1">
                                        Du <span x-text="hosp.admission"></span> au <span x-text="hosp.discharge || 'En cours'"></span>
                                    </p>
                                    <p class="text-sm mt-2">
                                        <span :class="hosp.status === 'actif' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'" 
                                            class="px-2 py-1 rounded-full text-xs font-medium" x-text="hosp.status"></span>
                                    </p>
                                </div>
                                <a :href="`/hospitalizations/${hosp.id}`" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                    Détails →
                                </a>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Factures -->
            <div x-show="activeTab === 'billing'" class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Factures et paiements</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="invoice in invoices" :key="invoice.id">
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900" x-text="invoice.date"></td>
                                    <td class="px-4 py-3 text-sm text-gray-900" x-text="invoice.description"></td>
                                    <td class="px-4 py-3 text-sm font-medium" x-text="formatMoney(invoice.amount)"></td>
                                    <td class="px-4 py-3">
                                        <span :class="{
                                            'bg-green-100 text-green-800': invoice.status === 'payé',
                                            'bg-yellow-100 text-yellow-800': invoice.status === 'partiel',
                                            'bg-red-100 text-red-800': invoice.status === 'impayé'
                                        }" class="px-2 py-1 rounded-full text-xs font-medium" x-text="invoice.status"></span>
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <a :href="`/billing/invoices/${invoice.id}`" class="text-blue-600 hover:text-blue-700">Voir</a>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal QR Code -->
    <div x-show="showQRModal" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
        style="display: none;">
        <div class="bg-white rounded-xl max-w-md w-full p-6" @click.away="showQRModal = false">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Code QR Patient</h3>
                <button @click="showQRModal = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="text-center">
                <div class="w-48 h-48 bg-gray-100 mx-auto rounded-lg flex items-center justify-center mb-4">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=NPI-<?= $_GET['id'] ?? 'DEMO' ?>" alt="QR Code" class="w-full h-full">
                </div>
                <p class="text-sm text-gray-600 mb-4">Ce QR code contient les informations d'urgence uniquement</p>
                <button @click="printQRCode()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Imprimer
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function patientDetails() {
    return {
        activeTab: 'consultations',
        showQRModal: false,
        patient: {
            id: <?= $_GET['id'] ?? 1 ?>,
            name: 'KOUASSI Jean Michel',
            npi: 'NPI-2024-001234',
            age: 45,
            gender: 'M',
            phone: '+229 97 00 00 00',
            bloodType: 'O+',
            doctor: 'Dr. AKOTEGNON',
            lastVisit: '15/01/2026',
            photo: null
        },
        tabs: [
            { id: 'consultations', label: 'Consultations' },
            { id: 'prescriptions', label: 'Ordonnances' },
            { id: 'lab', label: 'Laboratoire' },
            { id: 'imaging', label: 'Imagerie' },
            { id: 'hospitalizations', label: 'Hospitalisations' },
            { id: 'billing', label: 'Factures' }
        ],
        consultations: [
            { id: 1, date: '15/01/2026', doctor: 'AKOTEGNON', diagnosis: 'Hypertension artérielle' },
            { id: 2, date: '10/12/2025', doctor: 'SOSSOU', diagnosis: 'Paludisme simple' },
            { id: 3, date: '05/11/2025', doctor: 'AKOTEGNON', diagnosis: 'Suivi HTA' }
        ],
        prescriptions: [
            { id: 1, date: '15/01/2026', doctor: 'AKOTEGNON', status: 'dispensé', pharmacy: 'Pharmacie du Lac' },
            { id: 2, date: '10/12/2025', doctor: 'SOSSOU', status: 'dispensé', pharmacy: 'Pharmacie Centrale' }
        ],
        labResults: [
            { id: 1, date: '14/01/2026', type: ' NFS - Groupage sanguin', status: 'normal' },
            { id: 2, date: '10/12/2025', type: 'Goutte épaisse', status: 'normal' }
        ],
        imaging: [
            { id: 1, date: '12/01/2026', type: 'Radiographie thoracique' },
            { id: 2, date: '08/11/2025', type: 'Échographie abdominale' }
        ],
        hospitalizations: [
            { id: 1, service: 'Médecine Interne', admission: '01/11/2025', discharge: '05/11/2025', status: 'terminé' }
        ],
        invoices: [
            { id: 1, date: '15/01/2026', description: 'Consultation + Analyses', amount: 25000, status: 'payé' },
            { id: 2, date: '10/12/2025', description: 'Consultation + TTT', amount: 15000, status: 'payé' }
        ],

        formatMoney(amount) {
            return new Intl.NumberFormat('fr-FR').format(amount) + ' FCFA';
        },

        printQRCode() {
            window.print();
        },

        viewPrescription(id) {
            window.location.href = `/prescriptions/${id}`;
        },

        viewLabResult(id) {
            alert('Téléchargement du résultat...');
        }
    }
}
</script>
