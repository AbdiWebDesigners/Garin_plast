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
        // اتصال صحیح به سایدبار مدیریت
        include __DIR__ . '/../../admin/sidebar.php'; 
        ?>

        <div class="col-md-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="mb-1">مدیریت پرسنل و کارکنان</h3>
                    <div class="text-muted">ثبت پرونده پرسنلی، تعیین حقوق پایه پرسنل و مدیریت وضعیت اشتغال</div>
                </div>
                <div class="d-flex gap-2">
                    <a href="employee_add.php" class="btn btn-sm btn-primary d-flex align-items-center"><i class="fa fa-user-plus me-1"></i> افزودن کارمند جدید</a>
                    <a href="index.php" class="btn btn-sm btn-outline-secondary">لیست حقوق و دستمزد</a>
                </div>
            </div>

            <?php if(isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show small py-2 px-3 rounded-3" role="alert">
                    عملیات با موفقیت به‌روزرسانی شد.
                    <button type="button" class="btn-close shadow-none p-2.5" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="row g-3 mb-4">
                <div class="col-md-3"><div class="card text-white bg-primary shadow-sm"><div class="card-body"><h4 class="mb-0"><?= $employeeStats['total'] ?> نفر</h4><small>کل پرسنل ثبت شده</small></div></div></div>
                <div class="col-md-3"><div class="card text-white bg-success shadow-sm"><div class="card-body"><h4 class="mb-0"><?= $employeeStats['active'] ?> نفر</h4><small>کارکنان مشغول به کار (فعال)</small></div></div></div>
                <div class="col-md-3"><div class="card text-white bg-danger shadow-sm"><div class="card-body"><h4 class="mb-0"><?= $employeeStats['inactive'] ?> نفر</h4><small>پرسنل غیرفعال / تسویه شده</small></div></div></div>
                <div class="col-md-3"><div class="card text-white bg-warning shadow-sm"><div class="card-body"><h4 class="mb-0"><?= money($employeeStats['total_base_salary']) ?></h4><small>بودجه حقوق پایه ماهانه (فعالین)</small></div></div></div>
            </div>

            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-body">
                    <form method="get" class="row g-3 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label">جستجوی پرسنل</label>
                            <input type="text" name="search" class="form-control" value="<?= htmlspecialchars($search) ?>" placeholder="نام، نام خانوادگی، کد ملی، شماره تماس...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">وضعیت اشتغال</label>
                            <select name="status" class="form-select">
                                <option value="">همه وضعیت‌ها</option>
                                <option value="active" <?= $statusFilter === 'active' ? 'selected' : '' ?>>فعال</option>
                                <option value="inactive" <?= $statusFilter === 'inactive' ? 'selected' : '' ?>>غیرفعال</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100">جستجو</button>
                            <a href="employees.php" class="btn btn-outline-secondary w-100">ریست</a>
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
                                    <th>نام و نام خانوادگی</th>
                                    <th>کد ملی</th>
                                    <th>شماره تماس</th>
                                    <th>حقوق پایه مصوب</th>
                                    <th>شماره حساب / شبا</th>
                                    <th>وضعیت</th>
                                    <th class="text-nowrap text-end">عملیات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($employeesList)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">هیچ کارمندی با مشخصات وارد شده یافت نشد.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($employeesList as $emp): ?>
                                        <tr>
                                            <td><?= (int)$emp['id'] ?></td>
                                            <td class="fw-semibold text-dark">
                                                <?= htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']) ?>
                                            </td>
                                            <td><code class="text-dark"><?= htmlspecialchars($emp['national_code']) ?></code></td>
                                            <td><?= htmlspecialchars($emp['phone'] ?: '-') ?></td>
                                            <td class="fw-semibold text-secondary"><?= money($emp['base_salary']) ?></td>
                                            <td class="small text-muted"><?= htmlspecialchars($emp['bank_account'] ?: '-') ?></td>
                                            <td><?= statusBadge($emp['status']) ?></td>
                                            <td class="text-nowrap text-end">
                                                <a href="employee_edit.php?id=<?= (int)$emp['id'] ?>" class="btn btn-sm btn-warning">ویرایش پرونده</a>
                                                <a href="employees.php?action=toggle&id=<?= (int)$emp['id'] ?>" class="btn btn-sm btn-outline-dark" onclick="return confirm('آیا از تغییر وضعیت فعالیت این کارمند مطمئن هستید؟')">
                                                    <?= $emp['status'] === 'active' ? 'تعلیق / غیرفعال‌سازی' : 'فعال‌سازی مجدد' ?>
                                                </a>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>