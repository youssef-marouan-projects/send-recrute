<?php
// Shared top nav for the Tailwind-styled pages (Campaign, Signatures, Profile).
// Include with: require __DIR__ . '/../partials/tailwind_nav.php';
// Expects $activeNav to be set to one of: 'campaign' | 'signature' | 'profile' | 'email'
$navLink = function ($href, $label, $key) use (&$activeNav) {
    $isActive = ($activeNav ?? '') === $key;
    $classes = $isActive
        ? 'bg-indigo-600 text-white'
        : 'text-slate-600 hover:bg-slate-100';
    echo '<a href="' . $href . '" class="px-3 py-2 rounded-lg text-sm font-medium transition ' . $classes . '">' . $label . '</a>';
};
?>
<header class="bg-white border-b border-slate-200 sticky top-0 z-20">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between flex-wrap gap-2">
        <a href="/email" class="flex items-center gap-2 font-semibold text-slate-800">
            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-600 text-white text-sm">SR</span>
            <span>send-recrute</span>
        </a>
        <nav class="flex items-center gap-1 flex-wrap">
            <?php $navLink('/email', 'Generate Email', 'email'); ?>
            <?php $navLink('/campaign', 'Mass Send', 'campaign'); ?>
            <?php $navLink('/signature', 'Signatures', 'signature'); ?>
            <?php $navLink('/profile', 'Profile', 'profile'); ?>
            <a href="/auth/logout" class="px-3 py-2 rounded-lg text-sm font-medium text-rose-600 hover:bg-rose-50 transition">Log Out</a>
        </nav>
    </div>
</header>
