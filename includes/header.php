<?php
// با استفاده از __DIR__ مسیر فایل‌ها دقیقاً از داخل فولدر includes خوانده می‌شود
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title><?= $page_title ?? 'پنل مدیریت|گَرین پلاست' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        .admin-header { 
            background: #1b5e20; 
            color: white; 
            padding: 15px 0; 
        }
        body { background: #f8f9fa; }
        .wrapper{
    display:flex;
    min-height:100vh;
}

.main-content{
    flex:1;
    background:#f5f7fb;
}
    </style>
</head>
<body>

<div class="admin-header">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h4 class="mb-0">
                   <a href="<?= BASE_URL ?>admin/dashboard.php"
   class="text-white text-decoration-none">
                        <i class="fas fa-leaf"></i>  گَرین پلاست
                    </a>
                </h4>
            </div>
            <div class="col-md-6 text-end">
                <span class="badge bg-success me-3">
                    <i class="fas fa-user"></i> مدیر سیستم
                </span>
                <a href="<?= BASE_URL ?>logout.php" class="btn btn-sm btn-danger">
                    <i class="fas fa-sign-out-alt"></i> خروج
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">