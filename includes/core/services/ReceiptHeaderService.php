<?php

declare(strict_types=1);

class ReceiptHeaderService
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * ثبت هدر رسید انبار
     *
     * @throws Exception
     */
    public function save(array $data): int
    {
        $warehouseId = (int)($data['warehouse_id'] ?? 0);

        $supplierId = !empty($data['supplier_id'])
            ? (int)$data['supplier_id']
            : null;

        $receiptDate = trim($data['receipt_date'] ?? '');

        $receiptType = trim(
            $data['receipt_type'] ?? 'purchase'
        );

        $referenceNo = trim(
            $data['reference_no'] ?? ''
        );

        $description = trim(
            $data['description'] ?? ''
        );

        $createdBy = $_SESSION['user_id'] ?? null;

        if ($warehouseId <= 0) {
            throw new Exception('Warehouse is required.');
        }

        if ($receiptDate === '') {
            throw new Exception('Receipt date is required.');
        }

        /*
        |--------------------------------------------------------------------------
        | Generate Receipt Number
        |--------------------------------------------------------------------------
        */

        $receiptNumber = 'GR-' . date('YmdHis');

        /*
        |--------------------------------------------------------------------------
        | Insert Header
        |--------------------------------------------------------------------------
        */

        $sql = "
            INSERT INTO goods_receipts
            (
                receipt_number,
                receipt_type,
                supplier_id,
                warehouse_id,
                receipt_date,
                reference_no,
                description,
                status,
                created_by,
                approved_by,
                created_at
            )
            VALUES
            (
                :receipt_number,
                :receipt_type,
                :supplier_id,
                :warehouse_id,
                :receipt_date,
                :reference_no,
                :description,
                'approved',
                :created_by,
                :approved_by,
                NOW()
            )
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([

            ':receipt_number' => $receiptNumber,

            ':receipt_type'   => $receiptType,

            ':supplier_id'    => $supplierId,

            ':warehouse_id'   => $warehouseId,

            ':receipt_date'   => $receiptDate,

            ':reference_no'   => $referenceNo,

            ':description'    => $description,

            ':created_by'     => $createdBy,

            ':approved_by'    => $createdBy

        ]);

        $receiptId = (int)$this->pdo->lastInsertId();

        if ($receiptId <= 0) {
            throw new Exception(
                'Unable to create receipt header.'
            );
        }

        return $receiptId;
    }
}