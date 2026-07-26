<div class="container-fluid py-4">
    <div class="d-flex justify-content-between mb-4">
        <h2 class="fw-bold">🔍 کنترل کیفیت</h2>
        <a href="create.php" class="btn btn-success">ثبت کنترل جدید</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>دستور تولید</th>
                        <th>محصول</th>
                        <th>مرحله</th>
                        <th>نتیجه</th>
                        <th>تاریخ</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($checks as $c): ?>
                    <tr>
                        <td><?= htmlspecialchars($c['order_number'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($c['product_name'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($c['stage']) ?></td>
                        <td>
                            <span class="badge bg-<?= $c['result']=='pass' ? 'success' : 'danger' ?>">
                                <?= $c['result'] == 'pass' ? 'قبول' : 'رد' ?>
                            </span>
                        </td>
                        <td><?= $c['checked_at'] ?></td>
                        <td>
                            <a href="view.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-info">مشاهده</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>