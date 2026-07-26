<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

$pageTitle = 'تکمیل خرید | گَرین پلاست ';

// محاسبه جمع کل
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $price = (float)$item['price'];
    $qty   = (int)$item['quantity'];
    $total += $price * $qty;
}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<?php include '../includes/navbar.php'; ?>

<div class="container py-5">

    <div class="row">

        <!-- سبد خرید -->
        <div class="col-lg-7">

            <h3 class="mb-3">سبد خرید</h3>

            <table class="table table-bordered">

                <thead class="table-dark">
                    <tr>
                        <th>محصول</th>
                        <th>تعداد</th>
                        <th>قیمت</th>
                        <th>جمع</th>
                    </tr>
                </thead>

                <tbody>

                <?php foreach($_SESSION['cart'] as $item):

                    $price = (float)$item['price'];
                    $qty   = (int)$item['quantity'];
                    $sum   = $price * $qty;

                ?>

                    <tr>

                        <td><?= htmlspecialchars($item['title']) ?></td>

                        <td><?= $qty ?></td>

                        <td><?= number_format($price) ?> تومان</td>

                        <td><?= number_format($sum) ?> تومان</td>

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

        </div>

        <!-- فرم ثبت سفارش -->
        <div class="col-lg-5">

            <div class="card">

                <div class="card-header bg-success text-white">
                    اطلاعات خریدار
                </div>

                <div class="card-body">

                    <form action="process_order.php" method="POST">

                        <div class="mb-3">

                            <label class="form-label">
                                نام و نام خانوادگی
                            </label>

                            <input
                                type="text"
                                name="full_name"
                                class="form-control"
                                required>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                شماره تماس
                            </label>

                            <input
                                type="text"
                                name="phone"
                                class="form-control"
                                required>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                آدرس
                            </label>

                            <textarea
                                name="address"
                                class="form-control"
                                rows="5"
                                required></textarea>

                        </div>

                        <button
                            type="submit"
                            class="btn btn-success w-100 btn-lg">

                            ثبت نهایی سفارش

                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

<?php include '../includes/footer.php'; ?>

</body>
</html>