<?php declare(strict_types=1); ?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IlaraNet Bénin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-100 text-slate-900 min-h-screen">
<?php if (isset($_SESSION['user'])): ?>
    <nav class="bg-slate-900 text-white px-4 py-3 text-sm">
        <div class="max-w-6xl mx-auto flex flex-wrap gap-3 items-center">
            <a href="/dashboard" class="font-semibold">IlaraNet</a>
            <a href="/patients">Patients</a>
            <a href="/modules/emergency">Urgences</a>
            <a href="/modules/hospitalization">Hospitalisation</a>
            <a href="/modules/lab">Laboratoire</a>
            <a href="/modules/pharmacy">Pharmacie API</a>
        </div>
    </nav>
<?php endif; ?>
<?= $content ?? '' ?>
</body>
</html>
