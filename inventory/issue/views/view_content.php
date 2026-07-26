<?php if ($error !== ''): ?><div class="alert alert-danger"><?= issueEscape($error) ?></div><?php endif; ?>
<?php if ($success !== ''): ?><div class="alert alert-success"><?= issueEscape($success) ?></div><?php endif; ?>
<?php if ($voucher): ?>
<div class="card border-0 shadow-sm">
  <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
    <strong>حواله <?= issueEscape($voucher['voucher_number']) ?></strong>
    <div class="d-flex gap-2 flex-wrap">
      <a href="index.php" class="btn btn-sm btn-secondary">بازگشت</a>
      <a href="edit.php?id=<?= (int)$voucher['id'] ?>" class="btn btn-sm btn-warning"><i class="fa fa-pen"></i> ویرایش</a>
      <a href="print.php?id=<?= (int)$voucher['id'] ?>" target="_blank" class="btn btn-sm btn-info"><i class="fa fa-print"></i> پرینت</a>
      <form method="post" action="delete.php" class="d-inline" onsubmit="return confirm('این حواله و گردش‌های مرتبط حذف شوند؟');">
        <input type="hidden" name="id" value="<?= (int)$voucher['id'] ?>">
        <input type="hidden" name="csrf_token" value="<?= issueEscape(issueCsrfToken()) ?>">
        <button class="btn btn-sm btn-danger"><i class="fa fa-trash"></i> حذف</button>
      </form>
    </div>
  </div>
  <div class="card-body">
    <div class="row g-3 mb-4">
      <div class="col-md-3"><small class="text-muted">شماره حواله</small><div class="fw-bold"><?= issueEscape($voucher['voucher_number']) ?></div></div>
      <div class="col-md-3"><small class="text-muted">تاریخ</small><div><?= issueEscape($voucher['voucher_date']) ?></div></div>
      <div class="col-md-3"><small class="text-muted">انبار</small><div><?= issueEscape($voucher['warehouse_name'] ?? '-') ?></div></div>
      <div class="col-md-3"><small class="text-muted">وضعیت</small><div><?= issueEscape(issueStatusLabel((string)$voucher['status'])) ?></div></div>
      <div class="col-md-3"><small class="text-muted">درخواست‌کننده</small><div><?= issueEscape($voucher['requested_by'] ?? '-') ?></div></div>
      <div class="col-md-3"><small class="text-muted">تأییدکننده</small><div><?= issueEscape($voucher['approved_by'] ?? '-') ?></div></div>
      <div class="col-md-6"><small class="text-muted">توضیحات</small><div><?= nl2br(issueEscape($voucher['description'] ?? '-')) ?></div></div>
    </div>
    <div class="table-responsive"><table class="table table-bordered align-middle">
      <thead class="table-light"><tr><th>#</th><th>کالا</th><th>تعداد</th><th>واحد</th><th>قیمت واحد</th><th>مبلغ</th><th>سریال</th><th>Batch</th><th>انقضا</th><th>لوکیشن</th><th>توضیح</th></tr></thead>
      <tbody>
      <?php $grand = 0.0; foreach ($voucher['items'] as $item): $grand += (float)$item['total_cost']; ?>
        <tr><td><?= (int)$item['line_no'] ?></td><td><?= issueEscape($item['product_title']) ?><?= !empty($item['sku']) ? ' (' . issueEscape($item['sku']) . ')' : '' ?></td><td><?= issueEscape($item['quantity']) ?></td><td><?= issueEscape($item['unit']) ?></td><td><?= number_format((float)$item['unit_cost']) ?></td><td><?= number_format((float)$item['total_cost']) ?></td><td><?= issueEscape($item['serial_number'] ?? '-') ?></td><td><?= issueEscape($item['batch_number'] ?? '-') ?></td><td><?= issueEscape($item['expire_date'] ?? '-') ?></td><td><?= issueEscape($item['warehouse_location'] ?? '-') ?></td><td><?= issueEscape($item['description'] ?? '-') ?></td></tr>
      <?php endforeach; ?>
      <?php if (empty($voucher['items'])): ?><tr><td colspan="11" class="text-center text-muted">بدون آیتم</td></tr><?php endif; ?>
      </tbody><tfoot><tr class="table-light"><th colspan="5">جمع کل</th><th><?= number_format($grand) ?></th><th colspan="5"></th></tr></tfoot>
    </table></div>
  </div>
</div>
<?php endif; ?>
