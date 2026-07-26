<?php
/**
 * -------------------------------------------------------
 * Garin ERP
 * Inventory Dashboard
 * inventory/index.php
 * -------------------------------------------------------
 */


requireLogin();
requirePermission('view_inventory');

$page_title = "داشبورد انبار";

/*
|--------------------------------------------------------------------------
| آمار انبار
|--------------------------------------------------------------------------
*/

try {

    // تعداد کالاها
    $totalItems = $pdo->query("
        SELECT COUNT(*)
        FROM inventory
    ")->fetchColumn();

    // جمع موجودی
    $totalQuantity = $pdo->query("
        SELECT IFNULL(SUM(quantity),0)
        FROM inventory
    ")->fetchColumn();

    // کالاهای بحرانی
    $criticalItems = $pdo->query("
        SELECT COUNT(*)
        FROM inventory
        WHERE quantity<=min_stock
    ")->fetchColumn();

    // تعداد انبارها
    $warehouseCount = $pdo->query("
        SELECT COUNT(*)
        FROM warehouses
    ")->fetchColumn();

    // تعداد رسیدها
    $receiptCount = $pdo->query("
        SELECT COUNT(*)
        FROM goods_receipts
    ")->fetchColumn();

    // تعداد حواله‌ها
    $issueCount = $pdo->query("
        SELECT COUNT(*)
        FROM goods_issues
    ")->fetchColumn();

} catch(Exception $e){

    $totalItems = 0;
    $totalQuantity = 0;
    $criticalItems = 0;
    $warehouseCount = 0;
    $receiptCount = 0;
    $issueCount = 0;

}


/*
|--------------------------------------------------------------------------
| کالاهای کم موجودی
|--------------------------------------------------------------------------
*/

try{

$lowStock = $pdo->query("

SELECT *

FROM inventory

WHERE quantity<=min_stock

ORDER BY quantity ASC

LIMIT 10

")->fetchAll();

}catch(Exception $e){

$lowStock=[];

}


/*
|--------------------------------------------------------------------------
| آخرین رسیدها
|--------------------------------------------------------------------------
*/

try{

$lastReceipts=$pdo->query("

SELECT

receipt_number,
receipt_date,
status,
total_amount

FROM goods_receipts

ORDER BY id DESC

LIMIT 10

")->fetchAll();

}catch(Exception $e){

$lastReceipts=[];

}


/*
|--------------------------------------------------------------------------
| آخرین حواله ها
|--------------------------------------------------------------------------
*/

try{

$lastIssues=$pdo->query("

SELECT

issue_number,
issue_date,
status,
total_amount

FROM goods_issues

ORDER BY id DESC

LIMIT 10

")->fetchAll();

}catch(Exception $e){

$lastIssues=[];

}

?>

<div class="container-fluid py-4">

<div class="d-flex justify-content-between align-items-center mb-4">

<div>

<h2 class="fw-bold">

📦 داشبورد انبار

</h2>

<div class="text-muted">

مدیریت موجودی، رسیدها، حواله‌ها و گردش کالا

</div>

</div>

<div>

<a
href="receipts/create.php"
class="btn btn-success me-2">

<i class="fa fa-plus"></i>

ثبت رسید

</a>

<a
href="issues/create.php"
class="btn btn-primary">

<i class="fa fa-arrow-right"></i>

ثبت حواله

</a>

</div>

</div>



<div class="row g-3 mb-4">

<div class="col-lg-2">

<div class="card shadow-sm border-0">

<div class="card-body">

<div class="text-muted small">

کالاها

</div>

<h3 class="fw-bold">

<?= number_format($totalItems) ?>

</h3>

</div>

</div>

</div>



<div class="col-lg-2">

<div class="card shadow-sm border-0">

<div class="card-body">

<div class="text-muted small">

جمع موجودی

</div>



<h3 class="fw-bold text-success">

<?= number_format($totalQuantity) ?>

</h3>

</div>

</div>

</div>



<div class="col-lg-2">

<div class="card shadow-sm border-0">

<div class="card-body">

<div class="text-muted small">

کمبود موجودی

</div>

<h3 class="fw-bold text-danger">

<?= number_format($criticalItems) ?>

</h3>

</div>

</div>

</div>



<div class="col-lg-2">

<div class="card shadow-sm border-0">

<div class="card-body">

<div class="text-muted small">

انبارها

</div>

<h3 class="fw-bold">

<?= number_format($warehouseCount) ?>

</h3>

</div>

</div>

</div>



<div class="col-lg-2">

<div class="card shadow-sm border-0">

<div class="card-body">

<div class="text-muted small">

رسیدها

</div>

<h3 class="fw-bold text-primary">

<?= number_format($receiptCount) ?>

</h3>

</div>

</div>

</div>



<div class="col-lg-2">

<div class="card shadow-sm border-0">

<div class="card-body">

<div class="text-muted small">

حواله‌ها

</div>

<h3 class="fw-bold text-warning">

<?= number_format($issueCount) ?>

</h3>

</div>

</div>

</div>

</div>


CASE
WHEN transaction_type IN ('purchase','production')
THEN quantity

WHEN transaction_type IN ('sale','consume','issue')
THEN -quantity
END


<!-- =======================================================
    Quick Access
======================================================= -->

<div class="row mb-4">

    <div class="col-md-3 mb-3">

        <a href="receipts/index.php" class="text-decoration-none">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body text-center">

                    <i class="fa fa-truck-loading fa-3x text-success mb-3"></i>

                    <h5 class="fw-bold">
                        رسیدهای انبار
                    </h5>

                    <div class="text-muted small">
                        ثبت و مدیریت ورود کالا
                    </div>

                </div>

            </div>

        </a>

    </div>

    <div class="col-md-3 mb-3">

        <a href="issues/index.php" class="text-decoration-none">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body text-center">

                    <i class="fa fa-dolly fa-3x text-primary mb-3"></i>

                    <h5 class="fw-bold">

                        حواله انبار

                    </h5>

                    <div class="text-muted small">

                        خروج کالا از انبار

                    </div>

                </div>

            </div>

        </a>

    </div>

    <div class="col-md-3 mb-3">

        <a href="products/index.php" class="text-decoration-none">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body text-center">

                    <i class="fa fa-box fa-3x text-warning mb-3"></i>

                    <h5 class="fw-bold">

                        محصولات

                    </h5>

                    <div class="text-muted small">

                        مدیریت محصولات

                    </div>

                </div>

            </div>

        </a>

    </div>

    <div class="col-md-3 mb-3">

        <a href="reports/index.php" class="text-decoration-none">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body text-center">

                    <i class="fa fa-chart-column fa-3x text-danger mb-3"></i>

                    <h5 class="fw-bold">

                        گزارشات

                    </h5>

                    <div class="text-muted small">

                        گزارش‌های انبار

                    </div>

                </div>

            </div>

        </a>

    </div>

</div>


<!-- =======================================================
        Low Stock
======================================================= -->

<div class="card shadow-sm border-0 mb-4">

    <div class="card-header bg-white">

        <div class="d-flex justify-content-between align-items-center">

            <h5 class="fw-bold mb-0">

                کالاهای کم موجودی

            </h5>

            <span class="badge bg-danger">

                <?= count($lowStock) ?>

                کالا

            </span>

        </div>

    </div>

    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">

                    <tr>

                        <th width="70">

                            کد

                        </th>

                        <th>

                            شناسه کالا

                        </th>

                        <th>

                            نوع کالا

                        </th>

                        <th>

                            موجودی

                        </th>

                        <th>

                            حداقل

                        </th>

                        <th>

                            واحد

                        </th>

                        <th width="140">

                            وضعیت

                        </th>

                        <th width="120">

                            عملیات

                        </th>

                    </tr>

                </thead>

                <tbody>

                <?php if(empty($lowStock)): ?>

                    <tr>

                        <td colspan="8"
                            class="text-center py-5 text-muted">

                            هیچ کالایی کم موجودی نیست.

                        </td>

                    </tr>

                <?php else: ?>

                    <?php foreach($lowStock as $item): ?>

                        <tr>

                            <td>

                                <?= $item['id'] ?>

                            </td>

                            <td>

                                <?= htmlspecialchars($item['item_id']) ?>

                            </td>

                            <td>

                                <?php

                                switch($item['item_type']){

                                    case 'raw_material':

                                        echo '<span class="badge bg-secondary">مواد اولیه</span>';

                                    break;

                                    case 'semi_product':

                                        echo '<span class="badge bg-info">نیمه ساخته</span>';

                                    break;

                                    case 'finished_product':

                                        echo '<span class="badge bg-success">محصول</span>';

                                    break;

                                    default:

                                        echo '-';

                                }

                                ?>

                            </td>

                            <td class="fw-bold text-danger">

                                <?= number_format($item['quantity'],3) ?>

                            </td>

                            <td>

                                <?= number_format($item['min_stock'],3) ?>

                            </td>

                            <td>

                                <?= htmlspecialchars($item['unit']) ?>

                            </td>

                            <td>

                                <span class="badge bg-danger">

                                    نیاز به تامین

                                </span>

                            </td>

                            <td>

                                <a href="products/edit.php?id=<?= $item['item_id'] ?>"
                                   class="btn btn-sm btn-outline-primary">

                                    <i class="fa fa-edit"></i>

                                </a>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<!-- =======================================================
    آخرین رسیدها و حواله‌ها
======================================================= -->

<div class="row">

    <!-- آخرین رسیدها -->

    <div class="col-lg-6 mb-4">

        <div class="card shadow-sm border-0 h-100">

            <div class="card-header bg-success text-white">

                <div class="d-flex justify-content-between align-items-center">

                    <h5 class="mb-0">

                        آخرین رسیدهای انبار

                    </h5>

                    <a href="receipts/index_content.php"
                       class="btn btn-sm btn-light">

                        مشاهده همه

                    </a>

                </div>

            </div>

            <div class="table-responsive">

                <table class="table table-hover mb-0 align-middle">

                    <thead class="table-light">

                        <tr>

                            <th>شماره</th>

                            <th>تاریخ</th>

                            <th>مبلغ</th>

                            <th>وضعیت</th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php if(empty($lastReceipts)): ?>

                        <tr>

                            <td colspan="4"
                                class="text-center py-4 text-muted">

                                اطلاعاتی وجود ندارد.

                            </td>

                        </tr>

                    <?php else: ?>

                        <?php foreach($lastReceipts as $r): ?>

                        <tr>

                            <td>

                                <?= htmlspecialchars($r['receipt_number']) ?>

                            </td>

                            <td>

                                <?= htmlspecialchars($r['receipt_date']) ?>

                            </td>

                            <td class="text-success fw-bold">

                                <?= number_format($r['total_amount']) ?>

                            </td>

                            <td>

                                <?php

                                switch($r['status']){

                                    case 'approved':

                                        echo '<span class="badge bg-success">تایید شده</span>';

                                    break;

                                    case 'draft':

                                        echo '<span class="badge bg-warning text-dark">پیش نویس</span>';

                                    break;

                                    case 'cancelled':

                                        echo '<span class="badge bg-danger">لغو</span>';

                                    break;

                                    default:

                                        echo '<span class="badge bg-secondary">'.$r['status'].'</span>';

                                }

                                ?>

                            </td>

                        </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    <!-- آخرین حواله ها -->

    <div class="col-lg-6 mb-4">

        <div class="card shadow-sm border-0 h-100">

            <div class="card-header bg-primary text-white">

                <div class="d-flex justify-content-between align-items-center">

                    <h5 class="mb-0">

                        آخرین حواله‌های انبار

                    </h5>

                    <a href="issues/index.php"
                       class="btn btn-sm btn-light">

                        مشاهده همه

                    </a>

                </div>

            </div>

            <div class="table-responsive">

                <table class="table table-hover mb-0 align-middle">

                    <thead class="table-light">

                        <tr>

                            <th>شماره</th>

                            <th>تاریخ</th>

                            <th>مبلغ</th>

                            <th>وضعیت</th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php if(empty($lastIssues)): ?>

                        <tr>

                            <td colspan="4"
                                class="text-center py-4 text-muted">

                                اطلاعاتی وجود ندارد.

                            </td>

                        </tr>

                    <?php else: ?>

                        <?php foreach($lastIssues as $r): ?>

                        <tr>

                            <td>

                                <?= htmlspecialchars($r['issue_number']) ?>

                            </td>

                            <td>

                                <?= htmlspecialchars($r['issue_date']) ?>

                            </td>

                            <td class="text-danger fw-bold">

                                <?= number_format($r['total_amount']) ?>

                            </td>

                            <td>

                                <?php

                                switch($r['status']){

                                    case 'approved':

                                        echo '<span class="badge bg-success">تایید شده</span>';

                                    break;

                                    case 'draft':

                                        echo '<span class="badge bg-warning text-dark">پیش نویس</span>';

                                    break;

                                    case 'cancelled':

                                        echo '<span class="badge bg-danger">لغو</span>';

                                    break;

                                    default:

                                        echo '<span class="badge bg-secondary">'.$r['status'].'</span>';

                                }

                                ?>

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



<style>

.card{

    border-radius:14px;

}

.card-header{

    font-weight:bold;

}

.table thead th{

    white-space:nowrap;

}

.table tbody tr:hover{

    background:#f8fbff;

}

.badge{

    font-size:12px;

    padding:6px 10px;

}

.btn{

    border-radius:8px;

}

</style>

<?php require_once __DIR__.'/../includes/footer.php'; ?>
