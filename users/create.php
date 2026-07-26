<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

requireLogin();
requirePermission('manage_users');

global $pdo;

$errors = [];
$success = '';

/*
|--------------------------------------------------------------------------
| ثبت کاربر
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $fullname       = trim($_POST['fullname'] ?? '');
    $mobile         = trim($_POST['mobile'] ?? '');
    $email          = trim($_POST['email'] ?? '');
    $password       = $_POST['password'] ?? '';
    $role           = trim($_POST['role'] ?? 'user');
    $status         = isset($_POST['status']) ? 1 : 0;

    $job_title      = trim($_POST['job_title'] ?? '');
    $department     = trim($_POST['department'] ?? '');
    $national_code  = trim($_POST['national_code'] ?? '');
    $address        = trim($_POST['address'] ?? '');
    $description    = trim($_POST['description'] ?? '');

    /*
    |--------------------------------------------------------------------------
    | اعتبارسنجی
    |--------------------------------------------------------------------------
    */

    if ($fullname == '') {
        $errors[] = 'نام و نام خانوادگی الزامی است.';
    }

    if ($email == '') {
        $errors[] = 'ایمیل الزامی است.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'ایمیل معتبر نیست.';
    }

    if (strlen($password) < 6) {
        $errors[] = 'رمز عبور حداقل باید 6 کاراکتر باشد.';
    }

    /*
    |--------------------------------------------------------------------------
    | بررسی تکراری بودن ایمیل
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        SELECT id
        FROM users
        WHERE email=?
        LIMIT 1
    ");

    $stmt->execute([$email]);

    if ($stmt->fetch()) {

        $errors[] = 'این ایمیل قبلاً ثبت شده است.';
    }

    /*
    |--------------------------------------------------------------------------
    | آپلود آواتار
    |--------------------------------------------------------------------------
    */

    $avatar = null;

    if (
        isset($_FILES['avatar']) &&
        $_FILES['avatar']['error'] == 0
    ) {

        $uploadDir = __DIR__ . '/../uploads/avatars/';

        if (!is_dir($uploadDir)) {

            mkdir($uploadDir, 0777, true);
        }

        $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));

        $fileName = uniqid('avatar_') . '.' . $ext;

        move_uploaded_file(
            $_FILES['avatar']['tmp_name'],
            $uploadDir . $fileName
        );

        $avatar = 'uploads/avatars/' . $fileName;
    }

    /*
    |--------------------------------------------------------------------------
    | ذخیره
    |--------------------------------------------------------------------------
    */

    if (empty($errors)) {

        $stmt = $pdo->prepare("
        INSERT INTO users(

            fullname,
            mobile,
            email,
            password,
            role,
            job_title,
            department,
            national_code,
            address,
            description,
            avatar,
            status

        )

        VALUES(

            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?

        )
        ");

        $stmt->execute([

            $fullname,
            $mobile,
            $email,
            password_hash($password, PASSWORD_DEFAULT),
            $role,
            $job_title,
            $department,
            $national_code,
            $address,
            $description,
            $avatar,
            $status

        ]);

        header("Location:index.php?success=1");
        exit;
    }
}

/*
|--------------------------------------------------------------------------
| اطلاعات صفحه
|--------------------------------------------------------------------------
*/

$pageTitle = 'ایجاد کاربر';

$pageDescription = 'ثبت کاربر جدید';

$pageIcon = 'fa-user-plus';

$contentFile = __DIR__ . '/views/create_content.php';

/*
|--------------------------------------------------------------------------
| Layout
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../includes/layout.php';