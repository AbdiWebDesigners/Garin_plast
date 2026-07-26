<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

$pageTitle = 'سفارش‌های من';

$customer = null;
$orders = [];

try {
    if (isset($_SESSION['user_id'])) {
        $stmt = $pdo->prepare("SELECT * FROM customers WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $customer = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    if ($customer) {
        $stmt = $pdo->prepare("
            SELECT id, order_number, status, total_price, notes, created_at
            FROM orders
            WHERE customer_id = ?
            ORDER BY id DESC
        ");
        $stmt->execute([$customer['id']]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    die("خطا در دریافت سفارش‌ها: " . $e->getMessage());
}

function statusBadge($status) {
    if ($status === 'completed') return '<span class="badge bg-success">تکمیل شده</span>';
    if ($status === 'processing') return '<span class="badge bg-warning text-dark">در حال انجام</span>';
    return '<span class="badge bg-secondary">در انتظار</span>';
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
                <h4 class="mb-0 text-dark"><i class="fas fa-shopping-cart me-2"></i>سفارش‌های من</h4>
            </div>
            <div class="col-md-6 text-end">
                <a href="../customers/index.php" class="btn btn-sm btn-outline-secondary">بازگشت به پنل</a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-4 py-2">
    <div class="bg-white rounded-4 shadow-sm p-4">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>شماره سفارش</th>
                        <th>وضعیت</th>
                        <th>مبلغ کل</th>
                        <th>توضیحات</th>
                        <th>تاریخ ثبت</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">سفارشی یافت نشد.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?= (int)$order['id'] ?></td>
                                <td>INV-<?= str_pad((string)$order['id'], 5, '0', STR_PAD_LEFT) ?></td>
                                <td><?= statusBadge($order['status'] ?? 'pending') ?></td>
                                <td><?= number_format((float)($order['total_price'] ?? 0)) ?> تومان</td>
                                <td><?= nl2br(htmlspecialchars($order['notes'] ?? '-')) ?></td>
                                <td class="text-muted small"><?= htmlspecialchars($order['created_at'] ?? '-') ?></td>
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