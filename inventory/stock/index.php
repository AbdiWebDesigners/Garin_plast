<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireLogin();
requirePermission('view_inventory');

global $pdo;

$error = '';
$success = '';

$pageTitle = 'موجودی انبار';
$pageDescription = 'نمایش موجودی لحظه‌ای کالاها';
$pageIcon = 'fa-solid fa-boxes-stacked';

$warehouse_id = isset($_GET['warehouse_id']) ? (int)$_GET['warehouse_id'] : 0;

try {
    $warehouses = $pdo->query("
        SELECT id, warehouse_name
        FROM warehouses
        WHERE status = 'active'
        ORDER BY warehouse_name
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $warehouses = [];
}

try {
    $sql = "
        SELECT
            p.id,
            p.title,
            p.sku,
            p.price,
            COALESCE(
                SUM(
                    CASE
                        WHEN it.transaction_type IN ('purchase', 'production') THEN it.quantity
                        WHEN it.transaction_type IN ('sale', 'consume', 'issue') THEN -it.quantity
                        ELSE 0
                    END
                ),
                0
            ) AS stock
        FROM products p
        LEFT JOIN inventory_transactions it
            ON it.item_id = p.id
            AND it.item_type = 'finished_product'
    ";

    $params = [];

    if ($warehouse_id > 0) {
        $sql .= " AND it.warehouse_id = ? ";
        $params[] = $warehouse_id;
    }

    $sql .= "
        GROUP BY p.id, p.title, p.sku, p.price
        ORDER BY p.title ASC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $stocks = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $stocks = [];
    $error = $e->getMessage();
}

$totalItems = count($stocks);
$totalStock = 0;
$totalValue = 0;

foreach ($stocks as &$row) {
    $row['stock'] = (float)$row['stock'];
    $row['price'] = (float)$row['price'];
    $row['value'] = $row['stock'] * $row['price'];

    $totalStock += $row['stock'];
    $totalValue += $row['value'];
}
unset($row);

$contentFile = __DIR__ . '/views/index_content.php';

require_once dirname(__DIR__, 2) . '/includes/layout.php';