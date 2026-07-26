<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '../includes/config.php';
require_once __DIR__ . '../includes/auth.php';
require_once __DIR__ . '../includes/db.php';

requireLogin();   // حالا باید کار کنه

$pageTitle = 'داشبورد مدیریت';

$totalLeads = 0;
$newLeads = 0;
$contactedLeads = 0;
$totalQuotations = 0;
$draftQuotations = 0;
$sentQuotations = 0;
$totalCustomers = 0;
$totalProducts = 0;
$totalOrders = 0;
$pendingOrders = 0;
$processingOrders = 0;
$completedOrders = 0;
$totalInvoices = 0;
$unpaidInvoices = 0;
$paidInvoices = 0;
$totalPayments = 0;
$pendingPayments = 0;
$successfulPayments = 0;

$recentLeads = [];
$recentQuotations = [];
$recentOrders = [];
$recentInvoices = [];
$recentPayments = [];

try {
    $totalLeads = (int)$pdo->query("SELECT COUNT(*) FROM sales_leads")->fetchColumn();
    $newLeads = (int)$pdo->query("SELECT COUNT(*) FROM sales_leads WHERE status = 'new'")->fetchColumn();
    $contactedLeads = (int)$pdo->query("SELECT COUNT(*) FROM sales_leads WHERE status = 'contacted'")->fetchColumn();

    $totalQuotations = (int)$pdo->query("SELECT COUNT(*) FROM quotations")->fetchColumn();
    $draftQuotations = (int)$pdo->query("SELECT COUNT(*) FROM quotations WHERE status = 'draft'")->fetchColumn();
    $sentQuotations = (int)$pdo->query("SELECT COUNT(*) FROM quotations WHERE status = 'quotation_sent'")->fetchColumn();

    $totalCustomers = (int)$pdo->query("SELECT COUNT(*) FROM customers")->fetchColumn();
    $totalProducts = (int)$pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();

    $totalOrders = (int)$pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    $pendingOrders = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
    $processingOrders = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'processing'")->fetchColumn();
    $completedOrders = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'completed'")->fetchColumn();

    $totalInvoices = (int)$pdo->query("SELECT COUNT(*) FROM invoices")->fetchColumn();
    $unpaidInvoices = (int)$pdo->query("SELECT COUNT(*) FROM invoices WHERE status = 'unpaid'")->fetchColumn();
    $paidInvoices = (int)$pdo->query("SELECT COUNT(*) FROM invoices WHERE status = 'paid'")->fetchColumn();

    $totalPayments = (int)$pdo->query("SELECT COUNT(*) FROM payments")->fetchColumn();
    $pendingPayments = (int)$pdo->query("SELECT COUNT(*) FROM payments WHERE status = 'pending'")->fetchColumn();
    $successfulPayments = (int)$pdo->query("SELECT COUNT(*) FROM payments WHERE status = 'successful'")->fetchColumn();

    $recentLeads = $pdo->query("
        SELECT id, customer_name, company_name, phone, email, source, status, created_at
        FROM sales_leads
        ORDER BY id DESC
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);

    $recentQuotations = $pdo->query("
        SELECT q.id, q.quotation_number, q.total_price, q.final_price, q.status, q.created_at, c.company_name
        FROM quotations q
        LEFT JOIN customers c ON c.id = q.customer_id
        ORDER BY q.id DESC
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);

    $recentOrders = $pdo->query("
        SELECT o.id, o.order_number, o.total_price, o.status, o.created_at, c.company_name
        FROM orders o
        LEFT JOIN customers c ON c.id = o.customer_id
        ORDER BY o.id DESC
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);

    $recentInvoices = $pdo->query("
        SELECT
            i.id,
            i.invoice_number,
            i.total_amount,
            i.status,
            i.due_date,
            i.created_at,
            o.order_number,
            c.company_name
        FROM invoices i
        LEFT JOIN orders o ON o.id = i.order_id
        LEFT JOIN customers c ON c.id = o.customer_id
        ORDER BY i.id DESC
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);

    $recentPayments = $pdo->query("
        SELECT
            p.id,
            p.amount,
            p.payment_method,
            p.transaction_code,
            p.status,
            p.payment_date,
            i.invoice_number,
            i.total_amount
        FROM payments p
        LEFT JOIN invoices i ON i.id = p.invoice_id
        ORDER BY p.id DESC
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("خطا در دریافت اطلاعات داشبورد: " . $e->getMessage());
}

function leadBadge($status) {
    if ($status === 'new') return '<span class="badge bg-primary-subtle text-primary border border-primary-subtle">جدید</span>';
    if ($status === 'contacted') return '<span class="badge bg-info-subtle text-info border border-info-subtle">تماس گرفته شده</span>';
    if ($status === 'quotation_sent') return '<span class="badge bg-warning-subtle text-warning border border-warning-subtle">پیش‌فاکتور ارسال شد</span>';
    if ($status === 'negotiation') return '<span class="badge bg-success-subtle text-success border border-success-subtle">مذاکره</span>';
    return '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">نامشخص</span>';
}

function quotationBadge($status) {
    if ($status === 'draft') return '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">پیش‌نویس</span>';
    if ($status === 'quotation_sent') return '<span class="badge bg-warning-subtle text-warning border border-warning-subtle">ارسال شده</span>';
    if ($status === 'accepted') return '<span class="badge bg-success-subtle text-success border border-success-subtle">تأیید شده</span>';
    if ($status === 'rejected') return '<span class="badge bg-danger-subtle text-danger border border-danger-subtle">رد شده</span>';
    return '<span class="badge bg-dark-subtle text-dark border border-dark-subtle">نامشخص</span>';
}

function orderBadge($status) {
    if ($status === 'pending') return '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">در انتظار</span>';
    if ($status === 'processing') return '<span class="badge bg-warning-subtle text-warning border border-warning-subtle">در حال انجام</span>';
    if ($status === 'completed') return '<span class="badge bg-success-subtle text-success border border-success-subtle">تکمیل شده</span>';
    if ($status === 'cancelled') return '<span class="badge bg-danger-subtle text-danger border border-danger-subtle">لغو شده</span>';
    return '<span class="badge bg-dark-subtle text-dark border border-dark-subtle">نامشخص</span>';
}

function invoiceBadge($status) {
    if ($status === 'unpaid') return '<span class="badge bg-danger-subtle text-danger border border-danger-subtle">پرداخت‌نشده</span>';
    if ($status === 'paid') return '<span class="badge bg-success-subtle text-success border border-success-subtle">پرداخت‌شده</span>';
    if ($status === 'partially_paid') return '<span class="badge bg-warning-subtle text-warning border border-warning-subtle">پرداخت جزئی</span>';
    return '<span class="badge bg-dark-subtle text-dark border border-dark-subtle">نامشخص</span>';
}

function paymentBadge($status) {
    if ($status === 'pending') return '<span class="badge bg-warning-subtle text-warning border border-warning-subtle">در انتظار</span>';
    if ($status === 'successful') return '<span class="badge bg-success-subtle text-success border border-success-subtle">موفق</span>';
    if ($status === 'failed') return '<span class="badge bg-danger-subtle text-danger border border-danger-subtle">ناموفق</span>';
    return '<span class="badge bg-dark-subtle text-dark border border-dark-subtle">نامشخص</span>';
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
    <style>
        .sidebar { background: #343a40; min-height: 100vh; color: #ddd; }
        .nav-link { color: #ddd; padding: 12px 15px; display: block; text-decoration: none; }
        .nav-link:hover { color: white; background: #495057; }
        .main-content { background: #f8f9fa; min-height: 100vh; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <?php include __DIR__ . '/sidebar.php'; ?>

        <div class="col-md-10 main-content p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">داشبورد مدیریت</h2>
                <div>
                    <span class="badge bg-success">
                        خوش آمدید، <?= htmlspecialchars($_SESSION['fullname'] ?? 'مدیر'); ?>
                    </span>
                    <a href="logout.php" class="btn btn-danger btn-sm ms-3">خروج</a>
                </div>
            </div>

            <div class="alert alert-info border-0 shadow-sm">
                این داشبورد با مسیرهای روت تنظیم شده است و اطلاعات فروش، تولید، فاکتور و پرداخت را یکجا نشان می‌دهد.
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card bg-white shadow-sm border-0 rounded-4">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted small fw-bold">کل لیدها</div>
                                <h2 class="mb-0 fw-bold"><?= $totalLeads ?></h2>
                            </div>
                            <i class="fas fa-bullhorn fa-2x text-primary opacity-75"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card bg-white shadow-sm border-0 rounded-4">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted small fw-bold">کل پیش‌فاکتورها</div>
                                <h2 class="mb-0 fw-bold"><?= $totalQuotations ?></h2>
                            </div>
                            <i class="fas fa-file-invoice fa-2x text-success opacity-75"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card bg-white shadow-sm border-0 rounded-4">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted small fw-bold">کل سفارش‌ها</div>
                                <h2 class="mb-0 fw-bold"><?= $totalOrders ?></h2>
                            </div>
                            <i class="fas fa-box fa-2x text-warning opacity-75"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card bg-white shadow-sm border-0 rounded-4">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted small fw-bold">مشتریان</div>
                                <h2 class="mb-0 fw-bold"><?= $totalCustomers ?></h2>
                            </div>
                            <i class="fas fa-users fa-2x text-info opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card bg-white shadow-sm border-0 rounded-4">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted small fw-bold">لیدهای جدید</div>
                                <h2 class="mb-0 fw-bold"><?= $newLeads ?></h2>
                            </div>
                            <i class="fas fa-star fa-2x text-warning opacity-75"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card bg-white shadow-sm border-0 rounded-4">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted small fw-bold">پیش‌فاکتورهای پیش‌نویس</div>
                                <h2 class="mb-0 fw-bold"><?= $draftQuotations ?></h2>
                            </div>
                            <i class="fas fa-pen-to-square fa-2x text-secondary opacity-75"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card bg-white shadow-sm border-0 rounded-4">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted small fw-bold">سفارش‌های در انتظار</div>
                                <h2 class="mb-0 fw-bold"><?= $pendingOrders ?></h2>
                            </div>
                            <i class="fas fa-clock fa-2x text-dark opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card bg-white shadow-sm border-0 rounded-4">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted small fw-bold">کل فاکتورها</div>
                                <h2 class="mb-0 fw-bold"><?= $totalInvoices ?></h2>
                            </div>
                            <i class="fas fa-file-invoice-dollar fa-2x text-danger opacity-75"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card bg-white shadow-sm border-0 rounded-4">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted small fw-bold">فاکتورهای پرداخت‌نشده</div>
                                <h2 class="mb-0 fw-bold"><?= $unpaidInvoices ?></h2>
                            </div>
                            <i class="fas fa-credit-card fa-2x text-warning opacity-75"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card bg-white shadow-sm border-0 rounded-4">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted small fw-bold">پرداخت‌های موفق</div>
                                <h2 class="mb-0 fw-bold"><?= $successfulPayments ?></h2>
                            </div>
                            <i class="fas fa-circle-check fa-2x text-success opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-lg-4">
                    <div class="bg-white rounded-4 shadow-sm p-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">آخرین لیدها</h5>
                            <a href="sales/leads/index.php" class="btn btn-sm btn-outline-primary">مشاهده همه</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>مشتری</th>
                                        <th>شرکت</th>
                                        <th>وضعیت</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($recentLeads)): ?>
                                        <tr><td colspan="3" class="text-center text-muted py-4">سرنخی یافت نشد.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($recentLeads as $lead): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($lead['customer_name'] ?? '-') ?></td>
                                                <td><?= htmlspecialchars($lead['company_name'] ?? '-') ?></td>
                                                <td><?= leadBadge($lead['status'] ?? 'new') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="bg-white rounded-4 shadow-sm p-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">آخرین پیش‌فاکتورها</h5>
                            <a href="sales/quotations/index.php" class="btn btn-sm btn-outline-success">مشاهده همه</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>شماره</th>
                                        <th>مشتری</th>
                                        <th>وضعیت</th>
                                        <th>مبلغ نهایی</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($recentQuotations)): ?>
                                        <tr><td colspan="4" class="text-center text-muted py-4">پیش‌فاکتوری یافت نشد.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($recentQuotations as $q): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($q['quotation_number'] ?? ('Q-' . str_pad((string)$q['id'], 5, '0', STR_PAD_LEFT))) ?></td>
                                                <td><?= htmlspecialchars($q['company_name'] ?? '-') ?></td>
                                                <td><?= quotationBadge($q['status'] ?? 'draft') ?></td>
                                                <td><?= number_format((float)($q['final_price'] ?? 0)) ?> تومان</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="bg-white rounded-4 shadow-sm p-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">آخرین سفارش‌ها</h5>
                            <a href="sales/orders/index.php" class="btn btn-sm btn-outline-warning">مشاهده همه</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>شماره</th>
                                        <th>مشتری</th>
                                        <th>وضعیت</th>
                                        <th>مبلغ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($recentOrders)): ?>
                                        <tr><td colspan="4" class="text-center text-muted py-4">سفارشی یافت نشد.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($recentOrders as $order): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($order['order_number'] ?? ('ORD-' . str_pad((string)$order['id'], 5, '0', STR_PAD_LEFT))) ?></td>
                                                <td><?= htmlspecialchars($order['company_name'] ?? '-') ?></td>
                                                <td><?= orderBadge($order['status'] ?? 'pending') ?></td>
                                                <td><?= number_format((float)($order['total_price'] ?? 0)) ?> تومان</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-lg-6">
                    <div class="bg-white rounded-4 shadow-sm p-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">آخرین فاکتورها</h5>
                            <a href="admin/invoices/index.php" class="btn btn-sm btn-outline-danger">مشاهده همه</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>شماره</th>
                                        <th>سفارش</th>
                                        <th>مشتری</th>
                                        <th>وضعیت</th>
                                        <th>مبلغ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($recentInvoices)): ?>
                                        <tr><td colspan="5" class="text-center text-muted py-4">فاکتوری یافت نشد.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($recentInvoices as $invoice): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($invoice['invoice_number'] ?? ('INV-' . str_pad((string)$invoice['id'], 5, '0', STR_PAD_LEFT))) ?></td>
                                                <td><?= htmlspecialchars($invoice['order_number'] ?? '-') ?></td>
                                                <td><?= htmlspecialchars($invoice['company_name'] ?? '-') ?></td>
                                                <td><?= invoiceBadge($invoice['status'] ?? 'unpaid') ?></td>
                                                <td><?= number_format((float)($invoice['total_amount'] ?? 0)) ?> تومان</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="bg-white rounded-4 shadow-sm p-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">آخرین پرداخت‌ها</h5>
                            <a href="admin/payments/index.php" class="btn btn-sm btn-outline-success">مشاهده همه</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>فاکتور</th>
                                        <th>مبلغ</th>
                                        <th>روش</th>
                                        <th>وضعیت</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($recentPayments)): ?>
                                        <tr><td colspan="4" class="text-center text-muted py-4">پرداختی یافت نشد.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($recentPayments as $payment): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($payment['invoice_number'] ?? '-') ?></td>
                                                <td><?= number_format((float)($payment['amount'] ?? 0)) ?> تومان</td>
                                                <td><?= htmlspecialchars($payment['payment_method'] ?? '-') ?></td>
                                                <td><?= paymentBadge($payment['status'] ?? 'pending') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-5">
                <div class="col-md-6 col-lg-3">
                    <a href="sales/leads/index.php" class="text-decoration-none">
                        <div class="card bg-white shadow-sm border-0 rounded-4 h-100">
                            <div class="card-body text-center p-4">
                                <i class="fas fa-bullhorn fa-3x text-primary mb-3"></i>
                                <h5 class="fw-bold">لیدها</h5>
                                <p class="text-muted mb-0">مدیریت و پیگیری مشتریان بالقوه</p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-6 col-lg-3">
                    <a href="sales/quotations/index.php" class="text-decoration-none">
                        <div class="card bg-white shadow-sm border-0 rounded-4 h-100">
                            <div class="card-body text-center p-4">
                                <i class="fas fa-file-invoice fa-3x text-success mb-3"></i>
                                <h5 class="fw-bold">پیش‌فاکتورها</h5>
                                <p class="text-muted mb-0">ایجاد و مدیریت پیش‌فاکتور</p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-6 col-lg-3">
                    <a href="admin/customers/index.php" class="text-decoration-none">
                        <div class="card bg-white shadow-sm border-0 rounded-4 h-100">
                            <div class="card-body text-center p-4">
                                <i class="fas fa-users fa-3x text-info mb-3"></i>
                                <h5 class="fw-bold">مشتریان</h5>
                                <p class="text-muted mb-0">دسترسی به اطلاعات مشتریان</p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-6 col-lg-3">
                    <a href="admin/products/index.php" class="text-decoration-none">
                        <div class="card bg-white shadow-sm border-0 rounded-4 h-100">
                            <div class="card-body text-center p-4">
                                <i class="fas fa-boxes-stacked fa-3x text-warning mb-3"></i>
                                <h5 class="fw-bold">محصولات</h5>
                                <p class="text-muted mb-0">مشاهده کالاها و قیمت‌ها</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>