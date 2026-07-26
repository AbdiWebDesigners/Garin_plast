<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();

$id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_name = trim($_POST['item_name']);
    $quantity = (float)$_POST['quantity'];
    $unit = $_POST['unit'];
    $min_stock = (float)$_POST['min_stock'];

    $stmt = $pdo->prepare("UPDATE inventory SET item_name=?, quantity=?, unit=?, min_stock=? WHERE id=?");
    $stmt->execute([$item_name, $quantity, $unit, $min_stock, $id]);

    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM inventory WHERE id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch();

if (!$item) {
    die("کالا یافت نشد.");
}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>ویرایش موجودی</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card shadow">
        <div class="card-header bg-warning text-dark">
            <h4>ویرایش موجودی انبار</h4>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label>نام کالا</label>
                    <input type="text" name="item_name" class="form-control" value="<?= htmlspecialchars($item['item_name']) ?>" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>موجودی</label>
                        <input type="number" name="quantity" step="0.01" class="form-control" value="<?= $item['quantity'] ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>واحد</label>
                        <select name="unit" class="form-select">
                            <option value="کیلوگرم" <?= $item['unit']=='کیلوگرم' ? 'selected' : '' ?>>کیلوگرم</option>
                            <option value="تن" <?= $item['unit']=='تن' ? 'selected' : '' ?>>تن</option>
                            <option value="عدد" <?= $item['unit']=='عدد' ? 'selected' : '' ?>>عدد</option>
                            <option value="متر" <?= $item['unit']=='متر' ? 'selected' : '' ?>>متر</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label>حداقل موجودی</label>
                    <input type="number" name="min_stock" step="0.01" class="form-control" value="<?= $item['min_stock'] ?>">
                </div>
                <button type="submit" class="btn btn-warning">به‌روزرسانی</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>