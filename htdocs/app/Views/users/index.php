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
    a { color: #007bff; text-decoration: none; margin-right: 10px; }
    a:hover { text-decoration: underline; }
    .btn { display: inline-block; padding: 8px 16px; background: #28a745; color: white; border-radius: 5px; text-decoration: none; }
    .btn:hover { background: #218838; }
    .badge { display: inline-block; padding: 2px 10px; border-radius: 999px; font-size: 12px; font-weight: 600; text-transform: uppercase; }
    .badge-admin { background: #7c3aed; color: white; }
    .badge-user { background: #64748b; color: white; }
    .plan-badge { background: #2563eb; color: white; padding: 2px 10px; border-radius: 999px; font-size: 12px; font-weight: 600; text-transform: uppercase; }
    </style>
</head>

<body>
    <?php require __DIR__ . '/../partials/admin_nav.php'; ?>

    <h1>All Users</h1>
    <p>
        <a href="/user/create" class="btn">+ Add New User</a>
    </p>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Plan</th>
            <th>CVs</th>
            <th>Emails</th>
            <th>Actions</th>
        </tr>
        <?php if (empty($data['users'])): ?>
        <tr>
            <td colspan="8">No users found.</td>
        </tr>
        <?php else: ?>
        <?php foreach ($data['users'] as $user): ?>
        <tr>
            <td><?= (int) $user['id'] ?></td>
            <td><?= htmlspecialchars($user['name']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td><span class="badge badge-<?= htmlspecialchars($user['role']) ?>"><?= htmlspecialchars($user['role']) ?></span></td>
            <td><span class="plan-badge"><?= htmlspecialchars($user['plan_name'] ?? 'Free') ?></span></td>
            <td><?= (int) $user['cv_uploads_count'] ?></td>
            <td><?= (int) $user['emails_generated_count'] ?></td>
            <td>
                <a href="/user/show/<?= (int) $user['id'] ?>">View</a>
                <a href="/user/edit/<?= (int) $user['id'] ?>">Edit</a>
                <a href="/user/delete/<?= (int) $user['id'] ?>" onclick="return confirm('Delete this user?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </table>
</body>

</html>
