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
        <?php include __DIR__ . '/../../admin/sidebar.php'; ?>

        <div class="col-md-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="mb-1">محاسبه حقوق و کمیسیون بازاریابی</h3>
                    <div class="text-muted">محاسبه خودکار سهم کمیسیون از جدول فاکتورها و صدور فیش دستمزد</div>
                </div>
                <a href="index.php" class="btn btn-sm btn-outline-secondary">
                    <i class="fa fa-arrow-right me-1"></i> بازگشت به لیست
                </a>
            </div>

            <?php if (!empty($errorMsg)): ?>
                <div class="alert alert-danger rounded-3 small py-2 px-3 mb-4">
                    <i class="fa fa-exclamation-circle me-1"></i> <?= htmlspecialchars($errorMsg) ?>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <form method="post" action="calculate.php" autocomplete="off">
                        
                        <div class="row g-3 bg-light p-3 rounded-3 mb-4 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">انتخاب کارمند / بازاریاب</label>
                                <select name="employee_id" class="form-select shadow-none" required>
                                    <option value="">-- انتخاب کنید --</option>
                                    <?php foreach ($employees as $emp): ?>
                                        <option value="<?= $emp['id'] ?>" <?= ($selectedEmpId == $emp['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']) ?> (کد ملی: <?= htmlspecialchars($emp['national_code']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold small">سال کارکرد</label>
                                <select name="salary_year" class="form-select shadow-none">
                                    <option value="1405" <?= ($salaryYear == 1405) ? 'selected' : '' ?>>1405</option>
                                    <option value="1404" <?= ($salaryYear == 1404) ? 'selected' : '' ?>>1404</option>
                                    <option value="1406" <?= ($salaryYear == 1406) ? 'selected' : '' ?>>1406</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold small">ماه کارکرد</label>
                                <select name="salary_month" class="form-select shadow-none">
                                    <?php
                                    $months = [
                                        1 => 'فروردین', 2 => 'اردیبهشت', 3 => 'خرداد', 4 => 'تیر',
                                        5 => 'مرداد', 6 => 'شهریور', 7 => 'مهر', 8 => 'آبان',
                                        9 => 'آذر', 10 => 'دی', 11 => 'بهمن', 12 => 'اسفند'
                                    ];
                                    foreach ($months as $num => $name):
                                    ?>
                                        <option value="<?= $num ?>" <?= ($salaryMonth == $num) ? 'selected' : '' ?>><?= $name ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" name="action_load" class="btn btn-dark w-100 py-2 btn-sm fw-bold">
                                    <i class="fa fa-refresh me-1"></i> بارگذاری و استعلام
                                </button>
                            </div>
                        </div>

                        <div class="row g-4">
                            
                            <div class="col-md-6 border-end border-light">
                                <h6 class="text-success mb-3 fw-bold small"><i class="fa fa-plus-circle"></i> درآمدها و مزایا</h6>
                                
                                <div class="mb-3">
                                    <label class="form-label small">حقوق پایه مصوب (تومان)</label>
                                    <input type="number" name="base_salary" id="baseSalary" class="form-control shadow-none text-start" min="0" value="<?= htmlspecialchars($baseSalary) ?>">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small">کمیسیون بازاریابی و پاداش (تومان)</label>
                                    <input type="number" name="bonuses" id="bonuses" class="form-control shadow-none text-start text-success fw-bold" min="0" value="<?= htmlspecialchars($bonuses) ?>">
                                    <div class="form-text text-muted x-small">در صورت انتخاب بازاریاب، سهم کمیسیون فاکتورها خودکار محاسبه می‌شود.</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h6 class="text-danger mb-3 fw-bold small"><i class="fa fa-minus-circle"></i> کسورات قانونی</h6>
                                
                                <div class="row g-2">
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label small">مساعده و جریمه (تومان)</label>
                                        <input type="number" name="deductions" id="deductions" class="form-control shadow-none text-start" min="0" value="<?= htmlspecialchars($deductions) ?>">
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label small">حق بیمه سهم کارمند</label>
                                        <input type="number" name="insurance_amount" id="insuranceAmount" class="form-control shadow-none text-start" min="0" value="<?= htmlspecialchars($insuranceAmount) ?>">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label small">مالیات بر درآمد ماهانه</label>
                                        <input type="number" name="tax_amount" id="taxAmount" class="form-control shadow-none text-start" min="0" value="<?= htmlspecialchars($taxAmount) ?>">
                                    </div>
                                </div>
                            </div>

                            <hr class="text-muted my-3">

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">وضعیت اولیه تسویه فیش</label>
                                <select name="status" class="form-select shadow-none">
                                    <option value="unpaid">در انتظار پرداخت (معوقه)</option>
                                    <option value="paid">پرداخت‌شده (تسویه کامل)</option>
                                </select>
                            </div>

                            <div class="col-md-6 bg-dark text-white rounded-3 p-3 d-flex flex-column justify-content-center align-items-center">
                                <div class="small text-secondary mb-1">خالص دریافتی نهایی (تومان)</div>
                                <h3 class="mb-0 text-warning" id="netSalaryDisplay">0 تومان</h3>
                            </div>

                            <div class="col-md-12 d-flex gap-2 justify-content-end mt-3">
                                <button type="submit" name="action_submit" class="btn btn-primary px-4">
                                    <i class="fa fa-calculator me-1"></i> محاسبه و صدور نهایی فیش
                                </button>
                                <a href="index.php" class="btn btn-outline-secondary px-4">انصراف</a>
                            </div>

                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const baseSalaryInput = document.getElementById('baseSalary');
    const bonusesInput = document.getElementById('bonuses');
    const deductionsInput = document.getElementById('deductions');
    const insuranceInput = document.getElementById('insuranceAmount');
    const taxInput = document.getElementById('taxAmount');
    const netSalaryDisplay = document.getElementById('netSalaryDisplay');

    function calculateNetSalary() {
        const base = parseFloat(baseSalaryInput.value) || 0;
        const bonuses = parseFloat(bonusesInput.value) || 0;
        const deductions = parseFloat(deductionsInput.value) || 0;
        const insurance = parseFloat(insuranceInput.value) || 0;
        const tax = parseFloat(taxInput.value) || 0;

        const netSalary = base + bonuses - (deductions + insurance + tax);
        
        netSalaryDisplay.textContent = new Intl.NumberFormat('fa-IR').format(netSalary) + ' تومان';
        
        if(netSalary < 0) {
            netSalaryDisplay.classList.replace('text-warning', 'text-danger');
        } else {
            netSalaryDisplay.classList.replace('text-danger', 'text-warning');
        }
    }

    [baseSalaryInput, bonusesInput, deductionsInput, insuranceInput, taxInput].forEach(input => {
        input.addEventListener('input', calculateNetSalary);
    });

    calculateNetSalary();
});
</script>
</body>
</html>