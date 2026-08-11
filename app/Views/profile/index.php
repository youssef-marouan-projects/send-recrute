<?php $user = $data['user']; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($data['title'] ?? 'My Profile') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen">
    <?php $activeNav = 'profile'; require __DIR__ . '/../partials/tailwind_nav.php'; ?>

    <main class="max-w-3xl mx-auto px-4 sm:px-6 py-10">
        <h1 class="text-2xl font-bold text-slate-900">My Profile</h1>
        <p class="text-slate-500 mt-1">Personalize your account and configure how mass emails get sent.</p>

        <?php if (!empty($data['error'])): ?>
            <div class="mt-6 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3">
                <?= htmlspecialchars($data['error']) ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($data['success'])): ?>
            <div class="mt-6 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm px-4 py-3">
                <?= htmlspecialchars($data['success']) ?>
            </div>
        <?php endif; ?>

        <!-- Basic identity -->
        <section class="mt-8 bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
            <h2 class="text-lg font-semibold text-slate-900">Account</h2>
            <p class="text-sm text-slate-500 mt-1">Your login details and the name shown as the sender on your emails.</p>

            <form method="POST" action="/profile/update" class="mt-6 grid sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Full name</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Login email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Sender display name</label>
                    <input type="text" name="sender_name" value="<?= htmlspecialchars($user['sender_name'] ?? $user['name']) ?>"
                           placeholder="e.g. Youssef Marouan"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="text-xs text-slate-400 mt-1">This is the "From" name recipients will see on mass-sent emails.</p>
                </div>
                <div class="sm:col-span-2">
                    <button type="submit"
                            class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition">
                        Save Account Info
                    </button>
                </div>
            </form>
        </section>

        <!-- Mail sending / Gmail App Password -->
        <section class="mt-6 bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
            <h2 class="text-lg font-semibold text-slate-900">Mail Sending (Gmail)</h2>
            <p class="text-sm text-slate-500 mt-1">
                Mass Send emails go out through your own Gmail account over SMTP. Gmail requires an
                <strong>App Password</strong> for this — not your normal Gmail password.
            </p>

            <div class="mt-4 flex items-center gap-3">
                <span class="inline-flex h-2.5 w-2.5 rounded-full <?= !empty($user['sender_email']) && !empty($user['gmail_app_password']) ? 'bg-emerald-500' : 'bg-amber-400' ?>"></span>
                <span class="text-sm text-slate-600">
                    <?= (!empty($user['sender_email']) && !empty($user['gmail_app_password']))
                        ? 'Mail sending is configured for ' . htmlspecialchars($user['sender_email'])
                        : 'Mail sending is not configured yet.' ?>
                </span>
            </div>

            <a href="https://myaccount.google.com/apppasswords?pli=1&rapt=AEjHL4PboP3apw-D_JiqQhQToLn88v5BNw8sSA90N3oBITnYc3RSW_S-3ls2cCIm-etELIa6W0JHH07RiMZMJ5YP3LLmFtt77dlyNF3BHV3q4Aj1GwJ63JY"
               target="_blank" rel="noopener"
               class="mt-4 inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/>
                </svg>
                Get my Gmail App Password
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M7 17L17 7M7 7h10v10"/>
                </svg>
            </a>
            <p class="text-xs text-slate-400 mt-2">
                Opens Google's App Passwords page in a new tab. 2-Step Verification must be enabled on the Google account.
                Generate one, then paste the 16-character code below.
            </p>

            <form method="POST" action="/profile/mailSettings" class="mt-6 grid sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Gmail address</label>
                    <input type="email" name="sender_email" value="<?= htmlspecialchars($user['sender_email'] ?? '') ?>"
                           placeholder="you@gmail.com"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Gmail App Password</label>
                    <input type="password" name="gmail_app_password" placeholder="<?= !empty($user['gmail_app_password']) ? '•••• •••• •••• ••••  (already set — leave blank to keep)' : 'abcd efgh ijkl mnop' ?>"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm font-mono tracking-wider focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="sm:col-span-2">
                    <button type="submit"
                            class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition">
                        Save Mail Settings
                    </button>
                </div>
            </form>

            <?php if (!empty($user['gmail_app_password'])): ?>
            <form method="POST" action="/profile/clearAppPassword" onsubmit="return confirm('Remove the saved App Password?');" class="mt-3">
                <button type="submit" class="text-sm font-medium text-rose-600 hover:text-rose-700">
                    Remove App Password
                </button>
            </form>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
