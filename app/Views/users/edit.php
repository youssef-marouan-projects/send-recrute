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
        <h1 class="text-2xl font-bold text-slate-900 mb-6"><?= htmlspecialchars($data['title']) ?></h1>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
            <form method="POST" action="/user/update/<?= (int) $data['user']['id'] ?>" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Name</label>
                    <input type="text" name="name" required value="<?= htmlspecialchars($data['user']['name']) ?>"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" required value="<?= htmlspecialchars($data['user']['email']) ?>"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Role</label>
                    <select name="role"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="user" <?= $data['user']['role'] === 'user' ? 'selected' : '' ?>>User</option>
                        <option value="admin" <?= $data['user']['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Plan</label>
                    <select name="plan_id"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <?php foreach (($data['plans'] ?? []) as $plan): ?>
                        <option value="<?= (int) $plan['id'] ?>" <?= (int) $plan['id'] === (int) $data['user']['plan_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($plan['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="pt-2">
                    <button type="submit"
                            class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>

        <div class="mt-6">
            <a href="/user" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition">&larr; Back to Users</a>
        </div>
    </main>
</body>
</html>
