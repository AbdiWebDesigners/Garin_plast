<?php
if (!defined('BASE_URL')) {
    define('BASE_URL', '/garin/');
}

$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

function isActive($path) {
    global $currentPath;
    return str_contains($currentPath, $path) ? 'active bg-secondary text-white' : '';
}
?>

<div class="col-md-2 p-0 bg-dark text-white" style="min-height: 100vh;">
    <div class="p-3 border-bottom border-secondary">
        <h5 class="mb-0">پنل مدیریت</h5>
        <small class="text-white-50">
            <?= htmlspecialchars($_SESSION['fullname'] ?? 'مدیر') ?>
        </small>
    </div>

    <nav class="nav flex-column p-2">
        <a href="<?= BASE_URL ?>dashboard.php" class="nav-link text-white <?= isActive('/dashboard.php') ?>">
            <i class="fas fa-gauge me-2"></i>داشبورد
        </a>

        <a href="<?= BASE_URL ?>blog_posts/index.php" class="nav-link text-white <?= isActive('/blog_posts/') ?>">
            <i class="fas fa-blog me-2"></i>مقالات
        </a>

        <a href="<?= BASE_URL ?>categories/index.php" class="nav-link text-white <?= isActive('/categories/') ?>">
            <i class="fas fa-folder me-2"></i>دسته‌بندی‌ها
        </a>

        <a href="<?= BASE_URL ?>contact_requests/index.php" class="nav-link text-white <?= isActive('/contact_requests/') ?>">
            <i class="fas fa-envelope me-2"></i>درخواست‌های تماس
        </a>

        <a href="<?= BASE_URL ?>customers/index.php" class="nav-link text-white <?= isActive('/customers/') ?>">
            <i class="fas fa-users me-2"></i>مشتریان
        </a>

        <a href="<?= BASE_URL ?>faq/index.php" class="nav-link text-white <?= isActive('/faq/') ?>">
            <i class="fas fa-circle-question me-2"></i>سوالات متداول
        </a>

        <a href="<?= BASE_URL ?>invoices/index.php" class="nav-link text-white <?= isActive('/invoices/') ?>">
            <i class="fas fa-file-invoice-dollar me-2"></i>فاکتورها
        </a>

        <a href="<?= BASE_URL ?>leads/index.php" class="nav-link text-white <?= isActive('/leads/') ?>">
            <i class="fas fa-bullhorn me-2"></i>سرنخ‌ها
        </a>

        <a href="<?= BASE_URL ?>orders/index.php" class="nav-link text-white <?= isActive('/orders/') ?>">
            <i class="fas fa-cart-shopping me-2"></i>سفارش‌ها
        </a>

        <a href="<?= BASE_URL ?>order_items/index.php" class="nav-link text-white <?= isActive('/order_items/') ?>">
            <i class="fas fa-list me-2"></i>آیتم‌های سفارش
        </a>

        <a href="<?= BASE_URL ?>payments/index.php" class="nav-link text-white <?= isActive('/payments/') ?>">
            <i class="fas fa-credit-card me-2"></i>پرداخت‌ها
        </a>

        <a href="<?= BASE_URL ?>portfolio/index.php" class="nav-link text-white <?= isActive('/portfolio/') ?>">
            <i class="fas fa-briefcase me-2"></i>نمونه‌کارها
        </a>

        <a href="<?= BASE_URL ?>production_orders/index.php" class="nav-link text-white <?= isActive('/production_orders/') ?>">
            <i class="fas fa-industry me-2"></i>دستورات تولید
        </a>

        <a href="<?= BASE_URL ?>products/index.php" class="nav-link text-white <?= isActive('/products/') ?>">
            <i class="fas fa-box me-2"></i>محصولات
        </a>

        <a href="<?= BASE_URL ?>quotations/index.php" class="nav-link text-white <?= isActive('/quotations/') ?>">
            <i class="fas fa-file-invoice me-2"></i>پیش‌فاکتورها
        </a>

        <a href="<?= BASE_URL ?>sales_agents/index.php" class="nav-link text-white <?= isActive('/sales_agents/') ?>">
            <i class="fas fa-user-tie me-2"></i>کارشناسان فروش
        </a>

        <a href="<?= BASE_URL ?>settings/index.php" class="nav-link text-white <?= isActive('/settings/') ?>">
            <i class="fas fa-gear me-2"></i>تنظیمات
        </a>

        <a href="<?= BASE_URL ?>tickets/index.php" class="nav-link text-white <?= isActive('/tickets/') ?>">
            <i class="fas fa-ticket me-2"></i>تیکت‌ها
        </a>

        <a href="<?= BASE_URL ?>ticket_messages/index.php" class="nav-link text-white <?= isActive('/ticket_messages/') ?>">
            <i class="fas fa-comments me-2"></i>پیام‌های تیکت
        </a>

        <a href="<?= BASE_URL ?>users/index.php" class="nav-link text-white <?= isActive('/users/') ?>">
            <i class="fas fa-user-shield me-2"></i>کاربران
        </a>

        <a href="<?= BASE_URL ?>logout.php" class="nav-link text-danger mt-3">
            <i class="fas fa-right-from-bracket me-2"></i>خروج
        </a>
    </nav>
</div>