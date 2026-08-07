<!DOCTYPE html>
<html>

<head>
    <title><?= htmlspecialchars($data['title']) ?></title>
    <style>
    body { font-family: Arial, sans-serif; max-width: 1100px; margin: 40px auto; padding: 20px; }
    h1 { color: #333; }
    a { color: #007bff; text-decoration: none; }
    a:hover { text-decoration: underline; }
    .nav { margin-bottom: 16px; }
    .nav a { margin-right: 16px; }
    .card { border: 1px solid #ddd; border-radius: 8px; padding: 16px; margin-bottom: 16px; }
    .card-header { display: flex; justify-content: space-between; flex-wrap: wrap; gap: 8px; margin-bottom: 10px; font-size: 14px; color: #64748b; }
    .card-header strong { color: #1e293b; }
    .lang-badge { display: inline-block; padding: 2px 8px; border-radius: 6px; font-size: 12px; font-weight: 600; background: #dbeafe; color: #1e40af; }
    .job-post { background: #f9fafb; padding: 10px; border-radius: 6px; margin-bottom: 10px; font-size: 13px; color: #475569; white-space: pre-wrap; max-height: 100px; overflow-y: auto; }
    .result { background: #f0fdf4; border: 1px solid #bbf7d0; padding: 12px; border-radius: 6px; white-space: pre-wrap; font-size: 14px; }
    </style>
</head>

<body>
    <?php require __DIR__ . '/../partials/admin_nav.php'; ?>

    <h1>All Generated Emails</h1>

    <?php if (empty($data['emails'])): ?>
    <p>No emails generated yet.</p>
    <?php else: ?>
    <?php foreach ($data['emails'] as $email): ?>
    <div class="card">
        <div class="card-header">
            <div>
                <strong><?= htmlspecialchars($email['user_name']) ?></strong>
                (<?= htmlspecialchars($email['user_email']) ?>)
                <span class="lang-badge"><?= htmlspecialchars($email['language']) ?></span>
            </div>
            <div><?= htmlspecialchars($email['created_at']) ?></div>
        </div>
        <div class="job-post"><strong>Job post:</strong> <?= htmlspecialchars($email['job_post']) ?></div>
        <div class="result"><?= htmlspecialchars($email['result']) ?></div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</body>

</html>
