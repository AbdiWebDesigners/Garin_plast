<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

$pageTitle = 'پنل مشتری';

$customerName = 'مشتری گرامی';
$customerId = null;
$customerEmail = '';
$customerCompany = '';

try {
    if (isset($_SESSION['user_id'])) {
        $stmt = $pdo->prepare("SELECT * FROM customers WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $customer = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($customer) {
            $customerId = $customer['id'];
            $customerName = $customer['manager_name'] ?: ($customer['company_name'] ?: 'مشتری گرامی');
            $customerEmail = $customer['email'] ?? '';
            $customerCompany = $customer['company_name'] ?? '';
        }
    }
} catch (PDOException $e) {
    $customer = null;
}

$totalOrders = 0;
$pendingOrders = 0;
$completedOrders = 0;
$totalQuotes = 0;

if ($customerId) {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE customer_id = ?");
        $stmt->execute([$customerId]);
        $totalOrders = (int) $stmt->fetchColumn();

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE customer_id = ? AND status = 'pending'");
        $stmt->execute([$customerId]);
        $pendingOrders = (int) $stmt->fetchColumn();

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE customer_id = ? AND status = 'completed'");
        $stmt->execute([$customerId]);
        $completedOrders = (int) $stmt->fetchColumn();

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM quotations WHERE customer_id = ?");
        $stmt->execute([$customerId]);
        $totalQuotes = (int) $stmt->fetchColumn();
    } catch (PDOException $e) {
        // silent
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-body-secondary text-dark">

<div class="py-3 mb-4 bg-light border-bottom shadow-sm">
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="mb-0 text-dark">
                    <i class="fas fa-user-circle me-2"></i>پنل مشتری
                </h4>
            </div>

            <div class="d-flex gap-2">
<a href="<?= BASE_URL ?>admin/dashboard.php" class="text-dark text-decoration-none">  بازگشت به داشبورد </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-4 py-2">
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="fw-bold text-dark mb-1">سلام، <?= htmlspecialchars($customerName) ?></h3>
            <p class="text-muted small mb-0">
                <?= $customerCompany ? 'شرکت: ' . htmlspecialchars($customerCompany) : 'به پنل کاربری شما خوش آمدید.' ?>
            </p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card bg-white text-dark shadow-sm border-0 rounded-4">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted small fw-bold">کل سفارش‌ها</h6>
                        <h2 class="fw-bold mb-0 mt-1"><?= $totalOrders ?></h2>
                    </div>
                    <i class="fas fa-shopping-basket fa-2x text-primary opacity-75"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-white text-dark shadow-sm border-0 rounded-4">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted small fw-bold">سفارش‌های در انتظار</h6>
                        <h2 class="fw-bold mb-0 mt-1"><?= $pendingOrders ?></h2>
                    </div>
                    <i class="fas fa-clock fa-2x text-warning opacity-75"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-white text-dark shadow-sm border-0 rounded-4">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted small fw-bold">سفارش‌های تکمیل‌شده</h6>
                        <h2 class="fw-bold mb-0 mt-1"><?= $completedOrders ?></h2>
                    </div>
                    <i class="fas fa-check-double fa-2x text-success opacity-75"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-white text-dark shadow-sm border-0 rounded-4">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted small fw-bold">پیش‌فاکتورها</h6>
                        <h2 class="fw-bold mb-0 mt-1"><?= $totalQuotes ?></h2>
                    </div>
                    <i class="fas fa-file-invoice fa-2x text-info opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
          <a href="../orders/index.php" class="text-decoration-none">
                <div class="card bg-white text-dark shadow-sm border-0 rounded-4 h-100">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-shopping-cart fa-3x text-primary mb-3"></i>
                        <h5 class="fw-bold">سفارش های مشتریان</h5>
                        <p class="text-muted mb-0">مشاهده وضعیت و جزئیات سفارش‌ها</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="profile/index.php" class="text-decoration-none">
                <div class="card bg-white text-dark shadow-sm border-0 rounded-4 h-100">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-user-edit fa-3x text-success mb-3"></i>
                        <h5 class="fw-bold">پروفایل مشتریان</h5>
                        <p class="text-muted mb-0">ویرایش اطلاعات شخصی و شرکت</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="quotations/index.php" class="text-decoration-none">
                <div class="card bg-white text-dark shadow-sm border-0 rounded-4 h-100">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-file-contract fa-3x text-warning mb-3"></i>
                        <h5 class="fw-bold">پیش‌فاکتورها</h5>
                        <p class="text-muted mb-0">دسترسی به پیش‌فاکتورهای ثبت‌شده</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>