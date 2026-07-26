<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

$pageTitle = 'سبد خرید | گَرین پلاست';
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include '../includes/navbar.php'; ?>

<div class="container py-5">

    <h2 class="mb-4">سبد خرید</h2>

    <?php if (empty($_SESSION['cart'])): ?>

        <div class="alert alert-warning">
            سبد خرید خالی است.
        </div>

    <?php else: ?>

        <table class="table table-bordered table-striped align-middle">

            <thead class="table-dark">
            <tr>
                <th>ردیف</th>
                <th>محصول</th>
                <th>تعداد</th>
                <th>قیمت واحد</th>
                <th>جمع</th>
            </tr>
            </thead>

            <tbody>

            <?php

            $i = 1;
            $total = 0;

            foreach ($_SESSION['cart'] as $item):

                // بررسی وجود product_id
                if (!isset($item['product_id'])) {
                    die("خطا: product_id برای یکی از محصولات وجود ندارد.");
                }

                $qty      = (int)$item['quantity'];
                $price    = (float)$item['price'];
                $subtotal = $qty * $price;

                $total += $subtotal;
            ?>

                <tr>

                    <td><?= $i++ ?></td>

                    <td><?= htmlspecialchars($item['title']) ?></td>

                    <td><?= $qty ?></td>

                    <td><?= number_format($price) ?> تومان</td>

                    <td><?= number_format($subtotal) ?> تومان</td>

                </tr>

            <?php endforeach; ?>

            </tbody>

        </table>

        <div class="alert alert-success">

            <h4>
                جمع کل :
                <?= number_format($total) ?> تومان
            </h4>

        </div>

        <a href="checkout.php" class="btn btn-success btn-lg">
            ادامه و ثبت سفارش
        </a>

    <?php endif; ?>

</div>

<?php include '../includes/footer.php'; ?>

</body>
</html>