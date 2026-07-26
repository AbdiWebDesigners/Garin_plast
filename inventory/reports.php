<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();

// کالاهای کم موجودی
$lowStock = $pdo->query("
    SELECT * FROM inventory 
    WHERE quantity <= min_stock 
    ORDER BY quantity ASC
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>گزارش انبار</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <h2>گزارش انبار</h2>

    <h4 class="mt-4">کالاهای کم موجودی (نیاز به سفارش)</h4>
    <?php if (empty($lowStock)): ?>
        <div class="alert alert-success">همه کالاها موجودی کافی دارند.</div>
    <?php else: ?>
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>نام کالا</th>
                    <th>موجودی فعلی</th>
                    <th>حداقل موجودی</th>
                    <th>واحد</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lowStock as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['item_name']) ?></td>
                    <td><?= number_format($item['quantity'], 2) ?></td>
                    <td><?= number_format($item['min_stock'], 2) ?></td>
                    <td><?= $item['unit'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>