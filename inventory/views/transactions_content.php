<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="card-title mb-0 fw-bold small"><i class="fa fa-exchange-alt me-2"></i> ثبت رسید / حواله</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger small"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success small"><?= htmlspecialchars($success) ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">انتخاب کالا *</label>
                            <select name="product_id" class="form-select" required>
                                <option value="" disabled selected>انتخاب کنید...</option>
                                <?php foreach ($products as $p): ?>
                                    <option value="<?= $p['id'] ?>">
                                        <?= htmlspecialchars($p['title']) ?> (موجودی: <?= floatval($p['stock']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">نوع عملیات *</label>
                            <select name="transaction_type" class="form-select" required>
                                <option value="in">رسید انبار (ورود کالا +)</option>
                                <option value="out">حواله انبار (خروج کالا -)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">مقدار / تعداد *</label>
                            <input type="number" step="0.001" name="quantity" class="form-control text-start" required min="0.001">
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted">توضیحات (اختیاری)</label>
                            <textarea name="reference_note" class="form-control" rows="2" placeholder="مثلاً: خرید از تامین‌کننده، یا ارسال برای مشتری..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm">ثبت عملیات</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold small"><i class="fa fa-history me-2"></i> کاردکس تراکنش‌های اخیر</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0 text-center align-middle">
                            <thead class="table-light small">
                                <tr>
                                    <th>#</th>
                                    <th>تاریخ و زمان</th>
                                    <th>نام کالا</th>
                                    <th>عملیات</th>
                                    <th>مقدار</th>
                                    <th>توضیحات</th>
                                </tr>
                            </thead>
                            <tbody class="small">
                                <?php if(empty($transactions)): ?>
                                    <tr>
                                        <td colspan="6" class="text-muted py-4">هیچ تراکنشی ثبت نشده است.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($transactions as $index => $tr): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td style="direction: ltr;"><?= $tr['created_at'] ?></td>
                                            <td class="fw-bold"><?= htmlspecialchars($tr['product_title']) ?></td>
                                            <td>
                                                <?php if($tr['transaction_type'] === 'in'): ?>
                                                    <span class="badge bg-success px-2 py-1">رسید (+)</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger px-2 py-1">حواله (-)</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="fw-bold text-<?= $tr['transaction_type'] === 'in' ? 'success' : 'danger' ?>">
                                                <?= floatval($tr['quantity']) ?>
                                            </td>
                                            <td class="text-muted text-start" style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                <?= htmlspecialchars($tr['reference_note'] ?? '-') ?>
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