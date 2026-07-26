<?php if (!empty($error)): ?>
<div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>
<?php if (!empty($success)): ?>
<div class="alert alert-success"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<!-- اضافه شدن استایل min-height در این بخش -->
<div class="card border-0 shadow-sm" style="min-height: 70vh;">
  <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
    <strong>حواله‌های خروج انبار</strong>
    <a href="create.php" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> ثبت حواله خروج جدید</a>
  </div>
  <div class="card-body">
    <form method="get" class="row g-2 mb-3">
      <div class="col-md-4">
        <select name="warehouse_id" class="form-select">
          <option value="">همه انبارها</option>
          <?php foreach ($warehouses as $w): ?>
            <option value="<?= (int)$w['id'] ?>" <?= $warehouse_id === (int)$w['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($w['warehouse_name'], ENT_QUOTES, 'UTF-8') ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-auto"><button class="btn btn-primary">فیلتر</button></div>
    </form>
    <div class="table-responsive">
      <table class="table table-striped table-bordered align-middle">
        <thead>
          <tr>
            <th>#</th>
            <th>شماره حواله</th>
            <th>تاریخ</th>
            <th>انبار</th>
            <th>وضعیت</th>
            <th>تعداد اقلام</th>
            <th>مبلغ کل</th>
            <th>عملیات</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($vouchers)): ?>
            <tr><td colspan="8" class="text-center text-muted">حواله‌ای ثبت نشده است.</td></tr>
          <?php else: foreach ($vouchers as $v): ?>
            <tr>
              <td><?= (int)$v['id'] ?></td>
              <td><?= htmlspecialchars($v['voucher_number'], ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= htmlspecialchars($v['voucher_date'], ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= htmlspecialchars($v['warehouse_name'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= htmlspecialchars($v['status'], ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= (int)$v['items_count'] ?></td>
              <td><?= number_format((float)$v['total_cost']) ?></td>
              <td><a class="btn btn-sm btn-outline-primary" href="view.php?id=<?= (int)$v['id'] ?>">مشاهده</a></td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
