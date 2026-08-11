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

    <main class="max-w-6xl mx-auto px-4 sm:px-6 py-10">
        <h1 class="text-2xl font-bold text-slate-900 mb-6">All Uploaded CVs</h1>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="text-left px-4 py-3 font-medium text-slate-600">ID</th>
                            <th class="text-left px-4 py-3 font-medium text-slate-600">User</th>
                            <th class="text-left px-4 py-3 font-medium text-slate-600">Original Filename</th>
                            <th class="text-left px-4 py-3 font-medium text-slate-600">Type</th>
                            <th class="text-left px-4 py-3 font-medium text-slate-600">Size</th>
                            <th class="text-left px-4 py-3 font-medium text-slate-600">Uploaded</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if (empty($data['cvs'])): ?>
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-500">No CVs uploaded yet.</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($data['cvs'] as $cv): ?>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-3 text-slate-600"><?= (int) $cv['id'] ?></td>
                            <td class="px-4 py-3">
                                <span class="font-medium text-slate-900"><?= htmlspecialchars($cv['user_name']) ?></span>
                                <br><span class="text-xs text-slate-400"><?= htmlspecialchars($cv['user_email']) ?></span>
                            </td>
                            <td class="px-4 py-3 text-slate-900"><?= htmlspecialchars($cv['original_name']) ?></td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-slate-100 text-slate-600 uppercase">
                                    <?= htmlspecialchars($cv['extension']) ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-600"><?= $cv['size_bytes'] ? round($cv['size_bytes'] / 1024, 1) . ' KB' : '—' ?></td>
                            <td class="px-4 py-3 text-slate-600"><?= htmlspecialchars($cv['created_at']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
