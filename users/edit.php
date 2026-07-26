<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();
requirePermission('manage_users');

global $pdo;

/*
|--------------------------------------------------------------------------
| دریافت شناسه کاربر
|--------------------------------------------------------------------------
*/

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: index.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| دریافت اطلاعات کاربر
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
SELECT *
FROM users
WHERE id = ?
LIMIT 1
");

$stmt->execute([$id]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die('کاربر یافت نشد.');
}

/*
|--------------------------------------------------------------------------
| متغیرها
|--------------------------------------------------------------------------
*/

$error = '';
$success = '';

$fullname       = $user['fullname'];
$email          = $user['email'];
$mobile         = $user['mobile'];
$role           = $user['role'];
$status         = $user['status'];
$job_title      = $user['job_title'];
$department     = $user['department'];
$national_code  = $user['national_code'];
$address        = $user['address'];
$description    = $user['description'];
$avatar         = $user['avatar'];

/*
|--------------------------------------------------------------------------
| ثبت فرم
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $fullname      = trim($_POST['fullname'] ?? '');
    $email         = trim($_POST['email'] ?? '');
    $mobile        = trim($_POST['mobile'] ?? '');
    $password      = trim($_POST['password'] ?? '');
    $role          = trim($_POST['role'] ?? 'user');
    $status        = (int)($_POST['status'] ?? 1);

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

    } elseif ($email == '') {

        $error = 'ایمیل الزامی است.';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = 'ایمیل معتبر نیست.';

    } else {

        /*
        |--------------------------------------------------------------------------
        | بررسی تکراری نبودن ایمیل
        |--------------------------------------------------------------------------
        */

        $check = $pdo->prepare("
        SELECT id
        FROM users
        WHERE email = ?
        AND id <> ?
        ");

        $check->execute([
            $email,
            $id
        ]);

        if ($check->fetch()) {

            $error = 'این ایمیل قبلاً ثبت شده است.';

        }

    }

    /*
    |--------------------------------------------------------------------------
    | اگر خطایی نبود ادامه بده...
    |--------------------------------------------------------------------------
    */

    if (empty($error)) {
        /*
        |--------------------------------------------------------------------------
        | آپلود آواتار
        |--------------------------------------------------------------------------
        */

        if (
            isset($_FILES['avatar']) &&
            $_FILES['avatar']['error'] == 0
        ) {

            $ext = strtolower(pathinfo(
                $_FILES['avatar']['name'],
                PATHINFO_EXTENSION
            ));

            $allowed = ['jpg','jpeg','png','gif','webp'];

            if (in_array($ext,$allowed)) {

                $fileName = uniqid('avatar_').'.'.$ext;

                $uploadDir = __DIR__.'/../uploads/avatars/';

                if(!is_dir($uploadDir)){
                    mkdir($uploadDir,0777,true);
                }

                move_uploaded_file(
                    $_FILES['avatar']['tmp_name'],
                    $uploadDir.$fileName
                );

                $avatar = $fileName;
            }

        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE
        |--------------------------------------------------------------------------
        */

        if($password!=''){

            $passwordHash=password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $sql="
            UPDATE users SET

                fullname=?,
                mobile=?,
                email=?,
                password=?,
                role=?,
                status=?,
                job_title=?,
                department=?,
                national_code=?,
                address=?,
                description=?,
                avatar=?,
                updated_at=NOW()

            WHERE id=?
            ";

            $stmt=$pdo->prepare($sql);

            $stmt->execute([

                $fullname,
                $mobile,
                $email,
                $passwordHash,
                $role,
                $status,
                $job_title,
                $department,
                $national_code,
                $address,
                $description,
                $avatar,
                $id

            ]);

        }else{

            $sql="
            UPDATE users SET

                fullname=?,
                mobile=?,
                email=?,
                role=?,
                status=?,
                job_title=?,
                department=?,
                national_code=?,
                address=?,
                description=?,
                avatar=?,
                updated_at=NOW()

            WHERE id=?
            ";

            $stmt=$pdo->prepare($sql);

            $stmt->execute([

                $fullname,
                $mobile,
                $email,
                $role,
                $status,
                $job_title,
                $department,
                $national_code,
                $address,
                $description,
                $avatar,
                $id

            ]);

        }

        /*
        |--------------------------------------------------------------------------
        | ثبت لاگ
        |--------------------------------------------------------------------------
        */

        if(function_exists('logActivity')){

            logActivity(

                currentUserId(),

                'users',

                'edit',

                'ویرایش کاربر : '.$fullname

            );

        }

        $success='اطلاعات کاربر با موفقیت بروزرسانی شد.';

    }

}

/*
|--------------------------------------------------------------------------
| اطلاعات صفحه
|--------------------------------------------------------------------------
*/

$pageTitle='ویرایش کاربر';

$pageDescription='ویرایش اطلاعات کاربر';

$pageIcon='fa-user-pen';

$contentFile=__DIR__.'/views/edit_content.php';

/*
|--------------------------------------------------------------------------
| Layout
|--------------------------------------------------------------------------
*/

require_once __DIR__.'/../includes/layout.php';