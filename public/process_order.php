<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

if (empty($_SESSION['cart']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: cart.php");
    exit;
}

$full_name = trim($_POST['full_name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');

if (empty($full_name) || empty($phone) || empty($address)) {
    $_SESSION['error'] = 'لطفاً تمام فیلدها را پر کنید.';
    header("Location: checkout.php");
    exit;
}

$total_price = 0;
foreach ($_SESSION['cart'] as $item) {
    $total_price += $item['price'] * $item['quantity'];
}

try {
    $pdo->beginTransaction();

    $order_number = 'INV-' . date('YmdHis');

    $stmt = $pdo->prepare("INSERT INTO orders (customer_id, order_number, status, total_price, created_at) VALUES (NULL, ?, 'pending', ?, NOW())");
    $stmt->execute([$order_number, $total_price]);
    $order_id = $pdo->lastInsertId();

    $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, qty, price) VALUES (?, ?, ?, ?)");

    foreach ($_SESSION['cart'] as $product_id => $item) {
        $itemStmt->execute([$order_id, $product_id, $item['quantity'], $item['price']]);
    }

    $pdo->commit();

    unset($_SESSION['cart']);

    header("Location: order_success.php?order_id=$order_id");
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    die('خطا: ' . $e->getMessage());
}
?>