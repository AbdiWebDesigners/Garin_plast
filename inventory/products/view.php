<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$page_title = "جزئیات محصول";

require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/header.php';
require_once '../../includes/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("
    SELECT products.*, categories.title AS category_name
    FROM products
    LEFT JOIN categories ON products.category_id = categories.id
    WHERE products.id = ?
");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header("Location: index.php?error=notfound");
    exit;
}

/* گالری تصاویر اضافی (در صورت وجود جدول product_images) */
$gallery = [];
try {
    $stmt = $pdo->prepare("SELECT image FROM product_images WHERE product_id = ?");
    $stmt->execute([$id]);
    $gallery = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {
    $gallery = [];
}
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>جزئیات محصول</h2>
    <div>
        <a href="edit.php?id=<?= $product['id'] ?>" class="btn btn-warning">ویرایش</a>
        <a href="index.php" class="btn btn-secondary">بازگشت به فهرست</a>
    </div>
</div>

<div class="card">
    <div class="card-body">

        <div class="row">

            <div class="col-md-4 text-center mb-3">
                <?php if (!empty($product['image'])): ?>
                    <img
                        src="../../assets/uploads/products/<?= htmlspecialchars($product['image']) ?>"
                        class="img-fluid rounded mb-2"
                        style="max-height:280px;object-fit:cover;"
                    >
                <?php else: ?>
                    <div class="text-muted border rounded p-5">بدون تصویر</div>
                <?php endif; ?>

                <?php if (!empty($gallery)): ?>
                    <div class="d-flex flex-wrap gap-2 justify-content-center mt-2">
                        <?php foreach ($gallery as $img): ?>
                            <img
                                src="../../assets/uploads/products/<?= htmlspecialchars($img) ?>"
                                width="60" height="60"
                                style="object-fit:cover;border-radius:6px;"
                            >
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-md-8">

                <h3><?= htmlspecialchars($product['title']) ?></h3>

                <table class="table table-borderless">
                    <tr>
                        <th width="150">کد محصول (SKU)</th>
                        <td><?= htmlspecialchars($product['sku'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th>دسته‌بندی</th>
                        <td><?= htmlspecialchars($product['category_name'] ?? 'بدون دسته') ?></td>
                    </tr>
                    <tr>
                        <th>قیمت</th>
                        <td><?= number_format($product['price'], 0) ?> تومان</td>
                    </tr>
                    <tr>
                        <th>موجودی</th>
                        <td><?= (int)$product['stock'] ?> عدد</td>
                    </tr>
                    <tr>
                        <th>وضعیت</th>
                        <td>
                            <?php if ($product['status'] === 'active'): ?>
                                <span class="badge bg-success">فعال</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">غیرفعال</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>تاریخ ثبت</th>
                        <td><?= htmlspecialchars($product['created_at'] ?? '-') ?></td>
                    </tr>
                </table>

                <h5>توضیحات</h5>
                <p><?= nl2br(htmlspecialchars($product['description'] ?? 'بدون توضیحات')) ?></p>

            </div>

        </div>

    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
