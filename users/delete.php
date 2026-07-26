<?php
require_once __DIR__ . '/../includes/auth.php';
requirePermission('manage_users');

$id = (int)($_GET['id'] ?? ($_POST['id'] ?? 0));
if ($id <= 0) {
    die('شناسه نامعتبر است.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);

        header('Location: ' . BASE_URL . 'users/index.php?deleted=1');
        exit;
    } catch (Throwable $e) {
        die('خطا در حذف کاربر.');
    }
}

try {
    $stmt = $pdo->prepare("SELECT id, fullname, email, role FROM users WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die('کاربر یافت نشد.');
    }
} catch (Throwable $e) {
    die('خطا در دریافت اطلاعات کاربر.');
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>حذف کاربر</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            <h5 class="mb-3">حذف کاربر</h5>
            <p>آیا مطمئن هستید می‌خواهید این کاربر را حذف کنید؟</p>
            <ul class="mb-4">
                <li>نام: <?= htmlspecialchars($user['fullname'] ?? '-') ?></li>
                <li>ایمیل: <?= htmlspecialchars($user['email'] ?? '-') ?></li>
                <li>نقش: <?= htmlspecialchars($user['role'] ?? '-') ?></li>
            </ul>

            <form method="post" class="d-flex gap-2">
                <input type="hidden" name="id" value="<?= (int)$id ?>">
                <button type="submit" class="btn btn-danger">بله، حذف شود</button>
                <a href="<?= BASE_URL ?>users/index.php" class="btn btn-outline-secondary">انصراف</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>