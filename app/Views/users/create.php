<!DOCTYPE html>
<html>

<head>
    <title><?= htmlspecialchars($data['title']) ?></title>
    <style>
    body {
        font-family: Arial, sans-serif;
        max-width: 500px;
        margin: 40px auto;
        padding: 20px;
    }

    h1 {
        color: #333;
    }

    label {
        display: block;
        font-weight: 600;
        margin: 14px 0 6px;
    }

    input,
    select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 15px;
    }

    button {
        margin-top: 20px;
        padding: 10px 20px;
        background: #28a745;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    button:hover {
        background: #218838;
    }

    a {
        color: #007bff;
        text-decoration: none;
    }
    </style>
</head>

<body>
    <?php require __DIR__ . '/../partials/admin_nav.php'; ?>
    <h1><?= htmlspecialchars($data['title']) ?></h1>

    <form method="POST" action="/user/store">
        <label>Name</label>
        <input type="text" name="name" required>

        <label>Email</label>
        <input type="email" name="email" required>

        <label>Password</label>
        <input type="password" name="password" required minlength="6">

        <label>Role</label>
        <select name="role">
            <option value="user" selected>User</option>
            <option value="admin">Admin</option>
        </select>

        <label>Plan</label>
        <select name="plan_id">
            <?php foreach (($data['plans'] ?? []) as $plan): ?>
            <option value="<?= (int) $plan['id'] ?>" <?= $plan['slug'] === 'free' ? 'selected' : '' ?>>
                <?= htmlspecialchars($plan['name']) ?>
            </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Create User</button>
    </form>

    <p style="margin-top:20px;"><a href="/user">&larr; Back to Users</a></p>
</body>

</html>