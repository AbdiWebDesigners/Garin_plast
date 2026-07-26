<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';

$message = '';
$error = '';
$debugOtp = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mobile = trim($_POST['mobile'] ?? '');

    if ($mobile === '') {
        $error = 'شماره موبایل را وارد کنید.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, fullname, mobile, status FROM users WHERE mobile = ? LIMIT 1");
            $stmt->execute([$mobile]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user || (int)$user['status'] !== 1) {
                $message = 'اگر شماره موبایل در سیستم موجود باشد، کد بازیابی برای آن ایجاد می‌شود.';
            } else {
                $otp = (string) random_int(100000, 999999);
                $tokenHash = hash('sha256', $otp);
                $expiresAt = date('Y-m-d H:i:s', time() + 300);

                $pdo->beginTransaction();

                $delete = $pdo->prepare("DELETE FROM reset_tokens WHERE user_id = ?");
                $delete->execute([$user['id']]);

                $insert = $pdo->prepare("
                    INSERT INTO reset_tokens (user_id, token, expires_at, created_at, updated_at)
                    VALUES (?, ?, ?, NOW(), NOW())
                ");
                $insert->execute([$user['id'], $tokenHash, $expiresAt]);

                $pdo->commit();

                $_SESSION['reset_user_id'] = (int)$user['id'];
                $_SESSION['reset_mobile'] = $mobile;
                $_SESSION['reset_otp'] = $otp;
                $_SESSION['reset_otp_expires'] = $expiresAt;

                $message = 'کد بازیابی ساخته شد. برای تست، کد پایین را ببینید.';
                $debugOtp = $otp;
            }
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $error = 'خطا در پردازش درخواست.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فراموشی رمز عبور</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body{
            min-height:100vh;
            margin:0;
            background: linear-gradient(135deg, #0f172a 0%, #312e81 45%, #6d28d9 100%);
            display:flex;
            align-items:center;
        }
        .card-box{
            max-width:480px;
            margin:0 auto;
            border:0;
            border-radius:24px;
            overflow:hidden;
            box-shadow:0 20px 60px rgba(0,0,0,.28);
            background: rgba(255,255,255,.96);
        }
        .head{
            padding:28px;
            text-align:center;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color:#fff;
        }
        .body{ padding:30px; }
        .form-control{ border-radius:14px; padding:14px 16px; }
        .btn-main{
            border:0;
            border-radius:14px;
            padding:13px 16px;
            font-weight:700;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        }
        .otp-box{
            letter-spacing:6px;
            font-size:1.6rem;
            font-weight:700;
            text-align:center;
            background:#f8fafc;
            border:1px dashed #cbd5e1;
            border-radius:14px;
            padding:12px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card-box">
        <div class="head">
            <h3 class="mb-1">فراموشی رمز عبور</h3>
            <div class="opacity-75">کد بازیابی از طریق موبایل</div>
        </div>
        <div class="body">
            <?php if ($message): ?>
                <div class="alert alert-success rounded-4 border-0"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger rounded-4 border-0"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if ($debugOtp): ?>
                <div class="alert alert-warning rounded-4 border-0">
                    <div class="mb-2">کد تست شما:</div>
                    <div class="otp-box"><?= htmlspecialchars($debugOtp) ?></div>
                </div>
                <a href="reset_password.php" class="btn btn-main btn-primary w-100 text-white mb-3">رفتن به صفحه تایید کد</a>
            <?php endif; ?>

            <form method="POST" autocomplete="off">
                <div class="mb-3">
                    <label class="form-label fw-semibold">شماره موبایل</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-mobile-screen text-secondary"></i></span>
                        <input type="text" name="mobile" class="form-control" placeholder="09123456789" required value="<?= htmlspecialchars($_POST['mobile'] ?? '') ?>">
                    </div>
                </div>

                <button type="submit" class="btn btn-main btn-primary w-100 text-white">
                    ارسال کد بازیابی
                </button>
            </form>
        </div>
    </div>
</div>
</body>
</html>