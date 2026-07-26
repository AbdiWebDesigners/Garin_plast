<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_name = trim($_POST['item_name']);
    $quantity = (float)$_POST['quantity'];
    $unit = $_POST['unit'];
    $min_stock = (float)$_POST['min_stock'];

    $stmt = $pdo->prepare("INSERT INTO inventory (item_name, quantity, unit, min_stock) VALUES (?, ?, ?, ?)");
    $stmt->execute([$item_name, $quantity, $unit, $min_stock]);

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>ثبت کالا در انبار</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4>ثبت کالا جدید در انبار</h4>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label>نام کالا / ماده اولیه</label>
                    <input type="text" name="item_name" class="form-control" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>موجودی فعلی</label>
                        <input type="number" name="quantity" step="0.01" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>واحد</label>
                        <select name="unit" class="form-select">
                            <option value="کیلوگرم">کیلوگرم</option>
                            <option value="تن">تن</option>
                            <option value="عدد">عدد</option>
                            <option value="متر">متر</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label>حداقل موجودی (برای هشدار)</label>
                    <input type="number" name="min_stock" step="0.01" class="form-control" value="0">
                </div>
                <button type="submit" class="btn btn-primary">ثبت در انبار</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>