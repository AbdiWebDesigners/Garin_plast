<?php
// دریافت لیست حساب‌ها برای نمایش در منوی کشویی (Dropdown) فرم
global $pdo;
$stmtAccounts = $pdo->query("SELECT id, code, name FROM accounting_accounts ORDER BY code ASC");
$accounts = $stmtAccounts->fetchAll(PDO::FETCH_ASSOC);
?>

<?php if (isset($error) && $error): ?>
    <div class="alert alert-danger shadow-sm fw-bold border-0 border-start border-danger border-4">
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 fw-bold text-primary">فرم ثبت سند دوبل</h5>
        <a href="index.php" class="btn btn-sm btn-outline-secondary">بازگشت به داشبورد</a>
    </div>
    
    <div class="card-body p-4">
        <form method="POST" id="journalForm">
            <div class="row mb-4 bg-light p-3 rounded">
                <div class="col-md-3">
                    <label class="form-label fw-bold text-muted small">تاریخ سند</label>
                    <input type="date" name="date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="col-md-9">
                    <label class="form-label fw-bold text-muted small">شرح کلی سند</label>
                    <input type="text" name="description" class="form-control" placeholder="بابت..." required>
                </div>
            </div>

            <h6 class="fw-bold text-secondary mb-3">آرتیکل‌ها (اقلام سند)</h6>
            <div class="table-responsive">
                <table class="table table-bordered align-middle" id="itemsTable">
                    <thead class="table-light text-center small">
                        <tr>
                            <th>حساب معین</th>
                            <th>شرح ردیف</th>
                            <th width="15%" class="text-success">بدهکار (تومان)</th>
                            <th width="15%" class="text-danger">بستانکار (تومان)</th>
                            <th width="70">عملیات</th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        <tr>
                            <td>
                                <select name="items[0][account_id]" class="form-select" required>
                                    <option value="">انتخاب حساب...</option>
                                    <?php foreach ($accounts as $acc): ?>
                                        <option value="<?= $acc['id'] ?>"><?= htmlspecialchars($acc['code'] . ' - ' . $acc['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><input type="text" name="items[0][description]" class="form-control" placeholder="شرح (اختیاری)"></td>
                            <td><input type="number" step="0.01" name="items[0][debit]" class="form-control debit-input text-center" value="0" required></td>
                            <td><input type="number" step="0.01" name="items[0][credit]" class="form-control credit-input text-center" value="0" required></td>
                            <td class="text-center"><button type="button" class="btn btn-danger btn-sm opacity-50" disabled>حذف</button></td>
                        </tr>
                        <tr>
                            <td>
                                <select name="items[1][account_id]" class="form-select" required>
                                    <option value="">انتخاب حساب...</option>
                                    <?php foreach ($accounts as $acc): ?>
                                        <option value="<?= $acc['id'] ?>"><?= htmlspecialchars($acc['code'] . ' - ' . $acc['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><input type="text" name="items[1][description]" class="form-control" placeholder="شرح (اختیاری)"></td>
                            <td><input type="number" step="0.01" name="items[1][debit]" class="form-control debit-input text-center" value="0" required></td>
                            <td><input type="number" step="0.01" name="items[1][credit]" class="form-control credit-input text-center" value="0" required></td>
                            <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm" onclick="this.closest('tr').remove(); calculateTotals();">حذف</button></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="table-light fw-bold text-center">
                            <td colspan="2" class="text-end">جمع کل سند:</td>
                            <td id="totalDebit" class="text-success fs-5">0</td>
                            <td id="totalCredit" class="text-danger fs-5">0</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <button type="button" class="btn btn-outline-primary" onclick="addRow()">
                    <i class="fa fa-plus me-1"></i> افزودن ردیف جدید
                </button>
                <button type="submit" class="btn btn-success px-5 fw-bold shadow-sm">
                    ثبت نهایی سند
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// انتقال داده‌های دیتابیس به جاوااسکریپت
const accounts = <?= json_encode($accounts) ?>;
let rowCount = 2; 

function addRow() {
    let optionsHtml = '<option value="">انتخاب حساب...</option>';
    accounts.forEach(acc => {
        optionsHtml += `<option value="${acc.id}">${acc.code} - ${acc.name}</option>`;
    });

    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td><select name="items[${rowCount}][account_id]" class="form-select" required>${optionsHtml}</select></td>
        <td><input type="text" name="items[${rowCount}][description]" class="form-control" placeholder="شرح (اختیاری)"></td>
        <td><input type="number" step="0.01" name="items[${rowCount}][debit]" class="form-control debit-input text-center" value="0" required></td>
        <td><input type="number" step="0.01" name="items[${rowCount}][credit]" class="form-control credit-input text-center" value="0" required></td>
        <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm" onclick="this.closest('tr').remove(); calculateTotals();">حذف</button></td>
    `;
    document.getElementById('itemsBody').appendChild(tr);
    rowCount++;
    attachEventListeners();
}

function calculateTotals() {
    let totalDebit = 0;
    let totalCredit = 0;
    
    document.querySelectorAll('.debit-input').forEach(input => {
        totalDebit += parseFloat(input.value) || 0;
    });
    
    document.querySelectorAll('.credit-input').forEach(input => {
        totalCredit += parseFloat(input.value) || 0;
    });

    // فرمت کردن اعداد با ویرگول برای خوانایی بهتر (تومان)
    document.getElementById('totalDebit').innerText = totalDebit.toLocaleString();
    document.getElementById('totalCredit').innerText = totalCredit.toLocaleString();
    
    // تغییر رنگ جمع در صورت ناتراز بودن سند
    const tDebitEl = document.getElementById('totalDebit');
    const tCreditEl = document.getElementById('totalCredit');
    
    if (totalDebit !== totalCredit) {
        tDebitEl.classList.add('bg-warning', 'text-dark');
        tCreditEl.classList.add('bg-warning', 'text-dark');
    } else {
        tDebitEl.classList.remove('bg-warning', 'text-dark');
        tCreditEl.classList.remove('bg-warning', 'text-dark');
    }
}

function attachEventListeners() {
    document.querySelectorAll('.debit-input, .credit-input').forEach(input => {
        input.removeEventListener('input', calculateTotals);
        input.addEventListener('input', calculateTotals);
    });
}

// مقداردهی اولیه برای محاسبه رویدادها
attachEventListeners();
</script>