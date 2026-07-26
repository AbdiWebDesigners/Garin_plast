<?php
// دریافت لیست تمام حساب‌ها از دیتابیس برای نمایش در جدول
global $pdo;
$stmt = $pdo->query("SELECT * FROM accounting_accounts ORDER BY code ASC");
$accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php if ($error): ?>
    <div class="alert alert-danger shadow-sm fw-bold border-0 border-start border-danger border-4 mb-3">
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success shadow-sm fw-bold border-0 border-start border-success border-4 mb-3">
        <?= htmlspecialchars($success) ?>
    </div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 sticky-top" style="top: 20px; z-index: 10;">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 fw-bold text-primary"><i class="fa fa-plus-circle me-1"></i> تعریف سرفصل جدید</h6>
            </div>
            <div class="card-body p-4">
                <form method="POST">
                    <input type="hidden" name="add_account" value="1">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small">کد حساب</label>
                        <input type="text" name="code" class="form-control text-center ltr" placeholder="مثلا: 10101" required>
                        <div class="form-text small text-muted">کد منحصربه‌فرد برای ساختار درختواره مالی.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small">عنوان حساب</label>
                        <input type="text" name="name" class="form-control" placeholder="مثلا: صندوق مرکزی ریالی" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-muted small">نوع سطح حساب</label>
                        <select name="type" class="form-select">
                            <option value="کل">حساب کل</option>
                            <option value="معین" selected>حساب معین</option>
                            <option value="تفصیلی">حساب تفصیلی</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm">
                        ذخیره سرفصل جدید
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-secondary">لیست کدینگ حساب‌ها (<?= count($accounts) ?> مورد)</h6>
                <a href="index.php" class="btn btn-sm btn-outline-secondary">بازگشت به میز کار</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-center small fw-bold">
                            <tr>
                                <th width="15%">کد حساب</th>
                                <th>عنوان سرفصل</th>
                                <th width="20%">نوع سطح</th>
                                <th width="15%">عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($accounts)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted small">هیچ سرفصل حسابی در سیستم تعریف نشده است.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($accounts as $acc): ?>
                                    <tr class="text-center">
                                        <td class="fw-bold text-dark ltr"><?= htmlspecialchars($acc['code']) ?></td>
                                        <td class="text-start ps-4 fw-bold text-secondary"><?= htmlspecialchars($acc['name']) ?></td>
                                        <td>
                                            <?php if (($acc['type'] ?? '') === 'کل'): ?>
                                                <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill small fw-bold">کل</span>
                                            <?php elseif (($acc['type'] ?? '') === 'تفصیلی'): ?>
                                                <span class="badge bg-warning-subtle text-warning px-3 py-2 rounded-pill small fw-bold">تفصیلی</span>
                                            <?php else: ?>
                                                <span class="badge bg-info-subtle text-info px-3 py-2 rounded-pill small fw-bold">معین</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-light text-muted opacity-75" title="ویرایش" disabled>
                                                <i class="fa fa-edit"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>