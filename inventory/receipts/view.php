<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireLogin();
requirePermission('view_inventory');

global $pdo;

$page_title = 'مشاهده رسید انبار';
$pageTitle = 'مشاهده رسید انبار';
$pageDescription = 'جزئیات رسید ورود کالا';
$pageIcon = 'fa-solid fa-truck-ramp-box';

$error = '';
$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    die('شناسه رسید نامعتبر است.');
}

try {
    $stmt = $pdo->prepare("
        SELECT
            gr.*,
            w.warehouse_name,
            s.company_name AS supplier_name,
            u.fullname AS created_by_name,
            a.fullname AS approved_by_name
        FROM goods_receipts gr
        LEFT JOIN warehouses w ON w.id = gr.warehouse_id
        LEFT JOIN suppliers s ON s.id = gr.supplier_id
        LEFT JOIN users u ON u.id = gr.created_by
        LEFT JOIN users a ON a.id = gr.approved_by
        WHERE gr.id = ?
        LIMIT 1
    ");
    $stmt->execute([$id]);
    $receipt = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$receipt) {
        die('رسید یافت نشد.');
    }

    $itemStmt = $pdo->prepare("
        SELECT
            gri.*,
            p.title AS product_name,
            p.sku AS product_sku
        FROM goods_receipt_items gri
        LEFT JOIN products p ON p.id = gri.item_id
        WHERE gri.receipt_id = ?
        ORDER BY gri.line_no ASC, gri.id ASC
    ");
    $itemStmt->execute([$id]);
    $items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    die('خطا در دریافت اطلاعات رسید: ' . $e->getMessage());
}

$contentFile = __DIR__ . '/views/view_content.php';

require_once dirname(__DIR__, 2) . '/includes/layout.php';