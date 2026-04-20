<?php ob_start(); ?>
<div class="min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-xl shadow-lg p-6">
        <h1 class="text-xl font-bold">Code du service</h1>
        <p class="text-sm text-slate-500">Niveau Chef/Personnel: saisissez le code service à 6 chiffres.</p>
        <?php if (!empty($_SESSION['flash_error'])): ?>
            <div class="mt-4 p-3 rounded bg-red-50 text-red-700 text-sm"><?= htmlspecialchars($_SESSION['flash_error']) ?></div>
            <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>
        <form class="mt-5 space-y-3" action="/service-access" method="post">
            <input class="w-full border rounded p-2" name="service_code" placeholder="Code service" required>
            <button class="w-full bg-indigo-600 text-white py-2 rounded">Valider</button>
        </form>
    </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php'; ?>
