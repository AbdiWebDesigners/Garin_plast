<?php
require_once __DIR__ . '/../../includes/header.php';
requireLogin();
requirePermission('manage_orders');

$page_title = 'مدیریت سفارش‌ها';

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
?>

<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>سفارش‌ها</h3>
        <a href="create.php" class="btn btn-success">سفارش جدید</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>شماره سفارش</th>
                    <th>مشتری</th>
                    <th>مبلغ</th>
                    <th>وضعیت</th>
                    <th>تاریخ</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->query("
                    SELECT o.*, c.company_name 
                    FROM orders o 
                    LEFT JOIN customers c ON c.id = o.customer_id 
                    ORDER BY o.id DESC
                ");
                while ($order = $stmt->fetch()):
                ?>
                <tr>
                    <td><?= $order['id'] ?></td>
                    <td><?= htmlspecialchars($order['order_number'] ?? 'INV-' . $order['id']) ?></td>
                    <td><?= htmlspecialchars($order['company_name'] ?? 'مشتری ناشناس') ?></td>
                    <td><?= number_format($order['total_price'] ?? 0) ?> تومان</td>
                    <td><?= statusBadge($order['status']) ?></td>
                    <td><?= $order['created_at'] ?></td>
                    <td>
                        <a href="view.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-info">جزئیات</a>
                        <a href="update-status.php?id=<?= $order['id'] ?>&status=processing" class="btn btn-sm btn-warning">در حال انجام</a>
                        <a href="update-status.php?id=<?= $order['id'] ?>&status=completed" class="btn btn-sm btn-success">تکمیل</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>