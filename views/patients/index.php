<?php ob_start(); ?>
<div class="max-w-6xl mx-auto p-6" x-data="{open:false}">
    <header class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">Dashboard Centre de Santé</h1>
            <p class="text-slate-600">Bienvenue <?= htmlspecialchars($_SESSION['user']['name'] ?? '') ?></p>
        </div>
        <form action="/logout" method="post">
            <button class="bg-slate-800 text-white px-4 py-2 rounded">Déconnexion</button>
        </form>
    </header>

    <div class="grid md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow"><p class="text-sm">Patients enregistrés</p><p class="text-2xl font-bold"><?= count($patients) ?></p></div>
        <div class="bg-white p-4 rounded-lg shadow"><p class="text-sm">Modules actifs</p><p class="text-2xl font-bold">10+</p></div>
        <div class="bg-white p-4 rounded-lg shadow"><p class="text-sm">Conformité</p><p class="text-2xl font-bold">ANIP / OHADA</p></div>
    </div>

    <button @click="open=!open" class="mb-4 bg-indigo-600 text-white px-4 py-2 rounded">Nouveau patient</button>

    <form x-show="open" class="bg-white p-4 rounded shadow mb-6 grid md:grid-cols-4 gap-3" action="/patients" method="post">
        <input class="border rounded p-2" name="npi" placeholder="NPI" required>
        <input class="border rounded p-2" name="full_name" placeholder="Nom complet" required>
        <input class="border rounded p-2" name="phone" placeholder="Téléphone" required>
        <button class="bg-green-600 text-white rounded p-2">Enregistrer</button>
    </form>

    <div class="bg-white rounded shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50"><tr><th class="text-left p-3">NPI</th><th class="text-left p-3">Patient</th><th class="text-left p-3">Téléphone</th><th class="text-left p-3">Créé le</th></tr></thead>
            <tbody>
            <?php foreach ($patients as $patient): ?>
                <tr class="border-t">
                    <td class="p-3"><?= htmlspecialchars($patient['npi']) ?></td>
                    <td class="p-3"><?= htmlspecialchars($patient['full_name']) ?></td>
                    <td class="p-3"><?= htmlspecialchars($patient['phone']) ?></td>
                    <td class="p-3"><?= htmlspecialchars((string) $patient['created_at']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php'; ?>
