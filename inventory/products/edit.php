<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';
requireLogin();

// ... (کدهای شما برای واکشی اطلاعات محصول از دیتابیس و پردازش POST) ...

// اتصال به فایل نمای گرافیکی و لود کردن قالب اصلی
$contentFile = __DIR__ . '/views/edit_content.php';
require_once __DIR__ . '/../../includes/layout.php';