<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


session_start();

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';

require_once __DIR__ . '/includes/auth.php';

if (isLoggedIn()) {
    header("Location: " . BASE_URL . "admin/dashboard.php");
    exit;
}


$error = '';

function redirectByRole(): void
{
    header("Location: " . BASE_URL . "admin/dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'ایمیل و رمز عبور را وارد کنید.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, fullname, role, password, status FROM users WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            $validPassword = false;

            if ($user) {
                if (password_verify($password, $user['password'])) {
                    $validPassword = true;
                } elseif (md5($password) === $user['password']) {
                    $validPassword = true;
                    $newHash = password_hash($password, PASSWORD_DEFAULT);
                    $update = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $update->execute([$newHash, $user['id']]);
                } elseif ($password === $user['password']) {
                    $validPassword = true;
                    $newHash = password_hash($password, PASSWORD_DEFAULT);
                    $update = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $update->execute([$newHash, $user['id']]);
                }
            }

            if ($user && (int)$user['status'] === 1 && $validPassword) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = (int)$user['id'];
                $_SESSION['fullname'] = $user['fullname'];
                $_SESSION['role'] = strtolower(trim($user['role']));
                redirectByRole();
            }

            $error = 'ایمیل یا رمز عبور اشتباه است.';
        } catch (Throwable $e) {
            $error = 'خطا در ورود.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ورود به سیستم - CRM گارین</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body{
            min-height:100vh;
            margin:0;
            background:
                radial-gradient(circle at top left, rgba(255,255,255,.12), transparent 28%),
                linear-gradient(135deg, #0f172a 0%, #312e81 45%, #6d28d9 100%);
            display:flex;
            align-items:center;
        }
        .login-card{
            max-width:460px;
            margin:0 auto;
            border:0;
            border-radius:24px;
            overflow:hidden;
            box-shadow:0 20px 60px rgba(0,0,0,.28);
            backdrop-filter: blur(12px);
            background: rgba(255,255,255,.94);
        }
        .login-header{
            padding:28px 28px 18px;
            text-align:center;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color:#fff;
        }
        .brand-badge{
            width:64px;
            height:64px;
            border-radius:18px;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            margin-bottom:14px;
            background: rgba(255,255,255,.18);
            border:1px solid rgba(255,255,255,.25);
        }
        .brand-badge i{ font-size:28px; }
        .login-body{ padding:30px; }
        .form-control{ border-radius:14px; padding:14px 16px; }
        .input-group-text{ border-radius:14px; background:#f8fafc; }
        .btn-login{
            border:0;
            border-radius:14px;
            padding:13px 16px;
            font-weight:700;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            box-shadow: 0 10px 24px rgba(79,70,229,.25);
        }
        .hint{ color:#64748b; font-size:.92rem; }
    </style>
</head>
<body>
<div class="container">
    <div class="login-card">
        <div class="login-header">
            <div class="brand-badge">
                <i class="fas fa-shield-halved"></i>
            </div>
            <h3 class="mb-1">ورود به سیستم</h3>
            <div class="opacity-75">CRM گارین</div>
        </div>

        <div class="login-body">
            <?php if ($error): ?>
                <div class="alert alert-danger rounded-4 border-0 mb-4">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" autocomplete="off">
                <div class="mb-3">
                    <label class="form-label fw-semibold">ایمیل</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope text-secondary"></i></span>
                        <input type="email" name="email" class="form-control" placeholder="example@email.com" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">رمز عبور</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock text-secondary"></i></span>
                        <input type="password" name="password" class="form-control" placeholder="رمز عبور را وارد کنید" required>
                    </div>
                </div>
                <a href="forgetpassword.php">رمز عبور را فراموش کرده‌اید؟</a>
                <button type="submit" class="btn btn-login btn-primary w-100 text-white">
                    ورود به پنل
                </button>
            </form>
        </div>
    </div>
</div>
</body>
</html>