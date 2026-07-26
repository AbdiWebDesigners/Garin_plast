<?php
// فایل: garin/inventory/categories/index.php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

$pageTitle = 'مدیریت دسته‌بندی‌ها';
$message = '';

// عملیات حذف
if (isset($_GET['delete'])) {
    $deleteId = (int) $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$deleteId]);
        header("Location: index.php?deleted=1");
        exit;
    } catch (PDOException $e) {
        $message = 'خطا در حذف دسته‌بندی: ' . $e->getMessage();
    }
}

// عملیات جستجو
$q = trim($_GET['q'] ?? '');

try {
    $sql = "SELECT * FROM categories";
    $params = [];

    if ($q !== '') {
        $sql .= " WHERE title LIKE ? OR slug LIKE ?";
        $like = "%$q%";
        $params = [$like, $like];
    }

    $sql .= " ORDER BY id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("خطا در دریافت دسته‌بندی‌ها: " . $e->getMessage());
}

// معرفی فایل گرافیکی و لود کردن قالب اصلی سیستم
$contentFile = __DIR__ . '/views/categories_content.php';
require_once __DIR__ . '/../../includes/layout.php';