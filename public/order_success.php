<?php
session_start();
$order_id = (int)($_GET['order_id'] ?? 0);
$pageTitle = 'ثبت سفارش موفق | گارین پلاست';
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5 text-center">
    <div class="card mx-auto" style="max-width: 600px;">
        <div class="card-body py-5">
            <h1 class="text-success display-4">✅</h1>
            <h2 class="mb-4">سفارش با موفقیت ثبت شد!</h2>
            <h4>شماره سفارش: #<?= $order_id ?></h4>
            <p class="lead mb-4">سفارش شما ثبت شد. به زودی با شما تماس گرفته خواهد شد.</p>
            <a href="../index.php" class="btn btn-success btn-lg">بازگشت به صفحه اصلی</a>
        </div>
    </div>
</div>
</body>
</html>