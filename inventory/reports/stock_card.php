<?php
$rootPath = dirname(__DIR__, 2);
require_once $rootPath . '/includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();
requirePermission('view_inventory');

$page_title = "کارتکس انبار";

// دریافت پارامترها
$item_id = $_GET['item_id'] ?? null;
$warehouse_id = $_GET['warehouse_id'] ?? null;

// اگر آیتم مشخص شده باشد، کارتکس آن را نمایش بده
if ($item_id) {
    $stmt = $pdo->prepare("
        SELECT i.*, p.title as product_name 
        FROM inventory i 
        LEFT JOIN products p ON p.id = i.item_id 
        WHERE i.item_id = ?
    ");
    $stmt->execute([$item_id]);
    $item = $stmt->fetch();
} else {
    $item = null;
}
?>

<?php require_once $rootPath . '/includes/header.php'; ?>

<div class="container mt-4">
    <h4><i class="fas fa-clipboard-list"></i> کارتکس انبار (گردش کالا)</h4>

    <!-- فرم جستجو -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-5">
                    <label>کد کالا / شناسه</label>
                    <input type="text" name="item_id" class="form-control" value="<?= htmlspecialchars($item_id ?? '') ?>" placeholder="کد کالا را وارد کنید">
                </div>
                <div class="col-md-5">
                    <label>انبار</label>
                    <select name="warehouse_id" class="form-select">
                        <option value="">همه انبارها</option>
                        <!-- لیست انبارها را اینجا لود کنید -->
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">نمایش کارتکس</button>
                </div>
            </form>
        </div>
    </div>

    <?php if ($item): ?>
        <div class="card">
            <div class="card-header bg-info text-white">
                <strong>کارتکس کالا:</strong> <?= htmlspecialchars($item['product_name'] ?? $item['item_id']) ?>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <p><strong>موجودی فعلی:</strong> <?= number_format($item['quantity'], 3) ?> <?= $item['unit'] ?></p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>حداقل موجودی:</strong> <?= number_format($item['min_stock'] ?? 0, 3) ?></p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>انبار:</strong> <?= htmlspecialchars($item['warehouse_name'] ?? 'نامشخص') ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- جدول گردش کالا (ورود و خروج) -->
        <div class="mt-4">
            <h5>تاریخچه گردش کالا</h5>
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>تاریخ</th>
                        <th>نوع عملیات</th>
                        <th>تعداد</th>
                        <th>شماره سند</th>
                        <th>توضیحات</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- داده‌های گردش را اینجا لود کنید (از جداول goods_receipts و goods_issues) -->
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            تاریخچه کامل گردش در نسخه بعدی اضافه خواهد شد.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            برای نمایش کارتکس، کد کالا را در بالا جستجو کنید.
        </div>
    <?php endif; ?>
</div>

<?php require_once $rootPath . '/includes/footer.php'; ?>