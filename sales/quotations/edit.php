<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

$pageTitle = 'ویرایش پیش‌فاکتور';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) die('شناسه نامعتبر است.');

$errorMessage = '';

try {
    $stmt = $pdo->prepare("SELECT * FROM quotations WHERE id = ?");
    $stmt->execute([$id]);
    $quotation = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$quotation) die('پیش‌فاکتور یافت نشد.');

    $customers = $pdo->query("
        SELECT id, company_name, manager_name
        FROM customers
        WHERE status = 'active'
        ORDER BY id DESC
    ")->fetchAll(PDO::FETCH_ASSOC);

    $salesAgents = $pdo->query("
        SELECT sa.id, sa.position, u.fullname
        FROM sales_agents sa
        LEFT JOIN users u ON u.id = sa.user_id
        WHERE u.role = 'sales' OR u.role IS NULL
        ORDER BY sa.id DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("خطا در دریافت اطلاعات: " . $e->getMessage());
}

$old = [
    'customer_id' => $quotation['customer_id'] ?? '',
    'sales_agent_id' => $quotation['sales_agent_id'] ?? '',
    'quotation_number' => $quotation['quotation_number'] ?? '',
    'total_price' => $quotation['total_price'] ?? 0,
    'discount' => $quotation['discount'] ?? 0,
    'tax' => $quotation['tax'] ?? 0,
    'final_price' => $quotation['final_price'] ?? 0,
    'status' => $quotation['status'] ?? 'draft'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($old as $key => $value) {
        $old[$key] = trim($_POST[$key] ?? $value);
    }

    try {
        $customerId = $old['customer_id'] !== '' ? (int)$old['customer_id'] : null;
        $salesAgentId = $old['sales_agent_id'] !== '' ? (int)$old['sales_agent_id'] : null;

        $totalPrice = (float)$old['total_price'];
        $discount = (float)$old['discount'];
        $tax = (float)$old['tax'];
        $finalPrice = (float)$old['final_price'];

        if ($finalPrice <= 0) {
            $finalPrice = max(0, ($totalPrice - $discount) + $tax);
        }

        $stmt = $pdo->prepare("
            UPDATE quotations
            SET customer_id = ?, sales_agent_id = ?, quotation_number = ?, total_price = ?, discount = ?, tax = ?, final_price = ?, status = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $customerId,
            $salesAgentId,
            $old['quotation_number'],
            $totalPrice,
            $discount,
            $tax,
            $finalPrice,
            $old['status'],
            $id
        ]);

        header("Location: index.php?updated=1");
        exit;
    } catch (PDOException $e) {
        $errorMessage = 'خطا در ویرایش پیش‌فاکتور: ' . $e->getMessage();
    }
}

function oldVal($key, $default = '') {
    global $old;
    return htmlspecialchars($old[$key] ?? $default);
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
        <div class="row align-items-center">
            <div class="col-md-6">
                <h4 class="mb-0 text-dark"><i class="fas fa-pen-to-square me-2"></i>ویرایش پیش‌فاکتور</h4>
            </div>
            <div class="col-md-6 text-end">
                <a href="index.php" class="btn btn-sm btn-outline-secondary">بازگشت</a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-4 py-2">
    <?php if ($errorMessage): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($errorMessage) ?></div>
    <?php endif; ?>

    <div class="bg-white rounded-4 shadow-sm p-4">
        <form method="post">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">شماره پیش‌فاکتور</label>
                    <input type="text" name="quotation_number" class="form-control" value="<?= oldVal('quotation_number') ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label">مشتری</label>
                    <select name="customer_id" class="form-select">
                        <option value="">انتخاب کنید</option>
                        <?php foreach ($customers as $customer): ?>
                            <?php
                                $label = trim(($customer['company_name'] ?? '-') . ' - ' . ($customer['manager_name'] ?? ''));
                                if ($label === '-') $label = 'مشتری #' . $customer['id'];
                            ?>
                            <option value="<?= (int)$customer['id'] ?>" <?= (string)$old['customer_id'] === (string)$customer['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">کارشناس فروش</label>
                    <select name="sales_agent_id" class="form-select">
                        <option value="">انتخاب کنید</option>
                        <?php foreach ($salesAgents as $agent): ?>
                            <?php $label = trim(($agent['fullname'] ?? 'کارشناس فروش') . ' - ' . ($agent['position'] ?? '')); ?>
                            <option value="<?= (int)$agent['id'] ?>" <?= (string)$old['sales_agent_id'] === (string)$agent['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">مبلغ کل</label>
                    <input type="number" step="0.01" name="total_price" class="form-control" value="<?= oldVal('total_price', '0') ?>">
                </div>

                <div class="col-md-3">
                    <label class="form-label">تخفیف</label>
                    <input type="number" step="0.01" name="discount" class="form-control" value="<?= oldVal('discount', '0') ?>">
                </div>

                <div class="col-md-3">
                    <label class="form-label">مالیات</label>
                    <input type="number" step="0.01" name="tax" class="form-control" value="<?= oldVal('tax', '0') ?>">
                </div>

                <div class="col-md-3">
                    <label class="form-label">مبلغ نهایی</label>
                    <input type="number" step="0.01" name="final_price" class="form-control" value="<?= oldVal('final_price', '0') ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label">وضعیت</label>
                    <select name="status" class="form-select">
                        <option value="draft" <?= $old['status'] === 'draft' ? 'selected' : '' ?>>پیش‌نویس</option>
                        <option value="quotation_sent" <?= $old['status'] === 'quotation_sent' ? 'selected' : '' ?>>ارسال شده</option>
                        <option value="accepted" <?= $old['status'] === 'accepted' ? 'selected' : '' ?>>تأیید شده</option>
                        <option value="rejected" <?= $old['status'] === 'rejected' ? 'selected' : '' ?>>رد شده</option>
                    </select>
                </div>

                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i>ثبت تغییرات
                    </button>
                    <a href="index.php" class="btn btn-outline-secondary px-4">انصراف</a>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>