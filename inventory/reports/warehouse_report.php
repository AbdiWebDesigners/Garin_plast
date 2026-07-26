<?php
$rootPath = dirname(__DIR__, 2);
require_once $rootPath . '/includes/auth.php';
requirePermission('view_inventory');

$page_title = "گزارش انبارها";

$stmt = $pdo->query("
    SELECT w.*, COUNT(i.id) as item_count 
    FROM warehouses w 
    LEFT JOIN inventory i ON i.warehouse_id = w.id 
    GROUP BY w.id
");
$warehouses = $stmt->fetchAll();
?>

<?php require_once $rootPath . '/includes/header.php'; ?>

<div class="container mt-4">
    <h4>گزارش انبارها</h4>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>کد انبار</th>
                <th>نام انبار</th>
                <th>نوع</th>
                <th>تعداد کالا</th>
                <th>وضعیت</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($warehouses as $w): ?>
            <tr>
                <td><?= htmlspecialchars($w['warehouse_code']) ?></td>
                <td><?= htmlspecialchars($w['warehouse_name']) ?></td>
                <td><?= htmlspecialchars($w['warehouse_type']) ?></td>
                <td><strong><?= number_format($w['item_count']) ?></strong></td>
                <td>
                    <span class="badge bg-<?= $w['status']=='active' ? 'success' : 'danger' ?>">
                        <?= $w['status']=='active' ? 'فعال' : 'غیرفعال' ?>
                    </span>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once $rootPath . '/includes/footer.php'; ?>