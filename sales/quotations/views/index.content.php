<?php
// sales/quotations/views/index.content.php
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>پیش‌فاکتورها</h3>
        <a href="create.php" class="btn btn-primary">+ پیش‌فاکتور جدید</a>
    </div>

    <!-- جدول پیش‌فاکتورها -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>شماره پیش‌فاکتور</th>
                            <th>مشتری</th>
                            <th>مبلغ</th>
                            <th>وضعیت</th>
                            <th>تاریخ</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($quotations as $q): ?>
                        <tr>
                            <td><?= $q['id'] ?></td>
                            <td><?= htmlspecialchars($q['quotation_number'] ?? 'QUO-'.$q['id']) ?></td>
                            <td><?= htmlspecialchars($q['company_name'] ?? '-') ?></td>
                            <td><?= number_format($q['total_price'] ?? 0) ?> تومان</td>
                            <td>
    <?php 
    $status = $q['status'] ?? 'draft';
    if ($status === 'accepted') echo '<span class="badge bg-success">تأیید شده</span>';
    elseif ($status === 'sent') echo '<span class="badge bg-info">ارسال شده</span>';
    elseif ($status === 'rejected') echo '<span class="badge bg-danger">رد شده</span>';
    else echo '<span class="badge bg-secondary">پیش‌نویس</span>';
    ?>
</td>                            <td><?= $q['created_at'] ?? '-' ?></td>
                            <td>
                            <td>
    <a href="view.php?id=<?= (int)$q['id'] ?>" class="btn btn-sm btn-info mb-1">مشاهده</a>
    <a href="edit.php?id=<?= (int)$q['id'] ?>" class="btn btn-sm btn-warning mb-1">ویرایش</a>
    
    <?php if (isset($q['status']) && $q['status'] === 'accepted'): ?>
        <a href="convert_to_invoice.php?id=<?= (int)$q['id'] ?>" 
           class="btn btn-sm btn-success mb-1"
           onclick="return confirm('این پیش‌فاکتور به فاکتور تبدیل شود؟')">
            <i class="fas fa-file-invoice"></i> تبدیل به فاکتور
        </a>
    <?php endif; ?>
</td>                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>