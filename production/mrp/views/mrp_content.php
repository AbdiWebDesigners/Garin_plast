<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">📋 برنامه‌ریزی مواد (MRP)</h2>
            <small class="text-muted">محاسبه مواد اولیه مورد نیاز برای دستورات تولید</small>
        </div>
        <a href="../production_orders/create.php" class="btn btn-success">
            <i class="fa fa-plus"></i> دستور تولید جدید
        </a>
    </div>

    <div class="card shadow-sm">
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
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($orders)): ?>
                            <tr><td colspan="6" class="text-center py-4 text-muted">دستور فعالی وجود ندارد.</td></tr>
                        <?php else: ?>
                            <?php foreach($orders as $o): ?>
                            <tr>
                                <td><?= htmlspecialchars($o['order_number']) ?></td>
                                <td><?= htmlspecialchars($o['product_name'] ?? '-') ?></td>
                                <td><?= number_format($o['quantity'], 2) ?></td>
                                <td><?= htmlspecialchars($o['unit'] ?? 'عدد') ?></td>
                                <td>
                                    <span class="badge bg-<?= $o['status']=='in_progress' ? 'warning' : 'info' ?>">
                                        <?= $o['status'] ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="bom.php?order_id=<?= $o['id'] ?>" class="btn btn-sm btn-success">محاسبه مواد</a>
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