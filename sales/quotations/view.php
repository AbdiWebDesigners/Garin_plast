<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

$pageTitle = 'جزئیات پیش‌فاکتور';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) die('شناسه نامعتبر است.');

try {
    $stmt = $pdo->prepare("
        SELECT q.*, c.company_name, c.manager_name, c.phone, c.email,
               sa.position AS agent_position, u.fullname AS agent_name
        FROM quotations q
        LEFT JOIN customers c ON c.id = q.customer_id
        LEFT JOIN sales_agents sa ON sa.id = q.sales_agent_id
        LEFT JOIN users u ON u.id = sa.user_id
        WHERE q.id = ?
        LIMIT 1
    ");
    $stmt->execute([$id]);
    $quotation = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$quotation) {
        die('پیش‌فاکتور یافت نشد.');
    }
} catch (PDOException $e) {
    die("خطا در دریافت اطلاعات: " . $e->getMessage());
}

function qBadgeView($status) {
    if ($status === 'draft') return '<span class="badge bg-secondary">پیش‌نویس</span>';
    if ($status === 'quotation_sent') return '<span class="badge bg-warning text-dark">ارسال شده</span>';
    if ($status === 'accepted') return '<span class="badge bg-success">تأیید شده</span>';
    if ($status === 'rejected') return '<span class="badge bg-danger">رد شده</span>';
    return '<span class="badge bg-dark">نامشخص</span>';
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
                <h4 class="mb-0 text-dark"><i class="fas fa-eye me-2"></i>جزئیات پیش‌فاکتور</h4>
            </div>
            <div class="col-md-6 text-end">
                <a href="index.php" class="btn btn-sm btn-outline-secondary">بازگشت</a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-4 py-2">
    <div class="bg-white rounded-4 shadow-sm p-4">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="row g-3">
                    <div class="col-md-6"><div class="border rounded-3 p-3"><div class="text-muted small">شماره پیش‌فاکتور</div><div class="fw-semibold"><?= htmlspecialchars($quotation['quotation_number'] ?? '-') ?></div></div></div>
                    <div class="col-md-6"><div class="border rounded-3 p-3"><div class="text-muted small">وضعیت</div><div><?= qBadgeView($quotation['status'] ?? 'draft') ?></div></div></div>
                    <div class="col-md-6"><div class="border rounded-3 p-3"><div class="text-muted small">مبلغ کل</div><div class="fw-semibold"><?= number_format((float)($quotation['total_price'] ?? 0)) ?> تومان</div></div></div>
                    <div class="col-md-6"><div class="border rounded-3 p-3"><div class="text-muted small">تخفیف</div><div class="fw-semibold"><?= number_format((float)($quotation['discount'] ?? 0)) ?> تومان</div></div></div>
                    <div class="col-md-6"><div class="border rounded-3 p-3"><div class="text-muted small">مالیات</div><div class="fw-semibold"><?= number_format((float)($quotation['tax'] ?? 0)) ?> تومان</div></div></div>
                    <div class="col-md-6"><div class="border rounded-3 p-3"><div class="text-muted small">مبلغ نهایی</div><div class="fw-bold"><?= number_format((float)($quotation['final_price'] ?? 0)) ?> تومان</div></div></div>
                    <div class="col-md-6"><div class="border rounded-3 p-3"><div class="text-muted small">مشتری</div><div class="fw-semibold"><?= htmlspecialchars(trim(($quotation['company_name'] ?? '-') . ' - ' . ($quotation['manager_name'] ?? ''))) ?></div></div></div>
                    <div class="col-md-6"><div class="border rounded-3 p-3"><div class="text-muted small">کارشناس فروش</div><div class="fw-semibold"><?= htmlspecialchars(trim(($quotation['agent_name'] ?? '-') . ' ' . ($quotation['agent_position'] ?? ''))) ?></div></div></div>
                    <div class="col-md-6"><div class="border rounded-3 p-3"><div class="text-muted small">تلفن مشتری</div><div class="fw-semibold"><?= htmlspecialchars($quotation['phone'] ?? '-') ?></div></div></div>
                    <div class="col-md-6"><div class="border rounded-3 p-3"><div class="text-muted small">ایمیل مشتری</div><div class="fw-semibold"><?= htmlspecialchars($quotation['email'] ?? '-') ?></div></div></div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="border rounded-4 p-4 bg-light">
                    <div class="mb-3">
                        <div class="text-muted small">تاریخ ثبت</div>
                        <div class="fw-semibold"><?= htmlspecialchars($quotation['created_at'] ?? '-') ?></div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">آخرین بروزرسانی</div>
                        <div class="fw-semibold"><?= htmlspecialchars($quotation['updated_at'] ?? '-') ?></div>
                    </div>
                    <a href="edit.php?id=<?= (int)$quotation['id'] ?>" class="btn btn-warning w-100 mb-2">
                        <i class="fas fa-pen me-1"></i>ویرایش
                    </a>
                    <a href="create.php" class="btn btn-primary w-100 mb-2">
                        <i class="fas fa-plus me-1"></i>پیش‌فاکتور جدید
                    </a>
                    <a href="index.php" class="btn btn-outline-secondary w-100">بازگشت به لیست</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>