<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">📋 دستورات تولید</h2>
        </div>
        <a href="create.php" class="btn btn-success">
            <i class="fa fa-plus"></i> دستور جدید
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
                            <th>تاریخ</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $o): ?>
                        <tr>
                            <td><?= htmlspecialchars($o['order_number']) ?></td>
                            <td><?= htmlspecialchars($o['product_name'] ?? '-') ?></td>
                            <td><?= number_format($o['quantity'], 2) ?></td>
                            <td><?= htmlspecialchars($o['unit'] ?? 'عدد') ?></td>
                            <td>
                                <span class="badge bg-<?= $o['status']=='completed' ? 'success' : ($o['status']=='in_progress' ? 'warning' : 'info') ?>">
                                    <?= $o['status'] ?>
                                </span>
                            </td>
                            <td><?= $o['created_at'] ?></td>
                            <td>
                                <a href="view.php?id=<?= $o['id'] ?>" class="btn btn-sm btn-info">مشاهده</a>
                                <a href="edit.php?id=<?= $o['id'] ?>" class="btn btn-sm btn-warning">ویرایش</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>