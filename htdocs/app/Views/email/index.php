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
    </style>
</head>

<body>
    <div class="container">
        <h1>AI Job Application Email Generator</h1>
        <p class="subtitle">Upload your CV (PDF or DOCX) + Job description → Get a professional email</p>

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

            <label>Upload CV (PDF or DOCX)</label>
            <input type="file" name="cv_file" accept=".pdf,.docx" required>
            <div class="note">Recommended: DOCX works best. PDF needs the pdfparser folder.</div>

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
    </script>
</body>

</html>