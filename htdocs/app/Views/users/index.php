<!DOCTYPE html>
<html>

<head>
    <title><?= $data['title'] ?></title>
    <style>
    body {
        font-family: Arial, sans-serif;
        max-width: 900px;
        margin: 40px auto;
        padding: 20px;
    }

    h1 {
        color: #333;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th,
    td {
        padding: 12px;
        border: 1px solid #ddd;
        text-align: left;
    }

    th {
        background: #f4f4f4;
    }

    a {
        color: #007bff;
        text-decoration: none;
        margin-right: 10px;
    }

    a:hover {
        text-decoration: underline;
    }

    .btn {
        display: inline-block;
        padding: 8px 16px;
        background: #28a745;
        color: white;
        border-radius: 5px;
        text-decoration: none;
    }

    .btn:hover {
        background: #218838;
    }
    </style>
</head>

<body>
    <h1>All Users</h1>
    <a href="/user/create" class="btn">+ Add New User</a>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
        <?php if (empty($data['users'])): ?>
        <tr>
            <td colspan="4">No users found.</td>
        </tr>
        <?php else: ?>
        <?php foreach ($data['users'] as $user): ?>
        <tr>
            <td><?= $user['id'] ?></td>
            <td><?= htmlspecialchars($user['name']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td>
                <a href="/user/show/<?= $user['id'] ?>">View</a>
                <a href="/user/edit/<?= $user['id'] ?>">Edit</a>
                <a href="/user/delete/<?= $user['id'] ?>" onclick="return confirm('Delete this user?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </table>
</body>

</html>