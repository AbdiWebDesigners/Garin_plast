<?php
// فایل: garin/inventory/categories/edit.php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

$pageTitle = 'ویرایش دسته‌بندی';
$message = '';
$category = null;

$id = (int)($_GET['id'] ?? 0);
if ($id === 0) {
    header("Location: index.php");
    exit;
}

// دریافت اطلاعات فعلی دسته‌بندی
try {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$category) {
        die("دسته‌بندی مورد نظر یافت نشد.");
    }
} catch (PDOException $e) {
    die("خطا: " . $e->getMessage());
}

// پردازش فرم ویرایش
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $slug  = trim($_POST['slug'] ?? '');

    if ($title === '') {
        $message = 'عنوان دسته‌بندی الزامی است.';
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE categories SET title = ?, slug = ? WHERE id = ?");
            $stmt->execute([$title, $slug, $id]);
            header("Location: index.php?updated=1");
            exit;
        } catch (PDOException $e) {
            $message = 'خطا در ویرایش: ' . $e->getMessage();
        }
    }
}

// لود کردن فایل گرافیکی و قالب اصلی
$contentFile = __DIR__ . '/views/edit_content.php';
require_once __DIR__ . '/../../includes/layout.php';