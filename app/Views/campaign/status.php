<?php $campaign = $data['campaign']; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($data['title'] ?? 'Sending...') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen">
    <?php $activeNav = 'campaign'; require __DIR__ . '/../partials/tailwind_nav.php'; ?>

    <main class="max-w-2xl mx-auto px-4 sm:px-6 py-16">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8 text-center">
            <h1 id="statusTitle" class="text-xl font-bold text-slate-900">Sending your campaign&hellip;</h1>
            <p class="text-slate-500 text-sm mt-1">Keep this tab open until it's finished.</p>

            <div class="mt-8">
                <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden">
                    <div id="progressBar" class="bg-indigo-600 h-3 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
                <p id="progressLabel" class="mt-3 text-sm text-slate-600">0 / <?= (int) $campaign['total'] ?> processed</p>
            </div>

            <div class="mt-6 grid grid-cols-2 gap-4 text-sm">
                <div class="rounded-lg bg-emerald-50 border border-emerald-200 py-3">
                    <p id="sentCount" class="text-2xl font-bold text-emerald-700">0</p>
                    <p class="text-emerald-600">Sent</p>
                </div>
                <div class="rounded-lg bg-rose-50 border border-rose-200 py-3">
                    <p id="failedCount" class="text-2xl font-bold text-rose-700">0</p>
                    <p class="text-rose-600">Failed</p>
                </div>
            </div>

            <a id="doneLink" href="/campaign" class="hidden mt-8 inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition">
                Back to Mass Send
            </a>
        </div>
    </main>

    <script>
    const campaignId = <?= (int) $campaign['id'] ?>;
    const total = <?= (int) $campaign['total'] ?>;

    function render(state) {
        const processed = state.sent + state.failed;
        const pct = total > 0 ? Math.round((processed / total) * 100) : 100;
        document.getElementById('progressBar').style.width = pct + '%';
        document.getElementById('progressLabel').textContent = processed + ' / ' + total + ' processed';
        document.getElementById('sentCount').textContent = state.sent;
        document.getElementById('failedCount').textContent = state.failed;

        if (state.finished) {
            document.getElementById('statusTitle').textContent = 'Campaign finished';
            document.getElementById('doneLink').classList.remove('hidden');
        }
    }

    async function tick() {
        try {
            const res = await fetch(`/campaign/processBatch/${campaignId}`, { method: 'POST' });
            const state = await res.json();
            render(state);
            if (!state.finished) {
                setTimeout(tick, 400);
            }
        } catch (e) {
            setTimeout(tick, 1500);
        }
    }

    tick();
    </script>
</body>
</html>
