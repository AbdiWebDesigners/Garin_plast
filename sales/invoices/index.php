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

$pageTitle = 'لیست فاکتورها';
$search = trim($_GET['search'] ?? '');
$statusFilter = trim($_GET['status'] ?? '');
$paymentFilter = trim($_GET['payment_status'] ?? '');

$invoices = [];
$invoiceStats = [
    'total' => 0,
    'unpaid' => 0,
    'paid' => 0,
    'partially_paid' => 0
];

try {
    $invoiceStats['total'] = (int)$pdo->query("SELECT COUNT(*) FROM invoices")->fetchColumn();
    $invoiceStats['unpaid'] = (int)$pdo->query("SELECT COUNT(*) FROM invoices WHERE status = 'unpaid'")->fetchColumn();
    $invoiceStats['paid'] = (int)$pdo->query("SELECT COUNT(*) FROM invoices WHERE status = 'paid'")->fetchColumn();
    $invoiceStats['partially_paid'] = (int)$pdo->query("SELECT COUNT(*) FROM invoices WHERE status = 'partially_paid'")->fetchColumn();

    $sql = "
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
            o.created_at AS order_created_at,
            c.company_name,
            c.manager_name,
            c.phone,
            c.email,
            COALESCE(SUM(CASE WHEN p.status = 'successful' THEN p.amount ELSE 0 END), 0) AS paid_amount,
            COALESCE(SUM(CASE WHEN p.status = 'pending' THEN p.amount ELSE 0 END), 0) AS pending_payment_amount,
            COUNT(p.id) AS payment_count
        FROM invoices i
        LEFT JOIN orders o ON o.id = i.order_id
        LEFT JOIN customers c ON c.id = o.customer_id
        LEFT JOIN payments p ON p.invoice_id = i.id
    ";

    $where = [];
    $params = [];

    if ($search !== '') {
        $where[] = "(i.invoice_number LIKE ? OR o.order_number LIKE ? OR c.company_name LIKE ? OR c.manager_name LIKE ?)";
        $like = '%' . $search . '%';
        array_push($params, $like, $like, $like, $like);
    }

    if ($statusFilter !== '') {
        $where[] = "i.status = ?";
        $params[] = $statusFilter;
    }

    if ($paymentFilter !== '') {
        if ($paymentFilter === 'has_payment') {
            $where[] = "EXISTS (SELECT 1 FROM payments px WHERE px.invoice_id = i.id)";
        } elseif ($paymentFilter === 'no_payment') {
            $where[] = "NOT EXISTS (SELECT 1 FROM payments px WHERE px.invoice_id = i.id)";
        } elseif ($paymentFilter === 'successful') {
            $where[] = "EXISTS (SELECT 1 FROM payments px WHERE px.invoice_id = i.id AND px.status = 'successful')";
        }
    }

    if (!empty($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }

    $sql .= " GROUP BY i.id ORDER BY i.id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("خطا در دریافت فاکتورها: " . $e->getMessage());
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
        <?php include __DIR__ . '/../../admin/sidebar.php'; ?>

        <div class="col-md-10 p-4">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="mb-1">لیست فاکتورها</h3>
                    <div class="text-muted">مدیریت فاکتورها و ارتباط آن‌ها با پرداخت‌ها</div>
                </div>
                <div class="d-flex gap-2">
                    <a href="add.php" class="btn btn-primary">
                        <i class="fa fa-plus me-1"></i> ثبت فاکتور جدید
                    </a>
                    <a href="<?= BASE_URL ?>admin/dashboard.php" class="btn btn-outline-secondary">داشبورد</a>
                </div>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show rounded-3 small py-2 px-3 mb-4" role="alert">
                    <i class="fa fa-check-circle me-1"></i> فاکتور فروش جدید با موفقیت در سیستم ثبت شد.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="بستن"></button>
                </div>
            <?php endif; ?>

            <div class="row g-3 mb-4">
                <div class="col-md-3"><div class="card text-white bg-primary shadow-sm"><div class="card-body"><h4 class="mb-0"><?= (int)$invoiceStats['total'] ?></h4><small>کل فاکتورها</small></div></div></div>
                <div class="col-md-3"><div class="card text-white bg-danger shadow-sm"><div class="card-body"><h4 class="mb-0"><?= (int)$invoiceStats['unpaid'] ?></h4><small>پرداخت‌نشده</small></div></div></div>
                <div class="col-md-3"><div class="card text-white bg-success shadow-sm"><div class="card-body"><h4 class="mb-0"><?= (int)$invoiceStats['paid'] ?></h4><small>پرداخت‌شده</small></div></div></div>
                <div class="col-md-3"><div class="card text-white bg-warning shadow-sm"><div class="card-body"><h4 class="mb-0"><?= (int)$invoiceStats['partially_paid'] ?></h4><small>پرداخت جزئی</small></div></div></div>
            </div>

            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-body">
                    <form method="get" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">جستجو</label>
                            <input type="text" name="search" class="form-control" value="<?= htmlspecialchars($search) ?>" placeholder="شماره فاکتور، شماره سفارش، نام مشتری">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">وضعیت فاکتور</label>
                            <select name="status" class="form-select">
                                <option value="">همه</option>
                                <option value="unpaid" <?= $statusFilter === 'unpaid' ? 'selected' : '' ?>>پرداخت‌نشده</option>
                                <option value="partially_paid" <?= $statusFilter === 'partially_paid' ? 'selected' : '' ?>>پرداخت جزئی</option>
                                <option value="paid" <?= $statusFilter === 'paid' ? 'selected' : '' ?>>پرداخت‌شده</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">فیلتر پرداخت</label>
                            <select name="payment_status" class="form-select">
                                <option value="">همه</option>
                                <option value="has_payment" <?= $paymentFilter === 'has_payment' ? 'selected' : '' ?>>دارای پرداخت</option>
                                <option value="no_payment" <?= $paymentFilter === 'no_payment' ? 'selected' : '' ?>>بدون پرداخت</option>
                                <option value="successful" <?= $paymentFilter === 'successful' ? 'selected' : '' ?>>دارای پرداخت موفق</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100">اعمال</button>
                            <a href="index.php" class="btn btn-outline-secondary w-100">ریست</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>فاکتور</th>
                                    <th>سفارش</th>
                                    <th>مشتری</th>
                                    <th>مبلغ کل</th>
                                    <th>پرداخت‌شده</th>
                                    <th>تعداد پرداخت</th>
                                    <th>وضعیت</th>
                                    <th>سررسید</th>
                                    <th>عملیات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($invoices)): ?>
                                    <tr>
                                        <td colspan="10" class="text-center text-muted py-4">فاکتوری یافت نشد.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($invoices as $invoice): ?>
                                        <tr>
                                            <td><?= (int)$invoice['id'] ?></td>
                                            <td>
                                                <div class="fw-semibold"><?= htmlspecialchars($invoice['invoice_number'] ?? ('INV-' . str_pad((string)$invoice['id'], 5, '0', STR_PAD_LEFT))) ?></div>
                                                <div class="small text-muted"><?= htmlspecialchars($invoice['invoice_created_at'] ?? '-') ?></div>
                                            </td>
                                            <td>
                                                <div class="fw-semibold"><?= htmlspecialchars($invoice['order_number'] ?? '-') ?></div>
                                                <div class="small text-muted"><?= orderBadge($invoice['order_status'] ?? 'pending') ?></div>
                                            </td>
                                            <td>
                                                <div class="fw-semibold"><?= htmlspecialchars($invoice['company_name'] ?? '-') ?></div>
                                                <div class="small text-muted"><?= htmlspecialchars($invoice['manager_name'] ?? '-') ?></div>
                                            </td>
                                            <td><?= money($invoice['total_amount'] ?? 0) ?></td>
                                            <td><?= money($invoice['paid_amount'] ?? 0) ?></td>
                                            <td><?= (int)($invoice['payment_count'] ?? 0) ?></td>
                                            <td><?= invoiceBadge($invoice['invoice_status'] ?? 'unpaid') ?></td>
                                            <td><?= htmlspecialchars($invoice['due_date'] ?? '-') ?></td>
                                            <td class="text-nowrap">
                                                <a href="view.php?id=<?= (int)$invoice['id'] ?>" class="btn btn-sm btn-outline-primary">جزئیات</a>
                                                <a href="edit.php?id=<?= (int)$invoice['id'] ?>" class="btn btn-sm btn-warning">ویرایش</a>
                                                <a href="<?= BASE_URL ?>admin/payments/index.php?invoice_id=<?= (int)$invoice['id'] ?>" class="btn btn-sm btn-outline-success">پرداخت‌ها</a>
                                            </td>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
require __DIR__ . '/views/invoices_content.php';