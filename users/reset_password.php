<?php
require_once __DIR__ . '/includes/db.php';

$userId = 1; // آیدی کاربر موردنظر
$newPassword = '12345678';
$hash = password_hash($newPassword, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
$stmt->execute([$hash, $userId]);

echo "Password updated.";