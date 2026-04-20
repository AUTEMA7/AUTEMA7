<?php
/**
 * Liste des Patients - IlaraNet Bénin
 */

$pageTitle = 'Gestion des Patients';
ob_start();
?>

<div class="space-y-6" x-data="{ 
    showFilters: false, 
    searchQuery: '',
    selectedPatient: null
}">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Patients</h1>
            <p class="text-gray-600">Rechercher et gérer les dossiers patients</p>
        </div>
        <div class="flex items-center space-x-3">
            <button @click="showFilters = !showFilters" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                <span>Filtres</span>
            </button>
            <a href="/patients/create" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                <span>Nouveau Patient</span>
            </a>
        </div>
    </div>

    <!-- Filters Panel -->
    <div x-show="showFilters" x-transition class="bg-white rounded-xl shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type d'identification</label>
                <select class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    <option value="">Tous</option>
                    <option value="npi">NPI ANIP</option>
                    <option value="temporaire">NPI Temporaire</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sexe</label>
                <select class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    <option value="">Tous</option>
                    <option value="M">Masculin</option>
                    <option value="F">Féminin</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Assurance</label>
                <select class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    <option value="">Toutes</option>
                    <option value="cnss">CNSS</option>
                    <option value="crss">CRSS</option>
                    <option value="privee">Assurance Privée</option>
                    <option value="aucune">Aucune</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date inscription</label>
                <input type="date" class="w-full border border-gray-300 rounded-lg px-3 py-2">
            </div>
        </div>
        <div class="flex justify-end mt-4 space-x-3">
            <button class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">Réinitialiser</button>
            <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">Appliquer</button>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="bg-white rounded-xl shadow-sm p-4">
        <div class="flex items-center space-x-3">
            <div class="flex-1 relative">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input 
                    type="text" 
                    x-model="searchQuery"
                    placeholder="Rechercher par NPI, nom, prénom, téléphone..." 
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
            </div>
            <div class="flex items-center space-x-2 text-sm text-gray-600">
                <span><strong>1,247</strong> patients</span>
            </div>
        </div>
    </div>

    <!-- Patients Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NPI</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Âge/Sexe</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Téléphone</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assurance</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dernière visite</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <!-- Patient 1 -->
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-sm font-semibold">AK</div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">AKOTEGNON Jean Pierre</div>
                                <div class="text-xs text-gray-500">ID: PAT-2025-0001</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full font-mono">BJ2025001234X</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-900">45 ans</span>
                        <span class="ml-2 px-2 py-0.5 bg-blue-100 text-blue-700 text-xs rounded">M</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">+229 97 12 34 56</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full">CNSS</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">15/12/2025</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="/patients/1" class="text-blue-600 hover:text-blue-900" title="Voir dossier">📄</a>
                            <a href="/consultations/new?patient=1" class="text-green-600 hover:text-green-900" title="Nouvelle consultation">🩺</a>
                            <a href="#" class="text-gray-600 hover:text-gray-900" title="QR Code">📱</a>
                        </div>
                    </td>
                </tr>

                <!-- Patient 2 -->
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center text-sm font-semibold">DG</div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">DAVOGBE Marie Claire</div>
                                <div class="text-xs text-gray-500">ID: PAT-2025-0002</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full font-mono">BJ2025005678Y</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-900">32 ans</span>
                        <span class="ml-2 px-2 py-0.5 bg-pink-100 text-pink-700 text-xs rounded">F</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">+229 95 87 65 43</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 bg-purple-100 text-purple-700 text-xs rounded-full">Privée</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">14/12/2025</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="/patients/2" class="text-blue-600 hover:text-blue-900">📄</a>
                            <a href="/consultations/new?patient=2" class="text-green-600 hover:text-green-900">🩺</a>
                            <a href="#" class="text-gray-600 hover:text-gray-900">📱</a>
                        </div>
                    </td>
                </tr>

                <!-- Patient Inconnu (Urgence) -->
                <tr class="hover:bg-gray-50 transition-colors bg-red-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center text-sm font-semibold">?</div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">INCONNU-2025-000123</div>
                                <div class="text-xs text-gray-500">Homme ~30-40 ans</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs rounded-full font-mono">TEMP-2025-000123</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-900">~35 ans</span>
                        <span class="ml-2 px-2 py-0.5 bg-blue-100 text-blue-700 text-xs rounded">M</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">-</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded-full">Aucune</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">Aujourd'hui</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="/patients/temp/123" class="text-blue-600 hover:text-blue-900">📄</a>
                            <a href="/emergencies/123" class="text-red-600 hover:text-red-900">🚨</a>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
            <div class="text-sm text-gray-600">
                Affichage de <strong>1-10</strong> sur <strong>1,247</strong> patients
            </div>
            <div class="flex items-center space-x-2">
                <button class="px-3 py-1 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50" disabled>Précédent</button>
                <button class="px-3 py-1 bg-blue-600 text-white rounded-lg">1</button>
                <button class="px-3 py-1 border border-gray-300 rounded-lg hover:bg-gray-50">2</button>
                <button class="px-3 py-1 border border-gray-300 rounded-lg hover:bg-gray-50">3</button>
                <span class="text-gray-400">...</span>
                <button class="px-3 py-1 border border-gray-300 rounded-lg hover:bg-gray-50">125</button>
                <button class="px-3 py-1 border border-gray-300 rounded-lg hover:bg-gray-50">Suivant</button>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/app.php';
?>
