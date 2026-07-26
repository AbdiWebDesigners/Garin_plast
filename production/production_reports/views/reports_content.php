<div class="container-fluid py-4">
    <h2 class="fw-bold mb-4">📊 گزارشات تولید</h2>

    <!-- کارت‌های آماری -->
    <div class="row g-3 mb-5">
        <div class="col-lg-3 col-md-6">
            <div class="card bg-primary text-white shadow-sm">
                <div class="card-body">
                    <h5>کل دستورات</h5>
                    <h2><?= number_format($totalOrders) ?></h2>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card bg-success text-white shadow-sm">
                <div class="card-body">
                    <h5>تکمیل شده</h5>
                    <h2><?= number_format($completedOrders) ?></h2>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card bg-warning text-white shadow-sm">
                <div class="card-body">
                    <h5>در حال انجام</h5>
                    <h2><?= number_format($inProgress) ?></h2>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card bg-info text-white shadow-sm">
                <div class="card-body">
                    <h5>کل تولید</h5>
                    <h2><?= number_format($totalKg ?? 0) ?> کیلو</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- آخرین دستورات -->
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">آخرین دستورات تولید</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>شماره دستور</th>
                            <th>محصول</th>
                            <th>تعداد</th>
                            <th>واحد</th>
                            <th>وضعیت</th>
                            <th>تاریخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentOrders as $o): ?>
                        <tr>
                            <td><?= htmlspecialchars($o['order_number']) ?></td>
                            <td><?= htmlspecialchars($o['product_name'] ?? '-') ?></td>
                            <td><?= number_format($o['quantity'], 2) ?></td>
                            <td><?= htmlspecialchars($o['unit'] ?? 'عدد') ?></td>
                            <td>
                                <span class="badge bg-<?= $o['status']=='completed' ? 'success' : 'warning' ?>">
                                    <?= $o['status'] ?>
                                </span>
                            </td>
                            <td><?= $o['created_at'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>