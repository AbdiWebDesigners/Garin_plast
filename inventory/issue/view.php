<?php
declare(strict_types=1);
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/functions.php';
requireLogin();
requirePermission('viewinventory');
global $pdo;

$id = (int)($_GET['id'] ?? 0);
$error = '';
$success = isset($_GET['success']) ? 'عملیات با موفقیت انجام شد.' : '';
try {
    if ($id <= 0) throw new RuntimeException('شناسه حواله نامعتبر است.');
    $voucher = issueLoadVoucher($pdo, $id);
} catch (Throwable $e) {
    $voucher = null;
    $error = $e->getMessage();
}
$page_title = $pageTitle = 'مشاهده حواله خروج';
$pageDescription = 'جزئیات حواله خروج انبار';
$pageIcon = 'fa-solid fa-eye';
$contentFile = __DIR__ . '/views/view_content.php';
require_once dirname(__DIR__, 2) . '/includes/layout.php';
