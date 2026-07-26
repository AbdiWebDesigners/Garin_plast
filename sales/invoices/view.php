<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

if (empty($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit;
}

$pageTitle = 'جزئیات فاکتور';

$invoiceId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($invoiceId <= 0) {
    die('شناسه فاکتور نامعتبر است.');
}

try {
    $stmt = $pdo->prepare("
        SELECT
            i.id,
            i.order_id,
            i.invoice_number,
            i.subtotal,
            i.tax_amount,
            i.discount_amount,
            i.total_amount,
            i.status AS invoice_status,
            i.due_date,
            i.created_at AS invoice_created_at,

            o.order_number,
            o.status AS order_status,
            o.total_price,
            o.notes AS order_notes,
            o.created_at AS order_created_at,

            c.id AS customer_id,
            c.user_id AS customer_user_id,
            c.company_name,
            c.manager_name,
            c.phone,
            c.email,
            c.address,
            c.city

        FROM invoices i
        LEFT JOIN orders o ON o.id = i.order_id
        LEFT JOIN customers c ON c.id = o.customer_id
        WHERE i.id = ?
        LIMIT 1
    ");
    $stmt->execute([$invoiceId]);
    $invoice = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$invoice) {
        die('فاکتور مورد نظر یافت نشد.');
    }

    $stmt = $pdo->prepare("
        SELECT
            p.id,
            p.amount,
            p.payment_method,
            p.transaction_code,
            p.status,
            p.payment_date,
            p.created_at
        FROM payments p
        WHERE p.invoice_id = ?
        ORDER BY p.id DESC
    ");
    $stmt->execute([$invoiceId]);
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $paidAmount = 0;
    foreach ($payments as $pay) {
        if (($pay['status'] ?? '') === 'successful') {
            $paidAmount += (float)$pay['amount'];
        }
    }

    $remainingAmount = max(0, (float)$invoice['total_amount'] - $paidAmount);
    $paymentPercent = ((float)$invoice['total_amount'] > 0)
        ? round(($paidAmount / (float)$invoice['total_amount']) * 100, 2)
        : 0;

} catch (PDOException $e) {
    die("خطا در دریافت فاکتور: " . $e->getMessage());
}

function invoiceBadge($status) {
    if ($status === 'unpaid') return '<span class="badge bg-danger-subtle text-danger border border-danger-subtle">پرداخت‌نشده</span>';
    if ($status === 'paid') return '<span class="badge bg-success-subtle text-success border border-success-subtle">پرداخت‌شده</span>';
    if ($status === 'partially_paid') return '<span class="badge bg-warning-subtle text-warning border border-warning-subtle">پرداخت جزئی</span>';
    return '<span class="badge bg-dark-subtle text-dark border border-dark-subtle">نامشخص</span>';
}

function orderBadge($status) {
    if ($status === 'pending') return '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">در انتظار</span>';
    if ($status === 'processing') return '<span class="badge bg-warning-subtle text-warning border border-warning-subtle">در حال انجام</span>';
    if ($status === 'completed') return '<span class="badge bg-success-subtle text-success border border-success-subtle">تکمیل شده</span>';
    if ($status === 'cancelled') return '<span class="badge bg-danger-subtle text-danger border border-danger-subtle">لغو شده</span>';
    return '<span class="badge bg-dark-subtle text-dark border border-dark-subtle">نامشخص</span>';
}

function paymentBadge($status) {
    if ($status === 'pending') return '<span class="badge bg-warning-subtle text-warning border border-warning-subtle">در انتظار</span>';
    if ($status === 'successful') return '<span class="badge bg-success-subtle text-success border border-success-subtle">موفق</span>';
    if ($status === 'failed') return '<span class="badge bg-danger-subtle text-danger border border-danger-subtle">ناموفق</span>';
    return '<span class="badge bg-dark-subtle text-dark border border-dark-subtle">نامشخص</span>';
}

function money($v) {
    return number_format((float)$v) . ' تومان';
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
<body class="bg-light">
<div class="container-fluid">
    <div class="row">
        <?php include __DIR__ . '/../sidebar.php'; ?>

        <div class="col-md-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="mb-1">جزئیات فاکتور</h3>
                    <div class="text-muted">مشاهده اطلاعات فاکتور، سفارش و پرداخت‌ها</div>
                </div>
                <div class="d-flex gap-2">
                    <a href="index.php" class="btn btn-outline-secondary">بازگشت به لیست</a>
                    <a href="edit.php?id=<?= (int)$invoice['id'] ?>" class="btn btn-warning">ویرایش فاکتور</a>
                    <a href="../payments/index.php?invoice_id=<?= (int)$invoice['id'] ?>" class="btn btn-success">افزودن/مشاهده پرداخت</a>
                </div>
            </div>

            <!-- بقیه کد view.php بدون تغییر باقی مانده است -->
            <div class="row g-4 mb-4">
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 rounded-4 h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <div>
                                    <h5 class="fw-bold mb-1">
                                        فاکتور <?= htmlspecialchars($invoice['invoice_number'] ?? ('INV-' . str_pad((string)$invoice['id'], 5, '0', STR_PAD_LEFT))) ?>
                                    </h5>
                                    <div class="text-muted small">
                                        تاریخ صدور: <?= htmlspecialchars($invoice['invoice_created_at'] ?? '-') ?>
                                    </div>
                                </div>
                                <div>
                                    <?= invoiceBadge($invoice['invoice_status'] ?? 'unpaid') ?>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="border rounded-4 p-3 bg-light h-100">
                                        <div class="text-muted small mb-1">مشتری</div>
                                        <div class="fw-semibold"><?= htmlspecialchars($invoice['company_name'] ?? '-') ?></div>
                                        <div class="small text-muted"><?= htmlspecialchars($invoice['manager_name'] ?? '-') ?></div>
                                        <div class="small text-muted"><?= htmlspecialchars($invoice['phone'] ?? '-') ?></div>
                                        <div class="small text-muted"><?= htmlspecialchars($invoice['email'] ?? '-') ?></div>
                                        <div class="small text-muted"><?= htmlspecialchars($invoice['city'] ?? '-') ?></div>
                                        <div class="small text-muted mt-2"><?= nl2br(htmlspecialchars($invoice['address'] ?? '-')) ?></div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="border rounded-4 p-3 bg-light h-100">
                                        <div class="text-muted small mb-1">سفارش</div>
                                        <div class="fw-semibold">شماره سفارش: <?= htmlspecialchars($invoice['order_number'] ?? '-') ?></div>
                                        <div class="small text-muted">تاریخ سفارش: <?= htmlspecialchars($invoice['order_created_at'] ?? '-') ?></div>
                                        <div class="small text-muted">وضعیت سفارش: <?= orderBadge($invoice['order_status'] ?? 'pending') ?></div>
                                        <div class="small text-muted mt-2">یادداشت سفارش:</div>
                                        <div class="small"><?= nl2br(htmlspecialchars($invoice['order_notes'] ?? '-')) ?></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mt-3">
                                <div class="col-md-3">
                                    <div class="border rounded-4 p-3 text-center h-100">
                                        <div class="text-muted small">جمع جزء</div>
                                        <div class="fw-bold fs-5"><?= money($invoice['subtotal'] ?? 0) ?></div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="border rounded-4 p-3 text-center h-100">
                                        <div class="text-muted small">مالیات</div>
                                        <div class="fw-bold fs-5"><?= money($invoice['tax_amount'] ?? 0) ?></div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="border rounded-4 p-3 text-center h-100">
                                        <div class="text-muted small">تخفیف</div>
                                        <div class="fw-bold fs-5"><?= money($invoice['discount_amount'] ?? 0) ?></div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="border rounded-4 p-3 text-center h-100">
                                        <div class="text-muted small">مبلغ نهایی</div>
                                        <div class="fw-bold fs-5"><?= money($invoice['total_amount'] ?? 0) ?></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mt-1">
                                <div class="col-md-4">
                                    <div class="alert alert-success mb-0">
                                        <div class="small text-muted">جمع پرداخت‌شده</div>
                                        <div class="fw-bold"><?= money($paidAmount) ?></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="alert alert-warning mb-0">
                                        <div class="small text-muted">مانده قابل پرداخت</div>
                                        <div class="fw-bold"><?= money($remainingAmount) ?></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="alert alert-info mb-0">
                                        <div class="small text-muted">درصد پرداخت</div>
                                        <div class="fw-bold"><?= $paymentPercent ?>%</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 rounded-4 h-100">
                        <div class="card-body">
                            <h5 class="fw-bold mb-3">اطلاعات سریع</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span>شماره فاکتور</span>
                                    <strong><?= htmlspecialchars($invoice['invoice_number'] ?? '-') ?></strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span>شماره سفارش</span>
                                    <strong><?= htmlspecialchars($invoice['order_number'] ?? '-') ?></strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span>وضعیت فاکتور</span>
                                    <span><?= invoiceBadge($invoice['invoice_status'] ?? 'unpaid') ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span>تعداد پرداخت‌ها</span>
                                    <strong><?= count($payments) ?></strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span>سررسید</span>
                                    <strong><?= htmlspecialchars($invoice['due_date'] ?? '-') ?></strong>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">پرداخت‌های این فاکتور</h5>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>مبلغ</th>
                                    <th>روش پرداخت</th>
                                    <th>کد تراکنش</th>
                                    <th>وضعیت</th>
                                    <th>تاریخ پرداخت</th>
                                    <th>تاریخ ثبت</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($payments)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">پرداختی برای این فاکتور ثبت نشده است.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($payments as $payment): ?>
                                        <tr>
                                            <td><?= (int)$payment['id'] ?></td>
                                            <td><?= money($payment['amount'] ?? 0) ?></td>
                                            <td><?= htmlspecialchars($payment['payment_method'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($payment['transaction_code'] ?? '-') ?></td>
                                            <td><?= paymentBadge($payment['status'] ?? 'pending') ?></td>
                                            <td><?= htmlspecialchars($payment['payment_date'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($payment['created_at'] ?? '-') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
</body>
</html>