<?php
// فراخوانی پیش‌نیازها
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();

$error = null;
$success = null;

// پردازش فایل آپلود شده
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['holoo_file'])) {
    $file = $_FILES['holoo_file'];
    
    // بررسی اینکه فایلی بدون خطا آپلود شده باشد
    if ($file['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        
        // بررسی فرمت فایل
        if (strtolower($ext) === 'csv') {
            $handle = fopen($file['tmp_name'], "r");
            
            if ($handle !== FALSE) {
                try {
                    $pdo->beginTransaction();
                    
                    // ۱. ایجاد یک سند کل (Header) برای این درون‌ریزی
                    $stmtJournal = $pdo->prepare("INSERT INTO accounting_journals (journal_number, date, description, user_id, created_at) VALUES (?, ?, ?, ?, NOW())");
                    $journalNumber = 'HL-' . time(); // پیشوند HL برای فایل‌های هلو
                    $userId = $_SESSION['user_id'] ?? 1;
                    $stmtJournal->execute([$journalNumber, date('Y-m-d'), 'درون‌ریزی گروهی از سیستم هلو', $userId]);
                    
                    $journalId = $pdo->lastInsertId();
                    $stmtItem = $pdo->prepare("INSERT INTO accounting_journal_items (journal_id, account_id, description, debit, credit) VALUES (?, ?, ?, ?, ?)");
                    
                    $row = 0;
                    $importedCount = 0;
                    
                    // ۲. خواندن سطر به سطر فایل CSV
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $row++;
                        // رد کردن سطر اول (معمولاً هدر تیترها مثل: کد حساب، شرح، بدهکار، بستانکار)
                        if ($row == 1) continue;
                        
                        // فرض بر این است که ستون‌های CSV به این ترتیب هستند:
                        // ستون 0: ID یا کد حساب در دیتابیس شما
                        // ستون 1: شرح ردیف
                        // ستون 2: مبلغ بدهکار
                        // ستون 3: مبلغ بستانکار
                        // (اگر خروجی هلوی شما ترتیب دیگری دارد، اعداد داخل براکت را تغییر دهید)
                        
                        $accId = !empty($data[0]) ? (int)$data[0] : null;
                        $desc = $data[1] ?? '';
                        $debit = !empty($data[2]) ? (float)$data[2] : 0;
                        $credit = !empty($data[3]) ? (float)$data[3] : 0;

                        // فقط سطرهایی که حساب مشخص دارند ثبت می‌شوند
                        if ($accId) {
                            $stmtItem->execute([$journalId, $accId, $desc, $debit, $credit]);
                            $importedCount++;
                        }
                    }
                    
                    fclose($handle);
                    
                    // تایید و ذخیره نهایی
                    $pdo->commit();
                    $success = "تعداد $importedCount ردیف با موفقیت از فایل هلو خوانده شد و در قالب سند یکپارچه (شماره $journalNumber) در سیستم ثبت گردید.";
                    
                } catch (Exception $e) {
                    $pdo->rollBack(); // در صورت خطا، هیچ چیزی ثبت نمی‌شود
                    $error = "خطا در پردازش و ثبت در دیتابیس: " . $e->getMessage();
                }
            } else {
                $error = "سیستم قادر به خواندن فایل CSV نیست. لطفاً فایل را بررسی کنید.";
            }
        } else {
            $error = "فرمت فایل نامعتبر است. لطفاً فقط فایل با پسوند .csv (خروجی هلو) آپلود کنید.";
        }
    } else {
        $error = "لطفاً یک فایل را برای درون‌ریزی انتخاب کنید.";
    }
}

// تنظیم متغیرهای Layout سیستم ERP
$pageTitle = 'درون‌ریزی فایل هلو';
$pageDescription = 'انتقال اسناد و اطلاعات مالی از نرم‌افزار هلو به سیستم یکپارچه';
$pageIcon = 'fa-file-import'; 

// معرفی فایل محتوا
$contentFile = __DIR__ . '/views/import_content.php';

// فراخوانی قالب اصلی
require_once __DIR__ . '/../includes/layout.php';