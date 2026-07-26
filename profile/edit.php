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
        mobile,
        email,
        role,
        job_title,
        department,
        national_code,
        address,
        description,
        avatar,
        status,
        last_login,
        last_ip,
        created_at
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
| ذخیره اطلاعات
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $fullname      = trim($_POST['fullname'] ?? '');
    $mobile        = trim($_POST['mobile'] ?? '');
    $email         = trim($_POST['email'] ?? '');
    $job_title     = trim($_POST['job_title'] ?? '');
    $department    = trim($_POST['department'] ?? '');
    $national_code = trim($_POST['national_code'] ?? '');
    $address       = trim($_POST['address'] ?? '');
    $description   = trim($_POST['description'] ?? '');

    /*
    |--------------------------------------------------------------------------
    | اعتبارسنجی
    |--------------------------------------------------------------------------
    */

    if ($fullname == '') {

        $error = 'نام و نام خانوادگی الزامی است.';

    }

    elseif (mb_strlen($fullname) < 3) {

        $error = 'نام حداقل باید ۳ کاراکتر باشد.';

    }

    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = 'ایمیل معتبر نیست.';

    }

    elseif ($mobile != '' && !preg_match('/^09[0-9]{9}$/', $mobile)) {

        $error = 'شماره موبایل معتبر نیست.';

    }

    /*
    |--------------------------------------------------------------------------
    | بررسی ایمیل تکراری
    |--------------------------------------------------------------------------
    */

    if ($error == '') {

        $check = $pdo->prepare("
            SELECT id
            FROM users
            WHERE email=?
            AND id<>?
            LIMIT 1
        ");

        $check->execute([
            $email,
            $userId
        ]);

        if ($check->fetch()) {

            $error = 'این ایمیل قبلاً ثبت شده است.';

        }

    }

    /*
    |--------------------------------------------------------------------------
    | ذخیره
    |--------------------------------------------------------------------------
    */

    if ($error == '') {

        $update = $pdo->prepare("
            UPDATE users
            SET

                fullname=?,

                mobile=?,

                email=?,

                job_title=?,

                department=?,

                national_code=?,

                address=?,

                description=?

            WHERE id=?
        ");

        $saved = $update->execute([

            $fullname,

            $mobile,

            $email,

            $job_title,

            $department,

            $national_code,

            $address,

            $description,

            $userId

        ]);

        if ($saved) {

            /*
            |--------------------------------------------------------------------------
            | بروزرسانی Session
            |--------------------------------------------------------------------------
            */

            $_SESSION['fullname'] = $fullname;

            /*
            |--------------------------------------------------------------------------
            | ثبت لاگ
            |--------------------------------------------------------------------------
            */

            logActivity(

                'profile',

                'edit',

                $userId,

                'ویرایش اطلاعات پروفایل'

            );

            $success = 'اطلاعات با موفقیت ذخیره شد.';

            /*
            |--------------------------------------------------------------------------
            | خواندن مجدد اطلاعات
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

$pageTitle = 'ویرایش پروفایل';

$pageDescription = 'مدیریت اطلاعات حساب کاربری';

$pageIcon = 'fa-user-pen';

$contentFile = __DIR__ . '/views/edit_content.php';

require_once __DIR__ . '/../includes/layout.php';