<?php
// Shared admin nav bar. Included directly (not via Controller::view())
// so it can drop into any admin view with require __DIR__ . '/../partials/admin_nav.php';
?>
<div class="admin-nav">
    <div class="admin-nav-links">
        <a href="/user">All Users</a>
        <a href="/user/cvs">All CVs</a>
        <a href="/user/emails">All Generated Emails</a>
    </div>
    <div class="admin-nav-links">
        <a href="/email" class="admin-nav-cta">Generate Email / Upload CV</a>
        <a href="/auth/logout" class="admin-nav-logout">Log Out</a>
    </div>
</div>
<style>
.admin-nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 10px 16px;
    margin-bottom: 24px;
    font-size: 14px;
}
.admin-nav-links a {
    margin-right: 16px;
    color: #007bff;
    text-decoration: none;
}
.admin-nav-links a:last-child { margin-right: 0; }
.admin-nav-links a:hover { text-decoration: underline; }
.admin-nav-cta {
    background: #2563eb;
    color: white !important;
    padding: 6px 14px;
    border-radius: 6px;
}
.admin-nav-cta:hover { background: #1d4ed8; text-decoration: none !important; }
.admin-nav-logout { color: #dc2626 !important; font-weight: 600; }
</style>
