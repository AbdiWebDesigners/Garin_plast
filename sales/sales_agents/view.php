<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

$pageTitle = 'جزئیات کارشناس فروش';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    die('شناسه نامعتبر است.');
}

try {
    $stmt = $pdo->prepare("
        SELECT sa.*, u.fullname, u.mobile, u.email, u.role, u.status AS user_status
        FROM sales_agents sa
        LEFT JOIN users u ON u.id = sa.user_id
        WHERE sa.id = ?
        LIMIT 1
    ");
    $stmt->execute([$id]);
    $agent = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$agent) {
        die('کارشناس فروش یافت نشد.');
    }
} catch (PDOException $e) {
    die("خطا در دریافت اطلاعات: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">جزئیات کارشناس فروش</h3>
        <a href="index.php" class="btn btn-outline-secondary">بازگشت</a>
    </div>

    <div class="bg-white p-4 rounded-4 shadow-sm">
        <div class="row g-4">
            <div class="col-md-4 text-center">
                <?php if (!empty($agent['photo'])): ?>
                    <img src="<?= htmlspecialchars($agent['photo']) ?>" class="img-fluid rounded-4 mb-3" alt="">
                <?php else: ?>
                    <div class="bg-secondary-subtle rounded-4 d-flex align-items-center justify-content-center" style="height: 250px;">
                        <i class="fas fa-user-tie fa-4x text-secondary"></i>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-md-8">
                <div class="row g-3">
                    <div class="col-md-6"><div class="border rounded-3 p-3"><div class="text-muted small">نام و نام خانوادگی</div><div class="fw-semibold"><?= htmlspecialchars($agent['fullname'] ?? '-') ?></div></div></div>
                    <div class="col-md-6"><div class="border rounded-3 p-3"><div class="text-muted small">موبایل</div><div class="fw-semibold"><?= htmlspecialchars($agent['mobile'] ?? '-') ?></div></div></div>
                    <div class="col-md-6"><div class="border rounded-3 p-3"><div class="text-muted small">ایمیل</div><div class="fw-semibold"><?= htmlspecialchars($agent['email'] ?? '-') ?></div></div></div>
                    <div class="col-md-6"><div class="border rounded-3 p-3"><div class="text-muted small">سمت</div><div class="fw-semibold"><?= htmlspecialchars($agent['position'] ?? '-') ?></div></div></div>
                    <div class="col-12"><div class="border rounded-3 p-3"><div class="text-muted small">بیوگرافی</div><div class="fw-semibold"><?= nl2br(htmlspecialchars($agent['bio'] ?? '-')) ?></div></div></div>
                    <div class="col-md-6"><div class="border rounded-3 p-3"><div class="text-muted small">نقش کاربر</div><div class="fw-semibold"><?= htmlspecialchars($agent['role'] ?? '-') ?></div></div></div>
                    <div class="col-md-6"><div class="border rounded-3 p-3"><div class="text-muted small">وضعیت کاربر</div><div class="fw-semibold"><?= ((int)($agent['user_status'] ?? 0) === 1) ? 'فعال' : 'غیرفعال' ?></div></div></div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <a href="edit.php?id=<?= (int)$agent['id'] ?>" class="btn btn-warning">
                        <i class="fas fa-pen me-1"></i>ویرایش
                    </a>
                    <a href="index.php" class="btn btn-outline-secondary">بازگشت به لیست</a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>