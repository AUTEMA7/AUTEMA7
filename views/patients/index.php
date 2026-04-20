<?php ob_start(); ?>
<div class="max-w-6xl mx-auto p-6" x-data="{open:false, anip:false}">
    <h1 class="text-2xl font-bold mb-4">Patients</h1>
    <div class="flex gap-2 mb-4">
        <button @click="open=!open" class="bg-indigo-600 text-white px-4 py-2 rounded">Nouveau patient</button>
        <button @click="anip=!anip" class="bg-emerald-600 text-white px-4 py-2 rounded">Vérifier NPI (ANIP)</button>
    </div>

    <form x-show="open" class="bg-white p-4 rounded shadow mb-4 grid md:grid-cols-4 gap-2" action="/patients" method="post">
        <input class="border rounded p-2" name="npi" placeholder="NPI" required>
        <input class="border rounded p-2" name="full_name" placeholder="Nom complet" required>
        <input class="border rounded p-2" name="phone" placeholder="Téléphone" required>
        <button class="bg-indigo-600 text-white rounded px-4">Enregistrer</button>
    </form>

    <form x-show="anip" class="bg-white p-4 rounded shadow mb-4 grid md:grid-cols-4 gap-2" action="/integrations/anip/verify" method="post">
        <input class="border rounded p-2 md:col-span-3" name="npi" placeholder="NPI à vérifier" required>
        <button class="bg-emerald-600 text-white rounded px-4">Vérifier</button>
    </form>

    <div class="bg-white rounded shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50"><tr><th class="p-3 text-left">NPI</th><th class="p-3 text-left">Nom</th><th class="p-3 text-left">Téléphone</th><th class="p-3 text-left">Créé le</th></tr></thead>
            <tbody>
            <?php foreach ($patients as $patient): ?>
                <tr class="border-t"><td class="p-3"><?= htmlspecialchars($patient['npi']) ?></td><td class="p-3"><?= htmlspecialchars($patient['full_name']) ?></td><td class="p-3"><?= htmlspecialchars($patient['phone']) ?></td><td class="p-3"><?= htmlspecialchars((string) $patient['created_at']) ?></td></tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php'; ?>
