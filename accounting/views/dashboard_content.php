<?php
// استخراج تعداد سرفصل‌ها از دیتابیس (کانکشن $pdo در layout.php لود شده است)
try {
    global $pdo;
    $accountsCount = $pdo->query("SELECT COUNT(*) FROM accounting_accounts")->fetchColumn();
} catch (Exception $e) {
    $accountsCount = 0;
}
?>

<div class="row mb-4">
    <div class="col-12">
        <h4 class="fw-bold text-secondary border-bottom pb-2">میز کار حسابداری</h4>
        <p class="text-muted">لطفاً برای مدیریت سیستم مالی یکی از بخش‌های زیر را انتخاب کنید.</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white shadow-sm h-100 text-center py-4 border-0" style="transition: transform 0.2s;">
            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                <div class="display-4 mb-3 opacity-75">📝</div>
                <h5 class="fw-bold mb-3">صدور سند حسابداری</h5>
                <p class="small text-white-50 mb-4">ثبت اسناد دوبل</p>
                <a href="journal.php" class="btn btn-light w-100 stretched-link fw-bold text-primary">ورود به بخش</a>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-info text-white shadow-sm h-100 text-center py-4 border-0" style="transition: transform 0.2s;">
            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                <div class="display-4 mb-3 opacity-75">🗂️</div>
                <h5 class="fw-bold mb-3">کدینگ و سرفصل‌ها</h5>
                <p class="small text-white-50 mb-4">(<?= $accountsCount ?> سرفصل ثبت شده)</p>
                <a href="accounts.php" class="btn btn-light w-100 stretched-link fw-bold text-info">مدیریت حساب‌ها</a>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-success text-white shadow-sm h-100 text-center py-4 border-0" style="transition: transform 0.2s;">
            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                <div class="display-4 mb-3 opacity-75">📊</div>
                <h5 class="fw-bold mb-3">گزارشات و ترازنامه</h5>
                <p class="small text-white-50 mb-4">مشاهده تراز آزمایشی</p>
                <a href="reports.php" class="btn btn-light w-100 stretched-link fw-bold text-success">مشاهده گزارشات</a>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-warning text-dark shadow-sm h-100 text-center py-4 border-0" style="transition: transform 0.2s;">
            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                <div class="display-4 mb-3 opacity-75">⬇️</div>
                <h5 class="fw-bold mb-3">درون‌ریزی اطلاعات</h5>
                <p class="small text-dark mb-4 opacity-75">انتقال فایل CSV</p>
                <a href="import.php" class="btn btn-dark w-100 stretched-link fw-bold">آپلود فایل هلو</a>
            </div>
        </div>
    </div>
</div>