<?php ob_start(); ?>
<div class="min-h-screen flex items-center justify-center p-6">
    <div class="max-w-lg w-full bg-white rounded-xl shadow p-6 text-center">
        <h1 class="text-3xl font-bold text-red-600">403</h1>
        <p class="mt-2 font-semibold">Accès refusé</p>
        <p class="text-slate-600 mt-2">Votre rôle n'autorise pas l'accès à cette page.</p>
        <a href="/dashboard" class="inline-block mt-5 bg-slate-900 text-white px-4 py-2 rounded">Retour au dashboard</a>
    </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php'; ?>
