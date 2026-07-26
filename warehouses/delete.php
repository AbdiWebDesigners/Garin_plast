<?php

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();
requirePermission('manage_inventory');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    $_SESSION['error'] = 'شناسه انبار نامعتبر است.';
    header('Location: index.php');
    exit;
}

try {

    // بررسی وجود انبار
    $check = $pdo->prepare("SELECT warehouse_name FROM warehouses WHERE id = ? LIMIT 1");
    $check->execute([$id]);
    $warehouse = $check->fetch(PDO::FETCH_ASSOC);

    if (!$warehouse) {
        throw new Exception('انبار مورد نظر یافت نشد.');
    }

    // بررسی وابستگی به رسیدها
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM goods_receipts WHERE warehouse_id = ?");
    $stmt->execute([$id]);
    $receiptCount = (int)$stmt->fetchColumn();

    if ($receiptCount > 0) {
        throw new Exception('این انبار در رسیدهای انبار استفاده شده و قابل حذف نیست.');
    }

    // بررسی وابستگی به حواله‌ها
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM goods_issues WHERE warehouse_id = ?");
    $stmt->execute([$id]);
    $issueCount = (int)$stmt->fetchColumn();

    if ($issueCount > 0) {
        throw new Exception('این انبار در حواله‌های انبار استفاده شده و قابل حذف نیست.');
    }

    // حذف انبار
    $delete = $pdo->prepare("DELETE FROM warehouses WHERE id = ?");
    $delete->execute([$id]);

    $_SESSION['success'] = 'انبار «' . $warehouse['warehouse_name'] . '» با موفقیت حذف شد.';

} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
}

header('Location: index.php');
exit;