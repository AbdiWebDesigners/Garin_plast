<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

$pageTitle = 'افزودن کارشناس فروش';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname        = trim($_POST['fullname'] ?? '');
    $mobile          = trim($_POST['mobile'] ?? '');
    $email           = trim($_POST['email'] ?? '');
    $password        = trim($_POST['password'] ?? '');
    $photo           = trim($_POST['photo'] ?? '');
    $position        = trim($_POST['position'] ?? '');
    $bio             = trim($_POST['bio'] ?? '');
    $commissionRate  = (float)($_POST['commission_rate'] ?? 0);

    if ($fullname === '') {
        $errorMessage = 'نام و نام خانوادگی الزامی است.';
    } elseif ($password === '') {
        $errorMessage = 'رمز عبور الزامی است.';
    } elseif ($commissionRate < 0 || $commissionRate > 100) {
        $errorMessage = 'درصد کمیسیون باید بین صفر تا صد باشد.';
    } else {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("
                INSERT INTO users (fullname, mobile, email, password, role, status)
                VALUES (?, ?, ?, ?, 'sales', 1)
            ");
            $stmt->execute([
                $fullname,
                $mobile !== '' ? $mobile : null,
                $email !== '' ? $email : null,
                password_hash($password, PASSWORD_DEFAULT)
            ]);

            $userId = (int)$pdo->lastInsertId();

            $stmt = $pdo->prepare("
                INSERT INTO sales_agents
                    (user_id, photo, position, bio, commission_rate)
                VALUES
                    (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $userId,
                $photo !== '' ? $photo : null,
                $position !== '' ? $position : null,
                $bio !== '' ? $bio : null,
                $commissionRate
            ]);

            $pdo->commit();

            header("Location: index.php?added=1");
            exit;
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $errorMessage = 'خطا در ذخیره کارشناس فروش: ' . $e->getMessage();
        }
    }
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

<div class="container p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">
            <i class="fas fa-user-plus me-2"></i>
            افزودن کارشناس فروش
        </h3>

        <a href="index.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-right me-1"></i>
            بازگشت
        </a>
    </div>

    <?php if ($errorMessage): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($errorMessage) ?>
        </div>
    <?php endif; ?>

    <form method="post" class="bg-white p-4 rounded-4 shadow-sm" autocomplete="off">
        <div class="row g-3">

            <div class="col-md-6">
                <label class="form-label">نام و نام خانوادگی <span class="text-danger">*</span></label>
                <input
                    type="text"
                    name="fullname"
                    class="form-control"
                    required
                    value="<?= htmlspecialchars($_POST['fullname'] ?? '') ?>"
                >
            </div>

            <div class="col-md-6">
                <label class="form-label">موبایل</label>
                <input
                    type="text"
                    name="mobile"
                    class="form-control"
                    value="<?= htmlspecialchars($_POST['mobile'] ?? '') ?>"
                >
            </div>

            <div class="col-md-6">
                <label class="form-label">ایمیل</label>
                <input
                    type="email"
                    name="email"
                    class="form-control"
                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                >
            </div>

            <div class="col-md-6">
                <label class="form-label">رمز عبور <span class="text-danger">*</span></label>
                <input
                    type="password"
                    name="password"
                    class="form-control"
                    required
                >
            </div>

            <div class="col-md-6">
                <label class="form-label">عکس</label>
                <input
                    type="text"
                    name="photo"
                    class="form-control"
                    placeholder="uploads/agents/photo.jpg"
                    value="<?= htmlspecialchars($_POST['photo'] ?? '') ?>"
                >
            </div>

            <div class="col-md-6">
                <label class="form-label">سمت</label>
                <input
                    type="text"
                    name="position"
                    class="form-control"
                    placeholder="کارشناس فروش"
                    value="<?= htmlspecialchars($_POST['position'] ?? '') ?>"
                >
            </div>

            <div class="col-md-6">
                <label class="form-label">درصد کمیسیون</label>
                <div class="input-group">
                    <input
                        type="number"
                        name="commission_rate"
                        class="form-control"
                        min="0"
                        max="100"
                        step="0.01"
                        value="<?= htmlspecialchars($_POST['commission_rate'] ?? '0') ?>"
                    >
                    <span class="input-group-text">درصد</span>
                </div>
                <div class="form-text">
                    این مقدار هنگام انتخاب کارشناس در فاکتور به‌صورت خودکار نمایش داده می‌شود.
                </div>
            </div>

            <div class="col-12">
                <label class="form-label">بیوگرافی</label>
                <textarea
                    name="bio"
                    class="form-control"
                    rows="5"
                ><?= htmlspecialchars($_POST['bio'] ?? '') ?></textarea>
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fas fa-save me-1"></i>
                    ثبت کارشناس
                </button>

                <a href="index.php" class="btn btn-outline-secondary px-4">
                    انصراف
                </a>
            </div>

        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
