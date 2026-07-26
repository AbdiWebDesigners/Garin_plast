<?php
// فایل: garin/inventory/categories/add.php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

$pageTitle = 'افزودن دسته‌بندی';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $slug  = trim($_POST['slug'] ?? '');

    if ($title === '') {
        $message = 'عنوان دسته‌بندی الزامی است.';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO categories (title, slug) VALUES (?, ?)");
            $stmt->execute([$title, $slug]);
            header("Location: index.php?added=1");
            exit;
        } catch (PDOException $e) {
            $message = 'خطا در ثبت دسته‌بندی: ' . $e->getMessage();
        }
    }
}

// لود کردن فایل گرافیکی و قالب اصلی
$contentFile = __DIR__ . '/views/add_content.php';
require_once __DIR__ . '/../../includes/layout.php';