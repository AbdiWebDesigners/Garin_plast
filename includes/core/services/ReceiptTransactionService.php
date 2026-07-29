<?php

require_once __DIR__ . '/../managers/TransactionManager.php';

class ReceiptTransactionService
{
    private PDO $pdo;

    private TransactionManager $transactionManager;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->transactionManager = new TransactionManager($pdo);
    }

    public function create(
        int $receiptId,
        foreach ($items as $item) {

    $itemType = $item['item_type'];

    $itemId = (int)$item['item_id'];

    $quantity = (float)$item['quantity'];

    $unit = $item['unit'];

    $unitPrice = (float)$item['unit_price'];

    $totalCost = (float)$item['total_price'];

    $description = $item['description'] ?? '';

    $this->transactionManager->createReceiptTransaction(

        $itemType,

        $itemId,

        $warehouseId,

        $supplierId,

        $quantity,

        $unit,

        $unitPrice,

        $totalCost,

        $receiptId,

        $receiptDate,

        $description,

        $createdBy

    );

}
    ): void
    {

    }
}
// TODO:
// create receipt transaction
$this->transactionService->create(
    $receiptId,
    $data['items'],
    (int)$data['warehouse_id'],
    $data['supplier_id'] ?? null,
    $data['receipt_date'],
    $_SESSION['user_id'] ?? null
);