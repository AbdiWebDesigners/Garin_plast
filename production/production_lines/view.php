<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireLogin();

$id = (int)($_GET['id'] ?? 0);

if ($id === 0) {
    die("دستور تولید یافت نشد.");
}

$stmt = $pdo->prepare("
    SELECT po.*, p.title as product_name 
    FROM production_orders po 
    LEFT JOIN products p ON p.id = po.product_id 
    WHERE po.id = ?
");
$stmt->execute([$id]);
$order = $stmt->fetch();

if (!$order) {
    die("دستور تولید یافت نشد.");
}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>دستور تولید #<?= $order['order_number'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <div class="card shadow">
        <div class="card-header bg-info text-white">
            <h4>جزئیات دستور تولید #<?= htmlspecialchars($order['order_number']) ?></h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>محصول:</strong> <?= htmlspecialchars($order['product_name'] ?? '-') ?></p>
                    <p><strong>تعداد:</strong> <?= $order['quantity'] ?> <?= $order['unit'] ?? 'عدد' ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>وضعیت:</strong> <span class="badge bg-<?= $order['status']=='completed' ? 'success' : 'warning' ?>"><?= $order['status'] ?></span></p>
                    <p><strong>تاریخ شروع:</strong> <?= $order['start_date'] ?? '-' ?></p>
                    <p><strong>تاریخ پایان:</strong> <?= $order['end_date'] ?? '-' ?></p>
                </div>
            </div>

            <hr>
            <a href="index.php" class="btn btn-secondary">بازگشت به لیست</a>
        </div>
    </div>
</div>
</body>
</html>