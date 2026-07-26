<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();

$error = null;
$success = null;

// ۱. بررسی وجود شناسه (ID) در URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: accounts.php");
    exit;
}

$id = (int)$_GET['id'];

// ۲. دریافت اطلاعات حساب فعلی از دیتابیس
$stmt = $pdo->prepare("SELECT * FROM accounting_accounts WHERE id = ?");
$stmt->execute([$id]);
$account = $stmt->fetch(PDO::FETCH_ASSOC);

// اگر حسابی با این شناسه پیدا نشد، به لیست برگرد
if (!$account) {
    header("Location: accounts.php");
    exit;
}

// ۳. دریافت لیست حساب‌ها برای فیلد "حساب پدر"
// نکته: خود این حساب را از لیست حذف می‌کنیم (WHERE id != ?) تا کاربر نتواند یک حساب را زیرمجموعه خودش کند!
$stmtParents = $pdo->prepare("SELECT id, code, name, level FROM accounting_accounts WHERE id != ? ORDER BY code ASC");
$stmtParents->execute([$id]);
$parentAccounts = $stmtParents->fetchAll(PDO::FETCH_ASSOC);

// ۴. پردازش اطلاعات ارسال شده برای ویرایش
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code']);
    $name = trim($_POST['name']);
    $type = $_POST['type'];
    $parent_id = !empty($_POST['parent_id']) ? $_POST['parent_id'] : null;
    $level = (int)$_POST['level'];

    // اعتبارسنجی
    if (empty($code) || empty($name) || empty($type) || empty($level)) {
        $error = "لطفاً تمام فیلدهای ستاره‌دار را پر کنید.";
    } else {
        try {
            $updateStmt = $pdo->prepare("
                UPDATE accounting_accounts 
                SET code = ?, name = ?, type = ?, parent_id = ?, level = ? 
                WHERE id = ?
            ");
            $updateStmt->execute([$code, $name, $type, $parent_id, $level, $id]);
            
            // بروزرسانی متغیر حساب برای نمایش تغییرات جدید در فرم
            $account['code'] = $code;
            $account['name'] = $name;
            $account['type'] = $type;
            $account['parent_id'] = $parent_id;
            $account['level'] = $level;

            $success = "تغییرات با موفقیت ذخیره شد.";
            
            // در صورت تمایل می‌توانید کاربر را مستقیماً به لیست برگردانید:
            // header("Location: accounts.php");
            // exit;

        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = "کد حساب وارد شده متعلق به حساب دیگری است (تکراری).";
            } else {
                $error = "خطا در ویرایش اطلاعات: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>ویرایش سرفصل</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0">ویرایش سرفصل: <?= htmlspecialchars($account['name']) ?></h5>
                    <a href="accounts.php" class="btn btn-light btn-sm">بازگشت به لیست</a>
                </div>
                <div class="card-body p-4">
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">کد حساب <span class="text-danger">*</span></label>
                                <input type="text" name="code" class="form-control" value="<?= htmlspecialchars($account['code']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">نام حساب <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($account['name']) ?>" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">ماهیت (نوع حساب) <span class="text-danger">*</span></label>
                                <select name="type" class="form-select" required>
                                    <option value="asset" <?= $account['type'] === 'asset' ? 'selected' : '' ?>>دارایی (Asset)</option>
                                    <option value="liability" <?= $account['type'] === 'liability' ? 'selected' : '' ?>>بدهی (Liability)</option>
                                    <option value="equity" <?= $account['type'] === 'equity' ? 'selected' : '' ?>>حقوق صاحبان سهام (Equity)</option>
                                    <option value="revenue" <?= $account['type'] === 'revenue' ? 'selected' : '' ?>>درآمد (Revenue)</option>
                                    <option value="expense" <?= $account['type'] === 'expense' ? 'selected' : '' ?>>هزینه (Expense)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">سطح حساب <span class="text-danger">*</span></label>
                                <select name="level" class="form-select" required>
                                    <option value="1" <?= $account['level']