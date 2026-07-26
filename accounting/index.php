<?php
// تنظیم متغیرهای اختصاصی برای layout.php
$pageTitle = 'داشبورد حسابداری';
$pageDescription = 'مدیریت یکپارچه سیستم مالی، اسناد و گزارشات';
$pageIcon = 'fa-calculator';

// آدرس فایلی که محتوای اصلی داشبورد در آن قرار دارد
$contentFile = __DIR__ . '/views/dashboard_content.php';

// فراخوانی layout اصلی (که خودش db.php و auth.php را لود می‌کند)
require_once __DIR__ . '/../includes/layout.php';