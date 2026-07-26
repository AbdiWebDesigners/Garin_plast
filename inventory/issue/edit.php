<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/functions.php';

requireLogin();
requirePermission('viewinventory');

global $pdo;

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$error = '';

$voucher = null;
$warehouses = [];
$products = [];

try {
    if ($id <= 0) {
        throw new RuntimeException('شناسه حواله نامعتبر است.');
    }

    $warehouses = $pdo->query("
        SELECT id, warehouse_name
        FROM warehouses
        WHERE status = 'active'
           OR status IS NULL
           OR status = ''
        ORDER BY warehouse_name
    ")->fetchAll(PDO::FETCH_ASSOC);

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
        ORDER BY title
    ")->fetchAll(PDO::FETCH_ASSOC);

    $voucher = issueLoadVoucher($pdo, $id);

    if (!$voucher) {
        throw new RuntimeException('حواله خروج موردنظر پیدا نشد.');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        issueVerifyCsrf();

        $number = trim((string) ($_POST['voucher_number'] ?? ''));
        $date = issueParseDate((string) ($_POST['voucher_date'] ?? ''));
        $warehouseId = (int) ($_POST['warehouse_id'] ?? 0);
        $status = (string) ($_POST['status'] ?? 'draft');
        $items = $_POST['items'] ?? [];

        if (
            $number === '' ||
            $warehouseId <= 0 ||
            !is_array($items) ||
            count($items) === 0
        ) {
            throw new RuntimeException('اطلاعات فرم ناقص است.');
        }

        $allowedStatuses = [
            'draft',
            'approved',
            'issued',
            'cancelled',
        ];

        if (!in_array($status, $allowedStatuses, true)) {
            throw new RuntimeException('وضعیت حواله نامعتبر است.');
        }

        $pdo->beginTransaction();

        $duplicateStatement = $pdo->prepare("
            SELECT id
            FROM inventory_issue_vouchers
            WHERE voucher_number = ?
              AND id <> ?
            LIMIT 1
        ");

        $duplicateStatement->execute([$number, $id]);

        if ($duplicateStatement->fetchColumn()) {
            throw new RuntimeException('شماره حواله تکراری است.');
        }

        issueDeleteTransactions($pdo, $id);

        $deleteItemsStatement = $pdo->prepare("
            DELETE FROM inventory_issue_voucher_items
            WHERE voucher_id = ?
        ");

        $deleteItemsStatement->execute([$id]);

        $requestedBy = !empty($_POST['requested_by'])
            ? (int) $_POST['requested_by']
            : null;

        $approvedBy = !empty($_POST['approved_by'])
            ? (int) $_POST['approved_by']
            : null;

        $description = trim((string) ($_POST['description'] ?? ''));
        $description = $description !== '' ? $description : null;

        $updateVoucherStatement = $pdo->prepare("
            UPDATE inventory_issue_vouchers
            SET
                voucher_number = ?,
                voucher_date = ?,
                warehouse_id = ?,
                requested_by = ?,
                approved_by = ?,
                status = ?,
                description = ?
            WHERE id = ?
        ");

        $updateVoucherStatement->execute([
            $number,
            $date,
            $warehouseId,
            $requestedBy,
            $approvedBy,
            $status,
            $description,
            $id,
        ]);

        $productStatement = $pdo->prepare("
            SELECT
                COALESCE(price, 0) AS price,
                COALESCE(stock, 0) AS stock
            FROM products
            WHERE id = ?
            LIMIT 1
        ");

        $stockStatement = $pdo->prepare("
            SELECT COALESCE(
                SUM(
                    CASE
                        WHEN transaction_type IN ('purchase', 'production')
                            THEN quantity
                        WHEN transaction_type IN ('sale', 'consume', 'issue')
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
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

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
            $productId = (int) ($item['item_id'] ?? 0);
            $quantity = (float) ($item['quantity'] ?? 0);

            if ($productId <= 0 || $quantity <= 0) {
                throw new RuntimeException(
                    'اطلاعات ردیف ' . $lineNumber . ' نامعتبر است.'
                );
            }

            $productStatement->execute([$productId]);
            $product = $productStatement->fetch(PDO::FETCH_ASSOC);

            if (!$product) {
                throw new RuntimeException(
                    'کالای ردیف ' . $lineNumber . ' پیدا نشد.'
                );
            }

            $unitCost = (float) ($item['unit_cost'] ?? 0);

            if ($unitCost <= 0) {
                $unitCost = (float) $product['price'];
            }

            $stockStatement->execute([
                $productId,
                $warehouseId,
            ]);

            $stock = (float) $stockStatement->fetchColumn();

            if ($stock === 0.0) {
                $stock = (float) $product['stock'];
            }

            if (
                in_array($status, ['approved', 'issued'], true) &&
                $stock < $quantity
            ) {
                throw new RuntimeException(
                    'موجودی کالای ردیف ' . $lineNumber . ' کافی نیست.'
                );
            }

            $unit = trim((string) ($item['unit'] ?? 'piece'));

            if ($unit === '') {
                $unit = 'piece';
            }

            $totalCost = $quantity * $unitCost;

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
                (string) ($item['warehouse_location'] ?? '')
            );

            $itemDescription = trim(
                (string) ($item['description'] ?? '')
            );

            $insertItemStatement->execute([
                $id,
                $lineNumber,
                'finished_product',
                $productId,
                $quantity,
                $unit,
                $unitCost,
                $totalCost,
                $serialNumber !== '' ? $serialNumber : null,
                $batchNumber !== '' ? $batchNumber : null,
                $expireDate !== '' ? $expireDate : null,
                $warehouseLocation !== ''
                    ? $warehouseLocation
                    : null,
                $itemDescription !== ''
                    ? $itemDescription
                    : null,
            ]);

            if (in_array($status, ['approved', 'issued'], true)) {
                $createdBy = (int) (
                    $_SESSION['user']['id']
                    ?? $_SESSION['user_id']
                    ?? 0
                );

                $insertTransactionStatement->execute([
                    'finished_product',
                    $productId,
                    $warehouseId,
                    $quantity,
                    $unit,
                    $unitCost,
                    $totalCost,
                    $id,
                    $date,
                    $description,
                    $createdBy,
                ]);
            }

            $lineNumber++;
        }

        $pdo->commit();

        header('Location: view.php?id=' . $id . '&success=1');
        exit;
    }
} catch (Throwable $exception) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    $error = $exception->getMessage();
}

$page_title = 'ویرایش حواله خروج';
$pageTitle = 'ویرایش حواله خروج';
$pageDescription = 'ویرایش حواله';
$pageIcon = 'fa-solid fa-pen';




/*
 * مسیر واقعی فایل محتوای فرم ویرایش
 */
$contentFile = __DIR__ . '/views/edit_content.php';

/*
 * برای مشخص‌شدن دقیق مشکل مسیر
 */
if (!is_file($contentFile)) {
    die(
        'Edit content file not found: '
        . htmlspecialchars($contentFile, ENT_QUOTES, 'UTF-8')
    );
}

require_once __DIR__ . '/../../includes/layout.php';


echo '<pre>';

$dir = __DIR__ . '/views';

echo "Directory exists: ";
var_dump(is_dir($dir));

$files = scandir($dir);

print_r($files);

die;