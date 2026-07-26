<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireLogin();
requirePermission('view_inventory');

global $pdo;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: create.php');
    exit;
}

$warehouse_id = (int)($_POST['warehouse_id'] ?? 0);
$supplier_id = !empty($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : null;
$receipt_date = trim($_POST['receipt_date'] ?? date('Y-m-d'));
$receipt_type = trim($_POST['receipt_type'] ?? 'purchase');
$reference_no = trim($_POST['reference_no'] ?? '');
$description = trim($_POST['description'] ?? '');
$items = $_POST['items'] ?? [];

if ($warehouse_id <= 0) {
    die('انبار الزامی است.');
}

$allowedTypes = ['purchase', 'production', 'customer_return', 'inventory_adjustment'];
if (!in_array($receipt_type, $allowedTypes, true)) {
    $receipt_type = 'purchase';
}

$filteredItems = [];

foreach ($items as $idx => $item) {
    $item_id = (int)($item['item_id'] ?? 0);
    $quantity = (float)($item['quantity'] ?? 0);
    $unit_price = (float)($item['unit_price'] ?? 0);
    $unit = trim($item['unit'] ?? 'piece');
    $batch_number = trim($item['batch_number'] ?? '');
    $expire_date = !empty($item['expire_date']) ? $item['expire_date'] : null;
    $line_description = trim($item['description'] ?? '');
    $item_type = trim($item['item_type'] ?? 'finished_product');
    $discount = (float)($item['discount'] ?? 0);
    $tax = (float)($item['tax'] ?? 0);

    if ($item_id <= 0 || $quantity <= 0) {
        continue;
    }

    $total_price = ($quantity * $unit_price) - $discount + $tax;

    $filteredItems[] = [
        'line_no' => count($filteredItems) + 1,
        'item_type' => $item_type,
        'item_id' => $item_id,
        'quantity' => $quantity,
        'unit' => $unit,
        'unit_price' => $unit_price,
        'discount' => $discount,
        'tax' => $tax,
        'total_price' => $total_price,
        'batch_number' => $batch_number,
        'expire_date' => $expire_date,
        'description' => $line_description
    ];
}

if (empty($filteredItems)) {
    die('هیچ آیتم معتبری برای ثبت وجود ندارد.');
}

$receipt_number = 'GR-' . date('YmdHis');

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        INSERT INTO goods_receipts
        (receipt_number, receipt_type, supplier_id, warehouse_id, receipt_date, reference_no, description, status, created_by, approved_by, created_at)
        VALUES
        (?, ?, ?, ?, ?, ?, ?, 'approved', ?, ?, NOW())
    ");
    $stmt->execute([
        $receipt_number,
        $receipt_type,
        $supplier_id,
        $warehouse_id,
        $receipt_date . ' ' . date('H:i:s'),
        $reference_no,
        $description,
        $_SESSION['user_id'] ?? null,
        $_SESSION['user_id'] ?? null
    ]);

    $receipt_id = (int)$pdo->lastInsertId();

    $itemStmt = $pdo->prepare("
        INSERT INTO goods_receipt_items
        (receipt_id, line_no, item_type, item_id, quantity, received_quantity, rejected_quantity, unit, unit_price, discount, tax, total_price, batch_number, expire_date, qc_status, warehouse_location, description)
        VALUES
        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NULL, ?)
    ");

    $transStmt = $pdo->prepare("
        INSERT INTO inventory_transactions
        (inventory_id, item_type, item_id, warehouse_id, supplier_id, transaction_type, quantity, unit, unit_cost, total_cost, reference_type, reference_id, transaction_date, description, created_by, created_at)
        VALUES
        (NULL, ?, ?, ?, ?, 'purchase', ?, ?, ?, ?, 'goods_receipt', ?, ?, ?, ?, NOW())
    ");

    foreach ($filteredItems as $it) {
        $itemStmt->execute([
            $receipt_id,
            $it['line_no'],
            $it['item_type'],
            $it['item_id'],
            $it['quantity'],
            $it['quantity'],
            0,
            $it['unit'],
            $it['unit_price'],
            $it['discount'],
            $it['tax'],
            $it['total_price'],
            $it['batch_number'],
            $it['expire_date'],
            $it['description']
        ]);

        $transStmt->execute([
            $it['item_type'],
            $it['item_id'],
            $warehouse_id,
            $supplier_id,
            $it['quantity'],
            $it['unit'],
            $it['unit_price'],
            $it['total_price'],
            $receipt_id,
            $receipt_date . ' ' . date('H:i:s'),
            $it['description'],
            $_SESSION['user_id'] ?? null
        ]);
    }

    $pdo->commit();

    header('Location: index.php?success=' . urlencode('رسید با موفقیت ثبت شد.'));
    exit;
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    die('خطا در ثبت رسید: ' . $e->getMessage());
}