<?php

require_once __DIR__ . '/StockManager.php';
require_once __DIR__ . '/TransactionManager.php';
require_once __DIR__ . '/AverageCost.php';
require_once __DIR__ . '/Validation.php';

class ReceiptService
{

    private PDO $pdo;

    private StockManager $stock;

    private TransactionManager $transaction;

    private AverageCost $averageCost;

    private Validation $validation;

    public function __construct(PDO $pdo)
    {

        $this->pdo = $pdo;

        $this->stock = new StockManager($pdo);

        $this->transaction = new TransactionManager($pdo);

        $this->averageCost = new AverageCost($pdo);

        $this->validation = new Validation();

    }
