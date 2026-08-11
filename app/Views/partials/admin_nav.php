<?php
// Shared admin nav bar. Included directly (not via Controller::view())
// so it can drop into any admin view with require __DIR__ . '/../partials/admin_nav.php';
?>
<nav class="bg-white border-b border-slate-200 mb-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-4">
            <a href="/user" class="text-sm font-medium text-slate-600 hover:text-slate-900 transition">All Users</a>
            <a href="/user/cvs" class="text-sm font-medium text-slate-600 hover:text-slate-900 transition">All CVs</a>
            <a href="/user/emails" class="text-sm font-medium text-slate-600 hover:text-slate-900 transition">All Generated Emails</a>
        </div>
        <div class="flex items-center gap-3">
            <a href="/email" class="inline-flex items-center rounded-lg bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-indigo-700 transition">Generate Email / Upload CV</a>
            <a href="/auth/logout" class="text-sm font-medium text-rose-600 hover:text-rose-700 transition">Log Out</a>
        </div>
    </div>
</nav>
