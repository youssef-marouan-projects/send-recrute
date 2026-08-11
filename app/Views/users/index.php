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
        <div class="flex items-center justify-between flex-wrap gap-3 mb-6">
            <h1 class="text-2xl font-bold text-slate-900">All Users</h1>
            <a href="/user/create" class="inline-flex items-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 transition">+ Add New User</a>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="text-left px-4 py-3 font-medium text-slate-600">ID</th>
                            <th class="text-left px-4 py-3 font-medium text-slate-600">Name</th>
                            <th class="text-left px-4 py-3 font-medium text-slate-600">Email</th>
                            <th class="text-left px-4 py-3 font-medium text-slate-600">Role</th>
                            <th class="text-left px-4 py-3 font-medium text-slate-600">Plan</th>
                            <th class="text-left px-4 py-3 font-medium text-slate-600">CVs</th>
                            <th class="text-left px-4 py-3 font-medium text-slate-600">Emails</th>
                            <th class="text-left px-4 py-3 font-medium text-slate-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if (empty($data['users'])): ?>
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-slate-500">No users found.</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($data['users'] as $user): ?>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-3 text-slate-600"><?= (int) $user['id'] ?></td>
                            <td class="px-4 py-3 font-medium text-slate-900"><?= htmlspecialchars($user['name']) ?></td>
                            <td class="px-4 py-3 text-slate-600"><?= htmlspecialchars($user['email']) ?></td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold <?= htmlspecialchars($user['role']) === 'admin' ? 'bg-violet-100 text-violet-800' : 'bg-slate-100 text-slate-700' ?>">
                                    <?= htmlspecialchars($user['role']) ?>
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800">
                                    <?= htmlspecialchars($user['plan_name'] ?? 'Free') ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-600"><?= (int) $user['cv_uploads_count'] ?></td>
                            <td class="px-4 py-3 text-slate-600"><?= (int) $user['emails_generated_count'] ?></td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <a href="/user/show/<?= (int) $user['id'] ?>" class="text-indigo-600 hover:text-indigo-700 font-medium text-xs">View</a>
                                    <a href="/user/edit/<?= (int) $user['id'] ?>" class="text-indigo-600 hover:text-indigo-700 font-medium text-xs">Edit</a>
                                    <a href="/user/delete/<?= (int) $user['id'] ?>" onclick="return confirm('Delete this user?')" class="text-rose-600 hover:text-rose-700 font-medium text-xs">Delete</a>
                                </div>
                            </td>
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
