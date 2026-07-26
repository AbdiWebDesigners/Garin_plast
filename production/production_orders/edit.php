<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireLogin();

$id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = (int)$_POST['product_id'];
    $quantity = (float)$_POST['quantity'];
    $unit = $_POST['unit'];
    $start_date = $_POST['start_date'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE production_orders SET product_id=?, quantity=?, unit=?, start_date=?, status=? WHERE id=?");
    $stmt->execute([$product_id, $quantity, $unit, $start_date, $status, $id]);

    header("Location: index.php");
    exit;
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

$products = $pdo->query("SELECT id, title FROM products")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>ویرایش دستور تولید</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card shadow">
        <div class="card-header bg-warning text-dark">
            <h4>ویرایش دستور تولید</h4>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label>محصول</label>
                    <select name="product_id" class="form-select" required>
                        <?php foreach ($products as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= $p['id']==$order['product_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['title']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>مقدار</label>
                        <input type="number" name="quantity" step="0.01" class="form-control" value="<?= $order['quantity'] ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>واحد</label>
                        <select name="unit" class="form-select">
                            <option value="تعداد" <?= $order['unit']=='تعداد' ? 'selected' : '' ?>>تعداد</option>
                            <option value="تن" <?= $order['unit']=='تن' ? 'selected' : '' ?>>تن</option>
                            <option value="کیلوگرم" <?= $order['unit']=='کیلوگرم' ? 'selected' : '' ?>>کیلوگرم</option>
                            <option value="متر" <?= $order['unit']=='متر' ? 'selected' : '' ?>>متر</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label>تاریخ شروع</label>
                    <input type="date" name="start_date" class="form-control" value="<?= $order['start_date'] ?>">
                </div>
                <div class="mb-3">
                    <label>وضعیت</label>
                    <select name="status" class="form-select">
                        <option value="planned" <?= $order['status']=='planned' ? 'selected' : '' ?>>برنامه‌ریزی شده</option>
                        <option value="in_progress" <?= $order['status']=='in_progress' ? 'selected' : '' ?>>در حال اجرا</option>
                        <option value="completed" <?= $order['status']=='completed' ? 'selected' : '' ?>>تکمیل شده</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-warning">به‌روزرسانی</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>