<?php
declare(strict_types=1);

require_once __DIR__ . '/../../../includes/db.php';
require_once __DIR__ . '/../../../includes/auth.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

try {
    requireLogin();
    requirePermission('viewinventory');

    global $pdo;

    $itemId = filter_input(INPUT_GET, 'item_id', FILTER_VALIDATE_INT) ?: 0;
    $warehouseId = filter_input(INPUT_GET, 'warehouse_id', FILTER_VALIDATE_INT) ?: 0;

    if ($itemId <= 0 || $warehouseId <= 0) {
        throw new InvalidArgumentException('شناسه کالا یا انبار نامعتبر است.');
    }

    $stmt = $pdo->prepare("\n        SELECT\n            COALESCE(SUM(\n                CASE\n                    WHEN transaction_type IN ('purchase', 'production') THEN quantity\n                    WHEN transaction_type IN ('sale', 'consume', 'issue') THEN -quantity\n                    ELSE 0\n                END\n            ), 0) AS transaction_stock,\n            COUNT(*) AS transaction_count\n        FROM inventory_transactions\n        WHERE item_id = ?\n          AND warehouse_id = ?\n          AND item_type = 'finished_product'\n    ");
    $stmt->execute([$itemId, $warehouseId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

    $transactionStock = (float)($result['transaction_stock'] ?? 0);
    $transactionCount = (int)($result['transaction_count'] ?? 0);

    $fallbackStmt = $pdo->prepare('SELECT COALESCE(stock, 0) AS stock, COALESCE(price, 0) AS price FROM products WHERE id = ? LIMIT 1');
    $fallbackStmt->execute([$itemId]);
    $product = $fallbackStmt->fetch(PDO::FETCH_ASSOC);
    if (!$product) {
        throw new RuntimeException('کالای انتخاب‌شده پیدا نشد.');
    }
    $productStock = (float)$product['stock'];
    $productPrice = (float)$product['price'];

    $stock = $transactionCount > 0 ? $transactionStock : $productStock;

    echo json_encode([
        'success' => true,
        'stock' => rtrim(rtrim(number_format($stock, 3, '.', ''), '0'), '.'),
        'price' => rtrim(rtrim(number_format($productPrice, 2, '.', ''), '0'), '.'),
        'source' => $transactionCount > 0 ? 'inventory_transactions' : 'products.stock',
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'stock' => 0,
        'message' => $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}
