<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

requireLogin();

global $pdo;

$userId = currentUserId();

/*
|--------------------------------------------------------------------------
| تنظیمات صفحه
|--------------------------------------------------------------------------
*/

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

$search = trim($_GET['search'] ?? '');
$module = trim($_GET['module'] ?? '');

/*
|--------------------------------------------------------------------------
| شرط جستجو
|--------------------------------------------------------------------------
*/

$where = " WHERE user_id = ? ";
$params = [$userId];

if ($search !== '') {

    $where .= " AND (
        action LIKE ?
        OR description LIKE ?
    )";

    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

if ($module !== '') {

    $where .= " AND module = ?";

    $params[] = $module;
}

/*
|--------------------------------------------------------------------------
| تعداد کل رکوردها
|--------------------------------------------------------------------------
*/

$countSql = "
SELECT COUNT(*)
FROM activity_logs
{$where}
";

$stmt = $pdo->prepare($countSql);
$stmt->execute($params);

$totalRows = (int)$stmt->fetchColumn();

$totalPages = max(1, ceil($totalRows / $perPage));

/*
|--------------------------------------------------------------------------
| دریافت اطلاعات
|--------------------------------------------------------------------------
*/

$listSql = "
SELECT
    id,
    user_id,
    module,
    action,
    record_id,
    description,
    ip_address,
    user_agent,
    created_at
FROM activity_logs

{$where}

ORDER BY created_at DESC

LIMIT {$offset}, {$perPage}
";

$stmt = $pdo->prepare($listSql);
$stmt->execute($params);

$activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| دریافت لیست ماژول‌ها
|--------------------------------------------------------------------------
*/

$moduleStmt = $pdo->query("
SELECT DISTINCT module
FROM activity_logs
ORDER BY module
");

$modules = $moduleStmt->fetchAll(PDO::FETCH_COLUMN);

/*
|--------------------------------------------------------------------------
| Layout
|--------------------------------------------------------------------------
*/

$pageTitle = 'فعالیت‌های من';

$pageDescription = 'تاریخچه فعالیت‌های حساب کاربری';

$pageIcon = 'fa-clock-rotate-left';

$contentFile = __DIR__ . '/views/activity_content.php';

require_once __DIR__ . '/../includes/layout.php';