<?php
// فراخوانی پیش‌نیازها و اتصال به دیتابیس
require_once __DIR__ . '/../../includes/db.php'; 
require_once __DIR__ . '/../../includes/auth.php';

requireLogin();
requirePermission('view_inventory');

// ... (در اینجا اگر کد پردازشی برای افزودن یا حذف محصول دارید قرار می‌گیرد) ...

// معرفی فایل محتوا (این مسیر درست است چون پوشه views کنار همین فایل است)
$contentFile = __DIR__ . '/views/products_content.php';

// فراخوانی قالب اصلی از ریشه پروژه
require_once __DIR__ . '/../../includes/layout.php';