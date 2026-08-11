<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['title'] ?? 'send-recrute' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center">
    <div class="text-center px-4">
        <div class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-600 text-white text-2xl font-bold mb-6">SR</div>
        <h1 class="text-4xl font-bold text-slate-900">send-recrute</h1>
        <p class="text-slate-500 mt-3 text-lg max-w-md mx-auto">AI-powered job application email platform. Generate personalized emails and launch mass send campaigns.</p>
        <div class="mt-8 flex gap-4 justify-center flex-wrap">
            <a href="/auth/login" class="inline-flex items-center rounded-lg bg-indigo-600 px-6 py-3 text-sm font-medium text-white hover:bg-indigo-700 transition">Log In</a>
            <a href="/auth/register" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-6 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50 transition">Create Account</a>
        </div>
    </div>
</body>
</html>
