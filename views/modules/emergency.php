<?php ob_start(); ?>
<div class="max-w-6xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Module Urgences</h1>
    <form class="bg-white p-4 rounded shadow grid md:grid-cols-5 gap-2 mb-4" method="post" action="/modules/emergency">
        <input class="border rounded p-2" name="npi" placeholder="NPI" required>
        <input class="border rounded p-2" name="esi_level" type="number" min="1" max="5" placeholder="ESI" required>
        <input class="border rounded p-2" name="priority_color" placeholder="Couleur" required>
        <input class="border rounded p-2" name="notes" placeholder="Notes">
        <button class="bg-red-600 text-white rounded px-3">Enregistrer</button>
    </form>
    <div class="bg-white rounded shadow p-4 text-sm">
        <?php foreach ($cases as $case): ?><div class="border-b py-2">#<?= (int) $case['id'] ?> — <?= htmlspecialchars($case['npi']) ?> (ESI <?= (int) $case['esi_level'] ?>)</div><?php endforeach; ?>
    </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php'; ?>
