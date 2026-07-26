<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $production_order_id = (int)$_POST['production_order_id'];
    $stage = trim($_POST['stage']);
    $result = $_POST['result'];
    $notes = trim($_POST['notes'] ?? '');

    $stmt = $pdo->prepare("INSERT INTO quality_controls (production_order_id, stage, result, notes, checked_by) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$production_order_id, $stage, $result, $notes, $_SESSION['user_id']]);

    header("Location: index.php");
    exit;
}

$orders = $pdo->query("
    SELECT po.id, po.order_number, p.title as product_name 
    FROM production_orders po 
    LEFT JOIN products p ON p.id = po.product_id 
    WHERE po.status != 'completed'
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>ثبت کنترل کیفیت</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h4>ثبت کنترل کیفیت</h4>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label>دستور تولید</label>
                    <select name="production_order_id" class="form-select" required>
                        <option value="">انتخاب دستور تولید</option>
                        <?php foreach ($orders as $o): ?>
                            <option value="<?= $o['id'] ?>">#<?= $o['order_number'] ?> - <?= htmlspecialchars($o['product_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label>مرحله کنترل</label>
                    <input type="text" name="stage" class="form-control" placeholder="مواد اولیه / حین تولید / محصول نهایی" required>
                </div>
                <div class="mb-3">
                    <label>نتیجه</label>
                    <select name="result" class="form-select" required>
                        <option value="pass">قبول</option>
                        <option value="fail">رد</option>
                        <option value="warning">هشدار</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>یادداشت</label>
                    <textarea name="notes" class="form-control" rows="4"></textarea>
                </div>
                <button type="submit" class="btn btn-success">ثبت کنترل</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>