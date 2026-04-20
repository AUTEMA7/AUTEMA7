<?php
/**
 * Dashboard Directeur - Vue Globale
 * Tableau de bord complet avec tous les indicateurs
 */

$pageTitle = 'Tableau de Bord - Direction';
$hideSidebar = false;
$hideNavbar = false;

// Données simulées (à remplacer par des requêtes DB)
$stats = [
    'patients_today' => 47,
    'revenue_today' => 1250000,
    'beds_occupied' => 18,
    'beds_total' => 30,
    'emergencies_active' => 5,
    'appointments_pending' => 23,
    'staff_present' => 42,
    'staff_total' => 55,
];

$services = [
    ['name' => 'Consultation', 'activity' => 85, 'revenue' => 450000, 'color' => 'blue'],
    ['name' => 'Urgences', 'activity' => 92, 'revenue' => 320000, 'color' => 'red'],
    ['name' => 'Hospitalisation', 'activity' => 60, 'revenue' => 680000, 'color' => 'green'],
    ['name' => 'Laboratoire', 'activity' => 78, 'revenue' => 290000, 'color' => 'purple'],
    ['name' => 'Imagerie', 'activity' => 65, 'revenue' => 410000, 'color' => 'yellow'],
    ['name' => 'Pharmacie', 'activity' => 88, 'revenue' => 520000, 'color' => 'pink'],
];

$recentActivities = [
    ['action' => 'Nouvelle consultation', 'user' => 'Dr. AKOTEGNON', 'time' => 'Il y a 2 min', 'icon' => '🩺'],
    ['action' => 'Admission urgences', 'user' => 'Inf. GBAGUIDI', 'time' => 'Il y a 5 min', 'icon' => '🚨'],
    ['action' => 'Résultat labo disponible', 'user' => 'Lab. HOUNKPE', 'time' => 'Il y a 12 min', 'icon' => '🔬'],
    ['action' => 'Paiement reçu', 'user' => 'Cpt. DJIBRIL', 'time' => 'Il y a 18 min', 'icon' => '💰'],
    ['action' => 'Sortie patient', 'user' => 'Dr. SANYA', 'time' => 'Il y a 25 min', 'icon' => '🏠'],
];

$alerts = [
    ['type' => 'warning', 'message' => 'Stock bas: Paracétamol 1000mg (Pharmacie)', 'time' => 'Il y a 10 min'],
    ['type' => 'danger', 'message' => 'Urgence critique: Code Bleu déclenché', 'time' => 'Il y a 3 min'],
    ['type' => 'info', 'message' => 'Maintenance prévue demain 02h-04h', 'time' => 'Il y a 1 heure'],
];

ob_start();
?>

