<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'IlaraNet Bénin' ?></title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Configuration Tailwind -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                        benin: {
                            green: '#008751',
                            yellow: '#FCD116',
                            red: '#E8112D',
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        [x-cloak] { display: none !important; }
        .fade-in { animation: fadeIn 0.3s ease-in; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <?= $content ?>
    
    <!-- Notifications Flash -->
    <?php if (session_status() === PHP_SESSION_ACTIVE): ?>
        <?php 
        $flashTypes = ['success' => 'bg-green-500', 'error' => 'bg-red-500', 'warning' => 'bg-yellow-500', 'info' => 'bg-blue-500'];
        foreach ($flashTypes as $type => $color):
            $message = $_SESSION['_flash'][$type] ?? null;
            if ($message):
        ?>
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-transition
                 x-init="setTimeout(() => show = false, 5000)"
                 class="fixed top-4 right-4 z-50 <?= $color ?> text-white px-6 py-3 rounded-lg shadow-lg fade-in flex items-center gap-3">
                <?php if ($type === 'success'): ?>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <?php elseif ($type === 'error'): ?>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                <?php elseif ($type === 'warning'): ?>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <?php else: ?>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <?php endif; ?>
                <span><?= htmlspecialchars($message) ?></span>
                <button @click="show = false" class="ml-2 hover:opacity-75">×</button>
            </div>
            <?php unset($_SESSION['_flash'][$type]); ?>
        <?php endif; endforeach; ?>
    <?php endif; ?>
</body>
</html>
