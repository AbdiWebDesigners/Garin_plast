<?php
declare(strict_types=1);

require_once __DIR__ . '/../../../includes/db.php';
require_once __DIR__ . '/../../../includes/auth.php';

requireLogin();
requirePermission('viewinventory');

global $pdo;

header('Content-Type: application/json; charset=utf-8');

$warehouse_id = filter_input(INPUT_GET, 'warehouse_id', FILTER_VALIDATE_INT) ?: 0;
$item_type = trim((string)($_GET['item_type'] ?? 'finished_product'));

if ($warehouse_id <= 0) {
    echo json_encode([
        'success' => false,
        'html' => '<option value="">ابتدا انبار را انتخاب کنید</option>'
    ]);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT
            p.id,
            p.title,
            p.sku
        FROM products p
        WHERE (p.status = 'active' OR p.status IS NULL OR p.status = '')
        ORDER BY p.title ASC
    ");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $html = '<option value="">انتخاب کالا</option>';

    foreach ($products as $product) {
        $label = $product['title'];
        if (!empty($product['sku'])) {
            $label .= ' (' . $product['sku'] . ')';
        }
        $html .= '<option value="' . (int)$product['id'] . '">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</option>';
    }

    echo json_encode([
        'success' => true,
        'html' => $html
    ]);
} catch (Throwable $e) {
    echo json_encode([
        'success' => false,
        'html' => '<option value="">خطا در دریافت کالاها</option>',
        'error' => $e->getMessage()
    ]);
}