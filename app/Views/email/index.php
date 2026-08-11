<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Job Email Generator – Upload CV</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen">
    <?php $activeNav = 'email'; require __DIR__ . '/../partials/tailwind_nav.php'; ?>

    <main class="max-w-5xl mx-auto px-4 sm:px-6 py-10">

        <h1 class="text-2xl font-bold text-slate-900 mb-6">AI Job Application Email Generator</h1>
        <p class="text-slate-500 mt-1">Upload your CV (PDF or DOCX) + Job description &rarr; Get a professional email</p>

        <?php if (!empty($data['error'])): ?>
        <div class="mt-6 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3"><?= htmlspecialchars($data['error']) ?></div>
        <?php endif; ?>

        <div class="mt-8 grid lg:grid-cols-2 gap-6">
            <!-- Generator form -->
            <section class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h2 class="text-lg font-semibold text-slate-900 mb-4">New Email</h2>
                <form method="POST" enctype="multipart/form-data" action="/email" class="space-y-4">
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Your Full Name</label>
                            <input type="text" name="candidate_name" required
                                   value="<?= htmlspecialchars($data['candidate_name'] ?? '') ?>"
                                   placeholder="Marco Rossi"
                                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Email Language</label>
                            <select name="language"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="English" <?= ($data['language'] ?? '') === 'English' ? 'selected' : '' ?>>English</option>
                                <option value="Italian" <?= ($data['language'] ?? '') === 'Italian' ? 'selected' : '' ?>>Italian</option>
                                <option value="Spanish" <?= ($data['language'] ?? '') === 'Spanish' ? 'selected' : '' ?>>Spanish</option>
                                <option value="French" <?= ($data['language'] ?? '') === 'French' ? 'selected' : '' ?>>French</option>
                                <option value="Portuguese" <?= ($data['language'] ?? '') === 'Portuguese' ? 'selected' : '' ?>>Portuguese</option>
                                <option value="German" <?= ($data['language'] ?? '') === 'German' ? 'selected' : '' ?>>German</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">CV Source</label>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <label class="inline-flex items-center gap-2 border border-slate-300 rounded-lg px-4 py-3 cursor-pointer flex-1 text-sm font-medium">
                                <input type="radio" name="cv_source" value="new" id="cvSourceNew" checked class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                Upload a new CV
                            </label>
                            <label class="inline-flex items-center gap-2 border border-slate-300 rounded-lg px-4 py-3 cursor-pointer flex-1 text-sm font-medium">
                                <input type="radio" name="cv_source" value="existing" id="cvSourceExisting" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                Use a previously uploaded CV
                            </label>
                        </div>
                    </div>

                    <div id="newCvSection">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Upload CV (PDF or DOCX)</label>
                        <input type="file" name="cv_file" id="cvFileInput" accept=".pdf,.docx"
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-700 file:px-4 file:py-2 file:font-medium">
                        <p class="text-xs text-slate-400 mt-1.5">Recommended: DOCX works best. PDF needs the pdfparser folder.</p>
                    </div>

                    <div id="existingCvSection" class="hidden">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Choose a CV you already uploaded</label>
                        <?php $myCvs = $data['myCvs'] ?? []; ?>
                        <?php if (empty($myCvs)): ?>
                        <div class="border border-slate-200 rounded-lg py-4 text-center text-slate-400 text-sm">You haven't uploaded any CV yet.</div>
                        <?php else: ?>
                        <div class="border border-slate-200 rounded-lg max-h-[220px] overflow-y-auto">
                            <?php foreach ($myCvs as $i => $cv): ?>
                            <div class="flex items-center justify-between gap-3 p-3 border-b border-slate-100 last:border-b-0">
                                <label class="inline-flex items-center gap-2 flex-1 min-w-0 font-medium text-sm cursor-pointer">
                                    <input type="radio" name="existing_cv_id" value="<?= (int) $cv['id'] ?>" <?= $i === 0 ? 'checked' : '' ?> class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 flex-shrink-0">
                                    <span class="truncate"><?= htmlspecialchars($cv['original_name']) ?></span>
                                </label>
                                <span class="text-xs text-slate-400 flex-shrink-0"><?= htmlspecialchars(substr($cv['created_at'], 0, 10)) ?></span>
                                <a class="text-indigo-600 text-sm font-semibold hover:underline flex-shrink-0" href="/email/viewCv/<?= (int) $cv['id'] ?>" target="_blank" rel="noopener">View</a>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Job Post / Job Description</label>
                        <textarea name="job_post" required rows="6" placeholder="Paste the full job description here..."
                                  class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"><?= htmlspecialchars($data['job_post'] ?? '') ?></textarea>
                    </div>

                    <button type="submit"
                            class="w-full inline-flex justify-center items-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-indigo-700 transition">
                        Generate Professional Email
                    </button>
                </form>
            </section>

            <!-- Existing CVs -->
            <section class="space-y-4">
                <h2 class="text-lg font-semibold text-slate-900">Your CVs</h2>
                <?php $myCvs = $data['myCvs'] ?? []; ?>
                <?php if (empty($myCvs)): ?>
                    <div class="bg-white rounded-2xl border border-dashed border-slate-300 p-8 text-center text-slate-400 text-sm">
                        No CVs uploaded yet. Upload one in the form to get started.
                    </div>
                <?php else: ?>
                    <?php foreach ($myCvs as $cv): ?>
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-semibold text-slate-900"><?= htmlspecialchars($cv['original_name']) ?></p>
                                <p class="text-sm text-slate-500"><?= htmlspecialchars(substr($cv['created_at'], 0, 10)) ?></p>
                            </div>
                            <a class="text-indigo-600 text-sm font-semibold hover:underline" href="/email/viewCv/<?= (int) $cv['id'] ?>" target="_blank" rel="noopener">View</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </section>
        </div>

        <?php if (!empty($data['result'])): ?>
        <div class="mt-8 bg-emerald-50 border border-emerald-200 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-slate-900 mb-3">Generated Email</h3>
            <div class="bg-white border border-emerald-200 rounded-lg p-4 mt-3 whitespace-pre-wrap text-sm text-slate-700" id="emailResult"><?= htmlspecialchars($data['result']) ?></div>
            <button type="button" onclick="copyResult()"
                    class="mt-4 inline-flex items-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 transition">
                Copy to Clipboard
            </button>
        </div>
        <?php endif; ?>
    </main>

    <script>
    function copyResult() {
        const text = document.getElementById('emailResult').innerText;
        navigator.clipboard.writeText(text).then(() => alert('Email copied!'));
    }

    const newRadio = document.getElementById('cvSourceNew');
    const existingRadio = document.getElementById('cvSourceExisting');
    const newSection = document.getElementById('newCvSection');
    const existingSection = document.getElementById('existingCvSection');
    const cvFileInput = document.getElementById('cvFileInput');

    function updateCvSource() {
        if (existingRadio.checked) {
            newSection.classList.add('hidden');
            existingSection.classList.remove('hidden');
            cvFileInput.required = false;
        } else {
            newSection.classList.remove('hidden');
            existingSection.classList.add('hidden');
            cvFileInput.required = true;
        }
    }

    if (newRadio && existingRadio) {
        newRadio.addEventListener('change', updateCvSource);
        existingRadio.addEventListener('change', updateCvSource);
        updateCvSource();
    }
    </script>
</body>
</html>
