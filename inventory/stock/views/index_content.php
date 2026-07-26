<?php
// فرض: $stocks, $warehouses, $warehouse_id, $error, $success, $totalItems, $totalStock, $totalValue موجود هستند.
?>

<div class="container-fluid stock-page" dir="rtl">
    <div class="row g-0">

        <?php include __DIR__ . '/../../includes/sidebar.php'; ?>

        <main class="col-12 col-md-10 py-4 px-3 px-lg-4 stock-content">

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= htmlspecialchars($success) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold mb-1">موجودی انبار</h3>
                    <div class="text-muted">موجودی لحظه‌ای کالاها در انبارها</div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-lg-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <div class="text-muted small mb-1">تعداد کالاها</div>
                            <h3 class="fw-bold m-0"><?= number_format($totalItems ?? 0) ?></h3>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <div class="text-muted small mb-1">مجموع موجودی</div>
                            <h3 class="fw-bold text-primary m-0"><?= number_format($totalStock ?? 0, 3) ?></h3>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <div class="text-muted small mb-1">ارزش موجودی</div>
                            <h3 class="fw-bold text-success m-0">
                                <?= number_format($totalValue ?? 0) ?> <span class="fs-6 fw-normal text-muted">ریال</span>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white">
                    <h5 class="fw-bold mb-0">فیلتر موجودی</h5>
                </div>
                <div class="card-body">
                    <form method="get" action="">
                        <div class="row g-3">
                            <div class="col-md-10">
                                <label class="form-label">انتخاب انبار</label>
                                <select name="warehouse_id" class="form-select">
                                    <option value="0">همه انبارها</option>
                                    <?php if (!empty($warehouses)): ?>
                                        <?php foreach ($warehouses as $warehouse): ?>
                                            <option value="<?= (int)$warehouse['id'] ?>" <?= ((int)$warehouse_id === (int)$warehouse['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($warehouse['warehouse_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fa fa-search ms-1"></i> جستجو
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white">
                    <h5 class="fw-bold mb-0">موجودی کالاها</h5>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 stock-table">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>نام کالا</th>
                                <th>کد کالا</th>
                                <th>موجودی</th>
                                <th>قیمت واحد</th>
                                <th>ارزش موجودی</th>
                                <th>وضعیت</th>
                                <th class="text-center">کارتکس</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($stocks)): ?>
                                <?php $i = 1; foreach ($stocks as $stock): 
                                    $qty = (float)($stock['stock'] ?? 0);
                                    $price = (float)($stock['price'] ?? 0);
                                    $rowValue = $qty * $price;

                                    if ($qty > 0) {
                                        $statusLabel = 'موجود';
                                        $badgeClass = 'bg-success';
                                        $stockClass = 'stock-positive';
                                    } elseif ($qty == 0) {
                                        $statusLabel = 'ناموجود';
                                        $badgeClass = 'bg-warning text-dark';
                                        $stockClass = 'stock-zero';
                                    } else {
                                        $statusLabel = 'منفی';
                                        $badgeClass = 'bg-danger';
                                        $stockClass = 'stock-negative';
                                    }
                                ?>
                                    <tr>
                                        <td><?= $i++ ?></td>
                                        <td class="fw-bold"><?= htmlspecialchars($stock['title'] ?? '') ?></td>
                                        <td><code class="text-dark"><?= htmlspecialchars($stock['sku'] ?? '') ?></code></td>
                                        <td class="<?= $stockClass ?>"><?= number_format($qty, 3) ?></td>
                                        <td><?= number_format($price) ?> ریال</td>
                                        <td class="value-cell"><?= number_format($rowValue) ?> ریال</td>
                                        <td>
                                            <span class="badge <?= $badgeClass ?>">
                                                <?= $statusLabel ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="../transactions/index.php?item_id=<?= (int)($stock['id'] ?? 0) ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fa fa-history"></i> مشاهده کارتکس
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">هیچ کالایی در این انبار یافت نشد.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="card-footer bg-white">
                    <div class="row align-items-center">
                        <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">
                            <small class="text-muted">
                                تعداد ردیف‌ها: <strong><?= !empty($stocks) ? count($stocks) : 0 ?></strong>
                            </small>
                        </div>
                        <div class="col-md-6 text-center text-md-end">
                            <a href="print.php<?= !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '' ?>" class="btn btn-outline-secondary btn-sm">
                                <i class="fa fa-print ms-1"></i> چاپ
                            </a>
                            <a href="export_excel.php<?= !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '' ?>" class="btn btn-outline-success btn-sm ms-1">
                                <i class="fa fa-file-excel ms-1"></i> خروجی Excel
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>

<script>
setTimeout(function () {
    if (typeof bootstrap === "undefined" || !bootstrap.Alert) return;
    document.querySelectorAll(".stock-page .alert").forEach(function (item) {
        const alertInstance = bootstrap.Alert.getOrCreateInstance(item);
        alertInstance.close();
    });
}, 4000);
</script>

<style>
.stock-page .card { border-radius: 12px; }
.stock-page .card-header { border-bottom: 1px solid #ececec; }
.stock-page .table-responsive { overflow-x: auto; }
.stock-page .stock-table {
    display: table !important;
    width: 100% !important;
    min-width: 900px;
    table-layout: auto;
    white-space: nowrap;
}
.stock-page .stock-table thead { display: table-header-group !important; }
.stock-page .stock-table tbody { display: table-row-group !important; }
.stock-page .stock-table tr { display: table-row !important; }
.stock-page .stock-table th,
.stock-page .stock-table td {
    display: table-cell !important;
    width: auto !important;
    min-width: 0 !important;
    float: none !important;
    position: static !important;
    vertical-align: middle;
}
.stock-page .stock-table tbody tr:hover {
    background: #f8fbff;
    transition: background-color .2s;
}
.stock-page .badge { padding: 6px 10px; font-size: 12px; }
.stock-page .stock-positive { color: #198754; font-weight: bold; }
.stock-page .stock-zero { color: #b58100; font-weight: bold; }
.stock-page .stock-negative { color: #dc3545; font-weight: bold; }
.stock-page .value-cell { color: #0d6efd; font-weight: bold; }
.stock-page .stock-content {
    min-width: 0;
    overflow-x: hidden;
}
@media (max-width: 767.98px) {
    .stock-page .stock-content { width: 100%; }
}
</style>