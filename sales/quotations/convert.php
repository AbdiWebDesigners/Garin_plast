<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

if (empty($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit;
}

$quotationId = (int)($_GET['id'] ?? 0);

if ($quotationId <= 0) {
    die('شناسه پیش‌فاکتور معتبر نیست.');
}

try {
    $stmt = $pdo->prepare("SELECT * FROM quotations WHERE id = ? LIMIT 1");
    $stmt->execute([$quotationId]);
    $quotation = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$quotation) {
        die('پیش‌فاکتور یافت نشد.');
    }

    $check = $pdo->prepare("SELECT id FROM orders WHERE quotation_id = ? LIMIT 1");
    $check->execute([$quotationId]);
    $existingOrderId = (int)($check->fetchColumn() ?: 0);

    if ($existingOrderId > 0) {
        header("Location: ../orders/view.php?id=" . $existingOrderId . "&exists=1");
        exit;
    }

    $orderNumber = 'ORD-' . str_pad((string)$quotation['id'], 5, '0', STR_PAD_LEFT);
    $totalPrice = (float)($quotation['final_price'] ?? 0);
    $notes = 'تبدیل شده از پیش‌فاکتور #' . (int)$quotation['id'];
    $status = 'pending';

    $stmt = $pdo->prepare("
        INSERT INTO orders (customer_id, sales_agent_id, quotation_id, order_number, status, total_price, notes)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $quotation['customer_id'],
        $quotation['sales_agent_id'],
        $quotation['id'],
        $orderNumber,
        $status,
        $totalPrice,
        $notes
    ]);

    $orderId = (int)$pdo->lastInsertId();

    $pdo->prepare("
        UPDATE quotations
        SET status = 'accepted'
        WHERE id = ?
    ")->execute([$quotationId]);

    header("Location: ../orders/view.php?id=" . $orderId . "&created=1");
    exit;

} catch (PDOException $e) {
    die('خطا در تبدیل به سفارش: ' . $e->getMessage());
}