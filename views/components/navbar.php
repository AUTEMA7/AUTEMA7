<?php
/**
 * Navbar Component - IlaraNet Bénin
 * Barre de navigation supérieure avec notifications et recherche
 */

$user = $_SESSION['user'] ?? null;
$facilityName = $_SESSION['facility_name'] ?? 'IlaraNet';
$notifications = $_SESSION['notifications'] ?? [];
$unreadCount = count(array_filter($notifications, fn($n) => !$n['read']));
?>

<!-- Top Navbar -->
<nav 
    x-data="{ 
        searchOpen: false, 
        notificationsOpen: false, 
        profileOpen: false,
        searchQuery: ''
    }"
    class="fixed top-0 right-0 h-16 bg-white shadow-md z-40 transition-all duration-300"
    :class="sidebarOpen ? 'left-64' : 'left-20'"
>
    <div class="h-full px-6 flex items-center justify-between">
        <!-- Left: Search & Page Title -->
        <div class="flex items-center space-x-4 flex-1">
            <!-- Search Button -->
            <button 
                @click="searchOpen = !searchOpen" 
                class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
            >
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </button>
            
            <!-- Search Input (Alpine) -->
            <div 
                x-show="searchOpen" 
                x-transition
                class="flex items-center space-x-2 bg-gray-100 rounded-lg px-3 py-2"
            >
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input 
                    type="text" 
                    x-model="searchQuery"
                    placeholder="Rechercher patient (NPI), médicament, facture..." 
                    class="bg-transparent border-none outline-none text-sm w-64"
                    @keydown.escape="searchOpen = false"
                />
                <button @click="searchOpen = false" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <!-- Page Title -->
            <div class="border-l border-gray-300 pl-4">
                <h2 class="text-lg font-semibold text-gray-800"><?= $pageTitle ?? 'Tableau de Bord' ?></h2>
                <p class="text-xs text-gray-500"><?= $facilityName ?> • <?= date('d/m/Y H:i', time()) ?></p>
            </div>
        </div>
        
        <!-- Right: Actions -->
        <div class="flex items-center space-x-3">
            <!-- Quick Stats -->
            <div class="hidden md:flex items-center space-x-4 text-sm">
                <div class="flex items-center space-x-2 px-3 py-1 bg-green-50 rounded-lg">
                    <span class="text-green-600">🟢</span>
                    <span class="text-gray-700">Urgences: <strong>3</strong></span>
                </div>
                <div class="flex items-center space-x-2 px-3 py-1 bg-blue-50 rounded-lg">
                    <span class="text-blue-600">🏥</span>
                    <span class="text-gray-700">Lits: <strong>12/20</strong></span>
                </div>
            </div>
            
            <!-- Notifications -->
            <div class="relative" x-data="{ open: false }">
                <button 
                    @click="open = !open" 
                    class="relative p-2 hover:bg-gray-100 rounded-lg transition-colors"
                >
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <?php if ($unreadCount > 0): ?>
                        <span class="absolute top-1 right-1 w-4 h-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">
                            <?= $unreadCount ?>
                        </span>
                    <?php endif; ?>
                </button>
                
                <!-- Notifications Dropdown -->
                <div 
                    x-show="open" 
                    @click.away="open = false"
                    x-transition
                    class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border border-gray-200 overflow-hidden"
                >
                    <div class="p-4 border-b border-gray-200 bg-gray-50">
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold text-gray-800">Notifications</h3>
                            <button class="text-xs text-blue-600 hover:underline">Tout marquer comme lu</button>
                        </div>
                    </div>
                    <div class="max-h-96 overflow-y-auto">
                        <?php if (empty($notifications)): ?>
                            <div class="p-4 text-center text-gray-500 text-sm">
                                Aucune notification
                            </div>
                        <?php else: ?>
                            <?php foreach (array_slice($notifications, 0, 5) as $notif): ?>
                                <div class="p-3 border-b border-gray-100 hover:bg-gray-50 <?= !$notif['read'] ? 'bg-blue-50' : '' ?>">
                                    <div class="flex items-start space-x-3">
                                        <span class="text-xl"><?= $notif['icon'] ?? '📢' ?></span>
                                        <div class="flex-1">
                                            <p class="text-sm text-gray-800"><?= htmlspecialchars($notif['message']) ?></p>
                                            <p class="text-xs text-gray-500 mt-1"><?= $notif['time'] ?? 'À l\'instant' ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div class="p-3 border-t border-gray-200 bg-gray-50 text-center">
                        <a href="/notifications" class="text-sm text-blue-600 hover:underline">Voir toutes</a>
                    </div>
                </div>
            </div>
            
            <!-- Language Selector -->
            <select class="text-sm border border-gray-300 rounded-lg px-2 py-1 bg-white hover:border-gray-400">
                <option value="fr">🇫🇷 FR</option>
                <option value="en">🇬🇧 EN</option>
            </select>
            
            <!-- User Profile -->
            <div class="relative" x-data="{ open: false }">
                <button 
                    @click="open = !open" 
                    class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded-lg transition-colors"
                >
                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold">
                        <?= strtoupper(substr($user['first_name'] ?? 'U', 0, 1) . substr($user['last_name'] ?? 'ser', 0, 1)) ?>
                    </div>
                    <span class="hidden md:block text-sm font-medium text-gray-700">
                        <?= htmlspecialchars($user['first_name'] ?? 'Utilisateur') ?>
                    </span>
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                
                <!-- Profile Dropdown -->
                <div 
                    x-show="open" 
                    @click.away="open = false"
                    x-transition
                    class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl border border-gray-200 overflow-hidden"
                >
                    <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-blue-600 to-blue-700 text-white">
                        <p class="font-semibold"><?= htmlspecialchars($user['first_name'] ?? '') ?> <?= htmlspecialchars($user['last_name'] ?? '') ?></p>
                        <p class="text-xs opacity-80 capitalize"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $role))) ?></p>
                        <p class="text-xs opacity-70 mt-1">ID: <?= $user['id'] ?? 'N/A' ?></p>
                    </div>
                    <div class="py-2">
                        <a href="/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            👤 Mon Profil
                        </a>
                        <a href="/settings" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            ⚙️ Paramètres
                        </a>
                        <a href="/help" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            ❓ Aide & Support
                        </a>
                        <hr class="my-2 border-gray-200">
                        <a href="/logout" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                            🚪 Déconnexion
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Mobile Menu Toggle (visible on small screens) -->
<button 
    @click="sidebarOpen = !sidebarOpen"
    class="md:hidden fixed bottom-4 right-4 w-14 h-14 bg-blue-600 text-white rounded-full shadow-lg z-50 flex items-center justify-center hover:bg-blue-700 transition-colors"
>
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
    </svg>
</button>
