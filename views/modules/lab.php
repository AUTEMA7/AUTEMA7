<?php ob_start(); ?>
<div class="max-w-6xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Module Laboratoire</h1>
    <form class="bg-white p-4 rounded shadow grid md:grid-cols-4 gap-2 mb-4" method="post" action="/modules/lab">
        <input class="border rounded p-2" name="npi" placeholder="NPI" required>
        <input class="border rounded p-2" name="test_name" placeholder="Analyse" required>
        <select class="border rounded p-2" name="result_status"><option value="pending">pending</option><option value="critical">critical</option><option value="ready">ready</option></select>
        <button class="bg-indigo-600 text-white rounded px-3">Créer</button>
    </form>
    <div class="bg-white rounded shadow p-4 text-sm"><?php foreach ($requests as $r): ?><div class="border-b py-2">#<?= (int) $r['id'] ?> — <?= htmlspecialchars($r['npi']) ?> / <?= htmlspecialchars($r['test_name']) ?> / <?= htmlspecialchars($r['result_status']) ?></div><?php endforeach; ?></div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php'; ?>
