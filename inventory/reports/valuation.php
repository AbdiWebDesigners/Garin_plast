<?php
$rootPath = dirname(__DIR__, 2);
require_once $rootPath . '/includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();
requirePermission('view_inventory');

$page_title = "ارزش موجودی انبار";

try {
    $totalValue = $pdo->query("
        SELECT COALESCE(SUM(i.quantity * COALESCE(i.unit_price, 0)), 0) as total
        FROM inventory i
    ")->fetchColumn();

    $valueByType = $pdo->query("
        SELECT 
            COALESCE(i.item_type, 'نامشخص') as item_type,
            COUNT(*) as count,
            COALESCE(SUM(i.quantity * COALESCE(i.unit_price, 0)), 0) as value
        FROM inventory i
        GROUP BY i.item_type
        ORDER BY value DESC
    ")->fetchAll();
} catch(Exception $e) {
    $totalValue = 0;
    $valueByType = [];
}
?>

<?php require_once $rootPath . '/includes/header.php'; ?>

<div class="container mt-4">
    <h4><i class="fas fa-calculator"></i> ارزش موجودی انبار</h4>

    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card text-center p-5 shadow">
                <h1 class="text-success"><?= number_format($totalValue) ?> تومان</h1>
                <p class="text-muted fs-5">ارزش کل موجودی انبار</p>
            </div>
        </div>
    </div>

    <?php if (empty($valueByType)): ?>
        <div class="alert alert-info">هنوز داده‌ای برای نمایش وجود ندارد.</div>
    <?php else: ?>
        <div class="card">
            <div class="card-header">ارزش موجودی بر اساس نوع کالا</div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>نوع کالا</th>
                            <th>تعداد کالا</th>
                            <th>ارزش (تومان)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($valueByType as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['item_type']) ?></td>
                            <td><?= number_format($row['count']) ?></td>
                            <td class="fw-bold"><?= number_format($row['value']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once $rootPath . '/includes/footer.php'; ?>