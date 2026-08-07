<!DOCTYPE html>
<html>

<head>
    <title><?= htmlspecialchars($data['title']) ?></title>
    <style>
    body { font-family: Arial, sans-serif; max-width: 1100px; margin: 40px auto; padding: 20px; }
    h1 { color: #333; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
    th { background: #f4f4f4; }
    a { color: #007bff; text-decoration: none; }
    a:hover { text-decoration: underline; }
    .nav { margin-bottom: 16px; }
    .nav a { margin-right: 16px; }
    .ext-badge { display: inline-block; padding: 2px 8px; border-radius: 6px; font-size: 12px; font-weight: 600; text-transform: uppercase; background: #e2e8f0; color: #334155; }
    </style>
</head>

<body>
    <?php require __DIR__ . '/../partials/admin_nav.php'; ?>

    <h1>All Uploaded CVs</h1>

    <table>
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>Original Filename</th>
            <th>Type</th>
            <th>Size</th>
            <th>Uploaded</th>
        </tr>
        <?php if (empty($data['cvs'])): ?>
        <tr>
            <td colspan="6">No CVs uploaded yet.</td>
        </tr>
        <?php else: ?>
        <?php foreach ($data['cvs'] as $cv): ?>
        <tr>
            <td><?= (int) $cv['id'] ?></td>
            <td><?= htmlspecialchars($cv['user_name']) ?> <br><small style="color:#64748b;"><?= htmlspecialchars($cv['user_email']) ?></small></td>
            <td><?= htmlspecialchars($cv['original_name']) ?></td>
            <td><span class="ext-badge"><?= htmlspecialchars($cv['extension']) ?></span></td>
            <td><?= $cv['size_bytes'] ? round($cv['size_bytes'] / 1024, 1) . ' KB' : '—' ?></td>
            <td><?= htmlspecialchars($cv['created_at']) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </table>
</body>

</html>
