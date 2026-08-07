<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($data['title'] ?? 'Create Account') ?></title>
    <style>
    * {
        box-sizing: border-box;
    }

    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        background: #f1f5f9;
        margin: 0;
        padding: 20px;
        color: #1e293b;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
    }

    .container {
        width: 100%;
        max-width: 420px;
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        padding: 32px;
    }

    h1 {
        margin: 0 0 8px;
        font-size: 1.6rem;
    }

    .subtitle {
        color: #64748b;
        margin-bottom: 24px;
    }

    label {
        display: block;
        font-weight: 600;
        margin: 16px 0 6px;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"] {
        width: 100%;
        padding: 12px 14px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 15px;
    }

    .note {
        font-size: 13px;
        color: #64748b;
        margin-top: 8px;
    }

    button {
        background: #2563eb;
        color: white;
        border: none;
        padding: 14px 28px;
        font-size: 16px;
        font-weight: 600;
        border-radius: 8px;
        cursor: pointer;
        margin-top: 24px;
        width: 100%;
    }

    button:hover {
        background: #1d4ed8;
    }

    .error {
        background: #fef2f2;
        color: #b91c1c;
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        border: 1px solid #fecaca;
    }

    .switch {
        text-align: center;
        margin-top: 20px;
        font-size: 14px;
        color: #64748b;
    }

    .switch a {
        color: #2563eb;
        font-weight: 600;
        text-decoration: none;
    }

    .switch a:hover {
        text-decoration: underline;
    }
    </style>
</head>

<body>
    <div class="container">
        <h1>Create your account</h1>
        <p class="subtitle">Starts on the Free plan — upgrade anytime</p>

        <?php if (!empty($data['error'])): ?>
        <div class="error"><?= htmlspecialchars($data['error']) ?></div>
        <?php endif; ?>

        <form method="POST" action="/auth/store">
            <label>Full Name</label>
            <input type="text" name="name" required value="<?= htmlspecialchars($data['name'] ?? '') ?>">

            <label>Email</label>
            <input type="email" name="email" required value="<?= htmlspecialchars($data['email'] ?? '') ?>">

            <label>Password</label>
            <input type="password" name="password" required minlength="6">

            <label>Confirm Password</label>
            <input type="password" name="password_confirm" required minlength="6">

            <div class="note">By registering you get a Free account (role: user).</div>

            <button type="submit">Create Account</button>
        </form>

        <div class="switch">
            Already have an account? <a href="/auth/login">Log in</a>
        </div>
    </div>
</body>

</html>