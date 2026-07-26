<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireLogin();

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("
    SELECT qc.*, po.order_number, p.title as product_name, u.fullname as checker_name
    FROM quality_controls qc
    LEFT JOIN production_orders po ON po.id = qc.production_order_id
    LEFT JOIN products p ON p.id = po.product_id
    LEFT JOIN users u ON u.id = qc.checked_by
    WHERE qc.id = ?
");
$stmt->execute([$id]);
$check = $stmt->fetch();

if (!$check) {
    die("کنترل کیفیت یافت نشد.");
}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>جزئیات کنترل کیفیت</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <div class="card shadow">
        <div class="card-header bg-info text-white">
            <h4>جزئیات کنترل کیفیت</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>دستور تولید:</strong> #<?= htmlspecialchars($check['order_number']) ?></p>
                    <p><strong>محصول:</strong> <?= htmlspecialchars($check['product_name'] ?? '-') ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>مرحله:</strong> <?= htmlspecialchars($check['stage']) ?></p>
                    <p><strong>نتیجه:</strong> <span class="badge bg-<?= $check['result']=='pass' ? 'success' : 'danger' ?>"><?= $check['result'] == 'pass' ? 'قبول' : 'رد' ?></span></p>
                </div>
            </div>
            <hr>
            <p><strong>یادداشت:</strong> <?= nl2br(htmlspecialchars($check['notes'] ?? '-')) ?></p>
            <p><strong>بررسی توسط:</strong> <?= htmlspecialchars($check['checker_name'] ?? '-') ?></p>
            <p><strong>تاریخ:</strong> <?= $check['checked_at'] ?></p>
        </div>
        <div class="card-footer">
            <a href="index.php" class="btn btn-secondary">بازگشت به لیست</a>
        </div>
    </div>
</div>
</body>
</html>