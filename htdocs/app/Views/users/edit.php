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

    label {
        display: block;
        margin-top: 15px;
        font-weight: bold;
    }

    input[type="text"],
    input[type="email"] {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    button {
        margin-top: 20px;
        padding: 10px 20px;
        background: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    button:hover {
        background: #0056b3;
    }

    a {
        color: #007bff;
        text-decoration: none;
    }
    </style>
</head>

<body>
    <h1>Edit User</h1>

    <form action="/user/update/<?= $data['user']['id'] ?>" method="POST">
        <label>Name:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($data['user']['name']) ?>" required>

        <label>Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($data['user']['email']) ?>" required>

        <button type="submit">Update User</button>
    </form>

    <br><br>
    <a href="/user">← Back to list</a>
</body>

</html>