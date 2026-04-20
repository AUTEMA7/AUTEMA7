<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'IlaraNet Bénin' ?> - Plateforme de Gestion des Centres de Santé</title>
    
    <!-- Tailwind CSS (CDN pour développement) -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Chart.js pour les graphiques -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <!-- Leaflet CSS & JS pour les cartes -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <!-- QR Code Generator -->
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
    
    <!-- Custom Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1e40af',
                        secondary: '#0ea5e9',
                        success: '#10b981',
                        warning: '#f59e0b',
                        danger: '#ef4444',
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
        [x-cloak] { display: none !important; }
        
        /* Scrollbar custom */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #a1a1a1; }
        
        /* Animations */
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        .animate-fade-in { animation: fadeIn 0.3s ease-in-out; }
        
        @keyframes slideIn { from { transform: translateX(-20px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        .animate-slide-in { animation: slideIn 0.3s ease-out; }
        
        /* Print styles */
        @media print {
            .no-print { display: none !important; }
            body { background: white; }
        }
    </style>
    
    <?php if (isset($extraHead)): ?>
        <?= $extraHead ?>
    <?php endif; ?>
</head>
<body 
    x-data="{ 
        sidebarOpen: true, 
        flashMessages: <?= json_encode($_SESSION['flash'] ?? []) ?>,
        init() {
            // Auto-hide flash messages after 5 seconds
            setTimeout(() => { this.flashMessages = []; }, 5000);
            // Clear flash messages from session
            fetch('/api/clear-flash', { method: 'POST' }).catch(() => {});
        }
    }"
    class="bg-gray-50 min-h-screen"
>
    <!-- Flash Messages -->
    <div 
        x-show="flashMessages.length > 0"
        x-transition
        class="fixed top-20 right-6 z-50 space-y-2"
    >
        <template x-for="flash in flashMessages" :key="flash.id">
            <div 
                :class="{
                    'bg-green-500': flash.type === 'success',
                    'bg-red-500': flash.type === 'error',
                    'bg-yellow-500': flash.type === 'warning',
                    'bg-blue-500': flash.type === 'info'
                }"
                class="text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-3 animate-slide-in"
            >
                <span x-text="flash.icon || '📢'"></span>
                <span x-text="flash.message"></span>
                <button @click="flashMessages = flashMessages.filter(f => f.id !== flash.id)" class="ml-4 hover:opacity-75">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </template>
    </div>

    <!-- Sidebar -->
    <?php if (!isset($hideSidebar) || !$hideSidebar): ?>
        <?php include __DIR__ . '/components/sidebar.php'; ?>
    <?php endif; ?>

    <!-- Main Content Wrapper -->
    <div 
        class="transition-all duration-300"
        :class="sidebarOpen ? 'ml-64' : 'ml-20'"
    >
        <!-- Navbar -->
        <?php if (!isset($hideNavbar) || !$hideNavbar): ?>
            <?php include __DIR__ . '/components/navbar.php'; ?>
        <?php endif; ?>

        <!-- Page Content -->
        <main class="pt-20 pb-8 px-6">
            <?php echo $content ?? ''; ?>
        </main>
    </div>

    <!-- Modal Component (Reusable) -->
    <div 
        x-data="{ openModal: false, modalContent: '' }"
        @open-modal.window="openModal = true; modalContent = $event.detail.content;"
        @close-modal.window="openModal = false;"
        x-show="openModal"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
        x-cloak
    >
        <div 
            class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto"
            @click.away="openModal = false"
        >
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800" x-text="modalContent.title || 'Modal'"></h3>
                    <button @click="openModal = false" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div x-html="modalContent.body || ''"></div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div 
        x-data="{ confirmOpen: false, confirmCallback: null }"
        @confirm-dialog.window="confirmOpen = true; confirmCallback = $event.detail.callback;"
        x-show="confirmOpen"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
        x-cloak
    >
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <div class="flex items-center space-x-4 mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                    <span class="text-2xl">⚠️</span>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Confirmation requise</h3>
            </div>
            <p class="text-gray-600 mb-6">Êtes-vous sûr de vouloir effectuer cette action ? Cette opération peut être irréversible.</p>
            <div class="flex justify-end space-x-3">
                <button 
                    @click="confirmOpen = false" 
                    class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                >
                    Annuler
                </button>
                <button 
                    @click="if(confirmCallback) confirmCallback(); confirmOpen = false;" 
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
                >
                    Confirmer
                </button>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div 
        x-data="{ loading: false }"
        @loading-start.window="loading = true"
        @loading-stop.window="loading = false"
        x-show="loading"
        class="fixed inset-0 bg-white bg-opacity-75 z-50 flex items-center justify-center"
        x-cloak
    >
        <div class="text-center">
            <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-blue-600 mx-auto"></div>
            <p class="mt-4 text-gray-600 font-medium">Chargement en cours...</p>
        </div>
    </div>

    <script>
        // Global helper functions
        function dispatchEvent(name, detail = {}) {
            window.dispatchEvent(new CustomEvent(name, { detail }));
        }

        function showFlash(message, type = 'info', icon = '📢') {
            const flash = { id: Date.now(), message, type, icon };
            window.dispatchEvent(new CustomEvent('flash-message', { detail: flash }));
        }

        // CSRF Token for AJAX requests
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        
        // Auto-dismiss alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', () => {
            const alerts = document.querySelectorAll('.alert-auto-dismiss');
            alerts.forEach(alert => {
                setTimeout(() => alert.remove(), 5000);
            });
        });
    </script>
    
    <?php if (isset($extraScripts)): ?>
        <?= $extraScripts ?>
    <?php endif; ?>
</body>
</html>
