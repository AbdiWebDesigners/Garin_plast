<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/activity_logger.php';

requireLogin();

global $pdo;

$userId = currentUserId();

$success = '';
$error = '';

/*
|--------------------------------------------------------------------------
| دریافت اطلاعات کاربر
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        id,
        fullname,
        password
    FROM users
    WHERE id=?
    LIMIT 1
");

$stmt->execute([$userId]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {

    die('کاربر یافت نشد.');

}

/*
|--------------------------------------------------------------------------
| تغییر رمز عبور
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $currentPassword = trim($_POST['current_password'] ?? '');
    $newPassword     = trim($_POST['new_password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');

    /*
    |--------------------------------------------------------------------------
    | اعتبارسنجی
    |--------------------------------------------------------------------------
    */

    if ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {

        $error = 'تمام فیلدها الزامی هستند.';

    }

    elseif (!password_verify($currentPassword, $user['password'])) {

        $error = 'رمز عبور فعلی صحیح نیست.';

    }

    elseif ($newPassword !== $confirmPassword) {

        $error = 'تکرار رمز عبور صحیح نیست.';

    }

    elseif (strlen($newPassword) < 8) {

        $error = 'رمز عبور باید حداقل 8 کاراکتر باشد.';

    }

    elseif (!preg_match('/[A-Z]/', $newPassword)) {

        $error = 'رمز عبور باید حداقل یک حرف بزرگ انگلیسی داشته باشد.';

    }

    elseif (!preg_match('/[a-z]/', $newPassword)) {

        $error = 'رمز عبور باید حداقل یک حرف کوچک انگلیسی داشته باشد.';

    }

    elseif (!preg_match('/[0-9]/', $newPassword)) {

        $error = 'رمز عبور باید حداقل یک عدد داشته باشد.';

    }

    elseif (!preg_match('/[\W_]/', $newPassword)) {

        $error = 'رمز عبور باید حداقل یک کاراکتر خاص داشته باشد.';

    }

    elseif (password_verify($newPassword, $user['password'])) {

        $error = 'رمز عبور جدید نباید با رمز فعلی یکسان باشد.';

    }

    /*
    |--------------------------------------------------------------------------
    | ذخیره رمز
    |--------------------------------------------------------------------------
    */

    if ($error === '') {

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $update = $pdo->prepare("
            UPDATE users
            SET password=?
            WHERE id=?
        ");

        if ($update->execute([$hashedPassword, $userId])) {

            logActivity(
                'profile',
                'change_password',
                $userId,
                'تغییر رمز عبور'
            );

            $success = 'رمز عبور با موفقیت تغییر کرد.';

            /*
            |--------------------------------------------------------------------------
            | بروزرسانی اطلاعات
            |--------------------------------------------------------------------------
            */

            $stmt->execute([$userId]);

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

        } else {

            $error = 'خطا در ذخیره اطلاعات.';

        }

    }

}

/*
|--------------------------------------------------------------------------
| Layout
|--------------------------------------------------------------------------
*/

$pageTitle = 'تغییر رمز عبور';

$pageDescription = 'مدیریت امنیت حساب کاربری';

$pageIcon = 'fa-key';

$contentFile = __DIR__ . '/views/change_password_content.php';

require_once __DIR__ . '/../includes/layout.php';