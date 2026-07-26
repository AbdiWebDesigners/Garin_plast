<?php
/**
 * -------------------------------------------------------
 * Garin ERP
 * Module : Warehouses
 * File : edit.php
 * -------------------------------------------------------
 */

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();
requirePermission('manage_inventory');

$page_title = 'ویرایش انبار';

/*
|--------------------------------------------------------------------------
| دریافت شناسه
|--------------------------------------------------------------------------
*/

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {

    $_SESSION['error'] = 'شناسه انبار نامعتبر است.';

    header("Location: index.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| دریافت اطلاعات انبار
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT *
    FROM warehouses
    WHERE id = ?
    LIMIT 1
");

$stmt->execute([$id]);

$warehouse = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$warehouse) {

    $_SESSION['error'] = 'انبار مورد نظر پیدا نشد.';

    header("Location: index.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| ارسال اطلاعات به فرم
|--------------------------------------------------------------------------
*/

$warehouse_code = $warehouse['warehouse_code'];
$warehouse_name = $warehouse['warehouse_name'];
$warehouse_type = $warehouse['warehouse_type'];
$manager_id     = $warehouse['manager_id'];
$province       = $warehouse['province'];
$city           = $warehouse['city'];
$address        = $warehouse['address'];
$phone          = $warehouse['phone'];
$status         = $warehouse['status'];

$contentFile = __DIR__ . '/views/edit_content.php';

/*
|--------------------------------------------------------------------------
| Layout
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../includes/layout.php';