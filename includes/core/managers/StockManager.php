<?php

declare(strict_types=1);

final class StockManager
{
    public function __construct(private PDO $pdo) {}

    public function lock(string $itemType, int $itemId, int $warehouseId): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM inventory WHERE item_type=? AND item_id=? AND warehouse_id=? LIMIT 1 FOR UPDATE');
        $stmt->execute([$itemType, $itemId, $warehouseId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function receive(string $itemType, int $itemId, int $warehouseId, float $quantity, float $averageCost, string $unit): void
    {
        $stock = $this->lock($itemType, $itemId, $warehouseId);
        if ($stock) {
            $stmt = $this->pdo->prepare('UPDATE inventory SET quantity=quantity+?, unit_price=?, unit=? WHERE id=?');
            $stmt->execute([$quantity, $averageCost, $unit, $stock['id']]);
            return;
        }

        $stmt = $this->pdo->prepare('INSERT INTO inventory (item_type,item_id,warehouse_id,quantity,unit,unit_price,min_stock) VALUES (?,?,?,?,?,?,0)');
        $stmt->execute([$itemType, $itemId, $warehouseId, $quantity, $unit, $averageCost]);
    }

    public function synchronize(string $itemType, int $itemId, int $warehouseId, float $quantity, float $averageCost, string $unit): void
    {
        $stock = $this->lock($itemType, $itemId, $warehouseId);
        if ($stock) {
            $stmt = $this->pdo->prepare('UPDATE inventory SET quantity=?, unit_price=?, unit=? WHERE id=?');
            $stmt->execute([$quantity, $averageCost, $unit, $stock['id']]);
            return;
        }

        if ($quantity <= 0.000001) {
            return;
        }

        $stmt = $this->pdo->prepare('INSERT INTO inventory (item_type,item_id,warehouse_id,quantity,unit,unit_price,min_stock) VALUES (?,?,?,?,?,?,0)');
        $stmt->execute([$itemType, $itemId, $warehouseId, $quantity, $unit, $averageCost]);
    }
}
