<?php
// فراخوانی پیش‌نیازها قبل از لود شدن قالب اصلی
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();

$error = null;
$success = null;

// پردازش فرم افزودن سرفصل جدید
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_account'])) {
    $code = trim($_POST['code']);
    $name = trim($_POST['name']);
    $type = $_POST['type'] ?? 'معین'; // نوع حساب (کل، معین، تفصیلی)

    if (empty($code) || empty($name)) {
        $error = "لطفاً کد حساب و نام حساب را وارد کنید.";
    } else {
        try {
            // بررسی تکراری نبودن کد حساب
            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM accounting_accounts WHERE code = ?");
            $checkStmt->execute([$code]);
            if ($checkStmt->fetchColumn() > 0) {
                $error = "این کد حساب قبلاً در سیستم ثبت شده است.";
            } else {
                // ثبت در دیتابیس MariaDB
                $stmt = $pdo->prepare("INSERT INTO accounting_accounts (code, name, type, created_at) VALUES (?, ?, ?, NOW())");
                $stmt->execute([$code, $name, $type]);
                $success = "سرفصل حساب با موفقیت به سیستم اضافه شد.";
            }
        } catch (Exception $e) {
            $error = "خطا در ثبت اطلاعات: " . $e->getMessage();
        }
    }
}

// تنظیم متغیرهای Layout سیستم ERP
$pageTitle = 'کدینگ و سرفصل‌های حسابداری';
$pageDescription = 'مدیت، ویرایش و تعریف درختواره حساب‌های کل، معین و تفصیلی گارین پلاست';
$pageIcon = 'fa-sitemap'; 

// معرفی فایل محتوا
$contentFile = __DIR__ . '/views/accounts_content.php';

// فراخوانی قالب اصلی
require_once __DIR__ . '/../includes/layout.php';