<?php
// فراخوانی پیش‌نیازها قبل از لود شدن قالب اصلی
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'];
    $description = trim($_POST['description']);
    $journalItems = $_POST['items'] ?? [];
    
    // در صورتی که سیستم سشن یوزر را دارد استفاده می‌کنیم، در غیر این صورت مقدار پیش‌فرض 1
    $userId = $_SESSION['user_id'] ?? 1; 

    // ۱. بررسی تراز بودن سند قبل از ثبت
    $totalDebit = 0;
    $totalCredit = 0;
    foreach ($journalItems as $item) {
        $totalDebit += (float)($item['debit'] ?? 0);
        $totalCredit += (float)($item['credit'] ?? 0);
    }

    if ($totalDebit !== $totalCredit) {
        $error = "سند تراز نیست! مجموع مبالغ بدهکار و بستانکار باید با هم برابر باشند.";
    } elseif ($totalDebit == 0) {
        $error = "مبلغ سند نمی‌تواند صفر باشد.";
    } else {
        // ۲. ثبت سند در دیتابیس با استفاده از Transaction
        try {
            $pdo->beginTransaction();

            // ثبت هدر (اطلاعات کلی) سند
            // فرض بر این است که جدولی به نام accounting_journals دارید
            $stmt = $pdo->prepare("INSERT INTO accounting_journals (journal_number, date, description, user_id, created_at) VALUES (?, ?, ?, ?, NOW())");
            $journalNumber = 'J-' . time(); // تولید شماره سند یکتا
            $stmt->execute([$journalNumber, $date, $description, $userId]);
            
            $journalId = $pdo->lastInsertId();

            // ثبت ردیف‌های سند (آرتیکل‌ها)
            // فرض بر این است که جدولی به نام accounting_journal_items دارید
            $stmtItem = $pdo->prepare("INSERT INTO accounting_journal_items (journal_id, account_id, description, debit, credit) VALUES (?, ?, ?, ?, ?)");
            
            foreach ($journalItems as $item) {
                // اگر ردیفی حسابش انتخاب نشده بود، از آن رد شو
                if (empty($item['account_id'])) continue;
                
                $stmtItem->execute([
                    $journalId,
                    $item['account_id'],
                    $item['description'] ?? '',
                    $item['debit'] ?? 0,
                    $item['credit'] ?? 0
                ]);
            }

            // تایید و ذخیره نهایی در دیتابیس
            $pdo->commit();
            header("Location: index.php");
            exit;
            
        } catch (Exception $e) {
            // در صورت بروز هرگونه خطا، هیچ اطلاعاتی ثبت نمی‌شود (Rollback)
            $pdo->rollBack();
            $error = "خطا در ثبت سند در دیتابیس: " . $e->getMessage();
        }
    }
}

// تنظیم متغیرهای Layout
$pageTitle = 'ثبت سند حسابداری';
$pageDescription = 'صدور سند دستی و وارد کردن آرتیکل‌های بدهکار و بستانکار';
$pageIcon = 'fa-file-invoice-dollar'; 

// معرفی فایل محتوا
$contentFile = __DIR__ . '/views/journal_content.php';

// فراخوانی قالب اصلی
require_once __DIR__ . '/../includes/layout.php';