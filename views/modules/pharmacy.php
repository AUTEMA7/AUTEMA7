<?php ob_start(); ?>
<div class="max-w-6xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Module Pharmacie API</h1>
    <form class="bg-white p-4 rounded shadow grid md:grid-cols-4 gap-2 mb-4" method="post" action="/modules/pharmacy">
        <input class="border rounded p-2" name="npi" placeholder="NPI" required>
        <input class="border rounded p-2" name="medication" placeholder="Médicament" required>
        <input class="border rounded p-2" name="pharmacy_name" placeholder="Pharmacie" required>
        <button class="bg-indigo-600 text-white rounded px-3">Envoyer ordonnance</button>
    </form>
    <div class="bg-white rounded shadow p-4 text-sm"><?php foreach ($orders as $o): ?><div class="border-b py-2">#<?= (int) $o['id'] ?> — <?= htmlspecialchars($o['medication']) ?> / <?= htmlspecialchars($o['pharmacy_name']) ?> / <?= htmlspecialchars($o['token']) ?></div><?php endforeach; ?></div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php'; ?>
