<?php ob_start(); ?>
<div class="max-w-6xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Module Hospitalisation</h1>
    <form class="bg-white p-4 rounded shadow grid md:grid-cols-5 gap-2 mb-4" method="post" action="/modules/hospitalization">
        <input class="border rounded p-2" name="npi" placeholder="NPI" required>
        <input class="border rounded p-2" name="room_number" placeholder="Chambre" required>
        <input class="border rounded p-2" name="bed_number" placeholder="Lit" required>
        <input class="border rounded p-2" name="status" placeholder="Statut" value="active">
        <button class="bg-indigo-600 text-white rounded px-3">Admettre</button>
    </form>
    <div class="bg-white rounded shadow p-4 text-sm"><?php foreach ($admissions as $admission): ?><div class="border-b py-2">#<?= (int) $admission['id'] ?> — <?= htmlspecialchars($admission['npi']) ?> / <?= htmlspecialchars($admission['room_number']) ?>-<?= htmlspecialchars($admission['bed_number']) ?></div><?php endforeach; ?></div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php'; ?>
