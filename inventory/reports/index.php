<?php
$rootPath = dirname(__DIR__, 2);
require_once $rootPath . '/includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();
requirePermission('view_inventory');

$page_title = "گزارشات انبار";
?>

<?php require_once $rootPath . '/includes/header.php'; ?>

<div class="container mt-4">
    <h4><i class="fas fa-chart-bar"></i> گزارشات انبار</h4>

    <div class="row g-4 mt-3">
        <div class="col-lg-4 col-md-6">
            <a href="warehouse_report.php" class="text-decoration-none">
                <div class="card h-100 shadow-sm text-center p-4">
                    <i class="fas fa-warehouse fa-3x text-primary mb-3"></i>
                    <h5>گزارش انبارها</h5>
                </div>
            </a>
        </div>

        <div class="col-lg-4 col-md-6">
            <a href="valuation.php" class="text-decoration-none">
                <div class="card h-100 shadow-sm text-center p-4">
                    <i class="fas fa-calculator fa-3x text-success mb-3"></i>
                    <h5>ارزش موجودی</h5>
                </div>
            </a>
        </div>

        <div class="col-lg-4 col-md-6">
            <a href="supplier_inventory.php" class="text-decoration-none">
                <div class="card h-100 shadow-sm text-center p-4">
                    <i class="fas fa-truck fa-3x text-warning mb-3"></i>
                    <h5>موجودی تامین‌کنندگان</h5>
                </div>
            </a>
        </div>

        <div class="col-lg-4 col-md-6">
            <a href="stock_card.php" class="text-decoration-none">
                <div class="card h-100 shadow-sm text-center p-4">
                    <i class="fas fa-clipboard-list fa-3x text-info mb-3"></i>
                    <h5>کارتکس انبار</h5>
                </div>
            </a>
        </div>

        <div class="col-lg-4 col-md-6">
            <a href="inventory_report.php" class="text-decoration-none">
                <div class="card h-100 shadow-sm text-center p-4">
                    <i class="fas fa-boxes fa-3x text-danger mb-3"></i>
                    <h5>گزارش موجودی</h5>
                </div>
            </a>
        </div>
    </div>
</div>

<?php require_once $rootPath . '/includes/footer.php'; ?>