<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Job Email Generator – Upload CV</title>
    <style>
    * {
        box-sizing: border-box;
    }

    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        background: #f1f5f9;
        margin: 0;
        padding: 20px;
        color: #1e293b;
    }

    .container {
        max-width: 860px;
        margin: 0 auto;
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        padding: 32px;
    }

    h1 {
        margin: 0 0 8px;
        font-size: 1.8rem;
    }

    .subtitle {
        color: #64748b;
        margin-bottom: 28px;
    }

    label {
        display: block;
        font-weight: 600;
        margin: 18px 0 6px;
    }

    input[type="text"],
    select,
    textarea,
    input[type="file"] {
        width: 100%;
        padding: 12px 14px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 15px;
    }

    textarea {
        min-height: 140px;
        resize: vertical;
    }

    .row {
        display: flex;
        gap: 16px;
    }

    .row>div {
        flex: 1;
    }

    button {
        background: #2563eb;
        color: white;
        border: none;
        padding: 14px 28px;
        font-size: 16px;
        font-weight: 600;
        border-radius: 8px;
        cursor: pointer;
        margin-top: 24px;
        width: 100%;
    }

    button:hover {
        background: #1d4ed8;
    }

    .error {
        background: #fef2f2;
        color: #b91c1c;
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        border: 1px solid #fecaca;
    }

    .result-box {
        margin-top: 32px;
        background: #f0fdf4;
        border: 1px solid #86efac;
        border-radius: 12px;
        padding: 24px;
    }

    .result-content {
        white-space: pre-wrap;
        line-height: 1.6;
        background: white;
        padding: 18px;
        border-radius: 8px;
        border: 1px solid #bbf7d0;
    }

    .copy-btn {
        background: #16a34a;
        margin-top: 12px;
        width: auto;
        padding: 10px 20px;
        font-size: 14px;
    }

    .copy-btn:hover {
        background: #15803d;
    }

    .note {
        font-size: 13px;
        color: #64748b;
        margin-top: 6px;
    }

    .account-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 10px;
        padding: 12px 16px;
        margin-bottom: 24px;
        font-size: 14px;
    }

    .account-bar a {
        color: #2563eb;
        text-decoration: none;
        font-weight: 600;
    }

    .account-bar a:hover {
        text-decoration: underline;
    }

    .plan-badge {
        display: inline-block;
        background: #2563eb;
        color: white;
        padding: 2px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        margin-left: 6px;
    }

    .cv-source-toggle {
        display: flex;
        gap: 12px;
        margin-top: 8px;
    }

    .cv-source-toggle label {
        display: flex;
        align-items: center;
        gap: 6px;
        font-weight: 500;
        margin: 0;
        padding: 10px 14px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        cursor: pointer;
        flex: 1;
    }

    .cv-source-toggle input[type="radio"] {
        width: auto;
    }

    .cv-list {
        margin-top: 10px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        max-height: 220px;
        overflow-y: auto;
    }

    .cv-list-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 10px 14px;
        border-bottom: 1px solid #e2e8f0;
    }

    .cv-list-item:last-child {
        border-bottom: none;
    }

    .cv-list-item label {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0;
        font-weight: 500;
        cursor: pointer;
        flex: 1;
        min-width: 0;
    }

    .cv-list-item input[type="radio"] {
        width: auto;
        flex-shrink: 0;
    }

    .cv-name {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .cv-date {
        font-size: 12px;
        color: #94a3b8;
        flex-shrink: 0;
    }

    .cv-view-link {
        color: #2563eb;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        flex-shrink: 0;
    }

    .cv-view-link:hover {
        text-decoration: underline;
    }

    .cv-empty {
        padding: 14px;
        color: #94a3b8;
        font-size: 14px;
        text-align: center;
    }
    </style>
</head>

<body>
    <div class="container">
        <?php $user = $data['user'] ?? null; ?>
        <?php if ($user): ?>
        <div class="account-bar">
            <div>
                Hi, <strong><?= htmlspecialchars($user['name']) ?></strong>
                <span class="plan-badge"><?= htmlspecialchars($user['plan_name'] ?? 'Free') ?></span>
                &nbsp;&middot;&nbsp;
                <?php $isAdmin = ($user['role'] ?? '') === 'admin'; ?>
                CVs: <?= (int) $user['cv_uploads_count'] ?><?= $isAdmin || $user['max_cv_uploads'] === null ? ' / unlimited' : ' / ' . (int) $user['max_cv_uploads'] ?>
                &nbsp;&middot;&nbsp;
                Emails: <?= (int) $user['emails_generated_count'] ?><?= $isAdmin || $user['max_emails'] === null ? ' / unlimited' : ' / ' . (int) $user['max_emails'] ?>
            </div>
            <div>
                <?php if (($user['role'] ?? '') === 'admin'): ?>
                <a href="/user">Admin Panel</a> &nbsp;&middot;&nbsp;
                <?php endif; ?>
                <a href="/auth/logout">Log Out</a>
            </div>
        </div>
        <?php endif; ?>

        <h1>AI Job Application Email Generator</h1>
        <p class="subtitle">Upload your CV (PDF or DOCX) + Job description &rarr; Get a professional email</p>

        <?php if (!empty($data['error'])): ?>
        <div class="error"><?= htmlspecialchars($data['error']) ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" action="/email">
            <div class="row">
                <div>
                    <label>Your Full Name</label>
                    <input type="text" name="candidate_name" required
                        value="<?= htmlspecialchars($data['candidate_name'] ?? '') ?>" placeholder="Marco Rossi">
                </div>
                <div>
                    <label>Email Language</label>
                    <select name="language">
                        <option value="English">English</option>
                        <option value="Italian" <?= ($data['language'] ?? '') === 'Italian' ? 'selected' : '' ?>>
                            Italian</option>
                        <option value="Spanish" <?= ($data['language'] ?? '') === 'Spanish' ? 'selected' : '' ?>>
                            Spanish</option>
                        <option value="French" <?= ($data['language'] ?? '') === 'French' ? 'selected' : '' ?>>French
                        </option>
                        <option value="Portuguese" <?= ($data['language'] ?? '') === 'Portuguese' ? 'selected' : '' ?>>
                            Portuguese</option>
                        <option value="German" <?= ($data['language'] ?? '') === 'German' ? 'selected' : '' ?>>German
                        </option>
                    </select>
                </div>
            </div>

            <label>CV Source</label>
            <div class="cv-source-toggle">
                <label>
                    <input type="radio" name="cv_source" value="new" id="cvSourceNew" checked>
                    Upload a new CV
                </label>
                <label>
                    <input type="radio" name="cv_source" value="existing" id="cvSourceExisting">
                    Use a previously uploaded CV
                </label>
            </div>

            <div id="newCvSection">
                <label>Upload CV (PDF or DOCX)</label>
                <input type="file" name="cv_file" id="cvFileInput" accept=".pdf,.docx">
                <div class="note">Recommended: DOCX works best. PDF needs the pdfparser folder.</div>
            </div>

            <div id="existingCvSection" style="display:none;">
                <label>Choose a CV you already uploaded</label>
                <?php $myCvs = $data['myCvs'] ?? []; ?>
                <?php if (empty($myCvs)): ?>
                <div class="cv-list">
                    <div class="cv-empty">You haven't uploaded any CV yet.</div>
                </div>
                <?php else: ?>
                <div class="cv-list">
                    <?php foreach ($myCvs as $i => $cv): ?>
                    <div class="cv-list-item">
                        <label>
                            <input type="radio" name="existing_cv_id" value="<?= (int) $cv['id'] ?>" <?= $i === 0 ? 'checked' : '' ?>>
                            <span class="cv-name"><?= htmlspecialchars($cv['original_name']) ?></span>
                        </label>
                        <span class="cv-date"><?= htmlspecialchars(substr($cv['created_at'], 0, 10)) ?></span>
                        <a class="cv-view-link" href="/email/viewCv/<?= (int) $cv['id'] ?>" target="_blank" rel="noopener">View</a>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <label>Job Post / Job Description</label>
            <textarea name="job_post" required
                placeholder="Paste the full job description here..."><?= htmlspecialchars($data['job_post'] ?? '') ?></textarea>

            <button type="submit">Generate Professional Email</button>
        </form>

        <?php if (!empty($data['result'])): ?>
        <div class="result-box">
            <h3>Generated Email</h3>
            <div class="result-content" id="emailResult"><?= htmlspecialchars($data['result']) ?></div>
            <button class="copy-btn" onclick="copyResult()">Copy to Clipboard</button>
        </div>
        <?php endif; ?>
    </div>

    <script>
    function copyResult() {
        const text = document.getElementById('emailResult').innerText;
        navigator.clipboard.writeText(text).then(() => alert('Email copied!'));
    }

    // Toggle between "upload new CV" and "use an existing CV"
    const newRadio      = document.getElementById('cvSourceNew');
    const existingRadio = document.getElementById('cvSourceExisting');
    const newSection    = document.getElementById('newCvSection');
    const existingSection = document.getElementById('existingCvSection');
    const cvFileInput   = document.getElementById('cvFileInput');

    function updateCvSource() {
        if (existingRadio.checked) {
            newSection.style.display = 'none';
            existingSection.style.display = 'block';
            cvFileInput.required = false;
        } else {
            newSection.style.display = 'block';
            existingSection.style.display = 'none';
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