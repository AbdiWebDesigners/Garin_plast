<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/activity_logger.php';

requireLogin();

global $pdo;

$userId = currentUserId();

$error = '';
$success = '';

/*
|--------------------------------------------------------------------------
| دریافت اطلاعات کاربر
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        id,
        fullname,
        avatar
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
| آپلود تصویر
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_FILES['avatar'])) {

        $error = 'فایلی انتخاب نشده است.';

    } elseif ($_FILES['avatar']['error'] != UPLOAD_ERR_OK) {

        $error = 'خطا در آپلود فایل.';

    } else {

        $allowed = [

            'image/jpeg' => 'jpg',

            'image/png' => 'png',

            'image/webp' => 'webp'

        ];

        $mime = mime_content_type($_FILES['avatar']['tmp_name']);

        if (!isset($allowed[$mime])) {

            $error = 'فرمت فایل مجاز نیست.';

        }

        elseif ($_FILES['avatar']['size'] > 2 * 1024 * 1024) {

            $error = 'حداکثر حجم فایل ۲ مگابایت است.';

        }

        else {

            $extension = $allowed[$mime];

            $fileName = 'avatar_' . $userId . '_' . time() . '.' . $extension;

            $uploadDir = __DIR__ . '/../uploads/avatars/';

            $destination = $uploadDir . $fileName;

            if (!is_dir($uploadDir)) {

                mkdir($uploadDir, 0777, true);

            }

            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $destination)) {

                /*
                -------------------------------------------------------------
                حذف تصویر قبلی
                -------------------------------------------------------------
                */

                if (

                    !empty($user['avatar'])

                    &&

                    file_exists($uploadDir . $user['avatar'])

                ) {

                    unlink($uploadDir . $user['avatar']);

                }

                /*
                -------------------------------------------------------------
                ذخیره دیتابیس
                -------------------------------------------------------------
                */

                $update = $pdo->prepare("
                    UPDATE users
                    SET avatar=?
                    WHERE id=?
                ");

                $update->execute([

                    $fileName,

                    $userId

                ]);

                /*
                -------------------------------------------------------------
                Activity Log
                -------------------------------------------------------------
                */

                logActivity(

                    'profile',

                    'change_avatar',

                    $userId,

                    'تغییر تصویر پروفایل'

                );

                $success = 'تصویر پروفایل با موفقیت تغییر کرد.';

                /*
                -------------------------------------------------------------
                بروزرسانی اطلاعات
                -------------------------------------------------------------
                */

                $stmt->execute([$userId]);

                $user = $stmt->fetch(PDO::FETCH_ASSOC);

            } else {

                $error = 'ذخیره فایل انجام نشد.';

            }

        }

    }

}

/*
|--------------------------------------------------------------------------
| Layout
|--------------------------------------------------------------------------
*/

$pageTitle = 'تصویر پروفایل';

$pageDescription = 'مدیریت تصویر حساب کاربری';

$pageIcon = 'fa-image';

$contentFile = __DIR__ . '/views/avatar_content.php';

require_once __DIR__ . '/../includes/layout.php';