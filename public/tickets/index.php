<?php
require_once __DIR__ . '/../../includes/auth.php';
requireLogin();

$tickets = [];
try {
    $stmt = $pdo->query("
        SELECT t.*, u.fullname AS customer_name
        FROM tickets t
        LEFT JOIN users u ON u.id = t.customer_id
        ORDER BY t.created_at DESC
    ");
    $tickets = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
} catch (Throwable $e) {
    $tickets = [];
}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>مدیریت تیکت‌ها</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <div class="d-flex justify-content-between mb-4">
        <h2>تیکت‌ها</h2>
        <a href="create.php" class="btn btn-success">تیکت جدید</a>
    </div>

    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>شماره</th>
                <th>مشتری</th>
                <th>موضوع</th>
                <th>وضعیت</th>
                <th>اولویت</th>
                <th>تاریخ</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tickets as $t): ?>
            <tr>
                <td>#<?= $t['id'] ?></td>
                <td><?= htmlspecialchars($t['customer_name']) ?></td>
                <td><?= htmlspecialchars($t['subject']) ?></td>
                <td>
                    <span class="badge bg-<?= $t['status']=='open' ? 'warning' : 'success' ?>">
                        <?= $t['status'] ?>
                    </span>
                </td>
                <td><?= $t['priority'] ?></td>
                <td><?= $t['created_at'] ?></td>
                <td>
                    <a href="view.php?id=<?= $t['id'] ?>" class="btn btn-sm btn-primary">مشاهده</a>
<a href="delete.php?id=<?= (int)$t['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('مطمئن هستید؟')">حذف</a>

</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>