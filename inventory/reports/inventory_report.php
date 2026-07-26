<?php
$rootPath = dirname(__DIR__, 2);
require_once $rootPath . '/includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();
requirePermission('view_inventory');

$page_title = "گزارش موجودی انبار";

// آمار کلی
$totalItems = $pdo->query("SELECT COUNT(*) FROM inventory")->fetchColumn();
$totalQuantity = $pdo->query("SELECT COALESCE(SUM(quantity), 0) FROM inventory")->fetchColumn();
$criticalCount = $pdo->query("SELECT COUNT(*) FROM inventory WHERE quantity <= min_stock")->fetchColumn();
$zeroStockCount = $pdo->query("SELECT COUNT(*) FROM inventory WHERE quantity <= 0")->fetchColumn();

// کالاهای پرموجودی و کم‌موجودی
$lowStock = $pdo->query("
    SELECT * FROM inventory 
    WHERE quantity <= min_stock 
    ORDER BY quantity ASC 
    LIMIT 15
")->fetchAll();

$highStock = $pdo->query("
    SELECT * FROM inventory 
    WHERE quantity > min_stock * 3 
    ORDER BY quantity DESC 
    LIMIT 10
")->fetchAll();
?>

<?php require_once $rootPath . '/includes/header.php'; ?>

<div class="container mt-4">
    <h4><i class="fas fa-boxes"></i> گزارش موجودی انبار</h4>

    <!-- کارت‌های آماری -->
    <div class="row g-3 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5>تعداد کل کالاها</h5>
                    <h2><?= number_format($totalItems) ?></h2>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5>جمع موجودی</h5>
                    <h2><?= number_format($totalQuantity, 2) ?></h2>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <h5>کالاهای بحرانی</h5>
                    <h2><?= number_format($criticalCount) ?></h2>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5>موجودی صفر</h5>
                    <h2><?= number_format($zeroStockCount) ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- کالاهای کم موجودی -->
    <div class="card mb-4">
        <div class="card-header bg-danger text-white">
            <h5>کالاهای کم موجودی (نیاز به سفارش)</h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>کد کالا</th>
                        <th>نام کالا</th>
                        <th>موجودی فعلی</th>
                        <th>حداقل موجودی</th>
                        <th>وضعیت</th>
                    </tr>
                </thead>
                <tbody