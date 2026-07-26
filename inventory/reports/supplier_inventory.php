<?php
$rootPath = dirname(__DIR__, 2);
require_once $rootPath . '/includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();
requirePermission('view_inventory');

$page_title = "موجودی تامین‌کنندگان";

try {
    $suppliers = $pdo->query("
        SELECT 
            s.company_name,
            COUNT(gr.id) as receipt_count,
            COALESCE(SUM(gr.total_amount), 0) as total_value
        FROM suppliers s
        LEFT JOIN goods_receipts gr ON gr.supplier_id = s.id
        GROUP BY s.id, s.company_name
        ORDER BY total_value DESC
    ")->fetchAll();
} catch(Exception $e) {
    $suppliers = [];
}
?>

<?php require_once $rootPath . '/includes/header.php'; ?>

<div class="container mt-4">
    <h4><i class="fas fa-truck"></i> موجودی تامین‌کنندگان</h4>

    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>نام شرکت</th>
                        <th>تعداد رسید</th>
                        <th>ارزش کل دریافتی (تومان)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($suppliers)): ?>
                        <tr>
                            <td colspan="3" class="text-center py-4">اطلاعاتی یافت نشد.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($suppliers as $s): ?>
                        <tr>
                            <td><?= htmlspecialchars($s['company_name']) ?></td>
                            <td><?= number_format($s['receipt_count']) ?></td>
                            <td><?= number_format($s['total_value']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once $rootPath . '/includes/footer.php'; ?>