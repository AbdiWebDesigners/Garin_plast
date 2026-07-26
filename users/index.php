<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

requireLogin();

global $pdo;

/*
|--------------------------------------------------------------------------
| تنظیمات صفحه
|--------------------------------------------------------------------------
*/

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 15;
$offset = ($page - 1) * $perPage;

$search = trim($_GET['search'] ?? '');
$status = trim($_GET['status'] ?? '');
$role   = trim($_GET['role'] ?? '');

/*
|--------------------------------------------------------------------------
| شرط جستجو
|--------------------------------------------------------------------------
*/

$where = " WHERE 1 ";
$params = [];

if ($search !== '') {

    $where .= " AND (
        fullname LIKE ?
        OR mobile LIKE ?
        OR email LIKE ?
        OR avatar LIKE?
        OR role LIKE?
        OR status LIKE?
        OR last_login LIKE?
        OR created_at LIKE?
    )";

    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

if ($status !== '') {

    $where .= " AND status = ?";
    $params[] = $status;
}

if ($role !== '') {

    $where .= " AND role = ?";
    $params[] = $role;
}

/*
|--------------------------------------------------------------------------
| تعداد کل کاربران
|--------------------------------------------------------------------------
*/

$countSql = "
SELECT COUNT(*)
FROM users
{$where}
";

$stmt = $pdo->prepare($countSql);
$stmt->execute($params);

$totalRows = (int)$stmt->fetchColumn();

$totalPages = max(1, ceil($totalRows / $perPage));

/*
|--------------------------------------------------------------------------
| دریافت کاربران
|--------------------------------------------------------------------------
*/

$listSql = "
SELECT
    id,
    fullname,
    email,
    mobile,
    avatar,
    role,
    status,
    last_login,
    created_at
FROM users

{$where}

ORDER BY id DESC

LIMIT {$offset}, {$perPage}
";

$stmt = $pdo->prepare($listSql);
$stmt->execute($params);

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| آمار
|--------------------------------------------------------------------------
*/

$totalUsers = (int)$pdo->query("
SELECT COUNT(*)
FROM users
")->fetchColumn();

$activeUsers = (int)$pdo->query("
SELECT COUNT(*)
FROM users
WHERE status='1'
")->fetchColumn();

$inactiveUsers = (int)$pdo->query("
SELECT COUNT(*)
FROM users
WHERE status='0'
")->fetchColumn();

$adminUsers = (int)$pdo->query("
SELECT COUNT(*)
FROM users
WHERE role='admin'
")->fetchColumn();

/*
|--------------------------------------------------------------------------
| لیست Role ها
|--------------------------------------------------------------------------
*/

$roleStmt = $pdo->query("
SELECT DISTINCT role
FROM users
ORDER BY role
");

$roles = $roleStmt->fetchAll(PDO::FETCH_COLUMN);

/*
|--------------------------------------------------------------------------
| اطلاعات صفحه
|--------------------------------------------------------------------------
*/

$pageTitle = 'مدیریت کاربران';

$pageDescription = 'مدیریت کاربران سیستم';

$pageIcon = 'fa-users';

$contentFile = __DIR__ . '/views/index_content.php';

/*
|--------------------------------------------------------------------------
| Layout
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../includes/layout.php';