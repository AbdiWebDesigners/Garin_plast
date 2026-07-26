<div class="container-fluid" dir="rtl">
    <div class="row g-0">
        <?php include __DIR__ . '/../../includes/sidebar.php'; ?>

        <main class="col-12 col-md-10 py-4 px-3 px-lg-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold mb-1">جزئیات رسید انبار</h3>
                    <div class="text-muted">شماره رسید: <?= htmlspecialchars($receipt['receipt_number'] ?? '') ?></div>
                </div>
                <div class="d-flex gap-2">
                    <a href="edit.php?id=<?= (int)$receipt['id'] ?>" class="btn btn-warning">ویرایش</a>
                    <a href="print.php?id=<?= (int)$receipt['id'] ?>" class="btn btn-secondary">چاپ</a>
                    <a href="index.php" class="btn btn-outline-secondary">بازگشت</a>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3"><strong>شماره رسید:</strong> <?= htmlspecialchars($receipt['receipt_number'] ?? '') ?></div>
                        <div class="col-md-3"><strong>نوع رسید:</strong> <?= htmlspecialchars($receipt['receipt_type'] ?? '') ?></div>
                        <div class="col-md-3"><strong>تاریخ:</strong> <?= htmlspecialchars($receipt['receipt_date'] ?? '') ?></div>
                        <div class="col-md-3"><strong>وضعیت:</strong> <?= htmlspecialchars($receipt['status'] ?? '') ?></div>
                        <div class="col-md-3"><strong>انبار:</strong> <?= htmlspecialchars($receipt['warehouse_name'] ?? '') ?></div>
                        <div class="col-md-3"><strong>تأمین‌کننده:</strong> <?= htmlspecialchars($receipt['supplier_name'] ?? '-') ?></div>
                        <div class="col-md-3"><strong>شماره مرجع:</strong> <?= htmlspecialchars($receipt['reference_no'] ?? '-') ?></div>
                        <div class="col-md-3"><strong>ثبت‌کننده:</strong> <?= htmlspecialchars($receipt['created_by_name'] ?? '-') ?></div>
                    </div>
                    <div class="mt-3">
                        <strong>توضیحات:</strong>
                        <div class="text-muted"><?= nl2br(htmlspecialchars($receipt['description'] ?? '-')) ?></div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h5 class="fw-bold mb-0">آیتم‌های رسید</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>کالا</th>
                                <th>کد کالا</th>
                                <th>تعداد</th>
                                <th>واحد</th>
                                <th>قیمت واحد</th>
                                <th>تخفیف</th>
                                <th>مالیات</th>
                                <th>شماره سری</th>
                                <th>انقضا</th>
                                <th>مبلغ کل</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($items)): ?>
                                <?php foreach ($items as $row): ?>
                                    <tr>
                                        <td><?= (int)$row['line_no'] ?></td>
                                        <td><?= htmlspecialchars($row['product_name'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($row['product_sku'] ?? '-') ?></td>
                                        <td><?= number_format((float)$row['quantity'], 3) ?></td>
                                        <td><?= htmlspecialchars($row['unit'] ?? '') ?></td>
                                        <td><?= number_format((float)$row['unit_price'], 2) ?></td>
                                        <td><?= number_format((float)$row['discount'], 2) ?></td>
                                        <td><?= number_format((float)$row['tax'], 2) ?></td>
                                        <td><?= htmlspecialchars($row['serial_number'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($row['expire_date'] ?? '-') ?></td>
                                        <td><?= number_format((float)$row['total_price'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="11" class="text-center text-muted py-4">آیتمی برای نمایش وجود ندارد.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>