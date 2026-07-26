<?php

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireLogin();
requirePermission('view_inventory');

global $pdo;

$error   = '';
$success = '';

/*
|--------------------------------------------------------------------------
| اطلاعات صفحه
|--------------------------------------------------------------------------
*/

$page_title      = "رسیدهای انبار";
$pageTitle       = "رسیدهای انبار";
$pageDescription = "مدیریت رسیدهای ورود کالا";
$pageIcon        = "fa-solid fa-truck-ramp-box";

/*
|--------------------------------------------------------------------------
| فیلترها
|--------------------------------------------------------------------------
*/

$warehouse_id = (int)($_GET['warehouse_id'] ?? 0);
$status       = trim($_GET['status'] ?? '');
$date_from    = trim($_GET['date_from'] ?? '');
$date_to      = trim($_GET['date_to'] ?? '');

$where  = [];
$params = [];

/*
|--------------------------------------------------------------------------
| دریافت انبارها
|--------------------------------------------------------------------------
*/

try {

    $warehouses = $pdo->query("
        SELECT
            id,
            warehouse_name
        FROM warehouses
        WHERE status='active'
        ORDER BY warehouse_name
    ")->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {

    $warehouses = [];

}

/*
|--------------------------------------------------------------------------
| شروط جستجو
|--------------------------------------------------------------------------
*/

if ($warehouse_id > 0) {

    $where[] = "gr.warehouse_id = ?";
    $params[] = $warehouse_id;

}

if ($status !== '') {

    $where[] = "gr.status = ?";
    $params[] = $status;

}

if ($date_from !== '') {

    $where[] = "DATE(gr.receipt_date) >= ?";
    $params[] = $date_from;

}

if ($date_to !== '') {

    $where[] = "DATE(gr.receipt_date) <= ?";
    $params[] = $date_to;

}

$sqlWhere = '';

if (!empty($where)) {

    $sqlWhere = 'WHERE ' . implode(' AND ', $where);

}
/*
|--------------------------------------------------------------------------
| دریافت لیست رسیدها
|--------------------------------------------------------------------------
*/

try {

    $sql = "

    SELECT

        gr.id,
        gr.receipt_number,
        gr.receipt_date,
        gr.status,
        gr.description,

        w.warehouse_name,

        s.company_name AS supplier_name,

        u.fullname AS created_by,

        COUNT(gri.id) AS items_count,

        COALESCE(SUM(gri.quantity),0) AS total_quantity,

        COALESCE(SUM(gri.total_price),0) AS total_amount

    FROM goods_receipts gr

    LEFT JOIN warehouses w
        ON w.id = gr.warehouse_id

    LEFT JOIN suppliers s
        ON s.id = gr.supplier_id

    LEFT JOIN users u
        ON u.id = gr.created_by

    LEFT JOIN goods_receipt_items gri
        ON gri.receipt_id = gr.id

    $sqlWhere

    GROUP BY

        gr.id,
        gr.receipt_number,
        gr.receipt_date,
        gr.status,
        gr.description,
        w.warehouse_name,
        s.company_name,
        u.fullname

    ORDER BY

        gr.receipt_date DESC,
        gr.id DESC

    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute($params);

    $receipts = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {

    $receipts = [];

    $error = $e->getMessage();

}


/*
|--------------------------------------------------------------------------
| محاسبه آمار داشبورد
|--------------------------------------------------------------------------
*/

$totalReceipts = count($receipts);

$todayReceipts = 0;

$approvedReceipts = 0;

$totalAmount = 0;

$today = date('Y-m-d');

foreach ($receipts as &$row) {

    $row['total_quantity'] = (float)$row['total_quantity'];
    $row['total_amount']   = (float)$row['total_amount'];

    $totalAmount += $row['total_amount'];

    if (!empty($row['receipt_date']) &&
        substr($row['receipt_date'],0,10) === $today) {

        $todayReceipts++;

    }

    if ($row['status'] === 'approved') {

        $approvedReceipts++;

    }

}

unset($row);
/*
|--------------------------------------------------------------------------
| اطلاعات صفحه
|--------------------------------------------------------------------------
*/

$page_title = "رسیدهای انبار";
$pageTitle = "رسیدهای انبار";
$pageDescription = "مدیریت رسیدهای ورود کالا";
$pageIcon = "fa-solid fa-truck-ramp-box";

/*
|--------------------------------------------------------------------------
| فایل View
|--------------------------------------------------------------------------
*/

$contentFile = __DIR__ . "/views/index_content.php";

/*
|--------------------------------------------------------------------------
| بارگذاری Layout اصلی ERP
|--------------------------------------------------------------------------
*/

require_once dirname(__DIR__, 2) . "/includes/layout.php";