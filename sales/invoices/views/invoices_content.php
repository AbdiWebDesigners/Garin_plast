<?php
// مسیر پیشنهادی:
// garin/invoices/invoices_content.php

if (!isset($pageTitle)) {
    exit('دسترسی مستقیم به این فایل مجاز نیست.');
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title><?= e($pageTitle) ?></title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css"
        rel="stylesheet"
    >

    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        rel="stylesheet"
    >

    <style>
        body {
            background-color: #f6f7fb;
        }

        .invoice-stat-card {
            border: 0;
            border-radius: 16px;
            overflow: hidden;
        }

        .invoice-stat-card .card-body {
            padding: 20px;
        }

        .invoice-table {
            min-width: 1250px;
        }

        .invoice-table thead th {
            font-weight: 600;
            font-size: 0.85rem;
            border: none;
            padding: 13px 14px;
            white-space: nowrap;
        }

        .invoice-table tbody td {
            padding: 14px;
            vertical-align: middle;
            font-size: 0.9rem;
            border-bottom: 1px solid #f0f0f0;
        }

        .invoice-table tbody tr {
            transition: all 0.2s ease;
        }

        .invoice-table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .btn-icon {
            width: 34px;
            height: 34px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
        }

        .table-card {
            border-radius: 18px;
        }

        .filter-card {
            border-radius: 18px;
        }

        .invoice-number {
            font-size: 0.95rem;
        }

        .commission-box {
            line-height: 1.8;
        }

        .direct-sale-badge {
            font-size: 0.78rem;
        }

        .overdue-date {
            color: #dc3545;
            font-weight: 700;
        }

        .normal-date {
            color: #6c757d;
        }

        .customer-phone,
        .salesperson-contact {
            direction: ltr;
            display: inline-block;
        }

        @media (max-width: 767.98px) {
            .page-header {
                align-items: stretch !important;
            }

            .page-header-actions {
                width: 100%;
            }

            .page-header-actions .btn {
                flex: 1;
            }
        }
    </style>
</head>

<body>
<div class="container-fluid">
    <div class="row">

        <?php include __DIR__ . '/../../admin/sidebar.php'; ?>

        <main class="col-md-10 p-3 p-md-4">

            <div
                class="page-header d-flex flex-column flex-lg-row
                       justify-content-between align-items-lg-center
                       gap-3 mb-4"
            >
                <div>
                    <h3 class="mb-1">
                        لیست فاکتورها
                    </h3>

                    <div class="text-muted">
                        مدیریت فاکتورها، کارشناسان فروش و پرداخت‌ها
                    </div>
                </div>

                <div class="page-header-actions d-flex gap-2">
                    <a
                        href="add.php"
                        class="btn btn-primary"
                    >
                        <i class="fa-solid fa-plus ms-1"></i>
                        ثبت فاکتور جدید
                    </a>

                    <a
                        href="<?= e(BASE_URL) ?>admin/dashboard.php"
                        class="btn btn-outline-secondary"
                    >
                        <i class="fa-solid fa-house ms-1"></i>
                        داشبورد
                    </a>
                </div>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div
                    class="alert alert-success alert-dismissible
                           fade show rounded-3 mb-4"
                    role="alert"
                >
                    <i class="fa-solid fa-circle-check ms-1"></i>

                    فاکتور جدید با موفقیت ثبت شد.

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="بستن"
                    ></button>
                </div>
            <?php endif; ?>

            <?php if ($errorMsg !== ''): ?>
                <div class="alert alert-danger rounded-3 mb-4">
                    <i
                        class="fa-solid fa-triangle-exclamation ms-1"
                    ></i>

                    <?= e($errorMsg) ?>
                </div>
            <?php endif; ?>

            <div class="row g-3 mb-4">

                <div class="col-sm-6 col-xl-3">
                    <div
                        class="card invoice-stat-card text-white
                               bg-primary shadow-sm h-100"
                    >
                        <div class="card-body">
                            <div
                                class="d-flex justify-content-between
                                       align-items-center"
                            >
                                <div>
                                    <h4 class="mb-1">
                                        <?= (int)$invoiceStats['total'] ?>
                                    </h4>

                                    <small>کل فاکتورها</small>
                                </div>

                                <i
                                    class="fa-solid fa-file-invoice
                                           fa-2x opacity-50"
                                ></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div
                        class="card invoice-stat-card text-white
                               bg-danger shadow-sm h-100"
                    >
                        <div class="card-body">
                            <div
                                class="d-flex justify-content-between
                                       align-items-center"
                            >
                                <div>
                                    <h4 class="mb-1">
                                        <?= (int)$invoiceStats['unpaid'] ?>
                                    </h4>

                                    <small>پرداخت‌نشده</small>
                                </div>

                                <i
                                    class="fa-solid fa-circle-xmark
                                           fa-2x opacity-50"
                                ></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div
                        class="card invoice-stat-card text-white
                               bg-success shadow-sm h-100"
                    >
                        <div class="card-body">
                            <div
                                class="d-flex justify-content-between
                                       align-items-center"
                            >
                                <div>
                                    <h4 class="mb-1">
                                        <?= (int)$invoiceStats['paid'] ?>
                                    </h4>

                                    <small>پرداخت‌شده</small>
                                </div>

                                <i
                                    class="fa-solid fa-circle-check
                                           fa-2x opacity-50"
                                ></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div
                        class="card invoice-stat-card text-dark
                               bg-warning shadow-sm h-100"
                    >
                        <div class="card-body">
                            <div
                                class="d-flex justify-content-between
                                       align-items-center"
                            >
                                <div>
                                    <h4 class="mb-1">
                                        <?= (int)$invoiceStats[
                                            'partially_paid'
                                        ] ?>
                                    </h4>

                                    <small>پرداخت جزئی</small>
                                </div>

                                <i
                                    class="fa-solid fa-hourglass-half
                                           fa-2x opacity-50"
                                ></i>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div
                class="card filter-card shadow-sm border-0 mb-4"
            >
                <div class="card-body">

                    <form
                        method="get"
                        action="index.php"
                        class="row g-3 align-items-end"
                    >
                        <div class="col-lg-4">
                            <label
                                for="search"
                                class="form-label fw-semibold"
                            >
                                جستجو
                            </label>

                            <input
                                type="text"
                                name="search"
                                id="search"
                                class="form-control"
                                value="<?= e($search) ?>"
                                placeholder="شماره فاکتور، سفارش، مشتری یا کارشناس"
                            >
                        </div>

                        <div class="col-md-4 col-lg-3">
                            <label
                                for="status"
                                class="form-label fw-semibold"
                            >
                                وضعیت فاکتور
                            </label>

                            <select
                                name="status"
                                id="status"
                                class="form-select"
                            >
                                <option value="">
                                    همه وضعیت‌ها
                                </option>

                                <option
                                    value="unpaid"
                                    <?= $statusFilter === 'unpaid'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    پرداخت‌نشده
                                </option>

                                <option
                                    value="partially_paid"
                                    <?= $statusFilter === 'partially_paid'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    پرداخت جزئی
                                </option>

                                <option
                                    value="paid"
                                    <?= $statusFilter === 'paid'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    پرداخت‌شده
                                </option>
                            </select>
                        </div>

                        <div class="col-md-4 col-lg-3">
                            <label
                                for="payment_status"
                                class="form-label fw-semibold"
                            >
                                وضعیت پرداخت
                            </label>

                            <select
                                name="payment_status"
                                id="payment_status"
                                class="form-select"
                            >
                                <option value="">
                                    همه پرداخت‌ها
                                </option>

                                <option
                                    value="has_payment"
                                    <?= $paymentFilter === 'has_payment'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    دارای پرداخت
                                </option>

                                <option
                                    value="no_payment"
                                    <?= $paymentFilter === 'no_payment'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    بدون پرداخت
                                </option>

                                <option
                                    value="successful"
                                    <?= $paymentFilter === 'successful'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    دارای پرداخت موفق
                                </option>
                            </select>
                        </div>

                        <div class="col-md-4 col-lg-2">
                            <div class="d-flex gap-2">

                                <button
                                    type="submit"
                                    class="btn btn-primary flex-grow-1"
                                >
                                    <i
                                        class="fa-solid fa-filter ms-1"
                                    ></i>
                                    اعمال
                                </button>

                                <a
                                    href="index.php"
                                    class="btn btn-outline-secondary"
                                    title="حذف فیلترها"
                                >
                                    <i
                                        class="fa-solid fa-rotate-right"
                                    ></i>
                                </a>

                            </div>
                        </div>
                    </form>

                </div>
            </div>

            <div
                class="card table-card shadow-sm border-0"
            >
                <div class="card-body">

                    <div class="table-responsive">
                        <table
                            class="table table-hover align-middle
                                   invoice-table mb-0"
                        >
                            <thead class="table-primary">
                                <tr>
                                    <th class="text-center">
                                        #
                                    </th>

                                    <th>
                                        فاکتور
                                    </th>

                                    <th>
                                        سفارش
                                    </th>

                                    <th>
                                        مشتری
                                    </th>

                                    <th>
                                        کارشناس فروش
                                    </th>

                                    <th class="text-end">
                                        مبلغ کل
                                    </th>

                                    <th class="text-end">
                                        پرداخت‌شده
                                    </th>

                                    <th class="text-center">
                                        تعداد پرداخت
                                    </th>

                                    <th class="text-center">
                                        وضعیت
                                    </th>

                                    <th class="text-center">
                                        سررسید
                                    </th>

                                    <th class="text-center">
                                        عملیات
                                    </th>
                                </tr>
                            </thead>

                            <tbody>

                            <?php if ($errorMsg !== ''): ?>

                                <tr>
                                    <td
                                        colspan="11"
                                        class="text-center text-danger py-5"
                                    >
                                        امکان نمایش فاکتورها وجود ندارد.
                                    </td>
                                </tr>

                            <?php elseif ($invoices === []): ?>

                                <tr>
                                    <td
                                        colspan="11"
                                        class="text-center text-muted py-5"
                                    >
                                        <i
                                            class="fa-solid fa-inbox
                                                   fa-3x d-block mb-3
                                                   opacity-25"
                                        ></i>

                                        فاکتوری یافت نشد.
                                    </td>
                                </tr>

                            <?php else: ?>

                                <?php foreach ($invoices as $invoice): ?>
                                    <?php
                                    $invoiceId =
                                        (int)($invoice['id'] ?? 0);

                                    $totalAmount =
                                        (float)($invoice[
                                            'total_amount'
                                        ] ?? 0);

                                    $paidAmount =
                                        (float)($invoice[
                                            'paid_amount'
                                        ] ?? 0);

                                    $commissionRate =
                                        (float)($invoice[
                                            'commission_rate'
                                        ] ?? 0);

                                    $commissionCash =
                                        $totalAmount *
                                        ($commissionRate / 100);

                                    $dueDate =
                                        (string)($invoice[
                                            'due_date'
                                        ] ?? '');

                                    $isOverdue =
                                        $dueDate !== '' &&
                                        $dueDate !== '0000-00-00' &&
                                        $dueDate < date('Y-m-d') &&
                                        ($invoice['invoice_status'] ?? '')
                                            !== 'paid';

                                    $invoiceNumber =
                                        $invoice['invoice_number']
                                        ?: 'INV-' . str_pad(
                                            (string)$invoiceId,
                                            5,
                                            '0',
                                            STR_PAD_LEFT
                                        );
                                    ?>

                                    <tr>
                                        <td
                                            class="text-center fw-bold
                                                   text-muted"
                                        >
                                            <?= $invoiceId ?>
                                        </td>

                                        <td>
                                            <div
                                                class="invoice-number
                                                       fw-semibold
                                                       text-primary"
                                            >
                                                <?= e($invoiceNumber) ?>
                                            </div>

                                            <div class="small text-muted">
                                                <?= e(
                                                    $invoice[
                                                        'invoice_created_at'
                                                    ] ?? '-'
                                                ) ?>
                                            </div>
                                        </td>

                                        <td>
                                            <?php if (
                                                !empty(
                                                    $invoice['order_number']
                                                )
                                            ): ?>
                                                <div class="fw-semibold">
                                                    <?= e(
                                                        $invoice[
                                                            'order_number'
                                                        ]
                                                    ) ?>
                                                </div>

                                                <div class="small mt-1">
                                                    <?= orderBadge(
                                                        (string)(
                                                            $invoice[
                                                                'order_status'
                                                            ] ?? ''
                                                        )
                                                    ) ?>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted">
                                                    بدون سفارش
                                                </span>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <div class="fw-semibold">
                                                <?= e(
                                                    $invoice[
                                                        'company_name'
                                                    ] ?? '-'
                                                ) ?>
                                            </div>

                                            <div class="small text-muted">
                                                <?= e(
                                                    $invoice[
                                                        'manager_name'
                                                    ] ?? '-'
                                                ) ?>
                                            </div>

                                            <?php if (
                                                !empty($invoice['phone'])
                                            ): ?>
                                                <div
                                                    class="small text-muted
                                                           customer-phone"
                                                >
                                                    <?= e(
                                                        $invoice['phone']
                                                    ) ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <?php if (
                                                !empty(
                                                    $invoice[
                                                        'salesperson_name'
                                                    ]
                                                )
                                            ): ?>
                                                <div
                                                    class="commission-box"
                                                >
                                                    <div
                                                        class="fw-semibold
                                                               text-dark"
                                                    >
                                                        <?= e(
                                                            $invoice[
                                                                'salesperson_name'
                                                            ]
                                                        ) ?>
                                                    </div>

                                                    <div
                                                        class="small
                                                               text-primary
                                                               fw-bold"
                                                    >
                                                        <?= number_format(
                                                            $commissionRate,
                                                            2
                                                        ) ?>٪

                                                        —

                                                        <?= money(
                                                            $commissionCash
                                                        ) ?>
                                                    </div>

                                                    <?php if (
                                                        !empty(
                                                            $invoice[
                                                                'salesperson_mobile'
                                                            ]
                                                        )
                                                    ): ?>
                                                        <div
                                                            class="small
                                                                   text-muted
                                                                   salesperson-contact"
                                                        >
                                                            <?= e(
                                                                $invoice[
                                                                    'salesperson_mobile'
                                                                ]
                                                            ) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            <?php else: ?>
                                                <span
                                                    class="badge
                                                           bg-secondary-subtle
                                                           text-secondary
                                                           border
                                                           direct-sale-badge"
                                                >
                                                    فروش مستقیم شرکت
                                                </span>
                                            <?php endif; ?>
                                        </td>

                                        <td
                                            class="text-end fw-bold
                                                   text-nowrap"
                                        >
                                            <?= money($totalAmount) ?>
                                        </td>

                                        <td
                                            class="text-end fw-bold
                                                   text-success
                                                   text-nowrap"
                                        >
                                            <?= money($paidAmount) ?>
                                        </td>

                                        <td class="text-center">
                                            <span
                                                class="badge bg-info-subtle
                                                       text-info border
                                                       border-info-subtle"
                                            >
                                                <?= (int)(
                                                    $invoice[
                                                        'payment_count'
                                                    ] ?? 0
                                                ) ?>
                                            </span>
                                        </td>

                                        <td class="text-center">
                                            <?= invoiceBadge(
                                                (string)(
                                                    $invoice[
                                                        'invoice_status'
                                                    ] ?? ''
                                                )
                                            ) ?>
                                        </td>

                                        <td class="text-center">
                                            <small
                                                class="<?= $isOverdue
                                                    ? 'overdue-date'
                                                    : 'normal-date' ?>"
                                            >
                                                <?= $dueDate !== ''
                                                    ? e($dueDate)
                                                    : '-' ?>
                                            </small>

                                            <?php if ($isOverdue): ?>
                                                <div
                                                    class="small text-danger"
                                                >
                                                    سررسید گذشته
                                                </div>
                                            <?php endif; ?>
                                        </td>

                                        <td class="text-center">
                                            <div
                                                class="d-flex gap-1
                                                       justify-content-center"
                                            >
                                                <a
                                                    href="view.php?id=<?= $invoiceId ?>"
                                                    class="btn btn-sm
                                                           btn-outline-primary
                                                           btn-icon"
                                                    data-bs-toggle="tooltip"
                                                    title="جزئیات"
                                                >
                                                    <i
                                                        class="fa-solid
                                                               fa-eye"
                                                    ></i>
                                                </a>

                                                <a
                                                    href="edit.php?id=<?= $invoiceId ?>"
                                                    class="btn btn-sm
                                                           btn-warning
                                                           btn-icon"
                                                    data-bs-toggle="tooltip"
                                                    title="ویرایش"
                                                >
                                                    <i
                                                        class="fa-solid
                                                               fa-pen"
                                                    ></i>
                                                </a>

                                                <a
                                                    href="<?= e(BASE_URL) ?>admin/payments/index.php?invoice_id=<?= $invoiceId ?>"
                                                    class="btn btn-sm
                                                           btn-outline-success
                                                           btn-icon"
                                                    data-bs-toggle="tooltip"
                                                    title="پرداخت‌ها"
                                                >
                                                    <i
                                                        class="fa-solid
                                                               fa-credit-card"
                                                    ></i>
                                                </a>

                                                <a
                                                    href="print.php?id=<?= $invoiceId ?>"
                                                    target="_blank"
                                                    class="btn btn-sm
                                                           btn-outline-dark
                                                           btn-icon"
                                                    data-bs-toggle="tooltip"
                                                    title="چاپ"
                                                >
                                                    <i
                                                        class="fa-solid
                                                               fa-print"
                                                    ></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>

                                <?php endforeach; ?>

                            <?php endif; ?>

                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

        </main>
    </div>
</div>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tooltipElements = document.querySelectorAll(
        '[data-bs-toggle="tooltip"]'
    );

    tooltipElements.forEach(function (element) {
        new bootstrap.Tooltip(element);
    });
});
</script>
</body>
</html>