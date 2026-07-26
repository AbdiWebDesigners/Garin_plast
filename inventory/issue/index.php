<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireLogin();
requirePermission('viewinventory');

global $pdo;

$page_title = 'حواله خروج';
$pageTitle = 'حواله خروج';
$pageDescription = 'مدیریت حواله‌های خروج انبار';
$pageIcon = 'fa-solid fa-right-from-bracket';

$error = isset($_GET['error']) ? (string)$_GET['error'] : '';
$success = isset($_GET['success']) ? 'عملیات با موفقیت انجام شد.' : '';

$warehouse_id = (int)($_GET['warehouse_id'] ?? 0);

try {
    $warehouses = $pdo->query("
        SELECT id, warehouse_name
        FROM warehouses
        WHERE status = 'active' OR status IS NULL OR status = ''
        ORDER BY warehouse_name ASC
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    $warehouses = [];
    $error = $e->getMessage();
}

try {
    $sql = "
        SELECT
            v.id,
            v.voucher_number,
            v.voucher_date,
            v.warehouse_id,
            v.request_type,
            v.requested_by,
            v.approved_by,
            v.status,
            v.description,
            v.created_by,
            v.created_at,
            w.warehouse_name,
            COUNT(i.id) AS items_count,
            COALESCE(SUM(i.total_cost), 0) AS total_cost
        FROM inventory_issue_vouchers v
        LEFT JOIN warehouses w ON w.id = v.warehouse_id
        LEFT JOIN inventory_issue_voucher_items i ON i.voucher_id = v.id
    ";

    $params = [];

    if ($warehouse_id > 0) {
        $sql .= " WHERE v.warehouse_id = ? ";
        $params[] = $warehouse_id;
    }

    $sql .= "
        GROUP BY
            v.id, v.voucher_number, v.voucher_date, v.warehouse_id, v.request_type,
            v.requested_by, v.approved_by, v.status, v.description, v.created_by,
            v.created_at, w.warehouse_name
        ORDER BY v.id DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $vouchers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    $vouchers = [];
    $error = $e->getMessage();
}

$totalVouchers = count($vouchers);
$totalItems = 0;
$totalAmount = 0;

foreach ($vouchers as &$row) {
    $row['items_count'] = (int)$row['items_count'];
    $row['total_cost'] = (float)$row['total_cost'];
    $totalItems += $row['items_count'];
    $totalAmount += $row['total_cost'];
}
unset($row);

$contentFile = __DIR__ . '/views/index_content.php';

require_once dirname(__DIR__, 2) . '/includes/layout.php';