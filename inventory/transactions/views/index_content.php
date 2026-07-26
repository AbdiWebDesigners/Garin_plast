<div class="container-fluid py-4">

<?php if($error): ?>
<div class="alert alert-danger">
    <?= htmlspecialchars($error) ?>
</div>
<?php endif; ?>

<div class="row mb-4">

    <div class="col-lg-3">
        <div class="card shadow-sm border-0">
            <div class="card-body text-center">
                <h6 class="text-muted">تعداد تراکنش</h6>
                <h2><?= number_format($totalTransactions) ?></h2>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card shadow-sm border-0 bg-success text-white">
            <div class="card-body text-center">
                <h6>جمع ورود</h6>
                <h2><?= number_format($totalIn,2) ?></h2>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card shadow-sm border-0 bg-danger text-white">
            <div class="card-body text-center">
                <h6>جمع خروج</h6>
                <h2><?= number_format($totalOut,2) ?></h2>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card shadow-sm border-0 bg-primary text-white">
            <div class="card-body text-center">
                <h6>موجودی فعلی</h6>
                <h2><?= number_format($currentBalance,2) ?></h2>
            </div>
        </div>
    </div>

</div>

<div class="card shadow-sm border-0 mb-4">

    <div class="card-header bg-white">
        <h5 class="mb-0">فیلتر کارتکس</h5>
    </div>

    <div class="card-body">

        <form method="GET">

            <div class="row g-3">

                <div class="col-md-3">

                    <label class="form-label">کالا</label>

                    <<select name="item_id" class="form-select">

    <option value="0">همه کالاها</option>

    <?php foreach($products as $p): ?>

        <option
            value="<?= $p['id'] ?>"
            <?= $item_id==$p['id']?'selected':'' ?>>

            <?= htmlspecialchars($p['title']) ?>

            <?php if(!empty($p['sku'])): ?>

                (<?= htmlspecialchars($p['sku']) ?>)

            <?php endif; ?>

        </option>

    <?php endforeach; ?>

</select>

                </div>

                <div class="col-md-3">

                    <label class="form-label">انبار</label>

                    <select
                        name="warehouse_id"
                        class="form-select">

                        <option value="">همه انبارها</option>

                        <?php foreach($warehouses as $item): ?>

                        <option
                            value="<?= $item['id'] ?>"
                            <?= $warehouse_id==$item['id']?'selected':'' ?>>

                            <?= htmlspecialchars($item['warehouse_name']) ?>

                        </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <div class="col-md-2">

                    <label class="form-label">
                        از تاریخ
                    </label>

                    <input
                        type="date"
                        name="from_date"
                        value="<?= htmlspecialchars($from_date) ?>"
                        class="form-control">

                </div>

                <div class="col-md-2">

                    <label class="form-label">
                        تا تاریخ
                    </label>

                    <input
                        type="date"
                        name="to_date"
                        value="<?= htmlspecialchars($to_date) ?>"
                        class="form-control">

                </div>

                <div class="col-md-2 d-flex align-items-end">

                    <button
                        class="btn btn-success w-100">

                        <i class="fa fa-search"></i>

                        جستجو

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>

<div class="card shadow-sm border-0">

<div class="card-header bg-white">

<h5 class="mb-0">

کارتکس کالا

</h5>

</div>

<div class="table-responsive">

<table class="table table-bordered table-hover table-striped align-middle text-center mb-0">

<thead class="table-dark">

<tr>

<th>#</th>

<th>تاریخ</th>

<th>کالا</th>

<th>کد کالا</th>

<th>انبار</th>

<th>نوع عملیات</th>

<th>ورود</th>

<th>خروج</th>

<th>موجودی</th>

<th>کاربر</th>

<th>توضیحات</th>

</tr>

</thead>

<tbody>
    <?php if(count($transactions)): ?>

<?php
$balance = 0;
$row = 1;

foreach($transactions as $trx):

    $qtyIn  = 0;
    $qtyOut = 0;

    switch($trx['transaction_type']){

        case 'receipt':
        case 'production':
        case 'return_customer':
        case 'adjustment_plus':
            $qtyIn = $trx['quantity'];
            break;

        default:
            $qtyOut = $trx['quantity'];
            break;
    }

    $balance += ($qtyIn - $qtyOut);
?>

<tr>

    <td><?= $row++ ?></td>

    <td>
        <?= date('Y/m/d H:i',strtotime($trx['created_at'])) ?>
    </td>

    <td>
        <?= htmlspecialchars($trx['product_name']) ?>
    </td>

    <td>
        <?= htmlspecialchars($trx['product_code']) ?>
    </td>

    <td>
        <?= htmlspecialchars($trx['warehouse_name']) ?>
    </td>

    <td>

<?php

$typeTitle = [

'receipt'=>'رسید انبار',

'issue'=>'حواله انبار',

'production'=>'تولید',

'transfer'=>'انتقال',

'adjustment_plus'=>'اصلاح مثبت',

'adjustment_minus'=>'اصلاح منفی',

'return_customer'=>'برگشت مشتری',

'return_supplier'=>'برگشت تامین کننده'

];

echo $typeTitle[$trx['transaction_type']] ?? $trx['transaction_type'];

?>

    </td>

    <td class="text-success fw-bold">

        <?= $qtyIn ? number_format($qtyIn,2) : '-' ?>

    </td>

    <td class="text-danger fw-bold">

        <?= $qtyOut ? number_format($qtyOut,2) : '-' ?>

    </td>

    <td class="fw-bold">

        <?= number_format($balance,2) ?>

    </td>

    <td>

        <?= htmlspecialchars($trx['fullname']) ?>

    </td>

    <td>

        <?= htmlspecialchars($trx['description']) ?>

    </td>

</tr>

<?php endforeach; ?>

<?php else: ?>

<tr>

<td colspan="11">

هیچ تراکنشی یافت نشد.

</td>

</tr>

<?php endif; ?>
</tbody>

</table>

</div>

<div class="card-footer bg-white">

    <div class="row">

        <div class="col-md-6">

            <small class="text-muted">

                تعداد تراکنش‌ها :

                <strong>

                    <?= number_format($totalTransactions) ?>

                </strong>

            </small>

        </div>

        <div class="col-md-6 text-end">

            <button
                class="btn btn-outline-secondary btn-sm"
                onclick="window.print()">

                <i class="fa fa-print"></i>

                چاپ

            </button>

        </div>

    </div>

</div>

</div>

</div>

<style>

.table tbody tr:hover{

    background:#f8fbff;

    transition:.2s;

}

.card{

    border-radius:12px;

}

.card-header{

    border-bottom:1px solid #ececec;

}

.table th{

    white-space:nowrap;

}

.table td{

    vertical-align:middle;

}

@media print{

    .btn,
    form,
    .card-header{

        display:none !important;

    }

    body{

        background:#fff !important;

    }

}

</style>