<div class="space-y-6">
    <!-- Header with Date & Quick Actions -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Tableau de Bord</h1>
            <p class="text-gray-600">Vue globale en temps réel • <?= date('d/m/Y H:i') ?></p>
        </div>
        <div class="flex items-center space-x-3">
            <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                <span>Exporter</span>
            </button>
            <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                <span>Rapport Rapide</span>
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Patients Today -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Patients du Jour</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1"><?= number_format($stats['patients_today']) ?></p>
                    <p class="text-xs text-green-600 mt-2 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                        +12% vs hier
                    </p>
                </div>
                <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center">
                    <span class="text-2xl">👥</span>
                </div>
            </div>
        </div>

        <!-- Revenue -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Recettes du Jour</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1"><?= number_format($stats['revenue_today'], 0, ',', ' ') ?> FCFA</p>
                    <p class="text-xs text-green-600 mt-2 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                        +8% vs objectif
                    </p>
                </div>
                <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center">
                    <span class="text-2xl">💰</span>
                </div>
            </div>
        </div>

        <!-- Beds Occupancy -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Lits Occupés</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1"><?= $stats['beds_occupied'] ?>/<?= $stats['beds_total'] ?></p>
                    <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                        <div class="bg-purple-500 h-2 rounded-full" style="width: <?= ($stats['beds_occupied']/$stats['beds_total'])*100 ?>%"></div>
                    </div>
                </div>
                <div class="w-14 h-14 bg-purple-100 rounded-full flex items-center justify-center">
                    <span class="text-2xl">🛏️</span>
                </div>
            </div>
        </div>

        <!-- Emergencies -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Urgences Actives</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1"><?= $stats['emergencies_active'] ?></p>
                    <p class="text-xs text-red-600 mt-2 font-medium">⚠️ 2 cas critiques</p>
                </div>
                <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center">
                    <span class="text-2xl">🚨</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Services Performance (2 columns) -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-800">Performance par Service</h2>
                <select class="text-sm border border-gray-300 rounded-lg px-3 py-1">
                    <option>Aujourd'hui</option>
                    <option>Cette semaine</option>
                    <option>Ce mois</option>
                </select>
            </div>
            
            <div class="space-y-4">
                <?php foreach ($services as $service): ?>
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700"><?= $service['name'] ?></span>
                            <span class="text-sm text-gray-600"><?= number_format($service['revenue'], 0, ',', ' ') ?> FCFA</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div 
                                class="bg-<?= $service['color'] ?>-500 h-3 rounded-full transition-all duration-500" 
                                style="width: <?= $service['activity'] ?>%"
                            ></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1"><?= $service['activity'] ?>% d'activité</p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Alerts & Notifications -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-800">Alertes Critiques</h2>
                <span class="px-2 py-1 bg-red-100 text-red-600 text-xs rounded-full font-medium"><?= count($alerts) ?></span>
            </div>
            
            <div class="space-y-3">
                <?php foreach ($alerts as $alert): ?>
                    <div class="p-3 rounded-lg <?= $alert['type'] === 'danger' ? 'bg-red-50 border-l-4 border-red-500' : ($alert['type'] === 'warning' ? 'bg-yellow-50 border-l-4 border-yellow-500' : 'bg-blue-50 border-l-4 border-blue-500') ?>">
                        <p class="text-sm text-gray-800 font-medium"><?= $alert['message'] ?></p>
                        <p class="text-xs text-gray-500 mt-1"><?= $alert['time'] ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <button class="w-full mt-4 py-2 text-sm text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                Voir toutes les alertes
            </button>
        </div>
    </div>

    <!-- Activity Feed & Appointments -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Activity -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-800">Activité Récente</h2>
                <a href="/audit" class="text-sm text-blue-600 hover:underline">Voir tout</a>
            </div>
            
            <div class="space-y-4">
                <?php foreach ($recentActivities as $activity): ?>
                    <div class="flex items-start space-x-3 pb-4 border-b border-gray-100 last:border-0">
                        <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <span><?= $activity['icon'] ?></span>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-800 font-medium"><?= $activity['action'] ?></p>
                            <p class="text-xs text-gray-500"><?= $activity['user'] ?> • <?= $activity['time'] ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Appointments Overview -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-800">Rendez-vous du Jour</h2>
                <a href="/appointments" class="text-sm text-blue-600 hover:underline">Gérer</a>
            </div>
            
            <div class="grid grid-cols-3 gap-4 mb-6">
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <p class="text-2xl font-bold text-green-600">18</p>
                    <p class="text-xs text-gray-600">Effectués</p>
                </div>
                <div class="text-center p-4 bg-yellow-50 rounded-lg">
                    <p class="text-2xl font-bold text-yellow-600">23</p>
                    <p class="text-xs text-gray-600">En attente</p>
                </div>
                <div class="text-center p-4 bg-red-50 rounded-lg">
                    <p class="text-2xl font-bold text-red-600">5</p>
                    <p class="text-xs text-gray-600">Annulés</p>
                </div>
            </div>
            
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-sm font-semibold">AK</div>
                        <div>
                            <p class="text-sm font-medium">AKOTEGNON Jean</p>
                            <p class="text-xs text-gray-500">Dr. SANYA • 14:30</p>
                        </div>
                    </div>
                    <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs rounded-full">En attente</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center text-sm font-semibold">DG</div>
                        <div>
                            <p class="text-sm font-medium">DAVOGBE Marie</p>
                            <p class="text-xs text-gray-500">Dr. HOUNKPE • 15:00</p>
                        </div>
                    </div>
                    <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full">Confirmé</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Objectifs & Budget -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-semibold text-gray-800">Objectifs Mensuels vs Réalisé</h2>
            <select class="text-sm border border-gray-300 rounded-lg px-3 py-1">
                <option>Décembre 2025</option>
                <option>Novembre 2025</option>
                <option>Octobre 2025</option>
            </select>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="text-center">
                <div class="relative w-32 h-32 mx-auto">
                    <canvas id="chartRevenue"></canvas>
                </div>
                <p class="text-sm font-medium text-gray-700 mt-2">Recettes</p>
                <p class="text-xs text-gray-500">85% atteint</p>
            </div>
            <div class="text-center">
                <div class="relative w-32 h-32 mx-auto">
                    <canvas id="chartPatients"></canvas>
                </div>
                <p class="text-sm font-medium text-gray-700 mt-2">Patients</p>
                <p class="text-xs text-gray-500">92% atteint</p>
            </div>
            <div class="text-center">
                <div class="relative w-32 h-32 mx-auto">
                    <canvas id="chartBeds"></canvas>
                </div>
                <p class="text-sm font-medium text-gray-700 mt-2">Occupation</p>
                <p class="text-xs text-gray-500">60% atteint</p>
            </div>
            <div class="text-center">
                <div class="relative w-32 h-32 mx-auto">
                    <canvas id="chartStaff"></canvas>
                </div>
                <p class="text-sm font-medium text-gray-700 mt-2">Personnel</p>
                <p class="text-xs text-gray-500">76% présent</p>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize Charts
document.addEventListener('DOMContentLoaded', function() {
    const chartConfig = {
        type: 'doughnut',
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            cutout: '70%'
        }
    };

    // Revenue Chart
    new Chart(document.getElementById('chartRevenue'), {
        ...chartConfig,
        data: {
            labels: ['Atteint', 'Restant'],
            datasets: [{
                data: [85, 15],
                backgroundColor: ['#10b981', '#e5e7eb'],
                borderWidth: 0
            }]
        }
    });

    // Patients Chart
    new Chart(document.getElementById('chartPatients'), {
        ...chartConfig,
        data: {
            labels: ['Atteint', 'Restant'],
            datasets: [{
                data: [92, 8],
                backgroundColor: ['#3b82f6', '#e5e7eb'],
                borderWidth: 0
            }]
        }
    });

    // Beds Chart
    new Chart(document.getElementById('chartBeds'), {
        ...chartConfig,
        data: {
            labels: ['Atteint', 'Restant'],
            datasets: [{
                data: [60, 40],
                backgroundColor: ['#8b5cf6', '#e5e7eb'],
                borderWidth: 0
            }]
        }
    });

    // Staff Chart
    new Chart(document.getElementById('chartStaff'), {
        ...chartConfig,
        data: {
            labels: ['Présent', 'Absent'],
            datasets: [{
                data: [76, 24],
                backgroundColor: ['#f59e0b', '#e5e7eb'],
                borderWidth: 0
            }]
        }
    });
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/app.php';
?>
