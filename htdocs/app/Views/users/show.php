<!DOCTYPE html>
<html>

<head>
    <title><?= htmlspecialchars($data['title']) ?></title>
    <style>
    body { font-family: Arial, sans-serif; max-width: 500px; margin: 40px auto; padding: 20px; }
    h1 { color: #333; }
    table { width: 100%; border-collapse: collapse; margin-top: 16px; }
    td { padding: 10px; border: 1px solid #ddd; }
    td:first-child { font-weight: 600; width: 180px; background: #f9fafb; }
    a { color: #007bff; text-decoration: none; margin-right: 10px; }
    </style>
</head>

<body>
    <?php require __DIR__ . '/../partials/admin_nav.php'; ?>
    <?php $user = $data['user']; ?>
    <h1>User Details</h1>

    <table>
        <tr><td>ID</td><td><?= (int) $user['id'] ?></td></tr>
        <tr><td>Name</td><td><?= htmlspecialchars($user['name']) ?></td></tr>
        <tr><td>Email</td><td><?= htmlspecialchars($user['email']) ?></td></tr>
        <tr><td>Role</td><td><?= htmlspecialchars($user['role']) ?></td></tr>
        <tr><td>Plan</td><td><?= htmlspecialchars($user['plan_name'] ?? 'Free') ?></td></tr>
        <?php $isAdmin = ($user['role'] ?? '') === 'admin'; ?>
        <tr><td>CV Uploads</td><td><?= (int) $user['cv_uploads_count'] ?><?= $isAdmin || $user['max_cv_uploads'] === null ? ' / unlimited' : ' / ' . (int) $user['max_cv_uploads'] ?></td></tr>
        <tr><td>Emails Generated</td><td><?= (int) $user['emails_generated_count'] ?><?= $isAdmin || $user['max_emails'] === null ? ' / unlimited' : ' / ' . (int) $user['max_emails'] ?></td></tr>
        <tr><td>Joined</td><td><?= htmlspecialchars($user['created_at']) ?></td></tr>
    </table>

    <p style="margin-top:20px;">
        <a href="/user/edit/<?= (int) $user['id'] ?>">Edit</a>
        <a href="/user">&larr; Back to Users</a>
    </p>
</body>

</html>
