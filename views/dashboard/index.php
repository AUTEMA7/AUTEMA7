<?php ob_start(); ?>
<div class="max-w-6xl mx-auto p-6">
    <header class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold">Dashboard <?= htmlspecialchars(strtoupper((string) $_SESSION['user']['role'])) ?></h1>
            <p class="text-slate-600"><?= htmlspecialchars($_SESSION['user']['name']) ?></p>
        </div>
        <form action="/logout" method="post"><button class="bg-slate-800 text-white px-4 py-2 rounded">Déconnexion</button></form>
    </header>

    <?php if (!empty($_SESSION['flash_info'])): ?>
        <div class="mb-4 p-3 rounded bg-green-50 text-green-700"><?= htmlspecialchars($_SESSION['flash_info']) ?></div>
        <?php unset($_SESSION['flash_info']); ?>
    <?php endif; ?>

    <div class="grid md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-4 rounded shadow"><p class="text-sm">Patients</p><p class="text-2xl font-bold"><?= (int) $patients ?></p></div>
        <div class="bg-white p-4 rounded shadow"><p class="text-sm">2FA OTP</p><p class="text-2xl font-bold">Actif</p></div>
        <div class="bg-white p-4 rounded shadow"><p class="text-sm">Audit Trail</p><p class="text-2xl font-bold">Immuable</p></div>
    </div>

    <div class="bg-white rounded shadow p-4 mb-6">
        <h2 class="font-semibold mb-3">Paiement mobile (simulation)</h2>
        <form class="grid md:grid-cols-4 gap-2" action="/payments/mobile" method="post">
            <input class="border rounded p-2" name="amount" placeholder="Montant" required>
            <select class="border rounded p-2" name="channel"><option value="momo">MTN MoMo</option><option value="flooz">Flooz</option><option value="ccash">C-Cash</option></select>
            <button class="bg-indigo-600 text-white rounded px-4">Valider</button>
        </form>
    </div>

    <div class="bg-white rounded shadow p-4">
        <h2 class="font-semibold mb-3">Journal d'audit (WAT)</h2>
        <ul class="space-y-2 text-sm">
            <?php foreach ($auditLogs as $log): ?>
                <li class="border rounded p-2"><?= htmlspecialchars($log['event_time_wat']) ?> — <?= htmlspecialchars($log['action']) ?> (<?= htmlspecialchars($log['entity_type']) ?>)</li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php'; ?>
