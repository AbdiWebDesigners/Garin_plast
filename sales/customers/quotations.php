<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = 'پیش‌فاکتورهای من';
$quotations = [];
$error = '';
$customerId = 0;

function qBadge($status) {
    if ($status === 'draft') return '<span class="badge bg-secondary">پیش‌نویس</span>';
    if ($status === 'quotation_sent') return '<span class="badge bg-warning text-dark">ارسال شده</span>';
    if ($status === 'accepted') return '<span class="badge bg-success">تأیید شده</span>';
    if ($status === 'rejected') return '<span class="badge bg-danger">رد شده</span>';
    return '<span class="badge bg-dark">نامشخص</span>';
}

try {
    if (empty($_SESSION['user_id'])) {
        $error = 'کاربر وارد نشده است.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM customers WHERE user_id = ? LIMIT 1");
        $stmt->execute([$_SESSION['user_id']]);
        $customerId = (int)($stmt->fetchColumn() ?: 0);

        if ($customerId <= 0) {
            $stmt = $pdo->prepare("
                SELECT c.id
                FROM customers c
                LEFT JOIN users u ON u.id = c.user_id
                WHERE u.id = ? OR u.email = c.email OR u.mobile = c.phone
                LIMIT 1
            ");
            $stmt->execute([$_SESSION['user_id']]);
            $customerId = (int)($stmt->fetchColumn() ?: 0);
        }

        if ($customerId > 0) {
            $stmt = $pdo->prepare("
                SELECT 
                    q.id,
                    q.quotation_number,
                    q.total_price,
                    q.discount,
                    q.tax,
                    q.final_price,
                    q.status,
                    q.created_at,
                    c.company_name,
                    c.manager_name,
                    sa.position AS agent_position,
                    u.fullname AS agent_name
                FROM quotations q
                LEFT JOIN customers c ON c.id = q.customer_id
                LEFT JOIN sales_agents sa ON sa.id = q.sales_agent_id
                LEFT JOIN users u ON u.id = sa.user_id
                WHERE q.customer_id = ?
                ORDER BY q.id DESC
            ");
            $stmt->execute([$customerId]);
            $quotations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $error = 'مشتری مرتبط با کاربر فعلی پیدا نشد.';
        }
    }
} catch (PDOException $e) {
    $error = 'خطا در دریافت پیش‌فاکتورها: ' . $e->getMessage();
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
                <h4 class="mb-0 text-dark"><i class="fas fa-file-invoice me-2"></i>پیش‌فاکتورهای من</h4>
            </div>
            <div class="col-md-6 text-end">
                <a href="../index.php" class="btn btn-sm btn-outline-secondary">بازگشت به پنل</a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-4 py-2">
    <?php if ($error): ?>
        <div class="alert alert-warning"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="bg-white rounded-4 shadow-sm p-4">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>شماره</th>
                        <th>مشتری</th>
                        <th>کارشناس فروش</th>
                        <th>مبلغ کل</th>
                        <th>تخفیف</th>
                        <th>مالیات</th>
                        <th>نهایی</th>
                        <th>وضعیت</th>
                        <th>تاریخ ثبت</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($quotations)): ?>
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-3x d-block mb-3"></i>
                                پیش‌فاکتوری یافت نشد.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($quotations as $qrow): ?>
                            <tr>
                                <td><?= (int)$qrow['id'] ?></td>
                                <td class="fw-semibold">
                                    <?= htmlspecialchars($qrow['quotation_number'] ?? ('Q-' . str_pad((string)$qrow['id'], 5, '0', STR_PAD_LEFT))) ?>
                                </td>
                                <td><?= htmlspecialchars($qrow['company_name'] ?? ($qrow['manager_name'] ?? '-')) ?></td>
                                <td><?= htmlspecialchars(trim(($qrow['agent_name'] ?? '-') . ' ' . ($qrow['agent_position'] ?? ''))) ?></td>
                                <td><?= number_format((float)($qrow['total_price'] ?? 0)) ?> تومان</td>
                                <td><?= number_format((float)($qrow['discount'] ?? 0)) ?> تومان</td>
                                <td><?= number_format((float)($qrow['tax'] ?? 0)) ?> تومان</td>
                                <td class="fw-bold"><?= number_format((float)($qrow['final_price'] ?? 0)) ?> تومان</td>
                                <td><?= qBadge($qrow['status'] ?? 'draft') ?></td>
                                <td class="text-muted small"><?= htmlspecialchars($qrow['created_at'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>