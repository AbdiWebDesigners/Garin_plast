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

$pageTitle = 'ویرایش فاکتور';

$invoiceId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($invoiceId <= 0) {
    die('شناسه فاکتور نامعتبر است.');
}

$message = '';
$error = '';

// دریافت اطلاعات فاکتور
try {
    $stmt = $pdo->prepare("
        SELECT 
            i.*,
            o.order_number,
            o.status AS order_status,
            c.company_name,
            c.manager_name
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
} catch (PDOException $e) {
    die("خطا در دریافت اطلاعات: " . $e->getMessage());
}

// پردازش فرم
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $invoice_number   = trim($_POST['invoice_number'] ?? '');
    $status           = trim($_POST['status'] ?? 'unpaid');
    $due_date         = !empty($_POST['due_date']) ? $_POST['due_date'] : null;
    $subtotal         = (float)($_POST['subtotal'] ?? 0);
    $tax_amount       = (float)($_POST['tax_amount'] ?? 0);
    $discount_amount  = (float)($_POST['discount_amount'] ?? 0);
    $total_amount     = (float)($_POST['total_amount'] ?? 0);

    if ($total_amount <= 0) {
        $total_amount = $subtotal + $tax_amount - $discount_amount;
    }

    if (empty($invoice_number)) {
        $error = 'شماره فاکتور الزامی است.';
    } else {
        try {
            $stmt = $pdo->prepare("
                UPDATE invoices 
                SET invoice_number = ?,
                    subtotal = ?,
                    tax_amount = ?,
                    discount_amount = ?,
                    total_amount = ?,
                    status = ?,
                    due_date = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $invoice_number,
                $subtotal,
                $tax_amount,
                $discount_amount,
                $total_amount,
                $status,
                $due_date,
                $invoiceId
            ]);

            $message = 'فاکتور با موفقیت ویرایش شد.';

            // بروزرسانی اطلاعات
            $stmt = $pdo->prepare("SELECT * FROM invoices WHERE id = ?");
            $stmt->execute([$invoiceId]);
            $invoice = $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $error = "خطا در ویرایش: " . $e->getMessage();
        }
    }
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
                <h3>ویرایش فاکتور #<?= $invoiceId ?></h3>
                <div>
                    <a href="index.php" class="btn btn-outline-secondary">بازگشت به لیست</a>
                    <a href="view.php?id=<?= $invoiceId ?>" class="btn btn-outline-primary">مشاهده فاکتور</a>
                </div>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="post" class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label">شماره فاکتور</label>
                            <input type="text" name="invoice_number" class="form-control" 
                                   value="<?= htmlspecialchars($invoice['invoice_number'] ?? '') ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">وضعیت</label>
                            <select name="status" class="form-select">
                                <option value="unpaid" <?= $invoice['status']=='unpaid'?'selected':'' ?>>پرداخت‌نشده</option>
                                <option value="partially_paid" <?= $invoice['status']=='partially_paid'?'selected':'' ?>>پرداخت جزئی</option>
                                <option value="paid" <?= $invoice['status']=='paid'?'selected':'' ?>>پرداخت‌شده</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">سررسید</label>
                            <input type="date" name="due_date" class="form-control" 
                                   value="<?= htmlspecialchars($invoice['due_date'] ?? '') ?>">
                        </div>

                        <div class="col-12"><hr><h5>مبالغ</h5></div>

                        <div class="col-md-3">
                            <label>جمع جزء</label>
                            <input type="number" name="subtotal" class="form-control" step="0.01" 
                                   value="<?= (float)$invoice['subtotal'] ?>">
                        </div>
                        <div class="col-md-3">
                            <label>مالیات</label>
                            <input type="number" name="tax_amount" class="form-control" step="0.01" 
                                   value="<?= (float)$invoice['tax_amount'] ?>">
                        </div>
                        <div class="col-md-3">
                            <label>تخفیف</label>
                            <input type="number" name="discount_amount" class="form-control" step="0.01" 
                                   value="<?= (float)$invoice['discount_amount'] ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="fw-bold">مبلغ نهایی</label>
                            <input type="number" name="total_amount" class="form-control fw-bold" step="0.01" 
                                   value="<?= (float)$invoice['total_amount'] ?>">
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-save"></i> ذخیره تغییرات
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>