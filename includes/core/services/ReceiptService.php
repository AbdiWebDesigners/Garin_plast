/*
|--------------------------------------------------------------------------
| ثبت Header رسید
|--------------------------------------------------------------------------
*/

$sql = "
    INSERT INTO goods_receipts
    (
        warehouse_id,
        supplier_id,
        receipt_date,
        description,
        created_by,
        created_at
    )
    VALUES
    (
        ?,
        ?,
        ?,
        ?,
        ?,
        NOW()
    )
";

$stmt = $this->pdo->prepare($sql);

$stmt->execute([
    $warehouseId,
    $supplierId,
    $receiptDate,
    $description,
    $createdBy
]);

$receiptId = (int)$this->pdo->lastInsertId();

if ($receiptId <= 0) {

    throw new Exception("خطا در ایجاد رسید انبار.");

}
        return [

            'success' => true,

            'message' => 'Validation Passed'

        ];

    }

    catch (Exception $e) {

        if ($this->pdo->inTransaction()) {

            $this->pdo->rollBack();

        }

        return [

            'success' => false,

            'message' => $e->getMessage()

        ];

    }

}