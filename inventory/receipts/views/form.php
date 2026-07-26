<?php
$isEdit = !empty($receipt);
$itemsData = $isEdit && !empty($receipt_items) ? $receipt_items : [
    [
        'line_no' => 1,
        'item_type' => 'finished_product',
        'item_id' => '',
        'quantity' => '',
        'unit' => 'piece',
        'unit_price' => '',
        'discount' => 0,
        'tax' => 0,
        'serial_number' => '',
        'expire_date' => '',
        'description' => ''
    ]
];
?>

<div class="container-fluid" dir="rtl">
    <div class="row g-0">
        <?php include __DIR__ . '/../../includes/sidebar.php'; ?>

        <main class="col-12 col-md-10 py-4 px-3 px-lg-4">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
                <div>
                    <h3 class="fw-bold mb-1"><?= $isEdit ? 'ویرایش رسید انبار' : 'ثبت رسید انبار' ?></h3>
                    <div class="text-muted">ایجاد رسید ورود کالا به انبار</div>
                </div>
                <div class="mt-2 mt-md-0">
                    <a href="index.php" class="btn btn-outline-secondary">
                        <i class="fa fa-arrow-right ms-1"></i> بازگشت
                    </a>
                </div>
            </div>

            <form method="post" action="<?= $isEdit ? 'update.php?id=' . (int)$receipt['id'] : 'store.php' ?>">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white">
                        <h5 class="fw-bold mb-0">اطلاعات هدر رسید</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">نوع رسید</label>
                                <select name="receipt_type" class="form-select" required>
                                    <option value="purchase" <?= (($receipt['receipt_type'] ?? 'purchase') === 'purchase') ? 'selected' : '' ?>>خرید</option>
                                    <option value="production" <?= (($receipt['receipt_type'] ?? '') === 'production') ? 'selected' : '' ?>>تولید</option>
                                    <option value="customer_return" <?= (($receipt['receipt_type'] ?? '') === 'customer_return') ? 'selected' : '' ?>>برگشت مشتری</option>
                                    <option value="inventory_adjustment" <?= (($receipt['receipt_type'] ?? '') === 'inventory_adjustment') ? 'selected' : '' ?>>اصلاح موجودی</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">انبار</label>
                                <select name="warehouse_id" class="form-select" required>
                                    <option value="">انتخاب کنید</option>
                                    <?php foreach (($warehouses ?? []) as $warehouse): ?>
                                        <option value="<?= (int)$warehouse['id'] ?>" <?= ($isEdit && (int)$receipt['warehouse_id'] === (int)$warehouse['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($warehouse['warehouse_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">تأمین‌کننده</label>
                                <select name="supplier_id" class="form-select">
                                    <option value="">بدون تأمین‌کننده</option>
                                    <?php foreach (($suppliers ?? []) as $supplier): ?>
                                        <option value="<?= (int)$supplier['id'] ?>" <?= ($isEdit && (int)($receipt['supplier_id'] ?? 0) === (int)$supplier['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($supplier['company_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">تاریخ رسید</label>
                                <input type="datetime-local" name="receipt_date" class="form-control"
                                       value="<?= htmlspecialchars(isset($receipt['receipt_date']) ? date('Y-m-d\TH:i', strtotime($receipt['receipt_date'])) : date('Y-m-d\TH:i')) ?>" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">شماره مرجع</label>
                                <input type="text" name="reference_no" class="form-control"
                                       value="<?= htmlspecialchars($receipt['reference_no'] ?? '') ?>">
                            </div>

                            <div class="col-md-8">
                                <label class="form-label">توضیحات</label>
                                <input type="text" name="description" class="form-control"
                                       value="<?= htmlspecialchars($receipt['description'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">آیتم‌های رسید</h5>
                        <button type="button" class="btn btn-sm btn-success" id="addRowBtn">
                            <i class="fa fa-plus ms-1"></i> افزودن ردیف
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0" id="itemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th>نوع</th>
                                    <th>کالا</th>
                                    <th>تعداد</th>
                                    <th>واحد</th>
                                    <th>قیمت واحد</th>
                                    <th>تخفیف</th>
                                    <th>مالیات</th>
                                    <th>شماره سری</th>
                                    <th>انقضا</th>
                                    <th>توضیحات</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($itemsData as $idx => $item): ?>
                                    <tr>
                                        <td>
                                            <input type="hidden" name="items[<?= $idx ?>][item_type]" value="<?= htmlspecialchars($item['item_type'] ?? 'finished_product') ?>">
                                            <span class="badge bg-secondary"><?= htmlspecialchars($item['item_type'] ?? 'finished_product') ?></span>
                                        </td>
                                        <td>
                                            <select name="items[<?= $idx ?>][item_id]" class="form-select" required>
                                                <option value="">انتخاب کالا</option>
                                                <?php foreach (($products ?? []) as $product): ?>
                                                    <option value="<?= (int)$product['id'] ?>" <?= ((int)($item['item_id'] ?? 0) === (int)$product['id']) ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($product['title']) ?><?= !empty($product['sku']) ? ' - ' . htmlspecialchars($product['sku']) : '' ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td><input type="number" step="0.001" min="0" name="items[<?= $idx ?>][quantity]" class="form-control" value="<?= htmlspecialchars($item['quantity'] ?? '') ?>" required></td>
                                        <td>
                                            <select name="items[<?= $idx ?>][unit]" class="form-select">
                                                <option value="piece" <?= (($item['unit'] ?? 'piece') === 'piece') ? 'selected' : '' ?>>piece</option>
                                                <option value="kg" <?= (($item['unit'] ?? '') === 'kg') ? 'selected' : '' ?>>kg</option>
                                                <option value="g" <?= (($item['unit'] ?? '') === 'g') ? 'selected' : '' ?>>g</option>
                                                <option value="ton" <?= (($item['unit'] ?? '') === 'ton') ? 'selected' : '' ?>>ton</option>
                                                <option value="roll" <?= (($item['unit'] ?? '') === 'roll') ? 'selected' : '' ?>>roll</option>
                                                <option value="pack" <?= (($item['unit'] ?? '') === 'pack') ? 'selected' : '' ?>>pack</option>
                                            </select>
                                        </td>
                                        <td><input type="number" step="0.01" min="0" name="items[<?= $idx ?>][unit_price]" class="form-control" value="<?= htmlspecialchars($item['unit_price'] ?? '') ?>" required></td>
                                        <td><input type="number" step="0.01" min="0" name="items[<?= $idx ?>][discount]" class="form-control" value="<?= htmlspecialchars($item['discount'] ?? 0) ?>"></td>
                                        <td><input type="number" step="0.01" min="0" name="items[<?= $idx ?>][tax]" class="form-control" value="<?= htmlspecialchars($item['tax'] ?? 0) ?>"></td>
                                        <td><input type="text" name="items[<?= $idx ?>][serial_number]" class="form-control" value="<?= htmlspecialchars($item['serial_number'] ?? '') ?>"></td>
                                        <td><input type="date" name="items[<?= $idx ?>][expire_date]" class="form-control" value="<?= htmlspecialchars($item['expire_date'] ?? '') ?>"></td>
                                        <td><input type="text" name="items[<?= $idx ?>][description]" class="form-control" value="<?= htmlspecialchars($item['description'] ?? '') ?>"></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-danger remove-row" <?= (count($itemsData) === 1) ? 'disabled' : '' ?>>
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save ms-1"></i> ذخیره رسید
                    </button>
                </div>
            </form>
        </main>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let rowIndex = <?= count($itemsData) ?>;
    const tableBody = document.querySelector('#itemsTable tbody');
    const addBtn = document.getElementById('addRowBtn');

    if (!tableBody || !addBtn) return;

    const productsHtml = <?= json_encode(
        '<option value="">انتخاب کالا</option>' .
        implode('', array_map(function ($product) {
            $text = htmlspecialchars($product['title'], ENT_QUOTES, 'UTF-8');
            if (!empty($product['sku'])) {
                $text .= ' - ' . htmlspecialchars($product['sku'], ENT_QUOTES, 'UTF-8');
            }
            return '<option value="' . (int)$product['id'] . '">' . $text . '</option>';
        }, $products ?? []))
    ) ?>;

    function refreshRemoveButtons() {
        const rows = tableBody.querySelectorAll('tr');
        rows.forEach(row => {
            const btn = row.querySelector('.remove-row');
            if (btn) btn.disabled = rows.length === 1;
        });
    }

    addBtn.addEventListener('click', function () {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>
                <input type="hidden" name="items[${rowIndex}][item_type]" value="finished_product">
                <span class="badge bg-secondary">finished_product</span>
            </td>
            <td>
                <select name="items[${rowIndex}][item_id]" class="form-select" required>
                    ${productsHtml}
                </select>
            </td>
            <td><input type="number" step="0.001" min="0" name="items[${rowIndex}][quantity]" class="form-control" required></td>
            <td>
                <select name="items[${rowIndex}][unit]" class="form-select">
                    <option value="piece">piece</option>
                    <option value="kg">kg</option>
                    <option value="g">g</option>
                    <option value="ton">ton</option>
                    <option value="roll">roll</option>
                    <option value="pack">pack</option>
                </select>
            </td>
            <td><input type="number" step="0.01" min="0" name="items[${rowIndex}][unit_price]" class="form-control" required></td>
            <td><input type="number" step="0.01" min="0" name="items[${rowIndex}][discount]" class="form-control" value="0"></td>
            <td><input type="number" step="0.01" min="0" name="items[${rowIndex}][tax]" class="form-control" value="0"></td>
            <td><input type="text" name="items[${rowIndex}][serial_number]" class="form-control"></td>
            <td><input type="date" name="items[${rowIndex}][expire_date]" class="form-control"></td>
            <td><input type="text" name="items[${rowIndex}][description]" class="form-control"></td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger remove-row">
                    <i class="fa fa-trash"></i>
                </button>
            </td>
        `;
        tableBody.appendChild(tr);
        rowIndex++;
        refreshRemoveButtons();
    });

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.remove-row');
        if (!btn) return;
        const row = btn.closest('tr');
        if (row && tableBody.querySelectorAll('tr').length > 1) {
            row.remove();
            refreshRemoveButtons();
        }
    });

    refreshRemoveButtons();
});
</script>