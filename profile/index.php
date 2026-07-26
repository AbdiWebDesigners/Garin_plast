<?php

require_once __DIR__ . '/../includes/auth.php';
requireLogin();

global $pdo;

$userId = currentUserId();

$stmt = $pdo->prepare("
SELECT
    id,
    fullname,
    mobile,
    email,
    role,
    status,
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

$status = $user['status'] ? 'فعال' : 'غیرفعال';

$pageTitle = 'پروفایل من';
$pageDescription = 'مشاهده اطلاعات حساب کاربری';
$pageIcon = 'fa-user';

$contentFile = __DIR__ . '/views/index_content.php';

require_once __DIR__ . '/../includes/layout.php';