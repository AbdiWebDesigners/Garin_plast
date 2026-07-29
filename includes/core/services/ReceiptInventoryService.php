<?php

declare(strict_types=1);

require_once __DIR__ . '/../managers/StockManager.php';
require_once __DIR__ . '/../managers/AverageCost.php';

class ReceiptInventoryService
{
    private PDO $pdo;

    private StockManager $stockManager;

    private AverageCost $averageCost;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;

        $this->stockManager = new StockManager($pdo);

        $this->averageCost = new AverageCost($pdo);
    }

    /**
     * بروزرسانی موجودی انبار
     */
    public function update(
        array $items,
        int $warehouseId
    ): void
    {

        foreach ($items as $item) {

            $itemType = trim(
                $item['item_type'] ?? 'finished_product'
            );

            $itemId = (int)$item['item_id'];

            $quantity = (float)$item['quantity'];

            $unitPrice = (float)$item['unit_price'];

            $unit = trim(
                $item['unit'] ?? 'piece'
            );

            /*
            |--------------------------------------------------------------------------
            | افزایش موجودی
            |--------------------------------------------------------------------------
            */

            $this->stockManager->increaseStock(

                $itemType,

                $itemId,

                $warehouseId,

                $quantity,

                $unitPrice,

                $unit

            );

            /*
            |--------------------------------------------------------------------------
            | دریافت رکورد موجودی
            |--------------------------------------------------------------------------
            */

            $stock = $this->stockManager->getStock(

                $itemType,

                $itemId,

                $warehouseId

            );

            if (!$stock) {

                throw new Exception(
                    "Inventory Record Not Found."
                );

            }

            /*
            |--------------------------------------------------------------------------
            | محاسبه Average Cost
            |--------------------------------------------------------------------------
            */

            $currentQty = (float)$stock['quantity'];

            $currentAvg = (float)$stock['average_cost'];

            if ($currentQty > 0) {

                $newAverage = (

                    (($currentQty - $quantity) * $currentAvg)

                    +

                    ($quantity * $unitPrice)

                ) / $currentQty;

            } else {

                $newAverage = $unitPrice;

            }

            /*
            |--------------------------------------------------------------------------
            | ذخیره Average Cost
            |--------------------------------------------------------------------------
            */

            $this->stockManager->updateAverageCost(

                $itemType,

                $itemId,

                $warehouseId,

                round($newAverage, 2)

            );

        }

    }

}