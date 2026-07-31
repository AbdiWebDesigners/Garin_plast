<?php

declare(strict_types=1);

final class Validation
{
    private const RECEIPT_TYPES = ['purchase', 'production', 'customer_return', 'inventory_adjustment'];
    private const ITEM_TYPES = ['raw_material', 'finished_product', 'packaging', 'spare_part'];

    public function receipt(array $data): array
    {
        $warehouseId = (int)($data['warehouse_id'] ?? 0);
        if ($warehouseId <= 0) {
            throw new InvalidArgumentException('Warehouse is required.');
        }

        $receiptDate = trim((string)($data['receipt_date'] ?? ''));
        if ($receiptDate === '' || strtotime($receiptDate) === false) {
            throw new InvalidArgumentException('A valid receipt date is required.');
        }

        $receiptType = trim((string)($data['receipt_type'] ?? 'purchase'));
        if (!in_array($receiptType, self::RECEIPT_TYPES, true)) {
            throw new InvalidArgumentException('Invalid receipt type.');
        }

        $items = $data['items'] ?? null;
        if (!is_array($items) || $items === []) {
            throw new InvalidArgumentException('Receipt contains no items.');
        }

        $normalizedItems = [];
        foreach ($items as $index => $item) {
            if (!is_array($item)) {
                throw new InvalidArgumentException('Invalid receipt item at line ' . ($index + 1) . '.');
            }

            $itemType = trim((string)($item['item_type'] ?? 'finished_product'));
            if (!in_array($itemType, self::ITEM_TYPES, true)) {
                throw new InvalidArgumentException('Invalid item type at line ' . ($index + 1) . '.');
            }

            $itemId = (int)($item['item_id'] ?? 0);
            $quantity = (float)($item['quantity'] ?? 0);
            $unitPrice = (float)($item['unit_price'] ?? 0);
            $discount = (float)($item['discount'] ?? 0);
            $tax = (float)($item['tax'] ?? 0);

            if ($itemId <= 0 || $quantity <= 0 || $unitPrice < 0 || $discount < 0 || $tax < 0) {
                throw new InvalidArgumentException('Invalid values at receipt line ' . ($index + 1) . '.');
            }

            $normalizedItems[] = [
                'item_type' => $itemType,
                'item_id' => $itemId,
                'quantity' => $quantity,
                'unit' => trim((string)($item['unit'] ?? 'piece')) ?: 'piece',
                'unit_price' => $unitPrice,
                'discount' => $discount,
                'tax' => $tax,
                'total_price' => ($quantity * $unitPrice) - $discount + $tax,
                'batch_number' => trim((string)($item['batch_number'] ?? '')),
                'serial_number' => trim((string)($item['serial_number'] ?? '')),
                'expire_date' => !empty($item['expire_date']) ? (string)$item['expire_date'] : null,
                'description' => trim((string)($item['description'] ?? '')),
            ];
        }

        $data['warehouse_id'] = $warehouseId;
        $data['supplier_id'] = !empty($data['supplier_id']) ? (int)$data['supplier_id'] : null;
        $data['receipt_type'] = $receiptType;
        $data['receipt_date'] = date('Y-m-d H:i:s', strtotime($receiptDate));
        $data['reference_no'] = trim((string)($data['reference_no'] ?? ''));
        $data['description'] = trim((string)($data['description'] ?? ''));
        $data['items'] = $normalizedItems;

        return $data;
    }
}
