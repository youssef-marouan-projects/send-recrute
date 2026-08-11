<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($data['title']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen">
    <?php $activeNav = 'users'; require __DIR__ . '/../partials/tailwind_nav.php'; ?>

    <main class="max-w-2xl mx-auto px-4 sm:px-6 py-10">
        <h1 class="text-2xl font-bold text-slate-900 mb-6">User Details</h1>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <table class="min-w-full text-sm">
                <tbody class="divide-y divide-slate-100">
                    <tr>
                        <td class="px-4 py-3 font-semibold text-slate-700 bg-slate-50 w-40">ID</td>
                        <td class="px-4 py-3 text-slate-900"><?= (int) $data['user']['id'] ?></td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 font-semibold text-slate-700 bg-slate-50">Name</td>
                        <td class="px-4 py-3 text-slate-900"><?= htmlspecialchars($data['user']['name']) ?></td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 font-semibold text-slate-700 bg-slate-50">Email</td>
                        <td class="px-4 py-3 text-slate-900"><?= htmlspecialchars($data['user']['email']) ?></td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 font-semibold text-slate-700 bg-slate-50">Role</td>
                        <td class="px-4 py-3 text-slate-900"><?= htmlspecialchars($data['user']['role']) ?></td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 font-semibold text-slate-700 bg-slate-50">Plan</td>
                        <td class="px-4 py-3 text-slate-900"><?= htmlspecialchars($data['user']['plan_name'] ?? 'Free') ?></td>
                    </tr>
                    <?php $isAdmin = ($data['user']['role'] ?? '') === 'admin'; ?>
                    <tr>
                        <td class="px-4 py-3 font-semibold text-slate-700 bg-slate-50">CV Uploads</td>
                        <td class="px-4 py-3 text-slate-900"><?= (int) $data['user']['cv_uploads_count'] ?><?= $isAdmin || $data['user']['max_cv_uploads'] === null ? ' / unlimited' : ' / ' . (int) $data['user']['max_cv_uploads'] ?></td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 font-semibold text-slate-700 bg-slate-50">Emails Generated</td>
                        <td class="px-4 py-3 text-slate-900"><?= (int) $data['user']['emails_generated_count'] ?><?= $isAdmin || $data['user']['max_emails'] === null ? ' / unlimited' : ' / ' . (int) $data['user']['max_emails'] ?></td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 font-semibold text-slate-700 bg-slate-50">Joined</td>
                        <td class="px-4 py-3 text-slate-900"><?= htmlspecialchars($data['user']['created_at']) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex items-center gap-3">
            <a href="/user/edit/<?= (int) $data['user']['id'] ?>" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition">Edit User</a>
            <a href="/user" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition">&larr; Back to Users</a>
        </div>
    </main>
</body>
</html>
