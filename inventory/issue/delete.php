<?php
declare(strict_types=1);
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/functions.php';
requireLogin();
requirePermission('viewinventory');
global $pdo;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php', true, 302); exit;
}
try {
    issueVerifyCsrf();
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) throw new RuntimeException('شناسه حواله نامعتبر است.');
    $pdo->beginTransaction();
    issueLoadVoucher($pdo, $id);
    issueDeleteTransactions($pdo, $id);
    $pdo->prepare('DELETE FROM inventory_issue_voucher_items WHERE voucher_id = ?')->execute([$id]);
    $pdo->prepare('DELETE FROM inventory_issue_vouchers WHERE id = ?')->execute([$id]);
    $pdo->commit();
    header('Location: index.php?success=deleted', true, 302); exit;
} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    header('Location: index.php?error=' . urlencode($e->getMessage()), true, 302); exit;
}
