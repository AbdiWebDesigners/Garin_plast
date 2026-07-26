<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/functions.php';

requireLogin();
requirePermission('viewinventory');

global $pdo;

$error = '';

$warehouses = [];
$products = [];
$users = [];

$selectedWarehouseId = (int) (
    $_POST['warehouse_id']
    ?? $_GET['warehouse_id']
    ?? 0
);

/*
|--------------------------------------------------------------------------
| دریافت انبارها
|--------------------------------------------------------------------------
*/
try {
    $warehouses = $pdo->query("
        SELECT
            id,
            warehouse_name
        FROM warehouses
        WHERE status = 'active'
           OR status IS NULL
           OR status = ''
        ORDER BY warehouse_name ASC
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    $warehouses = [];
    $error = 'خطا در دریافت انبارها: ' . $e->getMessage();
}

/*
|--------------------------------------------------------------------------
| دریافت کالاها
|--------------------------------------------------------------------------
*/
try {
    $products = $pdo->query("
        SELECT
            id,
            COALESCE(
                NULLIF(title, ''),
                NULLIF(sku, ''),
                CONCAT('کالا شماره ', id)
            ) AS title,
            sku,
            COALESCE(price, 0) AS price,
            COALESCE(stock, 0) AS stock
        FROM products
        WHERE status = 'active'
           OR status IS NULL
           OR status = ''
        ORDER BY title ASC, id ASC
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    $products = [];

    if ($error === '') {
        $error = 'خطا در دریافت کالاها: ' . $e->getMessage();
    }
}

/*
|--------------------------------------------------------------------------
| دریافت کاربران فعال
|--------------------------------------------------------------------------
*/
try {
    $users = $pdo->query("
        SELECT
            id,
            fullname
        FROM users
        WHERE status = 1
        ORDER BY fullname ASC, id ASC
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    $users = [];

    if ($error === '') {
        $error = 'خطا در دریافت کاربران: ' . $e->getMessage();
    }
}

/*
|--------------------------------------------------------------------------
| ثبت حواله
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $voucherNumber = trim(
        (string) ($_POST['voucher_number'] ?? '')
    );

    $voucherDate = trim(
        (string) ($_POST['voucher_date'] ?? '')
    );

    $warehouseId = (int) ($_POST['warehouse_id'] ?? 0);

    $requestedBy = !empty($_POST['requested_by'])
        ? (int) $_POST['requested_by']
        : null;

    $approvedBy = !empty($_POST['approved_by'])
        ? (int) $_POST['approved_by']
        : null;

    $status = (string) ($_POST['status'] ?? 'draft');

    $description = trim(
        (string) ($_POST['description'] ?? '')
    );

    $createdBy = (int) (
        $_SESSION['user']['id']
        ?? $_SESSION['user_id']
        ?? 0
    );

    $items = is_array($_POST['items'] ?? null)
        ? $_POST['items']
        : [];

    $allowedStatuses = [
        'draft',
        'approved',
        'issued',
        'cancelled',
    ];

    if (!in_array($status, $allowedStatuses, true)) {
        $status = 'draft';
    }

    /*
    |--------------------------------------------------------------------------
    | اعتبارسنجی اطلاعات اصلی
    |--------------------------------------------------------------------------
    */
    if ($voucherNumber === '') {
        $error = 'شماره حواله الزامی است.';
    } elseif ($voucherDate === '') {
        $error = 'تاریخ حواله الزامی است.';
    } elseif ($warehouseId <= 0) {
        $error = 'انبار را انتخاب کنید.';
    } elseif ($createdBy <= 0) {
        $error = 'شناسه کاربر واردشده به سیستم معتبر نیست.';
    } elseif ($items === []) {
        $error = 'حداقل یک آیتم وارد کنید.';
    }

    /*
    |--------------------------------------------------------------------------
    | بررسی کاربران انتخاب‌شده
    |--------------------------------------------------------------------------
    */
    if ($error === '' && $requestedBy !== null) {
        $requestedUserStatement = $pdo->prepare("
            SELECT id
            FROM users
            WHERE id = ?
              AND status = 1
            LIMIT 1
        ");

        $requestedUserStatement->execute([$requestedBy]);

        if (!$requestedUserStatement->fetchColumn()) {
            $error = 'درخواست‌کننده انتخاب‌شده معتبر نیست.';
        }
    }

    if ($error === '' && $approvedBy !== null) {
        $approvedUserStatement = $pdo->prepare("
            SELECT id
            FROM users
            WHERE id = ?
              AND status = 1
            LIMIT 1
        ");

        $approvedUserStatement->execute([$approvedBy]);

        if (!$approvedUserStatement->fetchColumn()) {
            $error = 'تأییدکننده انتخاب‌شده معتبر نیست.';
        }
    }

    /*
    |--------------------------------------------------------------------------
    | تبدیل تاریخ فرم به فرمت MySQL
    |--------------------------------------------------------------------------
    */
    $mysqlVoucherDate = '';

    if ($error === '') {
        $dateTime = DateTime::createFromFormat(
            'Y-m-d\TH:i',
            $voucherDate
        );

        if (!$dateTime) {
            $dateTime = DateTime::createFromFormat(
                'Y-m-d\TH:i:s',
                $voucherDate
            );
        }

        if (!$dateTime) {
            $dateTime = DateTime::createFromFormat(
                'Y-m-d H:i:s',
                $voucherDate
            );
        }

        if (!$dateTime) {
            $error = 'فرمت تاریخ نامعتبر است.';
        } else {
            $mysqlVoucherDate = $dateTime->format('Y-m-d H:i:s');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | ذخیره در دیتابیس
    |--------------------------------------------------------------------------
    */
    if ($error === '') {
        try {
            $pdo->beginTransaction();

            /*
             * جلوگیری از شماره حواله تکراری
             */
            $duplicateStatement = $pdo->prepare("
                SELECT id
                FROM inventory_issue_vouchers
                WHERE voucher_number = ?
                LIMIT 1
            ");

            $duplicateStatement->execute([$voucherNumber]);

            if ($duplicateStatement->fetchColumn()) {
                throw new RuntimeException(
                    'شماره حواله تکراری است.'
                );
            }

            /*
             * بررسی انبار
             */
            $warehouseStatement = $pdo->prepare("
                SELECT id
                FROM warehouses
                WHERE id = ?
                  AND (
                      status = 'active'
                      OR status IS NULL
                      OR status = ''
                  )
                LIMIT 1
            ");

            $warehouseStatement->execute([$warehouseId]);

            if (!$warehouseStatement->fetchColumn()) {
                throw new RuntimeException(
                    'انبار انتخاب‌شده معتبر نیست.'
                );
            }

            /*
             * ثبت اطلاعات اصلی حواله
             */
            $insertVoucherStatement = $pdo->prepare("
                INSERT INTO inventory_issue_vouchers (
                    voucher_number,
                    voucher_date,
                    warehouse_id,
                    request_type,
                    requested_by,
                    approved_by,
                    status,
                    description,
                    created_by
                )
                VALUES (
                    ?,
                    ?,
                    ?,
                    'consumption',
                    ?,
                    ?,
                    ?,
                    ?,
                    ?
                )
            ");

            $insertVoucherStatement->execute([
                $voucherNumber,
                $mysqlVoucherDate,
                $warehouseId,
                $requestedBy,
                $approvedBy,
                $status,
                $description !== '' ? $description : null,
                $createdBy,
            ]);

            $voucherId = (int) $pdo->lastInsertId();

            /*
             * محاسبه موجودی از گردش انبار
             */
            $stockStatement = $pdo->prepare("
                SELECT COALESCE(
                    SUM(
                        CASE
                            WHEN transaction_type IN (
                                'purchase',
                                'production'
                            )
                                THEN quantity

                            WHEN transaction_type IN (
                                'sale',
                                'consume',
                                'issue'
                            )
                                THEN -quantity

                            ELSE 0
                        END
                    ),
                    0
                )
                FROM inventory_transactions
                WHERE item_id = ?
                  AND warehouse_id = ?
                  AND item_type = 'finished_product'
            ");

            /*
             * دریافت اطلاعات کالا
             */
            $productStatement = $pdo->prepare("
                SELECT
                    id,
                    COALESCE(price, 0) AS price,
                    COALESCE(stock, 0) AS stock
                FROM products
                WHERE id = ?
                  AND (
                      status = 'active'
                      OR status IS NULL
                      OR status = ''
                  )
                LIMIT 1
            ");

            /*
             * ثبت آیتم حواله
             */
            $insertItemStatement = $pdo->prepare("
                INSERT INTO inventory_issue_voucher_items (
                    voucher_id,
                    line_no,
                    item_type,
                    item_id,
                    quantity,
                    unit,
                    unit_cost,
                    total_cost,
                    serial_number,
                    batch_number,
                    expire_date,
                    warehouse_location,
                    description
                )
                VALUES (
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?
                )
            ");

            /*
             * ثبت گردش خروج کالا
             */
            $insertTransactionStatement = $pdo->prepare("
                INSERT INTO inventory_transactions (
                    item_type,
                    item_id,
                    warehouse_id,
                    transaction_type,
                    quantity,
                    unit,
                    unit_cost,
                    total_cost,
                    reference_type,
                    reference_id,
                    transaction_date,
                    description,
                    created_by
                )
                VALUES (
                    ?,
                    ?,
                    ?,
                    'consume',
                    ?,
                    ?,
                    ?,
                    ?,
                    'inventory_issue_voucher',
                    ?,
                    ?,
                    ?,
                    ?
                )
            ");

            $lineNumber = 1;

            foreach ($items as $item) {
                $itemId = (int) ($item['item_id'] ?? 0);

                $quantity = (float) (
                    $item['quantity']
                    ?? 0
                );

                $unit = trim(
                    (string) ($item['unit'] ?? 'piece')
                );

                if ($unit === '') {
                    $unit = 'piece';
                }

                $unitCost = (float) (
                    $item['unit_cost']
                    ?? 0
                );

                $itemType = 'finished_product';

                $serialNumber = trim(
                    (string) ($item['serial_number'] ?? '')
                );

                $batchNumber = trim(
                    (string) ($item['batch_number'] ?? '')
                );

                $expireDate = trim(
                    (string) ($item['expire_date'] ?? '')
                );

                $warehouseLocation = trim(
                    (string) (
                        $item['warehouse_location']
                        ?? ''
                    )
                );

                $itemDescription = trim(
                    (string) ($item['description'] ?? '')
                );

                if ($itemId <= 0) {
                    throw new RuntimeException(
                        'کالای ردیف '
                        . $lineNumber
                        . ' معتبر نیست.'
                    );
                }

                if ($quantity <= 0) {
                    throw new RuntimeException(
                        'مقدار ردیف '
                        . $lineNumber
                        . ' باید بیشتر از صفر باشد.'
                    );
                }

                /*
                 * دریافت محصول
                 */
                $productStatement->execute([$itemId]);

                $product = $productStatement->fetch(
                    PDO::FETCH_ASSOC
                );

                if (!$product) {
                    throw new RuntimeException(
                        'کالای ردیف '
                        . $lineNumber
                        . ' پیدا نشد یا غیرفعال است.'
                    );
                }

                if ($unitCost <= 0) {
                    $unitCost = (float) $product['price'];
                }

                /*
                 * محاسبه موجودی
                 */
                $stockStatement->execute([
                    $itemId,
                    $warehouseId,
                ]);

                $transactionStock = (float) (
                    $stockStatement->fetchColumn()
                );

                $currentStock = $transactionStock;

                /*
                 * اگر گردش انبار هنوز وجود ندارد،
                 * از stock جدول products استفاده می‌شود.
                 */
                if (
                    $currentStock == 0.0
                    && (float) $product['stock'] > 0
                ) {
                    $currentStock = (float) $product['stock'];
                }

                if (
                    in_array(
                        $status,
                        ['approved', 'issued'],
                        true
                    )
                    && $currentStock < $quantity
                ) {
                    throw new RuntimeException(
                        'موجودی کافی برای کالای ردیف '
                        . $lineNumber
                        . ' وجود ندارد. موجودی: '
                        . $currentStock
                    );
                }

                $totalCost = $quantity * $unitCost;

                /*
                 * ثبت آیتم
                 */
                $insertItemStatement->execute([
                    $voucherId,
                    $lineNumber,
                    $itemType,
                    $itemId,
                    $quantity,
                    $unit,
                    $unitCost,
                    $totalCost,
                    $serialNumber !== ''
                        ? $serialNumber
                        : null,
                    $batchNumber !== ''
                        ? $batchNumber
                        : null,
                    $expireDate !== ''
                        ? $expireDate
                        : null,
                    $warehouseLocation !== ''
                        ? $warehouseLocation
                        : null,
                    $itemDescription !== ''
                        ? $itemDescription
                        : null,
                ]);

                /*
                 * حواله draft یا cancelled موجودی را کم نمی‌کند.
                 */
                if (
                    in_array(
                        $status,
                        ['approved', 'issued'],
                        true
                    )
                ) {
                    $insertTransactionStatement->execute([
                        $itemType,
                        $itemId,
                        $warehouseId,
                        $quantity,
                        $unit,
                        $unitCost,
                        $totalCost,
                        $voucherId,
                        $mysqlVoucherDate,
                        $description !== ''
                            ? $description
                            : null,
                        $createdBy,
                    ]);
                }

                $lineNumber++;
            }

            $pdo->commit();

            header(
                'Location: view.php?id='
                . $voucherId
                . '&success=1',
                true,
                302
            );

            exit;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $error = $e->getMessage();
        }
    }
}

/*
|--------------------------------------------------------------------------
| اطلاعات صفحه
|--------------------------------------------------------------------------
*/
$page_title = 'ثبت حواله خروج';
$pageTitle = 'ثبت حواله خروج';
$pageDescription = 'ایجاد حواله خروج جدید';
$pageIcon = 'fa-solid fa-right-from-bracket';

$contentFile = __DIR__ . '/views/create_content.php';

require_once dirname(__DIR__, 2) . '/includes/layout.php';