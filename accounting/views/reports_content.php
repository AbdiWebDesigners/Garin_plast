<?php
global $pdo;

// کوئری برای محاسبه جمع گردش بدهکار و بستانکار هر حساب
// فقط حساب‌هایی نمایش داده می‌شوند که گردش داشته‌اند (HAVING)
$query = "
    SELECT 
        a.code, 
        a.name, 
        a.type,
        COALESCE(SUM(i.debit), 0) AS total_debit,
        COALESCE(SUM(i.credit), 0) AS total_credit
    FROM 
        accounting_accounts a
    LEFT JOIN 
        accounting_journal_items i ON a.id = i.account_id
    GROUP BY 
        a.id
    HAVING 
        total_debit > 0 OR total_credit > 0
    ORDER BY 
        a.code ASC
";

try {
    $stmt = $pdo->query($query);
    $trialBalance = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $trialBalance = [];
    $error = "خطا در دریافت گزارش: " . $e->getMessage();
}

// محاسبه جمع کل ستون‌ها برای نمایش در انتهای جدول
$grandTotalDebit = 0;
$grandTotalCredit = 0;
?>

<?php if (isset($error)): ?>
    <div class="alert alert-danger shadow-sm fw-bold border-0 border-start border-danger border-4 mb-3">
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0 fw-bold text-primary">تراز آزمایشی دو ستونی</h5>
            <small class="text-muted">مبتنی بر تمامی اسناد ثبت شده در سیستم</small>
        </div>
        <div>
            <button onclick="window.print()" class="btn btn-sm btn-outline-secondary me-2 d-print-none">
                <i class="fa fa-print me-1"></i> چاپ گزارش
            </button>
            <a href="index.php" class="btn btn-sm btn-light d-print-none">بازگشت</a>
        </div>
    </div>
    
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle mb-0">
                <thead class="table-light text-center small fw-bold">
                    <tr>
                        <th width="10%">کد حساب</th>
                        <th>عنوان حساب</th>
                        <th width="10%">سطح</th>
                        <th width="20%" class="text-success">گردش بدهکار (تومان)</th>
                        <th width="20%" class="text-danger">گردش بستانکار (تومان)</th>
                        <th width="15%" class="bg-light">مانده حساب</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($trialBalance)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <div class="fs-1 mb-3 opacity-25">📊</div>
                                هیچ سندی برای نمایش در ترازنامه وجود ندارد.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($trialBalance as $row): 
                            $grandTotalDebit += $row['total_debit'];
                            $grandTotalCredit += $row['total_credit'];
                            
                            // محاسبه مانده حساب
                            $balance = $row['total_debit'] - $row['total_credit'];
                            $balanceText = '-';
                            $balanceClass = 'text-muted';
                            
                            if ($balance > 0) {
                                $balanceText = number_format($balance) . ' (بد)';
                                $balanceClass = 'text-success fw-bold';
                            } elseif ($balance < 0) {
                                $balanceText = number_format(abs($balance)) . ' (بس)';
                                $balanceClass = 'text-danger fw-bold';
                            }
                        ?>
                            <tr class="text-center">
                                <td class="fw-bold text-secondary ltr"><?= htmlspecialchars($row['code']) ?></td>
                                <td class="text-start ps-3 fw-bold"><?= htmlspecialchars($row['name']) ?></td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill small">
                                        <?= htmlspecialchars($row['type'] ?? 'معین') ?>
                                    </span>
                                </td>
                                <td class="text-success"><?= number_format($row['total_debit']) ?></td>
                                <td class="text-danger"><?= number_format($row['total_credit']) ?></td>
                                <td class="bg-light <?= $balanceClass ?>"><?= $balanceText ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <?php if (!empty($trialBalance)): ?>
                <tfoot class="table-light fw-bold text-center fs-6">
                    <tr>
                        <td colspan="3" class="text-end pe-4">جمع کل عملیات:</td>
                        <td class="text-success"><?= number_format($grandTotalDebit) ?></td>
                        <td class="text-danger"><?= number_format($grandTotalCredit) ?></td>
                        <td class="bg-light">
                            <?php if ($grandTotalDebit === $grandTotalCredit): ?>
                                <span class="badge bg-success w-100 py-2">تراز است</span>
                            <?php else: ?>
                                <span class="badge bg-danger w-100 py-2">اختلاف تراز</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>

<style>
/* استایل‌های مخصوص پرینت برای تمیز درامدن گزارش روی کاغذ */
@media print {
    body { background-color: white !important; }
    .wrapper, .main-content { margin: 0 !important; padding: 0 !important; }
    .sidebar, .topbar, .navbar { display: none !important; }
    .card { border: none !important; box-shadow: none !important; }
    .card-header { border-bottom: 2px solid #000 !important; }
}
</style>