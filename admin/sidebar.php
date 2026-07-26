<?php
require_once __DIR__ . '/../includes/auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function navActive(string $needle): string
{
    $currentPath = $_SERVER['REQUEST_URI'] ?? '';
    return str_contains($currentPath, $needle) ? 'active bg-secondary text-white' : '';
}

function sidebarLink(string $permission, string $url, string $icon, string $label, string $activeNeedle): void
{
    if (!hasPermission($permission)) {
        return;
    }

    echo '<a href="' . htmlspecialchars(BASE_URL . $url) . '" class="nav-link text-white ' . navActive($activeNeedle) . '">';
    echo '<i class="fas ' . htmlspecialchars($icon) . ' me-2"></i>' . htmlspecialchars($label);
    echo '</a>';
}

function sidebarDropdown(string $permission, string $icon, string $label, array $subItems): void
{
    if (!hasPermission($permission)) {
        return;
    }

    $currentPath = $_SERVER['REQUEST_URI'] ?? '';
    $isOpen = false;

    foreach ($subItems as $item) {
        if (str_contains($currentPath, $item['activeNeedle'])) {
            $isOpen = true;
            break;
        }
    }

    $mainActive = $isOpen ? 'active bg-secondary' : '';
    $toggleIcon = $isOpen ? 'fa-angle-down' : 'fa-angle-left';

    echo '<div class="nav-item">';
    echo '<a href="#" class="nav-link text-white d-flex justify-content-between align-items-center ' . $mainActive . '" onclick="toggleSubmenu(this); return false;">';
    echo '<span><i class="fas ' . htmlspecialchars($icon) . ' me-2"></i>' . htmlspecialchars($label) . '</span>';
    echo '<i class="fas ' . $toggleIcon . ' submenu-icon"></i>';
    echo '</a>';

    echo '<div class="submenu ms-3 ' . ($isOpen ? 'show' : 'd-none') . '">';
    foreach ($subItems as $item) {
        if (hasPermission($item['permission'] ?? $permission)) {
            $subActive = str_contains($currentPath, $item['activeNeedle']) ? 'active bg-secondary text-white' : '';
            echo '<a href="' . htmlspecialchars(BASE_URL . $item['url']) . '" class="nav-link text-white ' . $subActive . ' ps-4">';
            echo '<i class="fas fa-circle-dot me-2" style="font-size: 0.75rem;"></i>' . htmlspecialchars($item['label']);
            echo '</a>';
        }
    }
    echo '</div>';
    echo '</div>';
}
?>

