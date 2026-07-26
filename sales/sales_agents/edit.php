<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

$pageTitle = 'ویرایش کارشناس فروش';
$errorMessage = '';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    die('شناسه نامعتبر است.');
}

try {
    $stmt = $pdo->prepare("
        SELECT sa.*, u.fullname, u.mobile, u.email, u.password
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $photo = trim($_POST['photo'] ?? '');
    $position = trim($_POST['position'] ?? '');
    $bio = trim($_POST['bio'] ?? '');

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("
            UPDATE users
            SET fullname = ?, mobile = ?, email = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $fullname,
            $mobile ?: null,
            $email ?: null,
            (int)$agent['user_id']
        ]);

        $stmt = $pdo->prepare("
            UPDATE sales_agents
            SET photo = ?, position = ?, bio = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $photo ?: null,
            $position ?: null,
            $bio ?: null,
            $id
        ]);

        $pdo->commit();
        header("Location: index.php?updated=1");
        exit;
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $errorMessage = 'خطا در ویرایش کارشناس فروش: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container p-4">
    <h3 class="mb-4">ویرایش کارشناس فروش</h3>

    <?php if ($errorMessage): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($errorMessage) ?></div>
    <?php endif; ?>

    <form method="post" class="bg-white p-4 rounded-4 shadow-sm">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">نام و نام خانوادگی</label>
                <input type="text" name="fullname" class="form-control" required value="<?= htmlspecialchars($agent['fullname'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">موبایل</label>
                <input type="text" name="mobile" class="form-control" value="<?= htmlspecialchars($agent['mobile'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">ایمیل</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($agent['email'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">عکس</label>
                <input type="text" name="photo" class="form-control" value="<?= htmlspecialchars($agent['photo'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">سمت</label>
                <input type="text" name="position" class="form-control" value="<?= htmlspecialchars($agent['position'] ?? '') ?>">
            </div>
            <div class="col-12">
                <label class="form-label">بیوگرافی</label>
                <textarea name="bio" class="form-control" rows="5"><?= htmlspecialchars($agent['bio'] ?? '') ?></textarea>
            </div>
            <div class="col-12">
                <button class="btn btn-primary">ثبت تغییرات</button>
                <a href="index.php" class="btn btn-outline-secondary">بازگشت</a>
            </div>
        </div>
    </form>
</div>
</body>
</html>