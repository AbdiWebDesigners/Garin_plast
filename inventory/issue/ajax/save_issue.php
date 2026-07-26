<?php

require_once __DIR__ . '/../../../includes/db.php';
require_once __DIR__ . '/../../../includes/auth.php';

requireLogin();
requirePermission('viewinventory');

global $pdo;

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method.');
    }

    $voucher_number   = trim($_POST['voucher_number'] ?? '');
    $voucher_date     = trim($_POST['voucher_date'] ?? '');
    $warehouse_id     = (int)($_POST['warehouse_id'] ?? 0);
    $requested_by     = !empty($_POST['requested_by']) ? (int)$_POST['requested_by'] : null;
    $approved_by      = !empty($_POST['approved_by']) ? (int)$_POST['approved_by'] : null;
    $status           = trim($_POST['status'] ?? 'draft');
    $description      = trim($_POST['description'] ?? '');
    $items            = $_POST['items'] ?? [];

    if ($voucher_number === '' || $voucher_date === '' || $warehouse_id <= 0) {
        throw new Exception('اطلاعات اصلی فرم ناقص است.');
    }

    if (!is_array($items) || count($items) === 0) {
        throw new Exception('حداقل یک ردیف کالا لازم است.');
    }

    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        INSERT INTO inventory_issue_vouchers
        (voucher_number, voucher_date, warehouse_id, requested_by, approved_by, status, description, created_at)
        VALUES
        (?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->execute([
        $voucher_number,
        $voucher_date,
        $warehouse_id,
        $requested_by,
        $approved_by,
        $status,
        $description
    ]);

    $voucher_id = (int)$pdo->lastInsertId();

    $productStmt = $pdo->prepare("SELECT id, title FROM products WHERE id = ? LIMIT 1");
    $stockStmt = $pdo->prepare("
        SELECT COALESCE(SUM(
            CASE
                WHEN transaction_type IN ('purchase','production') THEN quantity
                WHEN transaction_type IN ('sale','consume','issue') THEN -quantity
                ELSE 0
            END
        ), 0) AS stock
        FROM inventory_transactions
        WHERE item_id = ?
          AND item_type = 'finished_product'
          AND warehouse_id = ?
    ");

    $insertItem = $pdo->prepare("
        INSERT INTO inventory_issue_items
        (voucher_id, item_id, item_type, quantity, unit, unit_cost, serial_number, batch_number, expire_date, warehouse_location, description)
        VALUES
        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $insertTransaction = $pdo->prepare("
        INSERT INTO inventory_transactions
        (warehouse_id, item_id, item_type, transaction_type, quantity, unit_cost, reference_type, reference_id, note, created_at)
        VALUES
        (?, ?, ?, 'issue', ?, ?, 'inventory_issue', ?, ?, NOW())
    ");

    foreach ($items as $item) {
        $item_id = (int)($item['item_id'] ?? 0);
        $item_type = trim($item['item_type'] ?? 'finished_product');
        $quantity = (float)($item['quantity'] ?? 0);
        $unit = trim($item['unit'] ?? 'piece');
        $unit_cost = (float)($item['unit_cost'] ?? 0);
        $serial_number = trim($item['serial_number'] ?? '');
        $batch_number = trim($item['batch_number'] ?? '');
        $expire_date = trim($item['expire_date'] ?? '');
        $warehouse_location = trim($item['warehouse_location'] ?? '');
        $item_description = trim($item['description'] ?? '');

        if ($item_id <= 0 || $quantity <= 0) {
            continue;
        }

        $productStmt->execute([$item_id]);
        $product = $productStmt->fetch(PDO::FETCH_ASSOC);
        if (!$product) {
            throw new Exception("کالای انتخاب‌شده معتبر نیست: " . $item_id);
        }

        $stockStmt->execute([$item_id, $warehouse_id]);
        $currentStock = (float)$stockStmt->fetchColumn();

        if ($quantity > $currentStock) {
            throw new Exception("موجودی کالا '{$product['title']}' کافی نیست. موجودی فعلی: {$currentStock}");
        }

        $insertItem->execute([
            $voucher_id,
            $item_id,
            $item_type,
            $quantity,
            $unit,
            $unit_cost,
            $serial_number,
            $batch_number,
            $expire_date !== '' ? $expire_date : null,
            $warehouse_location,
            $item_description
        ]);

        $insertTransaction->execute([
            $warehouse_id,
            $item_id,
            $item_type,
            $quantity,
            $unit_cost,
            $voucher_id,
            $item_description
        ]);
    }

    $pdo->commit();

    header("Location: ../index.php?success=1");
    exit;
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    header("Location: ../create.php?error=" . urlencode($e->getMessage()));
    exit;
}