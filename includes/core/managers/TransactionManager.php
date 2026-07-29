<?php

class TransactionManager
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * ثبت تراکنش انبار
     */
    public function insert(array $data)
{
    $sql = "
        INSERT INTO inventory_transactions
        (
            inventory_id,
            item_type,
            item_id,
            warehouse_id,
            supplier_id,
            customer_id,
            production_order_id,
            invoice_id,
            order_id,
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
        VALUES
        (
            :inventory_id,
            :item_type,
            :item_id,
            :warehouse_id,
            :supplier_id,
            :customer_id,
            :production_order_id,
            :invoice_id,
            :order_id,
            :transaction_type,
            :quantity,
            :unit,
            :unit_cost,
            :total_cost,
            :reference_type,
            :reference_id,
            :transaction_date,
            :description,
            :created_by
        )
    ";

    $stmt = $this->pdo->prepare($sql);

    $stmt->execute([
        ':inventory_id'        => $data['inventory_id'] ?? null,
        ':item_type'           => $data['item_type'],
        ':item_id'             => $data['item_id'],
        ':warehouse_id'        => $data['warehouse_id'],
        ':supplier_id'         => $data['supplier_id'] ?? null,
        ':customer_id'         => $data['customer_id'] ?? null,
        ':production_order_id' => $data['production_order_id'] ?? null,
        ':invoice_id'          => $data['invoice_id'] ?? null,
        ':order_id'            => $data['order_id'] ?? null,
        ':transaction_type'    => $data['transaction_type'],
        ':quantity'            => $data['quantity'],
        ':unit'                => $data['unit'],
        ':unit_cost'           => $data['unit_cost'] ?? 0,
        ':total_cost'          => $data['total_cost'] ?? 0,
        ':reference_type'      => $data['reference_type'] ?? null,
        ':reference_id'        => $data['reference_id'] ?? null,
        ':transaction_date'    => $data['transaction_date'],
        ':description'         => $data['description'] ?? null,
        ':created_by'          => $data['created_by'] ?? null,
    ]);

    return (int)$this->pdo->lastInsertId();
}

    /**
     * ویرایش تراکنش
     */
    public function update(int $id, array $data)
    {

    }

    /**
     * حذف تراکنش
     */
    public function delete(int $id)
    {

    }

    /**
     * دریافت تراکنش
     */
    public function find(int $id)
    {

    }
}
/**
 * دریافت همه تراکنش‌های یک کالا
 */
public function findByItem(
    string $itemType,
    int $itemId
): array
{
    $sql = "
        SELECT *
        FROM inventory_transactions
        WHERE item_type = ?
        AND item_id = ?
        ORDER BY transaction_date ASC,id ASC
    ";

    $stmt = $this->pdo->prepare($sql);

    $stmt->execute([
        $itemType,
        $itemId
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * دریافت آخرین تراکنش کالا
 */
public function getLastTransaction(
    string $itemType,
    int $itemId,
    int $warehouseId
): ?array
{
    $sql = "
        SELECT *
        FROM inventory_transactions
        WHERE item_type = ?
        AND item_id = ?
        AND warehouse_id = ?
        ORDER BY id DESC
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
 * موجودی یک کالا از روی تراکنش‌ها
 */
public function getCurrentBalance(
    string $itemType,
    int $itemId,
    int $warehouseId
): float
{
    $sql = "
        SELECT
            SUM(
                CASE

                    WHEN transaction_type IN
                    (
                        'initial_stock',
                        'purchase',
                        'production',
                        'customer_return',
                        'inventory_adjustment'
                    )

                    THEN quantity

                    ELSE -quantity

                END
            ) balance

        FROM inventory_transactions

        WHERE item_type=?

        AND item_id=?

        AND warehouse_id=?
    ";

    $stmt = $this->pdo->prepare($sql);

    $stmt->execute([
        $itemType,
        $itemId,
        $warehouseId
    ]);

    return (float)$stmt->fetchColumn();
}