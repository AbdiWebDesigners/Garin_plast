public function save(int $receiptId, array $items): void
{
    if (empty($items)) {
        throw new Exception('Receipt contains no items.');
    }

    $stmt = $this->pdo->prepare("
        INSERT INTO goods_receipt_items
        (
            receipt_id,
            line_no,
            item_type,
            item_id,
            quantity,
            received_quantity,
            rejected_quantity,
            unit,
            unit_price,
            discount,
            tax,
            total_price,
            batch_number,
            expire_date,
            qc_status,
            warehouse_location,
            description
        )
        VALUES
        (
            ?,?,?,?,?,?,?,?,?,?,?,?,?,?,
            'pending',
            NULL,
            ?
        )
    ");

    $lineNo = 1;

    foreach ($items as $item) {

        $itemType = trim($item['item_type'] ?? 'finished_product');

        $itemId = (int)($item['item_id'] ?? 0);

        $quantity = (float)($item['quantity'] ?? 0);

        $unit = trim($item['unit'] ?? 'piece');

        $unitPrice = (float)($item['unit_price'] ?? 0);

        $discount = (float)($item['discount'] ?? 0);

        $tax = (float)($item['tax'] ?? 0);

        $batchNumber = trim($item['batch_number'] ?? '');

        $expireDate = !empty($item['expire_date'])
            ? $item['expire_date']
            : null;

        $description = trim($item['description'] ?? '');

        $totalPrice = ($quantity * $unitPrice)
                    - $discount
                    + $tax;

        if ($itemId <= 0) {
            throw new Exception("Invalid Item ID.");
        }

        if ($quantity <= 0) {
            throw new Exception("Invalid Quantity.");
        }

        if ($unitPrice < 0) {
            throw new Exception("Invalid Unit Price.");
        }

        $stmt->execute([
            $receiptId,
            $lineNo,
            $itemType,
            $itemId,
            $quantity,
            $quantity,
            0,
            $unit,
            $unitPrice,
            $discount,
            $tax,
            $totalPrice,
            $batchNumber,
            $expireDate,
            $description
        ]);

        $lineNo++;
    }
}