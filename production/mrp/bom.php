<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();

$production_order_id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("
    SELECT po.*, p.title as product_name 
    FROM production_orders po 
    LEFT JOIN products p ON p.id = po.product_id 
    WHERE po.id = ?
");
$stmt->execute([$production_order_id]);
$order = $stmt->fetch();

if (!$order) {
    die("دستور تولید یافت نشد.");
}

// محاسبه مواد
$materials = $pdo->prepare("
    SELECT i.item_name, b.quantity_per_unit * ? as required_quantity, i.unit
    FROM product_bom b
    LEFT JOIN inventory i ON i.id = b.material_id
    WHERE b.product_id = ?
");
$materials->execute([$order['quantity'], $order['product_id']]);
$materials = $materials->fetchAll();
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>محاسبه مواد مورد نیاز</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <h2>مواد مورد نیاز برای دستور #<?= $order['order_number'] ?></h2>
    <p><strong>محصول:</strong> <?= htmlspecialchars($order['product_name']) ?></p>
    <p><strong>تعداد:</strong> <?= $order['quantity'] ?> <?= $order['unit'] ?? 'عدد' ?></p>

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ماده اولیه</th>
                <th>مقدار مورد نیاز</th>
                <th>واحد</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($materials as $m): ?>
            <tr>
                <td><?= htmlspecialchars($m['item_name']) ?></td>
                <td><?= number_format($m['required_quantity'], 2) ?></td>
                <td><?= $m['unit'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>