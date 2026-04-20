<?php ob_start(); ?>
<div class="min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-xl shadow-lg p-6" x-data="{show:false}">
        <h1 class="text-2xl font-bold">IlaraNet Bénin</h1>
        <p class="text-sm text-slate-500 mt-1">Connexion Direction / Chef de service</p>

        <?php if (!empty($_SESSION['flash_error'])): ?>
            <div class="mt-4 p-3 rounded bg-red-50 text-red-700 text-sm"><?= htmlspecialchars($_SESSION['flash_error']) ?></div>
            <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>

        <form class="mt-6 space-y-4" action="/login" method="post">
            <input class="w-full border rounded p-2" type="email" name="email" placeholder="Email" required>
            <div class="relative">
                <input class="w-full border rounded p-2 pr-20" x-bind:type="show ? 'text' : 'password'" name="password" placeholder="Mot de passe" required>
                <button type="button" class="absolute right-2 top-2 text-sm text-indigo-600" @click="show=!show">Afficher</button>
            </div>
            <button class="w-full bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700">Se connecter</button>
        </form>
    </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php'; ?>
