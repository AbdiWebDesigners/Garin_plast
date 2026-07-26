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

$id = (int)($_GET['id'] ?? 0);
$status = trim($_GET['status'] ?? '');

$allowedStatuses = ['pending', 'processing', 'completed', 'cancelled'];

if ($id <= 0 || !in_array($status, $allowedStatuses, true)) {
    header("Location: index.php");
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);

    header("Location: view.php?id=" . $id . "&success=1");
    exit;
} catch (PDOException $e) {
    die("خطا در بروزرسانی وضعیت: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}