<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container-fluid">
    <div class="row">
        <?php 
        // اتصال به سایدبار مدیریت پروژه
        include __DIR__ . '/../../admin/sidebar.php'; 
        ?>

        <div class="col-md-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="mb-1">افزودن کارمند جدید</h3>
                    <div class="text-muted">ایجاد پرونده پرسنلی جدید و تعیین مشخصات مالی پایه</div>
                </div>
                <div>
                    <a href="employees.php" class="btn btn-sm btn-outline-secondary d-flex align-items-center">
                        <i class="fa fa-arrow-right me-1"></i> بازگشت به لیست پرسنل
                    </a>
                </div>
            </div>

            <?php if (!empty($errorMsg)): ?>
                <div class="alert alert-danger rounded-3 small py-2 px-3 mb-4" role="alert">
                    <i class="fa fa-exclamation-circle me-1"></i> <?= htmlspecialchars($errorMsg) ?>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white py-3 border-bottom border-light">
                    <h5 class="card-title mb-0 text-primary small fw-bold">
                        <i class="fa fa-user-edit me-1"></i> مشخصات فردی و مالی کارمند
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form method="post" action="employee_add.php" autocomplete="off">
                        <div class="row g-3">
                            
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">نام <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" class="form-control shadow-none text-dark" required 
                                       value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>" placeholder="مثال: علی">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">نام خانوادگی <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" class="form-control shadow-none text-dark" required 
                                       value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>" placeholder="مثال: محمدی">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">کد ملی (۱۰ رقمی) <span class="text-danger">*</span></label>
                                <input type="text" name="national_code" class="form-control shadow-none text-dark text-start" required maxlength="10"
                                       value="<?= htmlspecialchars($_POST['national_code'] ?? '') ?>" placeholder="0012345678">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">شماره همراه</label>
                                <input type="text" name="phone" class="form-control shadow-none text-dark text-start" maxlength="11"
                                       value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" placeholder="09123456789">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">حقوق پایه ماهانه (تومان) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="base_salary" class="form-control shadow-none text-dark text-start" required min="0"
                                           value="<?= htmlspecialchars($_POST['base_salary'] ?? '') ?>" placeholder="مثال: 12000000">
                                    <span class="input-group-text bg-light small">تومان</span>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">وضعیت اولیه اشتغال</label>
                                <select name="status" class="form-select shadow-none">
                                    <option value="active" <?= ($_POST['status'] ?? '') === 'active' ? 'selected' : '' ?>>فعال (مشغول به کار)</option>
                                    <option value="inactive" <?= ($_POST['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>غیرفعال (تعلیق / تسویه)</option>
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-semibold small">اطلاعات حساب بانکی (شماره حساب یا شبا)</label>
                                <input type="text" name="bank_account" class="form-control shadow-none text-dark text-start" 
                                       value="<?= htmlspecialchars($_POST['bank_account'] ?? '') ?>" placeholder="مثال: شماره شبا IR123456789012345678901234">
                            </div>

                            <div class="col-md-12 d-flex gap-2 mt-4 justify-content-end">
                                <button type="submit" class="btn btn-primary px-4 d-flex align-items-center">
                                    <i class="fa fa-save me-1"></i> ذخیره و ثبت کارمند
                                </button>
                                <a href="employees.php" class="btn btn-outline-secondary px-4">انصراف</a>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
            
        </div>
    </div>
</div>
</body>
</html>