<div class="col-md-2 p-0 bg-dark text-white" style="min-height:100vh;">
    <div class="p-3 border-bottom border-secondary">
        <h5 class="mb-0">پنل مدیریت</h5>
        <small class="text-white-50"><?= htmlspecialchars($_SESSION['fullname'] ?? 'کاربر') ?></small>
    </div>

    <nav class="nav flex-column p-2">
        <?php sidebarLink('view_dashboard', 'admin/dashboard.php', 'fa-gauge', 'داشبورد', '/admin/dashboard.php'); ?>

        <hr class="border-secondary my-2">

        <?php sidebarLink('view_profile', 'profile/index.php', 'fa-user', 'پروفایل من', '/profile/'); ?>

        <?php
        sidebarDropdown('view_accounting', 'fa-calculator', 'منو حسابداری', [
            ['permission' => 'view_accounting', 'url' => 'accounting/index.php', 'label' => 'حسابداری', 'activeNeedle' => '/accounting/'],
            ['permission' => 'view_accounting', 'url' => 'payroll/index.php', 'label' => 'حقوق و دستمزد', 'activeNeedle' => '/payroll/'],
        ]);
        ?>

        <?php
        sidebarDropdown('view_sales', 'fa-shopping-cart', 'فروش', [
            ['permission' => 'view_sales', 'url' => 'sales/customers/index.php', 'label' => 'مشتریان', 'activeNeedle' => '/sales/customers'],
            ['permission' => 'view_sales', 'url' => 'sales/leads/index.php', 'label' => 'سرنخ‌ها', 'activeNeedle' => '/sales/leads'],
            ['permission' => 'view_sales', 'url' => 'sales/quotations/index.php', 'label' => 'پیش‌فاکتورها', 'activeNeedle' => '/sales/quotations'],
            ['permission' => 'view_sales', 'url' => 'sales/orders/index.php', 'label' => 'سفارشات', 'activeNeedle' => '/sales/orders'],
            ['permission' => 'view_sales', 'url' => 'sales/invoices/index.php', 'label' => 'فاکتورها', 'activeNeedle' => '/sales/invoices'],
            ['permission' => 'view_sales', 'url' => 'sales/payments/index.php', 'label' => 'پرداخت‌ها', 'activeNeedle' => '/sales/payments'],
            ['permission' => 'view_sales', 'url' => 'sales/sales_agents/index.php', 'label' => 'کارشناسان فروش', 'activeNeedle' => '/sales/sales_agents'],
        ]);
        ?>

        <?php
        sidebarDropdown('view_warehouses', 'fa-warehouse', 'انبار', [
            ['permission' => 'view_warehouses', 'url' => 'warehouses/index.php', 'label' => 'مشاهده انبارها', 'activeNeedle' => '/warehouses/index'],
            ['permission' => 'view_warehouses', 'url' => 'inventory/index.php', 'label' => 'داشبورد انبار', 'activeNeedle' => '/inventory/index'],
            ['permission' => 'view_warehouses', 'url' => 'inventory/categories/index.php', 'label' => 'دسته‌بندی‌ها', 'activeNeedle' => '/inventory/categories'],
            ['permission' => 'view_warehouses', 'url' => 'inventory/products/index.php', 'label' => 'محصولات', 'activeNeedle' => '/inventory/products'],
            ['permission' => 'view_warehouses', 'url' => 'inventory/issue/index.php', 'label' => 'خروج از انبار', 'activeNeedle' => '/inventory/issue'],
            ['permission' => 'view_warehouses', 'url' => 'inventory/receipts/index.php', 'label' => 'رسیدهای انبار', 'activeNeedle' => '/inventory/receipts'],
            ['permission' => 'view_warehouses', 'url' => 'inventory/transactions/index.php', 'label' => 'کارتکس انبار', 'activeNeedle' => '/inventory/transactions'],
            ['permission' => 'view_warehouses', 'url' => 'inventory/reports/index.php', 'label' => 'گزارشات انبار', 'activeNeedle' => '/inventory/reports'],
            ['permission' => 'view_warehouses', 'url' => 'inventory/stock/index.php', 'label' => 'موجودی انبار', 'activeNeedle' => '/inventory/stock'],
        ]);
        ?>

        <?php
        sidebarDropdown('view_production', 'fa-industry', 'تولید', [
            ['permission' => 'view_production', 'url' => 'production/production_lines/index.php', 'label' => 'خطوط تولید', 'activeNeedle' => '/production_lines'],
            ['permission' => 'view_production', 'url' => 'production/production_orders/index.php', 'label' => 'دستورات تولید', 'activeNeedle' => '/production_orders'],
            ['permission' => 'view_production', 'url' => 'production/production_reports/index.php', 'label' => 'گزارشات تولید', 'activeNeedle' => '/production_reports'],
            ['permission' => 'view_production', 'url' => 'production/quality_control/index.php', 'label' => 'کنترل کیفیت', 'activeNeedle' => '/quality_control'],
            ['permission' => 'view_production', 'url' => 'production/mrp/index.php', 'label' => 'برنامه‌ریزی مواد', 'activeNeedle' => '/mrp'],
        ]);
        ?>

        <?php
        sidebarDropdown('view_support', 'fa-headset', 'پشتیبانی', [
            ['permission' => 'view_support', 'url' => 'public/faq/index.php', 'label' => 'دستورات و FAQ', 'activeNeedle' => '/faq'],
            ['permission' => 'view_support', 'url' => 'public/blog_posts/index.php', 'label' => 'مقالات', 'activeNeedle' => '/blog_posts'],
            ['permission' => 'view_support', 'url' => 'public/tickets/index.php', 'label' => 'تیکت‌ها', 'activeNeedle' => '/tickets'],
            ['permission' => 'view_support', 'url' => 'public/uploads/index.php', 'label' => 'مدیا و آپلودها', 'activeNeedle' => '/uploads'],
        ]);
        ?>

        <?php
        sidebarDropdown('manage_admin', 'fa-cogs', 'مدیریت', [
            ['permission' => 'manage_admin', 'url' => 'admin/logs/index.php', 'label' => 'مدیریت لاگ‌ها', 'activeNeedle' => '/logs'],
            ['permission' => 'manage_admin', 'url' => 'admin/permission/index.php', 'label' => 'مجوزها و دسترسی‌ها', 'activeNeedle' => '/permission'],
            ['permission' => 'manage_admin', 'url' => 'admin/portfolio/index.php', 'label' => 'نمونه‌کارها', 'activeNeedle' => '/portfolio'],
            ['permission' => 'manage_admin', 'url' => 'admin/roles/index.php', 'label' => 'نقش‌ها و دسترسی‌ها', 'activeNeedle' => '/roles'],
            ['permission' => 'manage_admin', 'url' => 'admin/settings/index.php', 'label' => 'تنظیمات', 'activeNeedle' => '/settings'],
            ['permission' => 'manage_users', 'url' => 'users/index.php', 'label' => 'کاربران', 'activeNeedle' => '/users/'],
        ]);
        ?>

        <hr class="border-secondary my-2">

        <a href="<?= BASE_URL ?>logout.php" class="nav-link text-danger">
            <i class="fas fa-right-from-bracket me-2"></i>خروج
        </a>
    </nav>
</div>

<script>
function toggleSubmenu(el) {
    const submenu = el.nextElementSibling;
    const icon = el.querySelector('.submenu-icon');

    document.querySelectorAll('.submenu').forEach(menu => {
        if (menu !== submenu) {
            menu.classList.add('d-none');
            menu.classList.remove('show');
        }
    });

    submenu.classList.toggle('d-none');
    submenu.classList.toggle('show');

    if (icon.classList.contains('fa-angle-down')) {
        icon.classList.remove('fa-angle-down');
        icon.classList.add('fa-angle-left');
    } else {
        icon.classList.remove('fa-angle-left');
        icon.classList.add('fa-angle-down');
    }
}
</script>

<style>
.submenu {
    transition: all 0.3s ease;
}
.nav-link.active {
    background-color: #495057 !important;
}
.submenu .nav-link {
    font-size: 0.95rem;
}
</style>