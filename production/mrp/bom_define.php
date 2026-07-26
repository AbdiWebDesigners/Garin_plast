<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = (int)$_POST['product_id'];
    $material_id = (int)$_POST['material_id'];
    $quantity_per_unit = (float)$_POST['quantity_per_unit'];

    $stmt = $pdo->prepare("INSERT INTO product_bom (product_id, material_id, quantity_per_unit) VALUES (?, ?, ?)");
    $stmt->execute([$product_id, $material_id, $quantity_per_unit]);

    $success = "ماده اولیه اضافه شد.";
}

// لیست محصولات
$products = $pdo->query("SELECT id, title FROM products")->fetchAll();

// لیست مواد اولیه
$materials = $pdo->query("SELECT id, item_name FROM inventory")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تعریف BOM برای محصول</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4>تعریف مواد اولیه برای محصول (BOM)</h4>
        </div>
        <div class="card-body">
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label>محصول</label>
                    <select name="product_id" class="form-select" required>
                        <option value="">انتخاب محصول (مثلاً نایلون دسته دار شفاف)</option>
                        <?php foreach ($products as $p): ?>
                            <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label>ماده اولیه</label>
                    <select name="material_id" class="form-select" required>
                        <option value="">انتخاب ماده اولیه</option>
                        <?php foreach ($materials as $m): ?>
                            <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['item_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label>مقدار مورد نیاز برای ۱ واحد محصول</label>
                    <input type="number" name="quantity_per_unit" step="0.001" class="form-control" placeholder="مثال: 1.05 کیلو گرانول برای ۱ کیلو نایلون" required>
                </div>
                <button type="submit" class="btn btn-primary">اضافه کردن به BOM</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>