<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once __DIR__ . '/../includes/db.php';

$success = false;
$product_title = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = (int)$_POST['product_id'];

    try {
        $stmt = $pdo->prepare("SELECT id, title, price FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();

        if ($product) {
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            $id = (int)$product['id'];

            if (isset($_SESSION['cart'][$id])) {
                $_SESSION['cart'][$id]['quantity']++;
            } else {
                $_SESSION['cart'][$id] = [
                    'product_id' => $id,
                    'title' => $product['title'],
                    'price' => $product['price'],
                    'quantity' => 1
                ];
            }

            $success = true;
            $product_title = $product['title'];
        } else {
            $error = 'محصول پیدا نشد.';
        }
    } catch (Throwable $e) {
        $error = 'خطا: ' . $e->getMessage();
    }
} else {
    $error = 'درخواست معتبر نیست یا product_id ارسال نشده است.';
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اضافه به سبد خرید</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5 text-center">
    <?php if ($success): ?>
        <div class="alert alert-success">
            <h4>✅ محصول "<?= htmlspecialchars($product_title) ?>" با موفقیت به سبد خرید اضافه شد.</h4>
        </div>
    <?php else: ?>
        <div class="alert alert-danger">
            <h4>❌ <?= htmlspecialchars($error ?: 'خطا در اضافه کردن محصول.') ?></h4>
        </div>
    <?php endif; ?>

    <a href="products.php" class="btn btn-success btn-lg">بازگشت به محصولات</a>
    <a href="cart.php" class="btn btn-primary btn-lg ms-3">مشاهده سبد خرید</a>
</div>
</body>
</html>