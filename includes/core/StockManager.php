<?php

class StockManager
{
    /**
     * @var PDO
     */
    private PDO $pdo;

    /**
     * Constructor
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * برگرداندن رکورد موجودی
     */
    public function getStock(
        string $itemType,
        int $itemId,
        int $warehouseId
    ): ?array {

        $sql = "
            SELECT *
            FROM inventory
            WHERE item_type = ?
              AND item_id = ?
              AND warehouse_id = ?
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            $itemType,
            $itemId,
            $warehouseId
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /**
     * اگر رکورد موجودی وجود نداشت ایجاد شود
     */
    public function ensureInventoryRecord(
        string $itemType,
        int $itemId,
        int $warehouseId,
        string $unit = 'piece'
    ): int {

        $stock = $this->getStock(
            $itemType,
            $itemId,
            $warehouseId
        );

        if ($stock) {
            return (int)$stock['id'];
        }

        $sql = "
            INSERT INTO inventory
            (
                item_type,
                item_id,
                warehouse_id,
                quantity,
                reserved_quantity,
                average_cost,
                unit
            )
            VALUES
            (
                ?,
                ?,
                ?,
                0,
                0,
                0,
                ?
            )
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            $itemType,
            $itemId,
            $warehouseId,
            $unit
        ]);

        return (int)$this->pdo->lastInsertId();
    }
    /**
     * افزایش موجودی کالا
     */
    public function increaseStock(
        string $itemType,
        int $itemId,
        int $warehouseId,
        float $quantity,
        float $unitCost = 0,
        string $unit = 'piece'
    ): bool {

        $inventoryId = $this->ensureInventoryRecord(
            $itemType,
            $itemId,
            $warehouseId,
            $unit
        );

        $sql = "
            UPDATE inventory
            SET
                quantity = quantity + ?,
                last_updated = NOW()
            WHERE id = ?
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            $quantity,
            $inventoryId
        ]);
    }

    /**
     * کاهش موجودی کالا
     */
    public function decreaseStock(
        string $itemType,
        int $itemId,
        int $warehouseId,
        float $quantity
    ): bool {

        $stock = $this->getStock(
            $itemType,
            $itemId,
            $warehouseId
        );

        if (!$stock) {
            throw new Exception("موجودی کالا یافت نشد.");
        }

        if ((float)$stock['quantity'] < $quantity) {
            throw new Exception("موجودی کالا کافی نیست.");
        }

        $sql = "
            UPDATE inventory
            SET
                quantity = quantity - ?,
                last_updated = NOW()
            WHERE id = ?
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            $quantity,
            $stock['id']
        ]);
    }

    /**
     * رزرو موجودی
     */
    public function reserveStock(
        string $itemType,
        int $itemId,
        int $warehouseId,
        float $quantity
    ): bool {

        $stock = $this->getStock(
            $itemType,
            $itemId,
            $warehouseId
        );

        if (!$stock) {
            throw new Exception("موجودی کالا یافت نشد.");
        }

        $available =
            (float)$stock['quantity']
            -
            (float)$stock['reserved_quantity'];

        if ($available < $quantity) {
            throw new Exception("موجودی قابل رزرو کافی نیست.");
        }

        $sql = "
            UPDATE inventory
            SET
                reserved_quantity = reserved_quantity + ?,
                last_updated = NOW()
            WHERE id = ?
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            $quantity,
            $stock['id']
        ]);
    }
    /**
     * آزادسازی موجودی رزرو شده
     */
    public function releaseReservedStock(
        string $itemType,
        int $itemId,
        int $warehouseId,
        float $quantity
    ): bool {

        $stock = $this->getStock(
            $itemType,
            $itemId,
            $warehouseId
        );

        if (!$stock) {
            throw new Exception("رکورد موجودی یافت نشد.");
        }

        if ((float)$stock['reserved_quantity'] < $quantity) {
            throw new Exception("مقدار رزرو شده کافی نیست.");
        }

        $sql = "
            UPDATE inventory
            SET
                reserved_quantity = reserved_quantity - ?,
                last_updated = NOW()
            WHERE id = ?
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            $quantity,
            $stock['id']
        ]);
    }

    /**
     * موجودی قابل استفاده
     */
    public function getAvailableStock(
        string $itemType,
        int $itemId,
        int $warehouseId
    ): float {

        $stock = $this->getStock(
            $itemType,
            $itemId,
            $warehouseId
        );

        if (!$stock) {
            return 0;
        }

        return
            (float)$stock['quantity']
            -
            (float)$stock['reserved_quantity'];
    }

    /**
     * دریافت میانگین قیمت
     */
    public function getAverageCost(
        string $itemType,
        int $itemId,
        int $warehouseId
    ): float {

        $stock = $this->getStock(
            $itemType,
            $itemId,
            $warehouseId
        );

        if (!$stock) {
            return 0;
        }

        return (float)$stock['average_cost'];
    }

    /**
     * بروزرسانی میانگین قیمت
     */
    public function updateAverageCost(
        string $itemType,
        int $itemId,
        int $warehouseId,
        float $newAverage
    ): bool {

        $stock = $this->getStock(
            $itemType,
            $itemId,
            $warehouseId
        );

        if (!$stock) {
            throw new Exception("رکورد موجودی یافت نشد.");
        }

        $sql = "
            UPDATE inventory
            SET
                average_cost = ?,
                last_updated = NOW()
            WHERE id = ?
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            $newAverage,
            $stock['id']
        ]);
    }

    /**
     * دریافت شناسه رکورد موجودی
     */
    public function getInventoryId(
        string $itemType,
        int $itemId,
        int $warehouseId
    ): ?int {

        $stock = $this->getStock(
            $itemType,
            $itemId,
            $warehouseId
        );

        if (!$stock) {
            return null;
        }

        return (int)$stock['id'];
    }
/**
 * آیا موجودی کافی است؟
 */
public function hasEnoughStock(
    string $itemType,
    int $itemId,
    int $warehouseId,
    float $quantity
): bool {

    return $this->getAvailableStock(
        $itemType,
        $itemId,
        $warehouseId
    ) >= $quantity;
}