<?php
// فایل: garin/payroll/employees.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

if (empty($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit;
}

$pageTitle = 'مدیریت کارکنان';
$search = trim($_GET['search'] ?? '');
$statusFilter = trim($_GET['status'] ?? '');

// عملیات تغییر وضعیت کارمند (فعال / غیرفعال) به صورت سریع
if (isset($_GET['action']) && $_GET['action'] === 'toggle' && isset($_GET['id'])) {
    $empId = (int)$_GET['id'];
    try {
        $stmt = $pdo->prepare("UPDATE employees SET status = IF(status='active', 'inactive', 'active') WHERE id = ?");
        $stmt->execute([$empId]);
        header("Location: employees.php?success=1");
        exit;
    } catch (PDOException $e) {
        $errorMsg = "خطا در تغییر وضعیت کارمند: " . $e->getMessage();
    }
}

$employeesList = [];
$employeeStats = [
    'total' => 0,
    'active' => 0,
    'inactive' => 0,
    'total_base_salary' => 0
];

try {
    // ۱. محاسبه آمار کلان کارکنان
    $statsQuery = $pdo->query("
        SELECT 
            COUNT(*) as total,
            COALESCE(SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END), 0) as active,
            COALESCE(SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END), 0) as inactive,
            COALESCE(SUM(CASE WHEN status = 'active' THEN base_salary ELSE 0 END), 0) as total_base_salary
        FROM employees
    ");
    
    if ($statsQuery) {
        $statsData = $statsQuery->fetch(PDO::FETCH_ASSOC);
        $employeeStats['total'] = (int)$statsData['total'];
        $employeeStats['active'] = (int)$statsData['active'];
        $employeeStats['inactive'] = (int)$statsData['inactive'];
        $employeeStats['total_base_salary'] = (float)$statsData['total_base_salary'];
    }

    // ۲. کوئری اصلی لیست کارکنان همراه با فیلترها
    $sql = "SELECT * FROM employees";
    $where = [];
    $params = [];

    if ($search !== '') {
        $where[] = "(first_name LIKE ? OR last_name LIKE ? OR national_code LIKE ? OR phone LIKE ?)";
        $like = '%' . $search . '%';
        array_push($params, $like, $like, $like, $like);
    }

    if ($statusFilter !== '') {
        $where[] = "status = ?";
        $params[] = $statusFilter;
    }

    if (!empty($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }

    $sql .= " ORDER BY id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $employeesList = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Throwable $e) {
    die("<div style='padding:20px; direction:rtl; background:#f8d7da; color:#721c24;'><b>خطا در دیتابیس کارکنان:</b> " . htmlspecialchars($e->getMessage()) . "</div>");
}

// توابع کمکی نمایش
function statusBadge($status) {
    if ($status === 'active') return '<span class="badge bg-success-subtle text-success border border-success-subtle">فعال</span>';
    if ($status === 'inactive') return '<span class="badge bg-danger-subtle text-danger border border-danger-subtle">غیرفعال</span>';
    return '<span class="badge bg-dark-subtle text-dark">نامشخص</span>';
}

function money($v) {
    return number_format((float)$v) . ' تومان';
}

// بارگذاری لایوت ظاهری
include __DIR__ . '/views/employees.php';