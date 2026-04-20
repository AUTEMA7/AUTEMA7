<?php
/**
 * Sidebar Component - IlaraNet Bénin
 * Navigation latérale dynamique selon le rôle
 */

$user = $_SESSION['user'] ?? null;
$role = $user['role'] ?? '';
$serviceId = $_SESSION['service_id'] ?? null;
$facilityId = $user['facility_id'] ?? null;

// Menu items par rôle
$menuItems = [
    'directeur' => [
        ['label' => 'Tableau de Bord', 'icon' => '📊', 'route' => '/dashboard', 'permission' => 'view_dashboard'],
        ['label' => 'Services', 'icon' => '🏥', 'route' => '/services', 'permission' => 'manage_services'],
        ['label' => 'Patients', 'icon' => '👥', 'route' => '/patients', 'permission' => 'view_patients'],
        ['label' => 'Consultations', 'icon' => '🩺', 'route' => '/consultations', 'permission' => 'view_consultations'],
        ['label' => 'Urgences', 'icon' => '🚨', 'route' => '/emergencies', 'permission' => 'view_emergencies'],
        ['label' => 'Hospitalisation', 'icon' => '🛏️', 'route' => '/hospitalizations', 'permission' => 'view_hospitalizations'],
        ['label' => 'Laboratoire', 'icon' => '🔬', 'route' => '/laboratory', 'permission' => 'view_laboratory'],
        ['label' => 'Imagerie', 'icon' => '📷', 'route' => '/imaging', 'permission' => 'view_imaging'],
        ['label' => 'Pharmacie', 'icon' => '💊', 'route' => '/pharmacy', 'permission' => 'view_pharmacy'],
        ['label' => 'Facturation', 'icon' => '💰', 'route' => '/billing', 'permission' => 'view_billing'],
        ['label' => 'Ressources Humaines', 'icon' => '👨‍⚕️', 'route' => '/hr', 'permission' => 'manage_hr'],
        ['label' => 'Rapports', 'icon' => '📈', 'route' => '/reports', 'permission' => 'view_reports'],
        ['label' => 'Audit Trail', 'icon' => '🔍', 'route' => '/audit', 'permission' => 'view_audit'],
        ['label' => 'Paramètres', 'icon' => '⚙️', 'route' => '/settings', 'permission' => 'manage_settings'],
    ],
    'chef_service' => [
        ['label' => 'Tableau de Bord Service', 'icon' => '📊', 'route' => '/dashboard/service', 'permission' => 'view_dashboard'],
        ['label' => 'Patients', 'icon' => '👥', 'route' => '/patients', 'permission' => 'view_patients'],
        ['label' => 'Consultations', 'icon' => '🩺', 'route' => '/consultations', 'permission' => 'view_consultations'],
        ['label' => 'Urgences', 'icon' => '🚨', 'route' => '/emergencies', 'permission' => 'view_emergencies'],
        ['label' => 'Hospitalisation', 'icon' => '🛏️', 'route' => '/hospitalizations', 'permission' => 'view_hospitalizations'],
        ['label' => 'Laboratoire', 'icon' => '🔬', 'route' => '/laboratory', 'permission' => 'view_laboratory'],
        ['label' => 'Imagerie', 'icon' => '📷', 'route' => '/imaging', 'permission' => 'view_imaging'],
        ['label' => 'Stock Service', 'icon' => '📦', 'route' => '/stock', 'permission' => 'manage_stock'],
        ['label' => 'Équipe', 'icon' => '👨‍⚕️', 'route' => '/team', 'permission' => 'manage_team'],
        ['label' => 'Rapports Service', 'icon' => '📈', 'route' => '/reports/service', 'permission' => 'view_reports'],
    ],
    'medecin' => [
        ['label' => 'Tableau de Bord', 'icon' => '📊', 'route' => '/dashboard', 'permission' => 'view_dashboard'],
        ['label' => 'Agenda', 'icon' => '📅', 'route' => '/appointments', 'permission' => 'view_appointments'],
        ['label' => 'Patients', 'icon' => '👥', 'route' => '/patients', 'permission' => 'view_patients'],
        ['label' => 'Consultations', 'icon' => '🩺', 'route' => '/consultations', 'permission' => 'create_consultation'],
        ['label' => 'Ordonnances', 'icon' => '📝', 'route' => '/prescriptions', 'permission' => 'create_prescription'],
        ['label' => 'Résultats Labo', 'icon' => '🔬', 'route' => '/lab-results', 'permission' => 'view_lab_results'],
        ['label' => 'Résultats Imagerie', 'icon' => '📷', 'route' => '/imaging-results', 'permission' => 'view_imaging_results'],
        ['label' => 'Téléconsultation', 'icon' => '📹', 'route' => '/teleconsultation', 'permission' => 'teleconsultation'],
    ],
    'infirmier' => [
        ['label' => 'Tableau de Bord', 'icon' => '📊', 'route' => '/dashboard', 'permission' => 'view_dashboard'],
        ['label' => 'Patients Hospitalisés', 'icon' => '🛏️', 'route' => '/hospitalized-patients', 'permission' => 'view_hospitalizations'],
        ['label' => 'Surveillance', 'icon' => '📋', 'route' => '/monitoring', 'permission' => 'create_monitoring'],
        ['label' => 'Urgences', 'icon' => '🚨', 'route' => '/emergencies', 'permission' => 'view_emergencies'],
        ['label' => 'Constantes', 'icon' => '❤️', 'route' => '/vitals', 'permission' => 'record_vitals'],
        ['label' => 'Médicaments', 'icon' => '💊', 'route' => '/medication-admin', 'permission' => 'administer_medication'],
    ],
    'accueil' => [
        ['label' => 'Accueil Patients', 'icon' => '👋', 'route' => '/reception', 'permission' => 'register_patient'],
        ['label' => 'Rendez-vous', 'icon' => '📅', 'route' => '/appointments', 'permission' => 'manage_appointments'],
        ['label' => 'Patients', 'icon' => '👥', 'route' => '/patients', 'permission' => 'view_patients'],
        ['label' => 'File d\'Attente', 'icon' => '🎫', 'route' => '/queue', 'permission' => 'manage_queue'],
        ['label' => 'Assurances', 'icon' => '📄', 'route' => '/insurance', 'permission' => 'manage_insurance'],
    ],
    'comptable' => [
        ['label' => 'Tableau de Bord', 'icon' => '📊', 'route' => '/dashboard', 'permission' => 'view_dashboard'],
        ['label' => 'Factures', 'icon' => '🧾', 'route' => '/invoices', 'permission' => 'manage_invoices'],
        ['label' => 'Encaissements', 'icon' => '💰', 'route' => '/payments', 'permission' => 'process_payments'],
        ['label' => 'Caisse', 'icon' => '🏦', 'route' => '/cash', 'permission' => 'manage_cash'],
        ['label' => 'Recouvrement', 'icon' => '📞', 'route' => '/collections', 'permission' => 'manage_collections'],
        ['label' => 'Rapports Financiers', 'icon' => '📈', 'route' => '/financial-reports', 'permission' => 'view_financial_reports'],
    ],
    'biologiste' => [
        ['label' => 'Tableau de Bord', 'icon' => '📊', 'route' => '/dashboard', 'permission' => 'view_dashboard'],
        ['label' => 'Demandes d\'Analyses', 'icon' => '📋', 'route' => '/lab-requests', 'permission' => 'view_lab_requests'],
        ['label' => 'Saisie Résultats', 'icon' => '🔬', 'route' => '/lab-results/input', 'permission' => 'input_lab_results'],
        ['label' => 'Historique', 'icon' => '📁', 'route' => '/lab-history', 'permission' => 'view_lab_history'],
    ],
    'radiologue' => [
        ['label' => 'Tableau de Bord', 'icon' => '📊', 'route' => '/dashboard', 'permission' => 'view_dashboard'],
        ['label' => 'Demandes d\'Imagerie', 'icon' => '📋', 'route' => '/imaging-requests', 'permission' => 'view_imaging_requests'],
        ['label' => 'Rapports Radiologiques', 'icon' => '📷', 'route' => '/imaging-reports', 'permission' => 'create_imaging_report'],
        ['label' => 'Archives', 'icon' => '🗄️', 'route' => '/imaging-archive', 'permission' => 'view_imaging_archive'],
    ],
    'pharmacien' => [
        ['label' => 'Tableau de Bord', 'icon' => '📊', 'route' => '/dashboard', 'permission' => 'view_dashboard'],
        ['label' => 'Ordonnances Reçues', 'icon' => '📝', 'route' => '/prescriptions/received', 'permission' => 'view_prescriptions'],
        ['label' => 'Dispensation', 'icon' => '💊', 'route' => '/dispensation', 'permission' => 'dispense_medication'],
        ['label' => 'Stock Pharmacie', 'icon' => '📦', 'route' => '/pharmacy-stock', 'permission' => 'manage_pharmacy_stock'],
        ['label' => 'Commandes', 'icon' => '🛒', 'route' => '/orders', 'permission' => 'manage_orders'],
        ['label' => 'Réseau Pharmacies', 'icon' => '🗺️', 'route' => '/pharmacy-network', 'permission' => 'view_network'],
    ],
];

