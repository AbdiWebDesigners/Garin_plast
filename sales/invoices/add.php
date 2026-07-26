<?php
// مسیر فایل: garin/invoices/add.php

declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

if (empty($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

$pageTitle = 'ثبت فاکتور فروش جدید';
$errorMsg = '';

/*
|--------------------------------------------------------------------------
| ایجاد توکن امنیتی فرم
|--------------------------------------------------------------------------
*/
if (empty($_SESSION['invoice_csrf_token'])) {
    $_SESSION['invoice_csrf_token'] = bin2hex(random_bytes(32));
}

$csrfToken = $_SESSION['invoice_csrf_token'];

/*
|--------------------------------------------------------------------------
| مقادیر اولیه فرم
|--------------------------------------------------------------------------
*/
$formData = [
    'order_id'         => '',
    'invoice_number'   => '',
    'subtotal'         => '',
    'tax_amount'       => '0',
    'discount_amount'  => '0',
    'total_amount'     => '',
    'status'           => 'unpaid',
    'due_date'         => '',
    'sales_agent_id'   => '',
    'commission_rate'  => '0',
];

/*
|--------------------------------------------------------------------------
| ساخت شماره فاکتور یکتا
|--------------------------------------------------------------------------
*/
function generateInvoiceNumber(): string
{
    return 'INV-' . date('Ymd-His') . '-' . random_int(1000, 9999);
}

/*
|--------------------------------------------------------------------------
| تبدیل مقدار ورودی به عدد
|--------------------------------------------------------------------------
*/
function normalizeAmount(mixed $value): float
{
    if (is_string($value)) {
        $value = str_replace([',', ' '], '', $value);
    }

    return is_numeric($value) ? (float)$value : 0;
}

/*
|--------------------------------------------------------------------------
| واکشی فهرست بازاریاب‌ها
|--------------------------------------------------------------------------
|
| ارتباط صحیح:
| sales_agents.user_id -> users.id
|
*/
$agents = [];

try {
    $agentsQuery = $pdo->query("
        SELECT
            sa.id,
            sa.user_id,
            sa.commission_rate,
            u.fullname,
            u.status AS user_status
        FROM sales_agents AS sa
        INNER JOIN users AS u
            ON u.id = sa.user_id
        WHERE u.status = 1
        ORDER BY u.fullname ASC
    ");

    $agents = $agentsQuery->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errorMsg = 'خطا در دریافت فهرست بازاریاب‌ها: ' . $e->getMessage();
}

/*
|--------------------------------------------------------------------------
| پردازش فرم
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData['order_id'] = trim((string)($_POST['order_id'] ?? ''));
    $formData['invoice_number'] = trim(
        (string)($_POST['invoice_number'] ?? '')
    );

    $formData['subtotal'] = (string)($_POST['subtotal'] ?? '');
    $formData['tax_amount'] = (string)($_POST['tax_amount'] ?? '0');
    $formData['discount_amount'] = (string)(
        $_POST['discount_amount'] ?? '0'
    );

    $formData['status'] = trim(
        (string)($_POST['status'] ?? 'unpaid')
    );

    $formData['due_date'] = trim(
        (string)($_POST['due_date'] ?? '')
    );

    $formData['sales_agent_id'] = trim(
        (string)($_POST['sales_agent_id'] ?? '')
    );

    $formData['commission_rate'] = (string)(
        $_POST['commission_rate'] ?? '0'
    );

    /*
    |--------------------------------------------------------------------------
    | بررسی CSRF
    |--------------------------------------------------------------------------
    */
    $postedToken = (string)($_POST['csrf_token'] ?? '');

    if (
        empty($postedToken) ||
        !hash_equals($_SESSION['invoice_csrf_token'], $postedToken)
    ) {
        $errorMsg = 'درخواست نامعتبر است. صفحه را تازه‌سازی و دوباره تلاش کنید.';
    } else {
        /*
        |--------------------------------------------------------------------------
        | پاک‌سازی و اعتبارسنجی داده‌ها
        |--------------------------------------------------------------------------
        */
        $orderId = $formData['order_id'] !== ''
            ? (int)$formData['order_id']
            : null;

        $invoiceNumber = $formData['invoice_number'] !== ''
            ? $formData['invoice_number']
            : generateInvoiceNumber();

        $subtotal = normalizeAmount($formData['subtotal']);
        $taxAmount = normalizeAmount($formData['tax_amount']);
        $discountAmount = normalizeAmount(
            $formData['discount_amount']
        );

        $salesAgentId = $formData['sales_agent_id'] !== ''
            ? (int)$formData['sales_agent_id']
            : null;

        $commissionRate = normalizeAmount(
            $formData['commission_rate']
        );

        $status = $formData['status'];
        $dueDate = $formData['due_date'];

        $allowedStatuses = [
            'unpaid',
            'partially_paid',
            'paid',
        ];

        /*
        |--------------------------------------------------------------------------
        | محاسبه مبلغ نهایی
        |--------------------------------------------------------------------------
        */
        $totalAmount = $subtotal + $taxAmount - $discountAmount;

        $formData['invoice_number'] = $invoiceNumber;
        $formData['total_amount'] = (string)$totalAmount;

        if ($subtotal <= 0) {
            $errorMsg = 'مبلغ پایه فاکتور باید بیشتر از صفر باشد.';
        } elseif ($taxAmount < 0) {
            $errorMsg = 'مبلغ مالیات نمی‌تواند منفی باشد.';
        } elseif ($discountAmount < 0) {
            $errorMsg = 'مبلغ تخفیف نمی‌تواند منفی باشد.';
        } elseif ($discountAmount > ($subtotal + $taxAmount)) {
            $errorMsg = 'مبلغ تخفیف نمی‌تواند بیشتر از مبلغ فاکتور باشد.';
        } elseif ($totalAmount <= 0) {
            $errorMsg = 'مبلغ نهایی فاکتور باید بیشتر از صفر باشد.';
        } elseif (!in_array($status, $allowedStatuses, true)) {
            $errorMsg = 'وضعیت انتخاب‌شده معتبر نیست.';
        } elseif ($dueDate === '') {
            $errorMsg = 'تاریخ سررسید فاکتور را وارد کنید.';
        } elseif (
            !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dueDate)
        ) {
            $errorMsg = 'فرمت تاریخ سررسید معتبر نیست.';
        } elseif (
            $commissionRate < 0 ||
            $commissionRate > 100
        ) {
            $errorMsg = 'درصد کمیسیون باید بین صفر تا صد باشد.';
        } elseif (
            $salesAgentId === null &&
            $commissionRate > 0
        ) {
            $errorMsg = 'برای ثبت کمیسیون باید یک بازاریاب انتخاب شود.';
        } else {
            try {
                /*
                |--------------------------------------------------------------------------
                | بررسی معتبر بودن بازاریاب
                |--------------------------------------------------------------------------
                */
                if ($salesAgentId !== null) {
                    $agentCheck = $pdo->prepare("
                        SELECT
                            sa.id,
                            sa.commission_rate
                        FROM sales_agents AS sa
                        INNER JOIN users AS u
                            ON u.id = sa.user_id
                        WHERE sa.id = ?
                          AND u.status = 1
                        LIMIT 1
                    ");

                    $agentCheck->execute([$salesAgentId]);
                    $selectedAgent = $agentCheck->fetch(
                        PDO::FETCH_ASSOC
                    );

                    if (!$selectedAgent) {
                        throw new RuntimeException(
                            'بازاریاب انتخاب‌شده معتبر یا فعال نیست.'
                        );
                    }
                } else {
                    $commissionRate = 0;
                }

                /*
                |--------------------------------------------------------------------------
                | بررسی تکراری نبودن شماره فاکتور
                |--------------------------------------------------------------------------
                */
                $invoiceCheck = $pdo->prepare("
                    SELECT id
                    FROM invoices
                    WHERE invoice_number = ?
                    LIMIT 1
                ");

                $invoiceCheck->execute([$invoiceNumber]);

                if ($invoiceCheck->fetchColumn()) {
                    throw new RuntimeException(
                        'شماره فاکتور قبلاً ثبت شده است.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | بررسی سفارش، در صورت وارد شدن order_id
                |--------------------------------------------------------------------------
                */
                if ($orderId !== null) {
                    $orderCheck = $pdo->prepare("
                        SELECT id
                        FROM orders
                        WHERE id = ?
                        LIMIT 1
                    ");

                    $orderCheck->execute([$orderId]);

                    if (!$orderCheck->fetchColumn()) {
                        throw new RuntimeException(
                            'سفارش انتخاب‌شده در سیستم وجود ندارد.'
                        );
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | ثبت فاکتور
                |--------------------------------------------------------------------------
                */
                $sql = "
                    INSERT INTO invoices (
                        order_id,
                        invoice_number,
                        subtotal,
                        tax_amount,
                        discount_amount,
                        total_amount,
                        status,
                        due_date,
                        created_at,
                        sales_agent_id,
                        commission_rate
                    ) VALUES (
                        :order_id,
                        :invoice_number,
                        :subtotal,
                        :tax_amount,
                        :discount_amount,
                        :total_amount,
                        :status,
                        :due_date,
                        NOW(),
                        :sales_agent_id,
                        :commission_rate
                    )
                ";

                $stmt = $pdo->prepare($sql);

                $stmt->bindValue(
                    ':order_id',
                    $orderId,
                    $orderId === null
                        ? PDO::PARAM_NULL
                        : PDO::PARAM_INT
                );

                $stmt->bindValue(
                    ':invoice_number',
                    $invoiceNumber,
                    PDO::PARAM_STR
                );

                $stmt->bindValue(
                    ':subtotal',
                    number_format($subtotal, 2, '.', ''),
                    PDO::PARAM_STR
                );

                $stmt->bindValue(
                    ':tax_amount',
                    number_format($taxAmount, 2, '.', ''),
                    PDO::PARAM_STR
                );

                $stmt->bindValue(
                    ':discount_amount',
                    number_format($discountAmount, 2, '.', ''),
                    PDO::PARAM_STR
                );

                $stmt->bindValue(
                    ':total_amount',
                    number_format($totalAmount, 2, '.', ''),
                    PDO::PARAM_STR
                );

                $stmt->bindValue(
                    ':status',
                    $status,
                    PDO::PARAM_STR
                );

                $stmt->bindValue(
                    ':due_date',
                    $dueDate,
                    PDO::PARAM_STR
                );

                $stmt->bindValue(
                    ':sales_agent_id',
                    $salesAgentId,
                    $salesAgentId === null
                        ? PDO::PARAM_NULL
                        : PDO::PARAM_INT
                );

                $stmt->bindValue(
                    ':commission_rate',
                    number_format($commissionRate, 2, '.', ''),
                    PDO::PARAM_STR
                );

                $stmt->execute();

                /*
                |--------------------------------------------------------------------------
                | جلوگیری از ارسال مجدد فرم
                |--------------------------------------------------------------------------
                */
                $_SESSION['invoice_csrf_token'] = bin2hex(
                    random_bytes(32)
                );

                header(
                    'Location: index.php?success=1&invoice_id=' .
                    urlencode((string)$pdo->lastInsertId())
                );
                exit;
            } catch (RuntimeException $e) {
                $errorMsg = $e->getMessage();
            } catch (PDOException $e) {
                $errorMsg = 'خطا در ثبت فاکتور: ' . $e->getMessage();
            }
        }
    }
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

    <title><?= htmlspecialchars($pageTitle) ?></title>

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
            background: #f6f7fb;
        }

        .invoice-card {
            border-radius: 18px;
        }

        .section-box {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 14px;
        }

        .amount-input {
            direction: ltr;
            text-align: left;
        }

        .small-description {
            font-size: 0.78rem;
        }
    </style>
</head>

<body>
<div class="container-fluid">
    <div class="row">

        <?php include __DIR__ . '/../../admin/sidebar.php'; ?>

        <main class="col-md-10 p-3 p-md-4">

            <div
                class="d-flex flex-column flex-md-row
                       justify-content-between align-items-md-center
                       gap-3 mb-4"
            >
                <div>
                    <h3 class="mb-1">
                        <?= htmlspecialchars($pageTitle) ?>
                    </h3>

                    <div class="text-muted">
                        ثبت اطلاعات مالی و تخصیص بازاریاب به فاکتور
                    </div>
                </div>

                <a
                    href="index.php"
                    class="btn btn-outline-secondary"
                >
                    <i class="fa-solid fa-arrow-right ms-1"></i>
                    بازگشت به فهرست فاکتورها
                </a>
            </div>

            <?php if ($errorMsg !== ''): ?>
                <div class="alert alert-danger">
                    <i
                        class="fa-solid fa-circle-exclamation ms-1"
                    ></i>

                    <?= htmlspecialchars($errorMsg) ?>
                </div>
            <?php endif; ?>

            <div
                class="card invoice-card shadow-sm border-0"
            >
                <div class="card-body p-3 p-md-4">

                    <form
                        method="post"
                        action=""
                        id="invoiceForm"
                        autocomplete="off"
                    >
                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= htmlspecialchars($csrfToken) ?>"
                        >

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label
                                    for="invoice_number"
                                    class="form-label fw-semibold"
                                >
                                    شماره فاکتور
                                </label>

                                <input
                                    type="text"
                                    name="invoice_number"
                                    id="invoice_number"
                                    class="form-control"
                                    maxlength="50"
                                    value="<?= htmlspecialchars(
                                        $formData['invoice_number']
                                    ) ?>"
                                    placeholder="در صورت خالی بودن، خودکار ساخته می‌شود"
                                >
                            </div>

                            <div class="col-md-6">
                                <label
                                    for="order_id"
                                    class="form-label fw-semibold"
                                >
                                    شناسه سفارش
                                </label>

                                <input
                                    type="number"
                                    name="order_id"
                                    id="order_id"
                                    class="form-control amount-input"
                                    min="1"
                                    value="<?= htmlspecialchars(
                                        $formData['order_id']
                                    ) ?>"
                                    placeholder="اختیاری"
                                >

                                <div class="form-text">
                                    در صورت مرتبط بودن فاکتور با یک سفارش،
                                    شناسه سفارش را وارد کنید.
                                </div>
                            </div>

                            <div class="col-12 mt-4">
                                <div class="section-box p-3">
                                    <h6 class="fw-bold mb-3">
                                        <i
                                            class="fa-solid fa-coins
                                                   text-primary ms-1"
                                        ></i>
                                        اطلاعات مالی فاکتور
                                    </h6>

                                    <div class="row g-3">

                                        <div class="col-md-6 col-lg-3">
                                            <label
                                                for="subtotal"
                                                class="form-label"
                                            >
                                                مبلغ پایه
                                                <span class="text-danger">
                                                    *
                                                </span>
                                            </label>

                                            <div class="input-group">
                                                <input
                                                    type="number"
                                                    name="subtotal"
                                                    id="subtotal"
                                                    class="form-control
                                                           amount-input"
                                                    min="0"
                                                    step="0.01"
                                                    required
                                                    value="<?= htmlspecialchars(
                                                        $formData['subtotal']
                                                    ) ?>"
                                                >

                                                <span class="input-group-text">
                                                    تومان
                                                </span>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-lg-3">
                                            <label
                                                for="tax_amount"
                                                class="form-label"
                                            >
                                                مبلغ مالیات
                                            </label>

                                            <div class="input-group">
                                                <input
                                                    type="number"
                                                    name="tax_amount"
                                                    id="tax_amount"
                                                    class="form-control
                                                           amount-input"
                                                    min="0"
                                                    step="0.01"
                                                    value="<?= htmlspecialchars(
                                                        $formData['tax_amount']
                                                    ) ?>"
                                                >

                                                <span class="input-group-text">
                                                    تومان
                                                </span>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-lg-3">
                                            <label
                                                for="discount_amount"
                                                class="form-label"
                                            >
                                                مبلغ تخفیف
                                            </label>

                                            <div class="input-group">
                                                <input
                                                    type="number"
                                                    name="discount_amount"
                                                    id="discount_amount"
                                                    class="form-control
                                                           amount-input"
                                                    min="0"
                                                    step="0.01"
                                                    value="<?= htmlspecialchars(
                                                        $formData[
                                                            'discount_amount'
                                                        ]
                                                    ) ?>"
                                                >

                                                <span class="input-group-text">
                                                    تومان
                                                </span>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-lg-3">
                                            <label
                                                for="total_amount"
                                                class="form-label fw-bold"
                                            >
                                                مبلغ نهایی
                                            </label>

                                            <div class="input-group">
                                                <input
                                                    type="text"
                                                    id="total_amount"
                                                    class="form-control
                                                           amount-input
                                                           fw-bold"
                                                    value="<?= htmlspecialchars(
                                                        $formData[
                                                            'total_amount'
                                                        ]
                                                    ) ?>"
                                                    readonly
                                                >

                                                <span class="input-group-text">
                                                    تومان
                                                </span>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label
                                    for="due_date"
                                    class="form-label fw-semibold"
                                >
                                    تاریخ سررسید
                                    <span class="text-danger">*</span>
                                </label>

                                <input
                                    type="date"
                                    name="due_date"
                                    id="due_date"
                                    class="form-control amount-input"
                                    required
                                    value="<?= htmlspecialchars(
                                        $formData['due_date']
                                    ) ?>"
                                >
                            </div>

                            <div class="col-md-6">
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
                                    <option
                                        value="unpaid"
                                        <?= $formData['status'] === 'unpaid'
                                            ? 'selected'
                                            : '' ?>
                                    >
                                        پرداخت نشده
                                    </option>

                                    <option
                                        value="partially_paid"
                                        <?= $formData['status']
                                            === 'partially_paid'
                                            ? 'selected'
                                            : '' ?>
                                    >
                                        پرداخت جزئی
                                    </option>

                                    <option
                                        value="paid"
                                        <?= $formData['status'] === 'paid'
                                            ? 'selected'
                                            : '' ?>
                                    >
                                        تسویه‌شده
                                    </option>
                                </select>
                            </div>

                            <div class="col-12 mt-4">
                                <div class="section-box p-3">
                                    <h6 class="fw-bold mb-3">
                                        <i
                                            class="fa-solid fa-user-tie
                                                   text-primary ms-1"
                                        ></i>
                                        بازاریاب و کمیسیون
                                    </h6>

                                    <div class="row g-3">

                                        <div class="col-md-6">
                                            <label
                                                for="sales_agent_id"
                                                class="form-label"
                                            >
                                                انتخاب بازاریاب
                                            </label>

                                            <select
                                                name="sales_agent_id"
                                                id="sales_agent_id"
                                                class="form-select"
                                            >
                                                <option value="">
                                                    فروش مستقیم شرکت
                                                </option>

                                                <?php foreach ($agents as $agent): ?>
                                                    <?php
                                                    $agentId = (int)$agent['id'];
                                                    $agentRate = (float)$agent[
                                                        'commission_rate'
                                                    ];
                                                    ?>

                                                    <option
                                                        value="<?= $agentId ?>"
                                                        data-rate="<?= $agentRate ?>"
                                                        <?= (string)$agentId ===
                                                            $formData[
                                                                'sales_agent_id'
                                                            ]
                                                            ? 'selected'
                                                            : '' ?>
                                                    >
                                                        <?= htmlspecialchars(
                                                            $agent['fullname']
                                                        ) ?>

                                                        — نرخ قرارداد:
                                                        <?= $agentRate ?>٪
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label
                                                for="commission_rate"
                                                class="form-label"
                                            >
                                                درصد کمیسیون این فاکتور
                                            </label>

                                            <div class="input-group">
                                                <input
                                                    type="number"
                                                    name="commission_rate"
                                                    id="commission_rate"
                                                    class="form-control
                                                           amount-input"
                                                    min="0"
                                                    max="100"
                                                    step="0.01"
                                                    value="<?= htmlspecialchars(
                                                        $formData[
                                                            'commission_rate'
                                                        ]
                                                    ) ?>"
                                                >

                                                <span class="input-group-text">
                                                    درصد
                                                </span>
                                            </div>

                                            <div
                                                class="form-text
                                                       small-description"
                                            >
                                                با انتخاب بازاریاب، نرخ قرارداد
                                                به‌صورت خودکار وارد می‌شود.
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div
                                class="col-12 d-flex flex-wrap
                                       justify-content-end gap-2 mt-4"
                            >
                                <a
                                    href="index.php"
                                    class="btn btn-outline-secondary px-4"
                                >
                                    انصراف
                                </a>

                                <button
                                    type="submit"
                                    class="btn btn-primary px-4"
                                >
                                    <i
                                        class="fa-solid fa-floppy-disk ms-1"
                                    ></i>
                                    ثبت و ذخیره فاکتور
                                </button>
                            </div>

                        </div>
                    </form>

                </div>
            </div>
        </main>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const subtotalInput = document.getElementById('subtotal');
    const taxInput = document.getElementById('tax_amount');
    const discountInput = document.getElementById('discount_amount');
    const totalInput = document.getElementById('total_amount');

    const agentSelect = document.getElementById('sales_agent_id');
    const commissionInput = document.getElementById(
        'commission_rate'
    );

    function parseNumber(value) {
        const number = parseFloat(value);

        return Number.isFinite(number) ? number : 0;
    }

    function calculateTotal() {
        const subtotal = parseNumber(subtotalInput.value);
        const tax = parseNumber(taxInput.value);
        const discount = parseNumber(discountInput.value);

        const total = Math.max(
            0,
            subtotal + tax - discount
        );

        totalInput.value = total.toFixed(2);
    }

    function updateCommission() {
        const selectedOption =
            agentSelect.options[agentSelect.selectedIndex];

        if (!selectedOption || selectedOption.value === '') {
            commissionInput.value = '0';
            commissionInput.readOnly = true;
            return;
        }

        const defaultRate = selectedOption.dataset.rate || '0';

        commissionInput.readOnly = false;
        commissionInput.value = defaultRate;
    }

    subtotalInput.addEventListener('input', calculateTotal);
    taxInput.addEventListener('input', calculateTotal);
    discountInput.addEventListener('input', calculateTotal);

    agentSelect.addEventListener('change', updateCommission);

    calculateTotal();

    if (agentSelect.value === '') {
        commissionInput.readOnly = true;
    }
});
</script>
</body>
</html>