<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

if (empty($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit;
}

$pageTitle = 'مدیریت سفارش‌ها';

try {
    $stmt = $pdo->query("
        SELECT 
            o.id,
            o.customer_id,
            o.order_number,
            o.status,
            o.total_price,
            o.notes,
            o.created_at,
            c.company_name AS customer_name
        FROM orders o
        LEFT JOIN customers c ON c.id = o.customer_id
        ORDER BY o.id DESC
    ");
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("خطا در دریافت سفارش‌ها: " . $e->getMessage());
}

$q = trim($_GET['q'] ?? '');

if ($q !== '') {
    $orders = array_values(array_filter($orders, function ($o) use ($q) {
        return stripos((string)($o['order_number'] ?? ''), $q) !== false
            || stripos((string)($o['customer_name'] ?? ''), $q) !== false
            || stripos((string)($o['notes'] ?? ''), $q) !== false;
    }));
}

$totalOrders = count($orders);
$processingCount = 0;
$completedCount = 0;
$totalRevenue = 0;

foreach ($orders as $o) {
    if (($o['status'] ?? '') === 'processing') {
        $processingCount++;
    }
    if (($o['status'] ?? '') === 'completed') {
        $completedCount++;
    }
    $totalRevenue += (float)($o['total_price'] ?? 0);
}

function statusBadge($status) {
    if ($status === 'completed') {
        return '<span class="badge bg-success-subtle text-success border border-success-subtle">تکمیل شده</span>';
    }
    if ($status === 'processing') {
        return '<span class="badge bg-warning-subtle text-warning border border-warning-subtle">در حال انجام</span>';
    }
    if ($status === 'cancelled') {
        return '<span class="badge bg-danger-subtle text-danger border border-danger-subtle">لغو شده</span>';
    }
    return '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">در انتظار</span>';
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-body-secondary text-dark">

<div class="py-3 mb-4 bg-light border-bottom shadow-sm">
    <div class="container-fluid px-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h4 class="mb-0">
                    <a href="admin/dashboard.php" class="text-dark text-decoration-none">
                        <i class="fas fa-home me-2"></i>پنل مدیریت
                    </a>
                </h4>
            </div>
            <div class="col-md-6 text-end">
                <a href="logout.php" class="btn btn-sm btn-danger">
                    <i class="fas fa-sign-out-alt me-1"></i>خروج
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-4 py-2">
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="fw-bold text-dark mb-1">مدیریت سفارش‌ها</h3>
            <p class="text-muted small mb-0">نمایش و کنترل وضعیت سفارشات مشتریان</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card bg-white text-dark shadow-sm border-0 rounded-4">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted small fw-bold">کل سفارش‌ها</h6>
                        <h2 class="fw-bold mb-0 mt-1"><?= $totalOrders ?></h2>
                    </div>
                    <i class="fas fa-shopping-basket fa-2x text-primary opacity-75"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-white text-dark shadow-sm border-0 rounded-4">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted small fw-bold">در حال انجام</h6>
                        <h2 class="fw-bold mb-0 mt-1"><?= $processingCount ?></h2>
                    </div>
                    <i class="fas fa-industry fa-2x text-warning opacity-75"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-white text-dark shadow-sm border-0 rounded-4">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted small fw-bold">تکمیل شده</h6>
                        <h2 class="fw-bold mb-0 mt-1"><?= $completedCount ?></h2>
                    </div>
                    <i class="fas fa-check-double fa-2x text-success opacity-75"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-white text-dark shadow-sm border-0 rounded-4">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted small fw-bold">مجموع فروش</h6>
                        <h2 class="fw-bold mb-0 mt-1 fs-4"><?= number_format($totalRevenue) ?> تومان</h2>
                    </div>
                    <i class="fas fa-wallet fa-2x text-info opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white text-dark rounded-4 shadow-sm p-4 mb-5">
        <div class="row mb-3">
            <div class="col-md-6">
                <form method="get" class="d-flex gap-2">
                    <input type="text" name="q" class="form-control" placeholder="جستجو در شماره سفارش یا نام مشتری..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show border-0 mb-3" role="alert">
                تغییر وضعیت سفارش با موفقیت انجام شد.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="70" class="text-center">#</th>
                        <th>شماره سفارش</th>
                        <th>نام مشتری</th>
                        <th>مبلغ کل</th>
                        <th>وضعیت</th>
                        <th>توضیحات</th>
                        <th>تاریخ ثبت</th>
                        <th width="240" class="text-center">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-3x d-block mb-3"></i>
                                هیچ رکوردی از سفارشات در دیتابیس یافت نشد.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $o): ?>
                            <tr>
                                <td class="text-center fw-bold text-muted"><?= (int)$o['id'] ?></td>
                                <td>
                                    <span class="badge bg-dark text-info border border-secondary px-2 py-1 fs-6">
                                        <?= htmlspecialchars($o['order_number'] ?? ('ORD-' . str_pad((string)$o['id'], 5, '0', STR_PAD_LEFT))) ?>
                                    </span>
                                </td>
                                <td class="fw-semibold">
                                    <?= htmlspecialchars($o['customer_name'] ?? 'مشتری عمومی') ?>
                                </td>
                                <td class="text-success fw-bold">
                                    <?= number_format((float)($o['total_price'] ?? 0)) ?> تومان
                                </td>
                                <td>
                                    <?= statusBadge($o['status'] ?? 'pending') ?>
                                </td>
                                <td class="text-muted small">
                                    <?= nl2br(htmlspecialchars($o['notes'] ?? '-')) ?>
                                </td>
                                <td class="text-muted small">
                                    <?= htmlspecialchars($o['created_at'] ?? '-') ?>
                                </td>
                                <td class="text-center">
                                    <a href="view.php?id=<?= (int)$o['id'] ?>" class="btn btn-sm btn-info text-white me-1" title="مشاهده">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="update-status.php?id=<?= (int)$o['id'] ?>&status=processing" class="btn btn-sm btn-outline-warning me-1" title="در حال انجام">
                                        <i class="fas fa-industry"></i>
                                    </a>
                                    <a href="update-status.php?id=<?= (int)$o['id'] ?>&status=completed" class="btn btn-sm btn-outline-success me-1" title="تکمیل شده">
                                        <i class="fas fa-check"></i>
                                    </a>
                                    <a href="update-status.php?id=<?= (int)$o['id'] ?>&status=cancelled" class="btn btn-sm btn-outline-danger" title="لغو">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>