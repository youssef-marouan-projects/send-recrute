<!DOCTYPE html>
<html>

<head>
    <title><?= htmlspecialchars($data['title']) ?></title>
    <style>
    body { font-family: Arial, sans-serif; max-width: 500px; margin: 40px auto; padding: 20px; }
    h1 { color: #333; }
    label { display: block; font-weight: 600; margin: 14px 0 6px; }
    input, select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 15px; }
    button { margin-top: 20px; padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; }
    button:hover { background: #0069d9; }
    a { color: #007bff; text-decoration: none; }
    </style>
</head>

<body>
    <?php require __DIR__ . '/../partials/admin_nav.php'; ?>
    <?php $user = $data['user']; ?>
    <h1><?= htmlspecialchars($data['title']) ?></h1>

    <form method="POST" action="/user/update/<?= (int) $user['id'] ?>">
        <label>Name</label>
        <input type="text" name="name" required value="<?= htmlspecialchars($user['name']) ?>">

        <label>Email</label>
        <input type="email" name="email" required value="<?= htmlspecialchars($user['email']) ?>">

        <label>Role</label>
        <select name="role">
            <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
        </select>

        <label>Plan</label>
        <select name="plan_id">
            <?php foreach (($data['plans'] ?? []) as $plan): ?>
            <option value="<?= (int) $plan['id'] ?>" <?= (int) $plan['id'] === (int) $user['plan_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($plan['name']) ?>
            </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Save Changes</button>
    </form>

    <p style="margin-top:20px;"><a href="/user">&larr; Back to Users</a></p>
</body>

</html>
