<?php
// فایل: garin/inventory/categories/view.php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

$id = (int)($_GET['id'] ?? 0);
if ($id === 0) {
    header("Location: index.php");
    exit;
}

// واکشی اطلاعات دسته‌بندی از دیتابیس
try {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$category) {
        die("دسته‌بندی مورد نظر یافت نشد.");
    }
} catch (PDOException $e) {
    die("خطا در ارتباط با پایگاه داده: " . $e->getMessage());
}

$pageTitle = 'جزئیات دسته‌بندی';

// لود کردن فایل گرافیکی و قالب اصلی
$contentFile = __DIR__ . '/views/view_content.php';
require_once __DIR__ . '/../../includes/layout.php';