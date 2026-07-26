<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireLogin();
requirePermission('view_inventory');

global $pdo;

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    die('شناسه رسید نامعتبر است.');
}

try {
    $stmt = $pdo->prepare("
        SELECT id, status
        FROM goods_receipts
        WHERE id = ?
        LIMIT 1
    ");
    $stmt->execute([$id]);
    $receipt = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$receipt) {
        die('رسید یافت نشد.');
    }

    if (($receipt['status'] ?? '') === 'cancelled') {
        die('این رسید قبلاً لغو شده است.');
    }

    $pdo->beginTransaction();

    $upd = $pdo->prepare("
        UPDATE goods_receipts
        SET status = 'cancelled'
        WHERE id = ?
    ");
    $upd->execute([$id]);

    $pdo->commit();

    header('Location: index.php?success=' . urlencode('رسید با موفقیت لغو شد.'));
    exit;
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    die('خطا در لغو رسید: ' . $e->getMessage());
}