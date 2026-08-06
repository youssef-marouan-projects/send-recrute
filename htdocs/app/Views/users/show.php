<!DOCTYPE html>
<html>

<head>
    <title><?= $data['title'] ?></title>
    <style>
    body {
        font-family: Arial, sans-serif;
        max-width: 600px;
        margin: 40px auto;
        padding: 20px;
    }

    h1 {
        color: #333;
    }

    .card {
        background: #f9f9f9;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #ddd;
    }

    p {
        margin: 10px 0;
    }

    a {
        color: #007bff;
        text-decoration: none;
    }
    </style>
</head>

<body>
    <h1>User Details</h1>

    <div class="card">
        <p><strong>ID:</strong> <?= $data['user']['id'] ?></p>
        <p><strong>Name:</strong> <?= htmlspecialchars($data['user']['name']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($data['user']['email']) ?></p>
    </div>

    <br>
    <a href="/user/edit/<?= $data['user']['id'] ?>">Edit</a> |
    <a href="/user">← Back to list</a>
</body>

</html>