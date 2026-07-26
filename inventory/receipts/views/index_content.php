<div class="container-fluid" dir="rtl">
    <div class="row g-0">
        <?php include __DIR__ . '/../../includes/sidebar.php'; ?>

        <main class="col-12 col-md-10 py-4 px-3 px-lg-4">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= htmlspecialchars($success) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
                <div>
                    <h3 class="fw-bold mb-1">رسیدهای انبار</h3>
                    <div class="text-muted">مدیریت رسیدهای ورود کالا</div>
                </div>
                <div class="mt-2 mt-md-0">
                    <a href="create.php" class="btn btn-primary">
                        <i class="fa fa-plus ms-1"></i> ثبت رسید جدید
                    </a>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <div class="text-muted small mb-1">تعداد رسیدها</div>
                            <h3 class="fw-bold m-0"><?= number_format($totalReceipts ?? 0) ?></h3>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <div class="text-muted small mb-1">رسیدهای امروز</div>
                            <h3 class="fw-bold text-primary m-0"><?= number_format($todayReceipts ?? 0) ?></h3>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <div class="text-muted small mb-1">رسیدهای تایید شده</div>
                            <h3 class="fw-bold text-success m-0"><?= number_format($approvedReceipts ?? 0) ?></h3>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <div class="text-muted small mb-1">مجموع مبلغ</div>
                            <h3 class="fw-bold text-success m-0">
                                <?= number_format($totalAmount ?? 0) ?> <span class="fs-6 fw-normal text-muted">ریال</span>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white">
                    <h5 class="fw-bold mb-0">فیلتر رسیدها</h5>
                </div>
                <div class="card-body">
                    <form method="get" action="">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">انبار</label>
                                <select name="warehouse_id" class="form-select">
                                    <option value="0">همه انبارها</option>
                                    <?php foreach (($warehouses ?? []) as $warehouse): ?>
                                        <option value="<?= (int)$warehouse['id'] ?>" <?= ((int)$warehouse_id === (int)$warehouse['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($warehouse['warehouse_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">وضعیت</label>
                                <select name="status" class="form-select">
                                    <option value="">همه وضعیت‌ها</option>
                                    <option value="draft" <?= ($status === 'draft') ? 'selected' : '' ?>>پیش‌نویس</option>
                                    <option value="approved" <?= ($status === 'approved') ? 'selected' : '' ?>>تایید شده</option>
                                    <option value="cancelled" <?= ($status === 'cancelled') ? 'selected' : '' ?>>لغو شده</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">از تاریخ</label>
                                <input type="date" name="date_from" class="form-control" value="<?= htmlspecialchars($date_from ?? '') ?>">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">تا تاریخ</label>
                                <input type="date" name="date_to" class="form-control" value="<?= htmlspecialchars($date_to ?? '') ?>">
                            </div>

                            <div class="col-12 d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-search ms-1"></i> جستجو
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                         
                                <tr>
                                    <th>#</th>
                                    <th>شماره رسید</th>
                                    <th>نوع</th>
                                    <th>تاریخ</th>
                                    <th>انبار</th>
                                    <th>تأمین‌کننده</th>
                                    <th>مرجع</th>
                                    <th>وضعیت</th>
                                    <th>مبلغ کل</th>
                                    <th>عملیات</th>
                               
                            </tr>
                        </thead>
                       <tbody>
                                <?php if (!empty($receipts)): ?>
                                    <?php foreach ($receipts as $row): ?>
                                        <tr>
                                            <td><?= (int)$row['id'] ?></td>
                                            <td><?= htmlspecialchars($row['receipt_number'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($row['receipt_type'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($row['receipt_date'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($row['warehouse_name'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($row['supplier_name'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($row['reference_no'] ?? '-') ?></td>
                                            <td>
                                                <?php
                                                $status = $row['status'] ?? 'draft';
                                                $badge = 'secondary';
                                                if ($status === 'draft') $badge = 'warning';
                                                elseif ($status === 'approved') $badge = 'success';
                                                elseif ($status === 'cancelled') $badge = 'dark';
                                                ?>
                                                <span class="badge bg-<?= $badge ?>">
                                                    <?= htmlspecialchars($status) ?>
                                                </span>
                                            </td>
                                            <td><?= number_format((float)($row['total_amount'] ?? 0), 2) ?></td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="view.php?id=<?= (int)$row['id'] ?>" class="btn btn-outline-primary">مشاهده</a>
                                                    <a href="edit.php?id=<?= (int)$row['id'] ?>" class="btn btn-outline-warning">ویرایش</a>
                                                    <a href="print.php?id=<?= (int)$row['id'] ?>" class="btn btn-outline-secondary">چاپ</a>

                                                    <?php if (($row['status'] ?? '') === 'draft'): ?>
                                                        <a href="delete.php?id=<?= (int)$row['id'] ?>"
                                                           class="btn btn-outline-danger"
                                                           onclick="return confirm('آیا از حذف این رسید مطمئن هستید؟')">
                                                            حذف
                                                        </a>
                                                    <?php elseif (($row['status'] ?? '') === 'approved'): ?>
                                                        <a href="cancel.php?id=<?= (int)$row['id'] ?>"
                                                           class="btn btn-outline-dark"
                                                           onclick="return confirm('آیا از لغو این رسید مطمئن هستید؟')">
                                                            لغو
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="10" class="text-center text-muted py-4">
                                            هیچ رسیدی ثبت نشده است.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody> </table>
                </div>
            </div>
        </main>
    </div>
</div>