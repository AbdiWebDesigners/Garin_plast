<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">🏭 خطوط تولید</h2>
        </div>
        <a href="create.php" class="btn btn-primary">
            <i class="fa fa-plus"></i> خط جدید
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>نام خط</th>
                            <th>ظرفیت ساعتی</th>
                            <th>وضعیت</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lines as $line): ?>
                        <tr>
                            <td><?= htmlspecialchars($line['name']) ?></td>
                            <td><?= number_format($line['capacity_per_hour']) ?> واحد/ساعت</td>
                            <td>
                                <span class="badge bg-<?= $line['status']=='active' ? 'success' : 'secondary' ?>">
                                    <?= $line['status'] == 'active' ? 'فعال' : 'غیرفعال' ?>
                                </span>
                            </td>
                            <td>
                                <a href="edit.php?id=<?= $line['id'] ?>" class="btn btn-sm btn-warning">ویرایش</a>
                                <a href="delete.php?id=<?= $line['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('مطمئن هستید؟')">حذف</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>