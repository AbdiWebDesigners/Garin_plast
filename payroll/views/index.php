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
        // با توجه به اینکه این فایل از روتِ payroll اینکلود می‌شود، مسیر سایدبار به این شکل به روت پروژه متصل می‌شود
        include __DIR__ . '/../../admin/sidebar.php'; 
        ?>

        <div class="col-md-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="mb-1">حقوق و دستمزد کارکنان</h3>
                    <div class="text-muted">مدیریت لیست حقوق ماهیانه، فیش‌های صادره، مزایا و کسورات</div>
                </div>
                <div class="d-flex gap-2">
                    <a href="calculate.php" class="btn btn-sm btn-primary d-flex align-items-center"><i class="fa fa-calculator me-1"></i> محاسبه حقوق جدید</a>
                    <a href="employees.php" class="btn btn-sm btn-outline-primary d-flex align-items-center"><i class="fa fa-users me-1"></i> لیست کارمندان</a>
                    <a href="<?= BASE_URL ?>admin/dashboard.php" class="btn btn-sm btn-outline-secondary">داشبورد</a>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-3"><div class="card text-white bg-primary shadow-sm"><div class="card-body"><h4 class="mb-0"><?= money($payrollStats['total_net']) ?></h4><small>کل حقوق خالص دوره</small></div></div></div>
                <div class="col-md-3"><div class="card text-white bg-success shadow-sm"><div class="card-body"><h4 class="mb-0"><?= money($payrollStats['total_paid']) ?></h4><small>مجموع پرداخت شده</small></div></div></div>
                <div class="col-md-3"><div class="card text-white bg-danger shadow-sm"><div class="card-body"><h4 class="mb-0"><?= money($payrollStats['total_unpaid']) ?></h4><small>معوقات (پرداخت نشده)</small></div></div></div>
                <div class="col-md-3"><div class="card text-white bg-warning shadow-sm"><div class="card-body"><h4 class="mb-0"><?= money($payrollStats['total_deductions']) ?></h4><small>جمع کسورات و مالیات</small></div></div></div>
            </div>

            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-body">
                    <form method="get" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">جستجوی کارمند</label>
                            <input type="text" name="search" class="form-control" value="<?= htmlspecialchars($search) ?>" placeholder="نام، نام خانوادگی، کد ملی...">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">ماه کارکرد</label>
                            <select name="month" class="form-select">
                                <option value="">همه ماه‌ها</option>
                                <?php for($i=1; $i<=12; $i++): ?>
                                    <option value="<?= $i ?>" <?= $monthFilter == $i ? 'selected' : '' ?>><?= getMonthName($i) ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">وضعیت تسویه</label>
                            <select name="status" class="form-select">
                                <option value="">همه وضعیت‌ها</option>
                                <option value="unpaid" <?= $statusFilter === 'unpaid' ? 'selected' : '' ?>>در انتظار پرداخت</option>
                                <option value="paid" <?= $statusFilter === 'paid' ? 'selected' : '' ?>>پرداخت‌شده</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100">اعمال</button>
                            <a href="index.php" class="btn btn-outline-secondary w-100">ریست</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>کارمند</th>
                                    <th>ماه کارکرد</th>
                                    <th>حقوق پایه</th>
                                    <th>مزایا و پاداش</th>
                                    <th>بیمه و کسورات</th>
                                    <th>خالص دریافتی</th>
                                    <th>وضعیت</th>
                                    <th class="text-nowrap text-end">عملیات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($payrollList)): ?>
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">هیچ رکورد حقوق و دستمزدی یافت نشد.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($payrollList as $item): ?>
                                        <tr>
                                            <td><?= (int)$item['id'] ?></td>
                                            <td>
                                                <div class="fw-semibold"><?= htmlspecialchars($item['first_name'] . ' ' . $item['last_name']) ?></div>
                                                <div class="small text-muted">کد ملی: <?= htmlspecialchars($item['national_code']) ?></div>
                                            </td>
                                            <td>
                                                <div class="fw-semibold"><?= getMonthName($item['salary_month']) ?></div>
                                                <div class="small text-muted">سال <?= (int)$item['salary_year'] ?></div>
                                            </td>
                                            <td><?= money($item['base_salary']) ?></td>
                                            <td class="text-success">+ <?= money($item['bonuses']) ?></td>
                                            <td class="text-danger">- <?= money($item['deductions'] + $item['insurance_amount'] + $item['tax_amount']) ?></td>
                                            <td class="fw-bold"><?= money($item['net_salary']) ?></td>
                                            <td><?= payrollBadge($item['status']) ?></td>
                                            <td class="text-nowrap text-end">
                                                <a href="slip.php?id=<?= (int)$item['id'] ?>" class="btn btn-sm btn-outline-primary">فیش حقوقی</a>
                                                <a href="edit.php?id=<?= (int)$item['id'] ?>" class="btn btn-sm btn-warning">ویرایش</a>
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
</div>
</body>
</html>