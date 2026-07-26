<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// چون پوشه payroll در روت پروژه قرار دارد، با یک سطح عقب رفتن به includes می‌رسیم
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

if (empty($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit;
}

$pageTitle = 'حقوق و دستمزد کارکنان';
$search = trim($_GET['search'] ?? '');
$monthFilter = trim($_GET['month'] ?? '');
$statusFilter = trim($_GET['status'] ?? '');

$payrollList = [];
$payrollStats = [
    'total_net' => 0,
    'total_paid' => 0,
    'total_unpaid' => 0,
    'total_deductions' => 0
];

try {
    // ۱. محاسبه آمارهای کلان سیستم حقوق و دستمزد
    $statsQuery = $pdo->query("
        SELECT 
            COALESCE(SUM(net_salary), 0) as total_net,
            COALESCE(SUM(CASE WHEN status = 'paid' THEN net_salary ELSE 0 END), 0) as total_paid,
            COALESCE(SUM(CASE WHEN status = 'unpaid' THEN net_salary ELSE 0 END), 0) as total_unpaid,
            COALESCE(SUM(deductions + insurance_amount + tax_amount), 0) as total_deductions
        FROM payroll
    ");
    
    if ($statsQuery) {
        $statsData = $statsQuery->fetch(PDO::FETCH_ASSOC);
        $payrollStats['total_net'] = (float)$statsData['total_net'];
        $payrollStats['total_paid'] = (float)$statsData['total_paid'];
        $payrollStats['total_unpaid'] = (float)$statsData['total_unpaid'];
        $payrollStats['total_deductions'] = (float)$statsData['total_deductions'];
    }

    // ۲. کوئری اصلی برای دریافت لیست فیش‌های حقوقی
    $sql = "
        SELECT 
            p.*,
            e.first_name,
            e.last_name,
            e.national_code
        FROM payroll p
        LEFT JOIN employees e ON e.id = p.employee_id
    ";

    $where = [];
    $params = [];

    if ($search !== '') {
        $where[] = "(e.first_name LIKE ? OR e.last_name LIKE ? OR e.national_code LIKE ?)";
        $like = '%' . $search . '%';
        array_push($params, $like, $like, $like);
    }

    if ($monthFilter !== '') {
        $where[] = "p.salary_month = ?";
        $params[] = (int)$monthFilter;
    }

    if ($statusFilter !== '') {
        $where[] = "p.status = ?";
        $params[] = $statusFilter;
    }

    if (!empty($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }

    $sql .= " ORDER BY p.salary_year DESC, p.salary_month DESC, p.id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $payrollList = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Throwable $e) {
    die("<div style='padding:20px; direction:rtl; background:#f8d7da; color:#721c24;'><b>خطا در ماژول حقوق و دستمزد:</b> " . htmlspecialchars($e->getMessage()) . "</div>");
}

// توابع کمکی مورد نیاز نمایشگر (View)
function getMonthName($m) {
    $months = [
        1 => 'فروردین', 2 => 'اردیبهشت', 3 => 'خرداد', 4 => 'تیر',
        5 => 'مرداد', 6 => 'شهریور', 7 => 'مهر', 8 => 'آبان',
        9 => 'آذر', 10 => 'دی', 11 => 'بهمن', 12 => 'اسفند'
    ];
    return $months[(int)$m] ?? 'نامشخص';
}

function payrollBadge($status) {
    if ($status === 'paid') return '<span class="badge bg-success-subtle text-success border border-success-subtle">پرداخت‌شده</span>';
    if ($status === 'unpaid') return '<span class="badge bg-danger-subtle text-danger border border-danger-subtle">در انتظار پرداخت</span>';
    return '<span class="badge bg-dark-subtle text-dark border border-dark-subtle">نامشخص</span>';
}

// فرمت‌دهی پول بر اساس ساختار فاکتورها
function money($v) {
    return number_format((float)$v) . ' تومان';
}

// بارگذاری محتوا و قالب نمایشی از پوشه views
include __DIR__ . '/views/index.php';