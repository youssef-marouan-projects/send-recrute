<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($data['title'] ?? 'Signatures') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen">
    <?php $activeNav = 'signature'; require __DIR__ . '/../partials/tailwind_nav.php'; ?>

    <main class="max-w-5xl mx-auto px-4 sm:px-6 py-10">
        <h1 class="text-2xl font-bold text-slate-900">Email Signatures</h1>
        <p class="text-slate-500 mt-1">Build a personalized HTML signature to attach to your Mass Send campaigns.</p>

        <?php if (!empty($_SESSION['signature_error'])): ?>
            <div class="mt-6 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3">
                <?= htmlspecialchars($_SESSION['signature_error']); unset($_SESSION['signature_error']); ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($_SESSION['signature_success'])): ?>
            <div class="mt-6 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm px-4 py-3">
                <?= htmlspecialchars($_SESSION['signature_success']); unset($_SESSION['signature_success']); ?>
            </div>
        <?php endif; ?>

        <div class="mt-8 grid lg:grid-cols-2 gap-6">
            <!-- Builder form -->
            <section class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h2 class="text-lg font-semibold text-slate-900 mb-4">New Signature</h2>
                <form method="POST" action="/signature/save" enctype="multipart/form-data" class="space-y-4">
                    <input type="hidden" name="edit_id" value="">

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Name</label>
                            <input name="name" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Title</label>
                            <input name="title" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                            <input type="email" name="email" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Phone</label>
                            <input name="phone" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">LinkedIn</label>
                            <input name="linkedin" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">GitHub</label>
                            <input name="github" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-slate-700 mb-1">Portfolio</label>
                            <input name="portfolio" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-slate-700 mb-1">Custom text</label>
                            <textarea name="custom_text" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Photo</label>
                            <input type="file" name="image" accept="image/*" class="w-full text-sm text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-700 file:px-3 file:py-1.5">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Image shape</label>
                            <select name="image_shape" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="circle">Circle</option>
                                <option value="rounded">Rounded</option>
                                <option value="square">Square</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Layout</label>
                            <select name="layout" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="horizontal">Horizontal</option>
                                <option value="vertical">Vertical</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Accent color</label>
                            <input type="color" name="accent_color" value="#3b82f6" class="w-full h-10 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div class="flex items-end">
                            <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                                <input type="checkbox" name="show_icons" checked class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                Show icons
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="w-full inline-flex justify-center items-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-indigo-700 transition">
                        Save Signature
                    </button>
                </form>
            </section>

            <!-- Existing signatures -->
            <section class="space-y-4">
                <h2 class="text-lg font-semibold text-slate-900">Your Signatures</h2>
                <?php if (empty($data['sigs'])): ?>
                    <div class="bg-white rounded-2xl border border-dashed border-slate-300 p-8 text-center text-slate-400 text-sm">
                        No signatures yet — create one on the left.
                    </div>
                <?php else: ?>
                    <?php foreach ($data['sigs'] as $sig): ?>
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-semibold text-slate-900"><?= htmlspecialchars($sig['name']) ?></p>
                                <p class="text-sm text-slate-500"><?= htmlspecialchars($sig['title'] ?? '') ?></p>
                            </div>
                            <form method="POST" action="/signature/delete/<?= (int) $sig['id'] ?>" onsubmit="return confirm('Delete this signature?');">
                                <button type="submit" class="text-xs font-medium text-rose-600 hover:text-rose-700">Delete</button>
                            </form>
                        </div>
                        <div class="mt-3 border-t border-slate-100 pt-3 text-sm text-slate-600">
                            <?php
                                require_once __DIR__ . '/../../Helpers/SignatureHelper.php';
                                $previewHtml = SignatureHelper::build($sig, !empty($sig['image_base64']));
                                // The helper embeds cid:signature_photo, which only resolves inside a
                                // sent email's MIME parts. For an on-page preview, swap it back to the
                                // actual base64 data URI.
                                if (!empty($sig['image_base64'])) {
                                    $previewHtml = str_replace('cid:signature_photo', $sig['image_base64'], $previewHtml);
                                }
                                echo $previewHtml;
                            ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </section>
        </div>
    </main>
</body>
</html>
