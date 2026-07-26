<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

$pageTitle = 'لیست مشتریان';

try {
    $stmt = $pdo->query("
        SELECT c.*, u.fullname AS user_fullname, u.email AS user_email, u.mobile AS user_mobile
        FROM customers c
        LEFT JOIN users u ON u.id = c.user_id
        ORDER BY c.id DESC
    ");
    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('خطا در دریافت مشتریان: ' . $e->getMessage());
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
                <h4 class="mb-0 text-dark"><i class="fas fa-users me-2"></i>لیست مشتریان</h4>
            </div>
            <div class="col-md-6 text-end">
                <a href="../dashboard.php" class="btn btn-sm btn-outline-secondary">داشبورد</a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-4 py-2">
    <div class="bg-white rounded-4 shadow-sm p-4">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>نام شرکت</th>
                        <th>مدیر</th>
                        <th>تلفن</th>
                        <th>ایمیل</th>
                        <th>کاربر مرتبط</th>
                        <th>وضعیت</th>
                        <th class="text-center">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($customers)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">مشتری‌ای یافت نشد.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($customers as $c): ?>
                            <tr>
                                <td><?= (int)$c['id'] ?></td>
                                <td class="fw-semibold"><?= htmlspecialchars($c['company_name'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($c['manager_name'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($c['phone'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($c['email'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($c['user_fullname'] ?? 'ندارد') ?></td>
                                <td>
                                    <span class="badge <?= (($c['status'] ?? '') === 'active') ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= htmlspecialchars($c['status'] ?? '-') ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="profile.php?id=<?= (int)$c['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i>پروفایل
                                    </a>
                                </td>
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