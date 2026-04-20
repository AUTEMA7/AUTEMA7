<?php
$pageTitle = 'Consultations Médicales';
ob_start();
?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Consultations</h1>
            <p class="text-gray-600">Historique et nouvelles consultations</p>
        </div>
        <a href="/consultations/new" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            <span>Nouvelle Consultation</span>
        </a>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Patient</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Médecin</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Diagnostic</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">AKOTEGNON Jean Pierre</td>
                    <td class="px-6 py-4">Dr. SANYA Michel</td>
                    <td class="px-6 py-4">15/12/2025 10:30</td>
                    <td class="px-6 py-4">Paludisme simple (CIM-11: 1B70)</td>
                    <td class="px-6 py-4"><span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full">Terminée</span></td>
                    <td class="px-6 py-4 text-right">
                        <a href="/consultations/1" class="text-blue-600 hover:text-blue-900 mr-2">📄</a>
                        <a href="/prescriptions/1" class="text-green-600 hover:text-green-900">💊</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../../layouts/app.php'; ?>
