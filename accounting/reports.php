<?php
// فراخوانی پیش‌نیازها
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();

// تنظیم متغیرهای Layout سیستم ERP
$pageTitle = 'گزارشات مالی و ترازنامه';
$pageDescription = 'مشاهده تراز آزمایشی حساب‌ها و وضعیت گردش مالی سیستم';
$pageIcon = 'fa-chart-pie'; 

// معرفی فایل محتوا
$contentFile = __DIR__ . '/views/reports_content.php';

// فراخوانی قالب اصلی
require_once __DIR__ . '/../includes/layout.php';