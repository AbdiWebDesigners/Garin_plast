<?php
// فایل: garin/payroll/calculate.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

if (empty($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit;
}

$pageTitle = 'محاسبه حقوق و دستمزد جدید';
$errorMsg = '';

// مقادیر پیش‌فرض فرم
$selectedEmpId = (int)($_REQUEST['employee_id'] ?? 0);
$salaryYear    = (int)($_REQUEST['salary_year'] ?? 1405);
$salaryMonth   = (int)($_REQUEST['salary_month'] ?? 1);
$baseSalary    = (float)($_POST['base_salary'] ?? 0);
$bonuses       = (float)($_POST['bonuses'] ?? 0);
$deductions    = (float)($_POST['deductions'] ?? 0);
$insuranceAmount = (float)($_POST['insurance_amount'] ?? 0);
$taxAmount     = (float)($_POST['tax_amount'] ?? 0);

// ۱. بارگذاری لیست تمامی کارکنان فعال برای منوی کشویی
$employees = [];
try {
    $empQuery =$pdo->query("SELECT id, first_name, last_name, national_code, base_salary FROM employees WHERE status = 'active' ORDER BY last_name ASC");
    $employees =$empQuery->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errorMsg = 'خطا در دریافت لیست کارکنان: ' . $e->getMessage();
}

// ۲. مرحله اول: محاسبه خودکار حقوق پایه و کمیسیون بازاریاب از روی فاکتورها
if (isset($_POST['action_load']) &&$selectedEmpId > 0) {
    try {
        $empStmt =$pdo->prepare("SELECT base_salary FROM employees WHERE id = ?");
        $empStmt->execute([$selectedEmpId]);
        $baseSalary = (float)$empStmt->fetchColumn();

        $agentStmt =$pdo->prepare("SELECT id, commission_rate FROM sales_agents WHERE user_id = ?");
        $agentStmt->execute([$selectedEmpId]);
        $agent =$agentStmt->fetch(PDO::FETCH_ASSOC);

        if ($agent && (float)$agent['commission_rate'] > 0) {
            $agentId =$agent['id'];
            $commissionRate = (float)$agent['commission_rate'];

            // استعلام فاکتورهای پرداخت شده این بازاریاب در ماه و سال مشخص
            $salesStmt =$pdo->prepare("
                SELECT COALESCE(SUM(total_amount), 0) as total_sales 
                FROM invoices 
                WHERE sales_agent_id = ? 
                  AND status = 'paid' 
                  AND YEAR(created_at) = ? 
                  AND MONTH(created_at) = ?
            ");
            $salesStmt->execute([$agentId, $salaryYear,$salaryMonth]);
            $totalSales = (float)$salesStmt->fetchColumn();

            // اعمال مبلغ کمیسیون در فیلد پاداش
            $bonuses = ($totalSales * $commissionRate) / 100;
        } else {
            $bonuses = 0;         }     } catch (PDOException$e) {
        $errorMsg = 'خطا در استعلام اطلاعات فاکتورها و کمیسیون: ' . $e->getMessage();
    }
}

// ۳. مرحله دوم: ثبت نهایی فیش حقوقی در دیتابیس
if (isset($_POST['action_submit'])) {
    $status = trim($_POST['status'] ?? 'unpaid');
    $netSalary =$baseSalary + $bonuses - ($deductions + $insuranceAmount +$taxAmount);

    if ($selectedEmpId <= 0 || $baseSalary < 0) {$errorMsg = 'لطفاً ابتدا کارمند را انتخاب کرده و مبالغ را بررسی کنید.';
    } elseif ($netSalary < 0) {$errorMsg = 'مجموع کسورات نمی‌تواند از مجموع درآمدها بیشتر باشد (خالص منفی).';
    } else {
        try {
            $checkStmt =$pdo->prepare("SELECT COUNT(*) FROM payroll WHERE employee_id = ? AND salary_year = ? AND salary_month = ?");
            $checkStmt->execute([$selectedEmpId, $salaryYear,$salaryMonth]);
            
            if ($checkStmt->fetchColumn() > 0) {$errorMsg = 'برای این کارمند قبلاً در سال و ماه انتخاب شده، فیش حقوقی صادر شده است.';
            } else {
                $sql = "INSERT INTO payroll (employee_id, salary_year, salary_month, base_salary, bonuses, deductions, insurance_amount, tax_amount, net_salary, status, created_at) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
                $stmt =$pdo->prepare($sql);$stmt->execute([
                    $selectedEmpId,$salaryYear, $salaryMonth,$baseSalary, 
                    $bonuses,$deductions, $insuranceAmount,$taxAmount, 
                    $netSalary,$status
                ]);

                header("Location: index.php?success=1");
                exit;
            }
        } catch (PDOException $e) {
            $errorMsg = 'خطا در ثبت نهایی فیش حقوقی: ' . $e->getMessage();
        }
    }
}

// بارگذاری فرانت‌اند و قالب ظاهری فرم
include __DIR__ . '/views/calculate.php';