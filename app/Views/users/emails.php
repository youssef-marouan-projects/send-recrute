<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($data['title']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen">
    <?php require __DIR__ . '/../partials/admin_nav.php'; ?>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 py-10">
        <h1 class="text-2xl font-bold text-slate-900 mb-6">All Generated Emails</h1>

        <?php if (empty($data['emails'])): ?>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8 text-center text-slate-500">No emails generated yet.</div>
        <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($data['emails'] as $email): ?>
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <div class="flex items-start justify-between gap-3 flex-wrap mb-3">
                    <div class="text-sm">
                        <span class="font-semibold text-slate-900"><?= htmlspecialchars($email['user_name']) ?></span>
                        <span class="text-slate-500">(<?= htmlspecialchars($email['user_email']) ?>)</span>
                        <span class="inline-flex items-center ml-2 px-2.5 py-0.5 rounded-md text-xs font-semibold bg-indigo-50 text-indigo-700"><?= htmlspecialchars($email['language']) ?></span>
                    </div>
                    <span class="text-xs text-slate-400"><?= htmlspecialchars($email['created_at']) ?></span>
                </div>
                <div class="bg-slate-50 border border-slate-200 rounded-lg p-3 mb-3 text-xs text-slate-600 whitespace-pre-wrap max-h-[100px] overflow-y-auto">
                    <strong>Job post:</strong> <?= htmlspecialchars($email['job_post']) ?>
                </div>
                <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-4 whitespace-pre-wrap text-sm text-slate-700">
                    <?= htmlspecialchars($email['result']) ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </main>
</body>
</html>
