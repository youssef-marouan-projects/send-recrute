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

    input {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        border: 1px solid #ccc;
        border-radius: 5px;
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
    <h1>Create New User</h1>

    <form action="/user/store" method="POST">
        <label>Name:</label>
        <input type="text" name="name" required>

        <label>Email:</label>
        <input type="email" name="email" required>

        <label>Password:</label>
        <input type="password" name="password" required>

        <button type="submit">Create User</button>
    </form>

    <br><br>
    <a href="/user">← Back to list</a>
</body>

</html>