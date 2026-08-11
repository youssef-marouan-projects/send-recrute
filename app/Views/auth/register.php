<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($data['title'] ?? 'Create Account') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-50 min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md bg-white rounded-2xl border border-slate-200 shadow-sm p-8">
        <div class="text-center mb-8">
            <a href="/" class="inline-flex h-10 w-10 items-center justify-center rounded-lg mb-4"><img src="/assets/logo.png" alt="send-recrute" class="h-10 w-10 object-contain"></a>
            <h1 class="text-2xl font-bold text-slate-900">Create your account</h1>
            <p class="text-slate-500 text-sm mt-1">Starts on the Free plan — upgrade anytime</p>
        </div>

        <?php if (!empty($data['error'])): ?>
            <div class="mb-6 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3"><?= htmlspecialchars($data['error']) ?></div>
        <?php endif; ?>

        <form method="POST" action="/auth/store" class="space-y-5">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Full Name</label>
                <input type="text" name="name" required value="<?= htmlspecialchars($data['name'] ?? '') ?>"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                <input type="email" name="email" required value="<?= htmlspecialchars($data['email'] ?? '') ?>"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                <input type="password" name="password" required minlength="6"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Confirm Password</label>
                <input type="password" name="password_confirm" required minlength="6"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <p class="text-xs text-slate-400">By registering you get a Free account (role: user).</p>
            <button type="submit"
                class="w-full inline-flex justify-center items-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 transition">
                Create Account
            </button>
        </form>

        <p class="text-center text-sm text-slate-500 mt-6">
            Already have an account? <a href="/auth/login" class="text-indigo-600 font-semibold hover:underline">Log in</a>
        </p>
    </div>
</body>

</html>