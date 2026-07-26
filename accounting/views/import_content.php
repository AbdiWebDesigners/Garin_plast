<?php if (isset($error) && $error): ?>
    <div class="alert alert-danger shadow-sm fw-bold border-0 border-start border-danger border-4 mb-4">
        <i class="fa fa-exclamation-triangle me-2"></i> <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<?php if (isset($success) && $success): ?>
    <div class="alert alert-success shadow-sm fw-bold border-0 border-start border-success border-4 mb-4">
        <i class="fa fa-check-circle me-2"></i> <?= htmlspecialchars($success) ?>
    </div>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-dark">آپلود فایل خروجی هلو (CSV)</h6>
                <a href="index.php" class="btn btn-sm btn-outline-secondary">بازگشت</a>
            </div>
            
            <div class="card-body p-5 text-center">
                <div class="mb-4">
                    <div class="display-1 text-primary opacity-50 mb-3">
                        <i class="fa fa-cloud-upload-alt"></i>
                    </div>
                    <h5 class="fw-bold text-secondary">فایل اکسل (CSV) خود را انتخاب کنید</h5>
                    <p class="text-muted small">
                        سیستم به صورت خودکار ردیف‌ها را می‌خواند و به عنوان یک سند حسابداری یکپارچه ثبت می‌کند.
                    </p>
                </div>

                <form method="POST" enctype="multipart/form-data" class="bg-light p-4 rounded-3 border border-dashed">
                    <div class="mb-4 text-start">
                        <label class="form-label fw-bold small text-muted">فایل CSV هلو</label>
                        <input class="form-control form-control-lg" type="file" name="holoo_file" accept=".csv" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm">
                        <i class="fa fa-cogs me-2"></i> پردازش و درون‌ریزی اطلاعات
                    </button>
                </form>
            </div>
            
            <div class="card-footer bg-white text-muted small p-4">
                <strong class="text-dark"><i class="fa fa-info-circle me-1"></i> راهنمای ساختار فایل:</strong>
                <ul class="mt-2 mb-0 ps-3">
                    <li>فایل شما حتماً باید دارای پسوند <code>.csv</code> باشد.</li>
                    <li>سیستم سطر اول را به عنوان "تیتر" در نظر گرفته و نادیده می‌گیرد.</li>
                    <li>ساختار ستون‌ها باید به این ترتیب باشد: <strong>ستون اول:</strong> ID حساب معین، <strong>ستون دوم:</strong> شرح ردیف، <strong>ستون سوم:</strong> مبلغ بدهکار، <strong>ستون چهارم:</strong> مبلغ بستانکار.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
.border-dashed {
    border-style: dashed !important;
    border-width: 2px !important;
    border-color: #dee2e6 !important;
}
</style>