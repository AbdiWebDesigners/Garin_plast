<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireLogin();
requirePermission('view_inventory');

global $pdo;

$page_title = 'ویرایش رسید انبار';
$pageTitle = 'ویرایش رسید انبار';
$pageDescription = 'ویرایش رسید ورود کالا';
$pageIcon = 'fa-solid fa-truck-ramp-box';

$error = '';
$success = '';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    die('شناسه رسید نامعتبر است.');
}

try {
    $stmt = $pdo->prepare("
        SELECT *
        FROM goods_receipts
        WHERE id = ?
        LIMIT 1
    ");
    $stmt->execute([$id]);
    $receipt = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$receipt) {
        die('رسید یافت نشد.');
    }

    $itemStmt = $pdo->prepare("
        SELECT *
        FROM goods_receipt_items
        WHERE receipt_id = ?
        ORDER BY line_no ASC, id ASC
    ");
    $itemStmt->execute([$id]);
    $receipt_items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    die('خطا در دریافت اطلاعات رسید: ' . $e->getMessage());
}

try {
    $warehouses = $pdo->query("
        SELECT id, warehouse_name
        FROM warehouses
        WHERE status = 'active'
        ORDER BY warehouse_name
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    $warehouses = [];
}

try {
    $suppliers = $pdo->query("
        SELECT id, company_name
        FROM suppliers
        ORDER BY company_name
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    $suppliers = [];
}

try {
    $products = $pdo->query("
        SELECT id, title, sku
        FROM products
        WHERE status = 'active'
        ORDER BY title
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    $products = [];
}

$contentFile = __DIR__ . '/views/form.php';

require_once dirname(__DIR__, 2) . '/includes/layout.php';