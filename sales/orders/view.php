<?php
require_once __DIR__ . '/../../includes/header.php';
requireLogin();
requirePermission('manage_orders');

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: index.php");
    exit;
}

$page_title = 'جزئیات سفارش #' . $id;

function statusBadge($status) {
    $status = strtolower($status ?? 'pending');
    if ($status === 'completed') {
        return '<span class="badge bg-success">تکمیل شده</span>';
    }
    if ($status === 'processing') {
        return '<span class="badge bg-warning">در حال انجام</span>';
    }
    if ($status === 'cancelled') {
        return '<span class="badge bg-danger">لغو شده</span>';
    }
    return '<span class="badge bg-secondary">در انتظار</span>';
}

try {
    $stmt = $pdo->prepare("
        SELECT 
            o.*,
            c.company_name,
            c.manager_name,
            c.phone,
            c.email,
            c.address
        FROM orders o
        LEFT JOIN customers c ON c.id = o.customer_id
        WHERE o.id = ?
    ");
    $stmt->execute([$id]);
    $order = $stmt->fetch();

    if (!$order) {
        die('<div class="alert alert-danger">سفارش یافت نشد.</div>');
    }

    $itemsStmt = $pdo->prepare("
        SELECT 
            oi.*,
            p.title as product_title
        FROM order_items oi
        LEFT JOIN products p ON p.id = oi.product_id
        WHERE oi.order_id = ?
        ORDER BY oi.id
    ");
    $itemsStmt->execute([$id]);
    $items = $itemsStmt->fetchAll();
} catch (PDOException $e) {
    die("خطا: " . $e->getMessage());
}
?>

<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>جزئیات سفارش #<?= $order['id'] ?></h3>
        <div>
            <a href="index.php" class="btn btn-outline-secondary me-2">بازگشت به لیست</a>
            <a href="update-status.php?id=<?= $order['id'] ?>&status=processing" class="btn btn-warning me-1">در حال انجام</a>
            <a href="update-status.php?id=<?= $order['id'] ?>&status=completed" class="btn btn-success">تکمیل شده</a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5>اطلاعات سفارش</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>مشتری:</strong> <?= htmlspecialchars($order['company_name'] ?? '-') ?></p>
                            <p><strong>مدیر:</strong> <?= htmlspecialchars($order['manager_name'] ?? '-') ?></p>
                            <p><strong>تلفن:</strong> <?= htmlspecialchars($order['phone'] ?? '-') ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>شماره سفارش:</strong> <?= htmlspecialchars($order['order_number'] ?? 'INV-' . $order['id']) ?></p>
                            <p><strong>وضعیت:</strong> <?= statusBadge($order['status']) ?></p>
                            <p><strong>مبلغ کل:</strong> <span class="text-success fw-bold"><?= number_format($order['total_price'] ?? 0) ?> تومان</span></p>
                            <p><strong>تاریخ ثبت:</strong> <?= $order['created_at'] ?></p>
                        </div>
                    </div>
                    <hr>
                    <p><strong>آدرس تحویل:</strong> <?= nl2br(htmlspecialchars($order['address'] ?? '-')) ?></p>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-light">
                    <h5>آیتم‌های سفارش</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>محصول</th>
                                <th>تعداد</th>
                                <th>قیمت واحد</th>
                                <th>جمع</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['product_title']) ?></td>
                                <td><?= $item['qty'] ?></td>
                                <td><?= number_format($item['price']) ?> تومان</td>
                                <td><?= number_format($item['qty'] * $item['price']) ?> تومان</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>