<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

if (empty($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit;
}

$pageTitle = 'مدیریت پرداخت‌ها';

$invoiceId = isset($_GET['invoice_id']) ? (int)$_GET['invoice_id'] : 0;
$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;

$success = '';
$error = '';

function money($v) {
    return number_format((float)$v) . ' تومان';
}

function paymentBadge($status) {
    if ($status === 'pending') return '<span class="badge bg-warning-subtle text-warning border border-warning-subtle">در انتظار</span>';
    if ($status === 'successful') return '<span class="badge bg-success-subtle text-success border border-success-subtle">موفق</span>';
    if ($status === 'failed') return '<span class="badge bg-danger-subtle text-danger border border-danger-subtle">ناموفق</span>';
    return '<span class="badge bg-dark-subtle text-dark border border-dark-subtle">نامشخص</span>';
}

function recalcInvoiceStatus(PDO $pdo, int $invoiceId): void {
    $stmt = $pdo->prepare("
        SELECT
            i.id,
            i.total_amount,
            COALESCE(SUM(CASE WHEN p.status = 'successful' THEN p.amount ELSE 0 END), 0) AS paid_amount
        FROM invoices i
        LEFT JOIN payments p ON p.invoice_id = i.id
        WHERE i.id = ?
        GROUP BY i.id
        LIMIT 1
    ");
    $stmt->execute([$invoiceId]);
    $invoice = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$invoice) {
        return;
    }

    $paidAmount = (float)$invoice['paid_amount'];
    $totalAmount = (float)$invoice['total_amount'];

    if ($paidAmount <= 0) {
        $invoiceStatus = 'unpaid';
    } elseif ($paidAmount >= $totalAmount) {
        $invoiceStatus = 'paid';
    } else {
        $invoiceStatus = 'partially_paid';
    }

    $stmt = $pdo->prepare("UPDATE invoices SET status = ? WHERE id = ?");
    $stmt->execute([$invoiceStatus, $invoiceId]);
}

try {
    if (isset($_POST['save_payment'])) {
        $paymentId = isset($_POST['payment_id']) ? (int)$_POST['payment_id'] : 0;
        $invoiceIdPost = (int)$_POST['invoice_id'];
        $amount = (float)$_POST['amount'];
        $paymentMethod = trim($_POST['payment_method'] ?? '');
        $transactionCode = trim($_POST['transaction_code'] ?? '');
        $status = trim($_POST['status'] ?? 'pending');
        $paymentDate = trim($_POST['payment_date'] ?? date('Y-m-d H:i:s'));

        if ($invoiceIdPost <= 0) {
            throw new Exception('فاکتور معتبر نیست.');
        }
        if ($amount <= 0) {
            throw new Exception('مبلغ پرداخت باید بزرگتر از صفر باشد.');
        }

        $pdo->beginTransaction();

        if ($paymentId > 0) {
            $stmt = $pdo->prepare("
                UPDATE payments
                SET invoice_id = ?, amount = ?, payment_method = ?, transaction_code = ?, status = ?, payment_date = ?
                WHERE id = ?
            ");
            $stmt->execute([$invoiceIdPost, $amount, $paymentMethod, $transactionCode, $status, $paymentDate, $paymentId]);
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO payments (invoice_id, amount, payment_method, transaction_code, status, payment_date, created_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$invoiceIdPost, $amount, $paymentMethod, $transactionCode, $status, $paymentDate]);
        }

        recalcInvoiceStatus($pdo, $invoiceIdPost);

        $pdo->commit();

        header("Location: index.php?invoice_id=" . $invoiceIdPost . "&saved=1");
        exit;
    }

    if (isset($_GET['delete'])) {
        $deleteId = (int)$_GET['delete'];

        $stmt = $pdo->prepare("SELECT invoice_id FROM payments WHERE id = ?");
        $stmt->execute([$deleteId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("DELETE FROM payments WHERE id = ?");
            $stmt->execute([$deleteId]);

            recalcInvoiceStatus($pdo, (int)$row['invoice_id']);

            $pdo->commit();

            $redirectInvoiceId = $invoiceId > 0 ? $invoiceId : (int)$row['invoice_id'];
            header("Location: index.php?invoice_id=" . $redirectInvoiceId . "&deleted=1");
            exit;
        }
    }

    $invoiceOptions = $pdo->query("
        SELECT i.id, i.invoice_number, i.total_amount, i.status, c.company_name
        FROM invoices i
        LEFT JOIN orders o ON o.id = i.order_id
        LEFT JOIN customers c ON c.id = o.customer_id
        ORDER BY i.id DESC
    ")->fetchAll(PDO::FETCH_ASSOC);

    $selectedInvoice = null;
    if ($invoiceId > 0) {
        $stmt = $pdo->prepare("
            SELECT i.id, i.invoice_number, i.total_amount, i.status, c.company_name
            FROM invoices i
            LEFT JOIN orders o ON o.id = i.order_id
            LEFT JOIN customers c ON c.id = o.customer_id
            WHERE i.id = ?
            LIMIT 1
        ");
        $stmt->execute([$invoiceId]);
        $selectedInvoice = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    $sql = "
        SELECT
            p.id,
            p.invoice_id,
            p.amount,
            p.payment_method,
            p.transaction_code,
            p.status,
            p.payment_date,
            p.created_at,
            i.invoice_number,
            i.total_amount,
            c.company_name
        FROM payments p
        LEFT JOIN invoices i ON i.id = p.invoice_id
        LEFT JOIN orders o ON o.id = i.order_id
        LEFT JOIN customers c ON c.id = o.customer_id
    ";

    $params = [];
    if ($invoiceId > 0) {
        $sql .= " WHERE p.invoice_id = ?";
        $params[] = $invoiceId;
    }

    $sql .= " ORDER BY p.id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $editPayment = null;
    if ($editId > 0) {
        $stmt = $pdo->prepare("SELECT * FROM payments WHERE id = ? LIMIT 1");
        $stmt->execute([$editId]);
        $editPayment = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($editPayment && $invoiceId <= 0) {
            $invoiceId = (int)$editPayment['invoice_id'];
        }
    }

} catch (Exception $e) {
    $error = $e->getMessage();
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
        <?php include __DIR__ . '/../../sidebar.php'; ?>

        <div class="col-md-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="mb-1">مدیریت پرداخت‌ها</h3>
                    <div class="text-muted">ثبت و مشاهده پرداخت‌های مرتبط با فاکتور</div>
                </div>
                <div class="d-flex gap-2">
                    <a href="../invoices/index.php" class="btn btn-outline-secondary">فهرست فاکتورها</a>
                    <?php if ($invoiceId > 0): ?>
                        <a href="../invoices/view.php?id=<?= (int)$invoiceId ?>" class="btn btn-primary">بازگشت به فاکتور</a>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($_GET['saved'])): ?>
                <div class="alert alert-success">پرداخت با موفقیت ثبت/ویرایش شد.</div>
            <?php endif; ?>

            <?php if (!empty($_GET['deleted'])): ?>
                <div class="alert alert-success">پرداخت با موفقیت حذف شد.</div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-body">
                    <form method="get" class="row g-3 align-items-end">
                        <div class="col-md-8">
                            <label class="form-label">فیلتر بر اساس فاکتور</label>
                            <select name="invoice_id" class="form-select" onchange="this.form.submit()">
                                <option value="">همه فاکتورها</option>
                                <?php foreach ($invoiceOptions as $inv): ?>
                                    <option value="<?= (int)$inv['id'] ?>" <?= $invoiceId === (int)$inv['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars(($inv['invoice_number'] ?? '-') . ' - ' . ($inv['company_name'] ?? '-')) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <a href="index.php" class="btn btn-outline-secondary w-100">نمایش همه</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body">
                            <h5 class="fw-bold mb-3"><?= $editPayment ? 'ویرایش پرداخت' : 'ثبت پرداخت جدید' ?></h5>
                            <form method="post">
                                <input type="hidden" name="payment_id" value="<?= (int)($editPayment['id'] ?? 0) ?>">

                                <div class="mb-3">
                                    <label class="form-label">فاکتور</label>
                                    <select name="invoice_id" class="form-select" required>
                                        <option value="">انتخاب کنید</option>
                                        <?php foreach ($invoiceOptions as $inv): ?>
                                            <option value="<?= (int)$inv['id'] ?>"
                                                <?= (int)($editPayment['invoice_id'] ?? $invoiceId) === (int)$inv['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars(($inv['invoice_number'] ?? '-') . ' - ' . ($inv['company_name'] ?? '-')) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">مبلغ</label>
                                    <input type="number" step="0.01" name="amount" class="form-control" value="<?= htmlspecialchars($editPayment['amount'] ?? '') ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">روش پرداخت</label>
                                    <input type="text" name="payment_method" class="form-control" value="<?= htmlspecialchars($editPayment['payment_method'] ?? '') ?>" placeholder="کارت به کارت، نقدی، درگاه...">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">کد تراکنش</label>
                                    <input type="text" name="transaction_code" class="form-control" value="<?= htmlspecialchars($editPayment['transaction_code'] ?? '') ?>">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">وضعیت</label>
                                    <select name="status" class="form-select">
                                        <option value="pending" <?= ($editPayment['status'] ?? 'pending') === 'pending' ? 'selected' : '' ?>>در انتظار</option>
                                        <option value="successful" <?= ($editPayment['status'] ?? '') === 'successful' ? 'selected' : '' ?>>موفق</option>
                                        <option value="failed" <?= ($editPayment['status'] ?? '') === 'failed' ? 'selected' : '' ?>>ناموفق</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">تاریخ پرداخت</label>
                                    <input type="text" name="payment_date" class="form-control" value="<?= htmlspecialchars($editPayment['payment_date'] ?? date('Y-m-d H:i:s')) ?>">
                                </div>

                                <button type="submit" name="save_payment" class="btn btn-primary w-100">
                                    <?= $editPayment ? 'ثبت تغییرات' : 'ثبت پرداخت' ?>
                                </button>

                                <?php if ($editPayment): ?>
                                    <a href="index.php?invoice_id=<?= (int)$invoiceId ?>" class="btn btn-outline-secondary w-100 mt-2">لغو ویرایش</a>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body">
                            <h5 class="fw-bold mb-3">لیست پرداخت‌ها</h5>

                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>فاکتور</th>
                                            <th>مشتری</th>
                                            <th>مبلغ</th>
                                            <th>روش پرداخت</th>
                                            <th>کد تراکنش</th>
                                            <th>وضعیت</th>
                                            <th>تاریخ پرداخت</th>
                                            <th>عملیات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($payments)): ?>
                                            <tr>
                                                <td colspan="9" class="text-center text-muted py-4">پرداختی یافت نشد.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($payments as $payment): ?>
                                                <tr>
                                                    <td><?= (int)$payment['id'] ?></td>
                                                    <td><?= htmlspecialchars($payment['invoice_number'] ?? '-') ?></td>
                                                    <td><?= htmlspecialchars($payment['company_name'] ?? '-') ?></td>
                                                    <td><?= money($payment['amount'] ?? 0) ?></td>
                                                    <td><?= htmlspecialchars($payment['payment_method'] ?? '-') ?></td>
                                                    <td><?= htmlspecialchars($payment['transaction_code'] ?? '-') ?></td>
                                                    <td><?= paymentBadge($payment['status'] ?? 'pending') ?></td>
                                                    <td><?= htmlspecialchars($payment['payment_date'] ?? '-') ?></td>
                                                    <td class="text-nowrap">
                                                        <a href="index.php?edit=<?= (int)$payment['id'] ?>&invoice_id=<?= (int)$payment['invoice_id'] ?>" class="btn btn-sm btn-outline-primary">ویرایش</a>
                                                        <a href="index.php?delete=<?= (int)$payment['id'] ?>&invoice_id=<?= (int)$payment['invoice_id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('حذف شود؟')">حذف</a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                            <?php if ($selectedInvoice): ?>
                                <div class="alert alert-info mt-3 mb-0">
                                    فاکتور انتخاب‌شده: <strong><?= htmlspecialchars($selectedInvoice['invoice_number'] ?? '-') ?></strong>
                                    | مشتری: <strong><?= htmlspecialchars($selectedInvoice['company_name'] ?? '-') ?></strong>
                                    | مبلغ کل: <strong><?= money($selectedInvoice['total_amount'] ?? 0) ?></strong>
                                    | وضعیت: <?= paymentBadge($selectedInvoice['status'] ?? 'pending') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
</body>
</html>