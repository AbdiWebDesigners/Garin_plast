<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();

$role = userRole();
$fullname = $_SESSION['fullname'] ?? 'کاربر';

function statCount(PDO $pdo, string $sql): int
{
    try {
        return (int) $pdo->query($sql)->fetchColumn();
    } catch (Throwable $e) {
        return 0;
    }
}

$stats = [];
if (isset($pdo) && $pdo instanceof PDO) {
    $stats = [
        [
            'title' => 'مشتریان',
            'count' => statCount($pdo, "SELECT COUNT(*) FROM customers"),
            'icon' => 'fa-users',
            'color' => 'primary',
            'permission' => 'view_customers',
            'link' => BASE_URL . 'admin/customers/index.php',
        ],
        [
            'title' => 'سرنخ‌ها',
            'count' => statCount($pdo, "SELECT COUNT(*) FROM leads"),
            'icon' => 'fa-bullhorn',
            'color' => 'success',
            'permission' => 'view_leads',
            'link' => BASE_URL . 'sales/leads/index.php',
        ],
        [
            'title' => 'پیش‌فاکتورها',
            'count' => statCount($pdo, "SELECT COUNT(*) FROM quotations"),
            'icon' => 'fa-file-invoice',
            'color' => 'warning',
            'permission' => 'view_quotations',
            'link' => BASE_URL . 'sales/quotations/index.php',
        ],
        [
            'title' => 'فاکتورها',
            'count' => statCount($pdo, "SELECT COUNT(*) FROM invoices"),
            'icon' => 'fa-file-invoice-dollar',
            'color' => 'info',
            'permission' => 'view_invoices',
            'link' => BASE_URL . 'sales/invoices/index.php',
        ],
        [
            'title' => 'تیکت‌ها',
            'count' => statCount($pdo, "SELECT COUNT(*) FROM tickets"),
            'icon' => 'fa-ticket',
            'color' => 'danger',
            'permission' => 'view_tickets',
            'link' => BASE_URL . 'tickets/index.php',
        ],
        [
            'title' => 'کاربران',
            'count' => statCount($pdo, "SELECT COUNT(*) FROM users"),
            'icon' => 'fa-user-shield',
            'color' => 'dark',
            'permission' => 'manage_users',
            'link' => BASE_URL . 'users/index.php',
        ],
    ];
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>داشبورد مدیریت</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container-fluid">
    <div class="row">
        <?php include __DIR__ . '/sidebar.php'; ?>

        <div class="col-md-10 p-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h4 class="mb-1">خوش آمدی، <?= htmlspecialchars($fullname) ?></h4>
                        <div class="text-muted">این خلاصه‌ای از فعالیت‌های پنل شماست.</div>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-secondary mb-2"><?= htmlspecialchars($role) ?></span>
                        <div class="small text-muted">پنل مدیریت Garin</div>
                        <a href="<?= BASE_URL ?>logout.php" class="btn btn-danger btn-sm mt-2">خروج</a>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <?php foreach ($stats as $stat): ?>
                    <?php if (hasPermission($stat['permission'])): ?>
                        
                        <div class="col-sm-6 col-lg-4">
                            <a href="<?= htmlspecialchars($stat['link']) ?>" class="text-decoration-none">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body d-flex align-items-center">
                                        <div class="rounded-circle bg-<?= htmlspecialchars($stat['color']) ?> text-white d-flex align-items-center justify-content-center ms-3" style="width:56px;height:56px;">
                                            <i class="fa-solid <?= htmlspecialchars($stat['icon']) ?>"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="text-muted small"><?= htmlspecialchars($stat['title']) ?></div>
                                            <div class="fs-4 fw-bold text-dark"><?= (int) $stat['count'] ?></div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white">
                    <strong>دسترسی‌های سریع</strong>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        <?php if (hasPermission('view_customers')): ?>
                            <a href="<?= BASE_URL ?>admin/customers/index.php" class="btn btn-outline-primary btn-sm">مشتریان</a>
                        <?php endif; ?>
                        <?php if (hasPermission('view_leads')): ?>
                            <a href="<?= BASE_URL ?>sales/leads/index.php" class="btn btn-outline-success btn-sm">سرنخ‌ها</a>
                        <?php endif; ?>
                        <?php if (hasPermission('view_quotations')): ?>
                            <a href="<?= BASE_URL ?>sales/quotations/index.php" class="btn btn-outline-warning btn-sm">پیش‌فاکتورها</a>
                        <?php endif; ?>
                        <?php if (hasPermission('view_invoices')): ?>
                            <a href="<?= BASE_URL ?>sales/invoices/index.php" class="btn btn-outline-info btn-sm">فاکتورها</a>
                        <?php endif; ?>
                        <?php if (hasPermission('view_tickets')): ?>
                            <a href="<?= BASE_URL ?>tickets/index.php" class="btn btn-outline-danger btn-sm">تیکت‌ها</a>
                        <?php endif; ?>
                        <?php if (hasPermission('manage_users')): ?>
                            <a href="<?= BASE_URL ?>users/index.php" class="btn btn-outline-dark btn-sm">کاربران</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>