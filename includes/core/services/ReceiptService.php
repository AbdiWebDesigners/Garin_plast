<?php

declare(strict_types=1);

require_once __DIR__ . '/Validation.php';

require_once __DIR__ . '/ReceiptHeaderService.php';
require_once __DIR__ . '/ReceiptItemService.php';
require_once __DIR__ . '/ReceiptInventoryService.php';
require_once __DIR__ . '/ReceiptTransactionService.php';

class ReceiptService
{
    private PDO $pdo;

    private Validation $validation;

    private ReceiptHeaderService $headerService;

    private ReceiptItemService $itemService;

    private ReceiptInventoryService $inventoryService;

    private ReceiptTransactionService $transactionService;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;

        $this->validation = new Validation();

        $this->headerService = new ReceiptHeaderService($pdo);

        $this->itemService = new ReceiptItemService($pdo);

        $this->inventoryService = new ReceiptInventoryService($pdo);

        $this->transactionService = new ReceiptTransactionService($pdo);
    }

    /**
     * ثبت کامل رسید انبار
     */
    public function store(array $data): array
    {
        try {

            /*
            |--------------------------------------------------------------------------
            | Validation
            |--------------------------------------------------------------------------
            */

            if (empty($data['warehouse_id'])) {
                throw new Exception('Warehouse is required.');
            }

            if (empty($data['receipt_date'])) {
                throw new Exception('Receipt date is required.');
            }

            if (
                empty($data['items']) ||
                !is_array($data['items'])
            ) {
                throw new Exception('Receipt contains no items.');
            }

            /*
            |--------------------------------------------------------------------------
            | Begin Transaction
            |--------------------------------------------------------------------------
            */

            $this->pdo->beginTransaction();

            /*
            |--------------------------------------------------------------------------
            | Header
            |--------------------------------------------------------------------------
            */

            $receiptId = $this->headerService->save($data);

            /*
            |--------------------------------------------------------------------------
            | Items
            |--------------------------------------------------------------------------
            */

            $this->itemService->save(
                $receiptId,
                $data['items']
            );

            /*
            |--------------------------------------------------------------------------
            | Inventory
            |--------------------------------------------------------------------------
            */

            $this->inventoryService->update(
                $data['items'],
                (int)$data['warehouse_id']
            );

            /*
            |--------------------------------------------------------------------------
            | Inventory Transactions
            |--------------------------------------------------------------------------
            */

            $this->transactionService->create(
                $receiptId,
                $data['items'],
                (int)$data['warehouse_id'],
                !empty($data['supplier_id'])
                    ? (int)$data['supplier_id']
                    : null,
                $data['receipt_date'],
                $_SESSION['user_id'] ?? null
            );

            /*
            |--------------------------------------------------------------------------
            | Commit
            |--------------------------------------------------------------------------
            */

            $this->pdo->commit();

            return [
                'success'    => true,
                'receipt_id' => $receiptId,
                'message'    => 'Receipt saved successfully.'
            ];

        } catch (Throwable $e) {

            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}