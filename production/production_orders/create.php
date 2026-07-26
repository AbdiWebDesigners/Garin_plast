<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_number = 'PO-' . time();
    $product_id = (int)$_POST['product_id'];
    $quantity = (float)$_POST['quantity'];
    $unit = $_POST['unit'] ?? 'تعداد';
    $start_date = $_POST['start_date'] ?? null;

    $stmt = $pdo->prepare("INSERT INTO production_orders (order_number, product_id, quantity, unit, start_date, status) VALUES (?, ?, ?, ?, ?, 'planned')");
    $stmt->execute([$order_number, $product_id, $quantity, $unit, $start_date]);

    header("Location: index.php");
    exit;
}

$products = $pdo->query("SELECT id, title FROM products WHERE status = 'active'")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>ایجاد دستور تولید</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4>دستور تولید جدید</h4>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label>محصول</label>
                    <select name="product_id" class="form-select" required>
                        <option value="">انتخاب محصول</option>
                        <?php foreach ($products as $p): ?>
                            <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>مقدار</label>
                        <input type="number" name="quantity" step="0.01" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>واحد</label>
                        <select name="unit" class="form-select">
                            <option value="تعداد">تعداد</option>
                            <option value="تن">تن</option>
                            <option value="کیلوگرم">کیلوگرم</option>
                            <option value="متر">متر</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label>تاریخ شروع</label>
                    <input type="date" name="start_date" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">ثبت دستور تولید</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>