<?php
// فایل: garin/payroll/employee_add.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

if (empty($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit;
}

$pageTitle = 'افزودن کارمند جدید';
$errorMsg = '';
$successMsg = '';

// بررسی ارسال فرم (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $nationalCode = trim($_POST['national_code'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $baseSalary = trim($_POST['base_salary'] ?? 0);
    $bankAccount = trim($_POST['bank_account'] ?? '');
    $status = trim($_POST['status'] ?? 'active');

    // اعتبارسنجی فیلدهای ضروری
    if ($firstName === '' || $lastName === '' || $nationalCode === '') {
        $errorMsg = 'لطفاً نام، نام خانوادگی و کد ملی را وارد نمایید.';
    } elseif (!is_numeric($baseSalary) || $baseSalary < 0) {
        $errorMsg = 'حقوق پایه باید یک عدد معتبر و بزرگتر از صفر باشد.';
    } else {
        try {
            // بررسی تکراری نبودن کد ملی
            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM employees WHERE national_code = ?");
            $checkStmt->execute([$nationalCode]);
            if ($checkStmt->fetchColumn() > 0) {
                $errorMsg = 'کارمندی با این کد ملی قبلاً در سیستم ثبت شده است.';
            } else {
                // درج اطلاعات کارمند جدید در دیتابیس
                $sql = "INSERT INTO employees (first_name, last_name, national_code, phone, base_salary, bank_account, status) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$firstName, $lastName, $nationalCode, $phone, (float)$baseSalary, $bankAccount, $status]);

                // انتقال به صفحه لیست کارکنان همراه با پیغام موفقیت
                header("Location: employees.php?success=1");
                exit;
            }
        } catch (PDOException $e) {
            $errorMsg = 'خطا در ذخیره‌سازی اطلاعات: ' . $e->getMessage();
        }
    }
}

// بارگذاری ظاهر فرم از پوشه views
include __DIR__ . '/views/employee_add.php';