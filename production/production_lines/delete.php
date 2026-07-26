<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireLogin();

$id = (int)($_GET['id'] ?? 0);

if ($id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM production_lines WHERE id = ?");
        $stmt->execute([$id]);

        header("Location: index.php");
        exit;
    } catch (Exception $e) {
        echo "خطا در حذف: " . $e->getMessage();
    }
} else {
    header("Location: index.php");
    exit;
}
?>