<?php
// فایل: garin/payroll/slip.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

if (empty($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit;
}

$pageTitle = 'فیش حقوقی پرسنل';
$slipId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($slipId <= 0) {
    header("Location: index.php");
    exit;
}

try {
    // واکشی اطلاعات فیش حقوقی همراه با مشخصات کامل کارمند
    $sql = "
        SELECT 
            p.*,
            e.first_name,
            e.last_name,
            e.national_code,
            e.phone,
            e.bank_account
        FROM payroll p
        INNER JOIN employees e ON e.id = p.employee_id
        WHERE p.id = ?
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$slipId]);
    $slip = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$slip) {
        die("<div style='padding:20px; direction:rtl; background:#f8d7da; color:#721c24;'>فیش حقوقی مورد نظر در سیستم یافت نشد.</div>");
    }

} catch (PDOException $e) {
    die("<div style='padding:20px; direction:rtl; background:#f8d7da; color:#721c24;'>خطا در بارگذاری فیش حقوقی: " . $e->getMessage() . "</div>");
}

// توابع کمکی تبدیل ماه و فرمت پول
function getMonthName($m) {
    $months = [
        1 => 'فروردین', 2 => 'اردیبهشت', 3 => 'خرداد', 4 => 'تیر',
        5 => 'مرداد', 6 => 'شهریور', 7 => 'مهر', 8 => 'آبان',
        9 => 'آذر', 10 => 'دی', 11 => 'بهمن', 12 => 'اسفند'
    ];
    return $months[(int)$m] ?? 'نامشخص';
}

function money($v) {
    return number_format((float)$v) . ' تومان';
}

// بارگذاری ظاهر فیش حقوقی
include __DIR__ . '/views/slip.php';