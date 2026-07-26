<?php
require_once __DIR__ . '/../../includes/auth.php';
requireLogin();

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    die('شناسه نامعتبر است.');
}

try {
    $pdo->prepare("DELETE FROM ticket_messages WHERE ticket_id = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM tickets WHERE id = ?")->execute([$id]);

    header("Location: index.php");
    exit;
} catch (Throwable $e) {
    die('خطا در حذف تیکت.');
}
?>