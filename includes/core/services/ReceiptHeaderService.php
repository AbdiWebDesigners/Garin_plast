<?php

class ReceiptHeaderService
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function save(array $data): int
    {
        // INSERT goods_receipts

        return $receiptId;
    }
}