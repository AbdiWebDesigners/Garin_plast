<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireLogin();
requirePermission('view_inventory');

global $pdo;

$page_title = 'ثبت رسید انبار';
$pageTitle = 'ثبت رسید انبار';
$pageDescription = 'ایجاد رسید جدید ورود کالا';
$pageIcon = 'fa-solid fa-truck-ramp-box';

$error = '';
$success = '';

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