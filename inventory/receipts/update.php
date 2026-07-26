<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireLogin();
requirePermission('view_inventory');

global $pdo;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    die('شناسه رسید نامعتبر است.');
}

$warehouse_id = (int)($_POST['warehouse_id'] ?? 0);
$supplier_id = !empty($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : null;
$receipt_date_raw = trim($_POST['receipt_date'] ?? '');
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

$receipt_date = $receipt_date_raw
    ? date('Y-m-d H:i:s', strtotime($receipt_date_raw))
    : date('Y-m-d H:i:s');

$filteredItems = [];
foreach ($items as $item) {
    $item_id = (int)($item['item_id'] ?? 0);
    $quantity = (float)($item['quantity'] ?? 0);
    $unit_price = (float)($item['unit_price'] ?? 0);
    $unit = trim($item['unit'] ?? 'piece');
    $discount = (float)($item['discount'] ?? 0);
    $tax = (float)($item['tax'] ?? 0);
    $serial_number = trim($item['serial_number'] ?? '');
    $expire_date = !empty($item['expire_date']) ? $item['expire_date'] : null;
    $line_description = trim($item['description'] ?? '');
    $item_type = trim($item['item_type'] ?? 'finished_product');

    if ($item_id <= 0 || $quantity <= 0) {
        continue;
    }

    $filteredItems[] = [
        'item_type' => $item_type,
        'item_id' => $item_id,
        'quantity' => $quantity,
        'unit' => $unit,
        'unit_price' => $unit_price,
        'discount' => $discount,
        'tax' => $tax,
        'total_price' => ($quantity * $unit_price) - $discount + $tax,
        'serial_number' => $serial_number,
        'expire_date' => $expire_date,
        'description' => $line_description
    ];
}

if (empty($filteredItems)) {
    die('هیچ آیتم معتبری برای ثبت وجود ندارد.');
}

try {
    $pdo->beginTransaction();

    $check = $pdo->prepare("SELECT id, status FROM goods_receipts WHERE id = ? LIMIT 1");
    $check->execute([$id]);
    $existing = $check->fetch(PDO::FETCH_ASSOC);

    if (!$existing) {
        throw new Exception('رسید یافت نشد.');
    }

    if (($existing['status'] ?? '') === 'cancelled') {
        throw new Exception('رسید لغوشده قابل ویرایش نیست.');
    }

    $status = ($existing['status'] ?? 'draft') === 'approved' ? 'approved' : 'draft';
    $approvedBy = $status === 'approved' ? ($_SESSION['user_id'] ?? null) : null;

    $stmt = $pdo->prepare("
        UPDATE goods_receipts
        SET receipt_type = ?, supplier_id = ?, warehouse_id = ?, receipt_date = ?, reference_no = ?, description = ?, status = ?, approved_by = ?, created_by = ?
        WHERE id = ?
    ");
    $stmt->execute([
        $receipt_type,
        $supplier_id,
        $warehouse_id,
        $receipt_date,
        $reference_no,
        $description,
        $status,
        $approvedBy,
        $_SESSION['user_id'] ?? null,
        $id
    ]);

    $delItems = $pdo->prepare("DELETE FROM goods_receipt_items WHERE receipt_id = ?");
    $delItems->execute([$id]);

    $delTrans = $pdo->prepare("
        DELETE FROM inventory_transactions
        WHERE reference_type = 'goods_receipt'
        AND reference_id = ?
    ");
    $delTrans->execute([$id]);

    $itemStmt = $pdo->prepare("
        INSERT INTO goods_receipt_items
        (receipt_id, line_no, item_type, item_id, quantity, received_quantity, rejected_quantity, unit, unit_price, discount, tax, total_price, batch_number, serial_number, expire_date, qc_status, warehouse_location, description)
        VALUES
        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NULL, ?, ?, 'pending', NULL, ?)
    ");

    $transStmt = $pdo->prepare("
        INSERT INTO inventory_transactions
        (inventory_id, item_type, item_id, warehouse_id, supplier_id, transaction_type, quantity, unit, unit_cost, total_cost, reference_type, reference_id, transaction_date, description, created_by, created_at)
        VALUES
        (NULL, ?, ?, ?, ?, 'purchase', ?, ?, ?, ?, 'goods_receipt', ?, ?, ?, ?, NOW())
    ");

    $lineNo = 1;
    foreach ($filteredItems as $it) {
        $itemStmt->execute([
            $id,
            $lineNo++,
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
            $it['serial_number'],
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
            $id,
            $receipt_date,
            $it['description'],
            $_SESSION['user_id'] ?? null
        ]);
    }

    $pdo->commit();

    header('Location: index.php?success=' . urlencode('رسید با موفقیت به‌روزرسانی شد.'));
    exit;
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    die('خطا در بروزرسانی رسید: ' . $e->getMessage());
}