$currentMenu = $menuItems[$role] ?? [];
?>

<!-- Sidebar -->
<aside 
    x-data="{ open: true }" 
    :class="open ? 'w-64' : 'w-20'"
    class="fixed left-0 top-0 h-full bg-gradient-to-b from-blue-900 to-blue-800 text-white transition-all duration-300 z-50 shadow-xl"
>
    <!-- Logo & Toggle -->
    <div class="p-4 border-b border-blue-700 flex items-center justify-between">
        <div class="flex items-center space-x-3" x-show="open">
            <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                <span class="text-blue-900 font-bold text-xl">I</span>
            </div>
            <div>
                <h1 class="font-bold text-lg">IlaraNet</h1>
                <p class="text-xs text-blue-300">Bénin</p>
            </div>
        </div>
        <button @click="open = !open" class="p-2 hover:bg-blue-700 rounded-lg transition-colors">
            <svg x-show="open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            <svg x-show="!open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    </div>

    <!-- User Info -->
    <div class="p-4 border-b border-blue-700" x-show="open">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
                <span class="font-semibold"><?= strtoupper(substr($user['first_name'] ?? 'U', 0, 1) . substr($user['last_name'] ?? 'ser', 0, 1)) ?></span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-sm truncate"><?= htmlspecialchars($user['first_name'] ?? '') ?> <?= htmlspecialchars($user['last_name'] ?? '') ?></p>
                <p class="text-xs text-blue-300 capitalize"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $role))) ?></p>
                <?php if ($serviceId): ?>
                    <p class="text-xs text-blue-400">Service: <?= $serviceId ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 overflow-y-auto py-4">
        <?php foreach ($currentMenu as $item): ?>
            <a 
                href="<?= $item['route'] ?>" 
                class="flex items-center px-4 py-3 hover:bg-blue-700 transition-colors group"
                title="<?= !open ? $item['label'] : '' ?>"
            >
                <span class="text-xl w-6 text-center"><?= $item['icon'] ?></span>
                <span class="ml-3 whitespace-nowrap" x-show="open"><?= $item['label'] ?></span>
            </a>
        <?php endforeach; ?>
    </nav>

    <!-- Logout -->
    <div class="p-4 border-t border-blue-700">
        <a 
            href="/logout" 
            class="flex items-center px-4 py-3 hover:bg-red-600 rounded-lg transition-colors group"
            title="<?= !open ? 'Déconnexion' : '' ?>"
        >
            <span class="text-xl">🚪</span>
            <span class="ml-3" x-show="open">Déconnexion</span>
        </a>
    </div>
</aside>
