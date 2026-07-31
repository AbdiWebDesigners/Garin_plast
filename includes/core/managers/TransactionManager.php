<?php

declare(strict_types=1);

final class TransactionManager
{
    private const INBOUND_TYPES = [
        'initial_stock',
        'purchase',
        'production',
        'customer_return',
        'inventory_adjustment',
        'in',
    ];

    public function __construct(private PDO $pdo) {}

    public function insert(array $data): int
    {
        $stmt = $this->pdo->prepare("INSERT INTO inventory_transactions
            (inventory_id,item_type,item_id,warehouse_id,supplier_id,customer_id,production_order_id,invoice_id,order_id,transaction_type,quantity,unit,unit_cost,total_cost,reference_type,reference_id,transaction_date,description,created_by,created_at)
            VALUES (:inventory_id,:item_type,:item_id,:warehouse_id,:supplier_id,:customer_id,:production_order_id,:invoice_id,:order_id,:transaction_type,:quantity,:unit,:unit_cost,:total_cost,:reference_type,:reference_id,:transaction_date,:description,:created_by,NOW())");
        $stmt->execute([
            ':inventory_id' => $data['inventory_id'] ?? null,
            ':item_type' => $data['item_type'],
            ':item_id' => $data['item_id'],
            ':warehouse_id' => $data['warehouse_id'],
            ':supplier_id' => $data['supplier_id'] ?? null,
            ':customer_id' => $data['customer_id'] ?? null,
            ':production_order_id' => $data['production_order_id'] ?? null,
            ':invoice_id' => $data['invoice_id'] ?? null,
            ':order_id' => $data['order_id'] ?? null,
            ':transaction_type' => $data['transaction_type'],
            ':quantity' => $data['quantity'],
            ':unit' => $data['unit'],
            ':unit_cost' => $data['unit_cost'] ?? 0,
            ':total_cost' => $data['total_cost'] ?? 0,
            ':reference_type' => $data['reference_type'] ?? null,
            ':reference_id' => $data['reference_id'] ?? null,
            ':transaction_date' => $data['transaction_date'],
            ':description' => $data['description'] ?? null,
            ':created_by' => $data['created_by'] ?? null,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function createReceiptTransaction(array $data): int
    {
        $data['reference_type'] = 'goods_receipt';
        return $this->insert($data);
    }

    public function deleteReceiptTransactions(int $receiptId): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM inventory_transactions WHERE reference_type = 'goods_receipt' AND reference_id = ?");
        $stmt->execute([$receiptId]);
    }

    public function getReceiptStockKeys(int $receiptId): array
    {
        $stmt = $this->pdo->prepare("SELECT DISTINCT item_type, item_id, warehouse_id
            FROM inventory_transactions
            WHERE reference_type = 'goods_receipt' AND reference_id = ?");
        $stmt->execute([$receiptId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLedger(string $itemType, int $itemId, int $warehouseId): array
    {
        $stmt = $this->pdo->prepare('SELECT transaction_type, quantity, unit, unit_cost, total_cost
            FROM inventory_transactions
            WHERE item_type = ? AND item_id = ? AND warehouse_id = ?
            ORDER BY transaction_date ASC, id ASC');
        $stmt->execute([$itemType, $itemId, $warehouseId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function calculateStockSnapshot(string $itemType, int $itemId, int $warehouseId): array
    {
        $quantity = 0.0;
        $averageCost = 0.0;
        $unit = 'piece';

        foreach ($this->getLedger($itemType, $itemId, $warehouseId) as $transaction) {
            $transactionQuantity = abs((float)$transaction['quantity']);
            if ($transactionQuantity <= 0) {
                continue;
            }

            $unit = trim((string)($transaction['unit'] ?? '')) ?: $unit;
            $type = strtolower(trim((string)$transaction['transaction_type']));

            if (in_array($type, self::INBOUND_TYPES, true)) {
                $unitCost = (float)($transaction['unit_cost'] ?? 0);
                if ($unitCost <= 0 && $transactionQuantity > 0) {
                    $unitCost = (float)($transaction['total_cost'] ?? 0) / $transactionQuantity;
                }

                $newQuantity = $quantity + $transactionQuantity;
                $averageCost = $newQuantity > 0
                    ? (($quantity * $averageCost) + ($transactionQuantity * $unitCost)) / $newQuantity
                    : 0.0;
                $quantity = $newQuantity;
                continue;
            }

            $quantity -= $transactionQuantity;
            if ($quantity < -0.000001) {
                throw new RuntimeException('Inventory ledger results in negative stock.');
            }
            if ($quantity <= 0.000001) {
                $quantity = 0.0;
                $averageCost = 0.0;
            }
        }

        return [
            'quantity' => $quantity,
            'unit_price' => $averageCost,
            'unit' => $unit,
        ];
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM inventory_transactions WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function findByItem(string $itemType, int $itemId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM inventory_transactions WHERE item_type=? AND item_id=? ORDER BY transaction_date,id');
        $stmt->execute([$itemType, $itemId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCurrentBalance(string $itemType, int $itemId, int $warehouseId): float
    {
        return (float)$this->calculateStockSnapshot($itemType, $itemId, $warehouseId)['quantity'];
    }
}
