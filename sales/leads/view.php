<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

$pageTitle = 'جزئیات مشتری بالقوه';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    die('شناسه نامعتبر است.');
}

try {
    $stmt = $pdo->prepare("
        SELECT sl.*, sa.position AS agent_position, u.fullname AS agent_name, p.title AS product_title
        FROM sales_leads sl
        LEFT JOIN sales_agents sa ON sa.id = sl.sales_agent_id
        LEFT JOIN users u ON u.id = sa.user_id
        LEFT JOIN products p ON p.id = sl.interest_product_id
        WHERE sl.id = ?
        LIMIT 1
    ");
    $stmt->execute([$id]);
    $lead = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$lead) {
        die('مشتری بالقوه یافت نشد.');
    }
} catch (PDOException $e) {
    die("خطا در دریافت اطلاعات: " . $e->getMessage());
}

function badge($status) {
    if ($status === 'new') return '<span class="badge bg-primary">جدید</span>';
    if ($status === 'contacted') return '<span class="badge bg-info">تماس گرفته شده</span>';
    if ($status === 'quotation_sent') return '<span class="badge bg-warning text-dark">پیش‌فاکتور ارسال شد</span>';
    if ($status === 'negotiation') return '<span class="badge bg-success">مذاکره</span>';
    if ($status === 'won') return '<span class="badge bg-success">برنده</span>';
    if ($status === 'lost') return '<span class="badge bg-danger">از دست رفته</span>';
    return '<span class="badge bg-secondary">نامشخص</span>';
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
                <h4 class="mb-0 text-dark"><i class="fas fa-eye me-2"></i>جزئیات مشتری بالقوه</h4>
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
            <div class="col-md-8">
                <h5 class="fw-bold mb-3"><?= htmlspecialchars($lead['customer_name'] ?? '-') ?></h5>
                <div class="row g-3">
                    <div class="col-md-6"><div class="border rounded-3 p-3"><div class="text-muted small">شرکت</div><div class="fw-semibold"><?= htmlspecialchars($lead['company_name'] ?? '-') ?></div></div></div>
                    <div class="col-md-6"><div class="border rounded-3 p-3"><div class="text-muted small">تلفن</div><div class="fw-semibold"><?= htmlspecialchars($lead['phone'] ?? '-') ?></div></div></div>
                    <div class="col-md-6"><div class="border rounded-3 p-3"><div class="text-muted small">ایمیل</div><div class="fw-semibold"><?= htmlspecialchars($lead['email'] ?? '-') ?></div></div></div>
                    <div class="col-md-6"><div class="border rounded-3 p-3"><div class="text-muted small">منبع</div><div class="fw-semibold"><?= htmlspecialchars($lead['source'] ?? '-') ?></div></div></div>
                    <div class="col-md-6"><div class="border rounded-3 p-3"><div class="text-muted small">کارشناس فروش</div><div class="fw-semibold"><?= htmlspecialchars(($lead['agent_name'] ?? '-') . ' ' . ($lead['agent_position'] ?? '')) ?></div></div></div>
                    <div class="col-md-6"><div class="border rounded-3 p-3"><div class="text-muted small">محصول مورد علاقه</div><div class="fw-semibold"><?= htmlspecialchars($lead['product_title'] ?? '-') ?></div></div></div>
                    <div class="col-md-6"><div class="border rounded-3 p-3"><div class="text-muted small">پیگیری بعدی</div><div class="fw-semibold"><?= htmlspecialchars($lead['next_followup'] ?? '-') ?></div></div></div>
                    <div class="col-md-6"><div class="border rounded-3 p-3"><div class="text-muted small">وضعیت</div><div class="fw-semibold"><?= badge($lead['status'] ?? 'new') ?></div></div></div>
                    <div class="col-12"><div class="border rounded-3 p-3"><div class="text-muted small">یادداشت‌ها</div><div class="fw-semibold"><?= nl2br(htmlspecialchars($lead['notes'] ?? '-')) ?></div></div></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="border rounded-4 p-4 bg-light">
                    <div class="mb-3">
                        <div class="text-muted small">تاریخ ثبت</div>
                        <div class="fw-semibold"><?= htmlspecialchars($lead['created_at'] ?? '-') ?></div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">آخرین بروزرسانی</div>
                        <div class="fw-semibold"><?= htmlspecialchars($lead['updated_at'] ?? '-') ?></div>
                    </div>
                    <a href="add.php" class="btn btn-primary w-100 mb-2"><i class="fas fa-plus me-1"></i>افزودن جدید</a>
                    <a href="index.php" class="btn btn-outline-secondary w-100">بازگشت به لیست</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>