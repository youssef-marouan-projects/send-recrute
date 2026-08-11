<?php $rows = $data['rows'] ?? []; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($data['title'] ?? 'Mass Send') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen">
    <?php $activeNav = 'campaign'; require __DIR__ . '/../partials/tailwind_nav.php'; ?>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 py-10">
        <h1 class="text-2xl font-bold text-slate-900">Mass Send Campaign</h1>
        <p class="text-slate-500 mt-1">Upload an Excel file with an <code class="bg-slate-100 px-1.5 py-0.5 rounded text-sm">email</code> column (and optional <code class="bg-slate-100 px-1.5 py-0.5 rounded text-sm">post</code> column) to send a personalized application email to every recruiter at once.</p>

        <?php if (!empty($data['error'])): ?>
            <div class="mt-6 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3"><?= htmlspecialchars($data['error']) ?></div>
        <?php endif; ?>
        <?php if (!empty($data['success'])): ?>
            <div class="mt-6 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm px-4 py-3"><?= htmlspecialchars($data['success']) ?></div>
        <?php endif; ?>

        <?php if (!$data['user']['sender_email'] || !$data['user']['gmail_app_password']): ?>
        <div class="mt-6 rounded-lg bg-amber-50 border border-amber-200 text-amber-800 text-sm px-4 py-3 flex items-center justify-between gap-3 flex-wrap">
            <span>You haven't set up mail sending yet.</span>
            <a href="/profile" class="font-medium underline">Configure it in your Profile &rarr;</a>
        </div>
        <?php endif; ?>

        <!-- Upload -->
        <section class="mt-6 bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <form method="POST" action="/campaign" enctype="multipart/form-data" class="flex items-center gap-3 flex-wrap">
                <input type="file" name="file" accept=".xlsx,.xls" required
                       class="text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-700 file:px-4 file:py-2 file:font-medium">
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition">
                    Load Excel
                </button>
            </form>
            <?php if (!empty($rows)): ?>
            <form method="POST" action="/campaign/clear" class="mt-3">
                <button type="submit" class="text-sm font-medium text-slate-500 hover:text-rose-600">Clear all</button>
            </form>
            <?php endif; ?>
        </section>

        <?php if (!empty($rows)): ?>
        <!-- Review table -->
        <section class="mt-6 bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h2 class="text-lg font-semibold text-slate-900 mb-3"><?= count($rows) ?> recipients loaded</h2>
            <div class="max-h-64 overflow-y-auto rounded-lg border border-slate-200">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 sticky top-0">
                        <tr>
                            <th class="text-left px-4 py-2 font-medium text-slate-600">Email</th>
                            <th class="text-left px-4 py-2 font-medium text-slate-600">Job Post</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($rows as $i => $row): ?>
                        <tr>
                            <td class="px-4 py-2 text-slate-800"><?= htmlspecialchars($row['email']) ?></td>
                            <td class="px-4 py-2 text-slate-500 truncate max-w-xs"><?= htmlspecialchars(mb_substr($row['post'] ?? '', 0, 60)) ?></td>
                            <td class="px-4 py-2 text-right">
                                <form method="POST" action="/campaign/deleteRow/<?= $i ?>">
                                    <button type="submit" class="text-xs text-rose-600 hover:text-rose-700">Remove</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Send form -->
        <section class="mt-6 bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h2 class="text-lg font-semibold text-slate-900 mb-4">Compose &amp; Send</h2>
            <form method="POST" action="/campaign/send" class="space-y-4">
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Default subject</label>
                        <input name="subject" value="Application" placeholder="Application"
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <p class="text-xs text-slate-400 mt-1">Used when a row has no <code>post</code> value.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">CV to attach</label>
                        <select name="existing_cv_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                            <option value="">None</option>
                            <?php foreach ($data['myCvs'] as $cv): ?>
                            <option value="<?= (int) $cv['id'] ?>"><?= htmlspecialchars($cv['original_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Signature</label>
                        <select name="signature_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                            <option value="">None</option>
                            <?php foreach ($data['sigs'] as $sig): ?>
                            <option value="<?= (int) $sig['id'] ?>"><?= htmlspecialchars($sig['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <p class="text-xs text-slate-400 mt-1">Manage signatures on the <a href="/signature" class="underline">Signatures page</a>.</p>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Fallback message</label>
                        <textarea name="message" rows="4" placeholder="Used for rows without a job post — otherwise Groq generates a personalized body per row from the CV + post."
                                  class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                    </div>
                </div>
                <button type="submit"
                        class="w-full inline-flex justify-center items-center rounded-lg bg-indigo-600 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-700 transition">
                    Send to <?= count($rows) ?> recipients
                </button>
            </form>
        </section>
        <?php endif; ?>
    </main>
</body>
</html>
