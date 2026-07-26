<?php
// فایل: garin/payroll/employee_edit.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

if (empty($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit;
}

$pageTitle = 'ویرایش پرونده کارمند';
$errorMsg = '';
$empId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($empId <= 0) {
    header("Location: employees.php");
    exit;
}

try {
    // دریافت اطلاعات فعلی کارمند از دیتابیس
    $stmt = $pdo->prepare("SELECT * FROM employees WHERE id = ?");
    $stmt->execute([$empId]);
    $emp = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$emp) {
        die("<div style='padding:20px; direction:rtl; background:#f8d7da; color:#721c24;'>کارمندی با این مشخصات یافت نشد.</div>");
    }
} catch (PDOException $e) {
    die("<div style='padding:20px; direction:rtl; background:#f8d7da; color:#721c24;'>خطا در ارتباط با دیتابیس: " . $e->getMessage() . "</div>");
}

// بررسی ارسال فرم ویرایش (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $nationalCode = trim($_POST['national_code'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $baseSalary = trim($_POST['base_salary'] ?? 0);
    $bankAccount = trim($_POST['bank_account'] ?? '');
    $status = trim($_POST['status'] ?? 'active');

    // پر کردن داده‌های موقت برای نمایش مجدد در صورت بروز خطا
    $emp['first_name'] = $firstName;
    $emp['last_name'] = $lastName;
    $emp['national_code'] = $nationalCode;
    $emp['phone'] = $phone;
    $emp['base_salary'] = $baseSalary;
    $emp['bank_account'] = $bankAccount;
    $emp['status'] = $status;

    // اعتبارسنجی
    if ($firstName === '' || $lastName === '' || $nationalCode === '') {
        $errorMsg = 'لطفاً نام، نام خانوادگی و کد ملی را وارد نمایید.';
    } elseif (!is_numeric($baseSalary) || $baseSalary < 0) {
        $errorMsg = 'حقوق پایه باید یک عدد معتبر و بزرگتر از صفر باشد.';
    } else {
        try {
            // بررسی تکراری نبودن کد ملی (به جز خود این کارمند)
            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM employees WHERE national_code = ? AND id != ?");
            $checkStmt->execute([$nationalCode, $empId]);
            
            if ($checkStmt->fetchColumn() > 0) {
                $errorMsg = 'این کد ملی قبلاً برای کارمند دیگری ثبت شده است.';
            } else {
                // به‌روزرسانی اطلاعات در دیتابیس
                $sql = "UPDATE employees SET 
                            first_name = ?, 
                            last_name = ?, 
                            national_code = ?, 
                            phone = ?, 
                            base_salary = ?, 
                            bank_account = ?, 
                            status = ? 
                        WHERE id = ?";
                $updateStmt = $pdo->prepare($sql);
                $updateStmt->execute([$firstName, $lastName, $nationalCode, $phone, (float)$baseSalary, $bankAccount, $status, $empId]);

                // انتقال به صفحه لیست همراه با پیام موفقیت
                header("Location: employees.php?success=1");
                exit;
            }
        } catch (PDOException $e) {
            $errorMsg = 'خطا در به‌روزرسانی اطلاعات: ' . $e->getMessage();
        }
    }
}

// بارگذاری ظاهر فرم ویرایش از پوشه views
include __DIR__ . '/views/employee_edit.php';