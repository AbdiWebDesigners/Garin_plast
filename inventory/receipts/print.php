<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireLogin();
requirePermission('view_inventory');

global $pdo;

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    die('شناسه رسید نامعتبر است.');
}

try {
    $stmt = $pdo->prepare("
        SELECT
            gr.*,
            w.warehouse_name,
            s.company_name AS supplier_name,
            u.fullname AS created_by_name,
            a.fullname AS approved_by_name
        FROM goods_receipts gr
        LEFT JOIN warehouses w ON w.id = gr.warehouse_id
        LEFT JOIN suppliers s ON s.id = gr.supplier_id
        LEFT JOIN users u ON u.id = gr.created_by
        LEFT JOIN users a ON a.id = gr.approved_by
        WHERE gr.id = ?
        LIMIT 1
    ");
    $stmt->execute([$id]);
    $receipt = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$receipt) {
        die('رسید یافت نشد.');
    }

    $itemStmt = $pdo->prepare("
        SELECT
            gri.*,
            p.title AS product_name,
            p.sku AS product_sku
        FROM goods_receipt_items gri
        LEFT JOIN products p ON p.id = gri.item_id
        WHERE gri.receipt_id = ?
        ORDER BY gri.line_no ASC, gri.id ASC
    ");
    $itemStmt->execute([$id]);
    $items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    die('خطا در دریافت اطلاعات رسید: ' . $e->getMessage());
}
?>
<!doctype html>
<html lang="fa" dir="rtl">
    
    <div class="text-center mb-3">
    <img src="../../attachments/logo.png" alt="لوگوی شرکت" style="max-height:90px; width:auto; display:block; margin:0 auto 12px;">
    <h3 class="fw-bold mb-1">رسید انبار</h3>
    <div>شماره رسید: <?= htmlspecialchars($receipt['receipt_number'] ?? '') ?></div>
</div>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>چاپ رسید انبار</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <style>
        body { background: #fff; color: #000; }
        .print-box { max-width: 1100px; margin: 0 auto; padding: 24px; }
        .table th, .table td { vertical-align: middle; }
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; }
            .print-box { padding: 0; max-width: 100%; }
        }
    </style>
</head>
<body>
<div class="print-box">
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <button class="btn btn-secondary" onclick="window.print()">چاپ</button>
        <a href="view.php?id=<?= (int)$receipt['id'] ?>" class="btn btn-outline-secondary">بازگشت</a>
    </div>

    <div class="text-center mb-4">
        <h3 class="fw-bold mb-1">رسید انبار</h3>
        <div>شماره رسید: <?= htmlspecialchars($receipt['receipt_number'] ?? '') ?></div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3"><strong>نوع رسید:</strong> <?= htmlspecialchars($receipt['receipt_type'] ?? '') ?></div>
        <div class="col-md-3"><strong>تاریخ:</strong> <?= htmlspecialchars($receipt['receipt_date'] ?? '') ?></div>
        <div class="col-md-3"><strong>انبار:</strong> <?= htmlspecialchars($receipt['warehouse_name'] ?? '') ?></div>
        <div class="col-md-3"><strong>تأمین‌کننده:</strong> <?= htmlspecialchars($receipt['supplier_name'] ?? '-') ?></div>
        <div class="col-md-3"><strong>شماره مرجع:</strong> <?= htmlspecialchars($receipt['reference_no'] ?? '-') ?></div>
        <div class="col-md-3"><strong>وضعیت:</strong> <?= htmlspecialchars($receipt['status'] ?? '') ?></div>
        <div class="col-md-3"><strong>ثبت‌کننده:</strong> <?= htmlspecialchars($receipt['created_by_name'] ?? '-') ?></div>
        <div class="col-md-3"><strong>تأییدکننده:</strong> <?= htmlspecialchars($receipt['approved_by_name'] ?? '-') ?></div>
    </div>

    <div class="mb-4">
        <strong>توضیحات:</strong>
        <div><?= nl2br(htmlspecialchars($receipt['description'] ?? '-')) ?></div>
    </div>

    <table class="table table-bordered">
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
                    <td colspan="11" class="text-center">آیتمی برای نمایش وجود ندارد.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>