<?php
$saveUrl = 'create.php';
$getProductsUrl = 'ajax/get_products.php';
$getStockUrl = 'ajax/get_stock.php';
?>

<?php if (!empty($error)): ?>
<div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
<div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
        <strong>ثبت حواله خروج</strong>
        <a href="index.php" class="btn btn-sm btn-secondary">بازگشت</a>
    </div>

    <div class="card-body">
        <form method="post" action="<?= htmlspecialchars($saveUrl) ?>" id="voucherForm">
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label">شماره حواله</label>
                    <input type="text" name="voucher_number" class="form-control" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">تاریخ</label>
                    <input type="datetime-local" name="voucher_date" class="form-control" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">انبار</label>
                    <select name="warehouse_id" id="warehouse_id" class="form-select" required>
                        <option value="">انتخاب انبار</option>
                        <?php foreach ($warehouses as $w): ?>
                            <option value="<?= (int)$w['id'] ?>" <?= $selectedWarehouseId === (int)$w['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($w['warehouse_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <?php
                $selectedRequestedBy = (string)($_POST['requested_by'] ?? '');
                $selectedApprovedBy = (string)($_POST['approved_by'] ?? '');
                ?>
                <div class="col-md-4">
                    <label class="form-label">درخواست‌کننده</label>
                    <select name="requested_by" class="form-select">
                        <option value="">انتخاب کنید</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= (int)$user['id'] ?>" <?= $selectedRequestedBy === (string)$user['id'] ? 'selected' : '' ?>><?= issueEscape($user['fullname']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">تأییدکننده</label>
                    <select name="approved_by" class="form-select">
                        <option value="">انتخاب کنید</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= (int)$user['id'] ?>" <?= $selectedApprovedBy === (string)$user['id'] ? 'selected' : '' ?>><?= issueEscape($user['fullname']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">وضعیت</label>
                    <select name="status" class="form-select">
                        <option value="draft">draft</option>
                        <option value="approved">approved</option>
                        <option value="issued">issued</option>
                        <option value="cancelled">cancelled</option>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">توضیحات</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>
            </div>

            <div id="warehouseHelp" class="alert alert-warning py-2 mb-3" role="alert">
                برای نمایش موجودی، ابتدا انبار را انتخاب کنید. قیمت، نوع و واحد با انتخاب کالا به‌صورت خودکار تکمیل می‌شوند.
            </div>

            <div class="table-responsive">
                <table class="table table-bordered align-middle" id="itemsTable">
                    <thead class="table-light">
                        <tr>
                            <th style="min-width:220px;">کالا</th>
                            <th style="min-width:110px;">موجودی</th>
                            <th>نوع</th>
                            <th>تعداد</th>
                            <th>واحد</th>
                            <th>قیمت واحد</th>
                            <th>سریال</th>
                            <th>Batch</th>
                            <th>انقضا</th>
                            <th>لوکیشن</th>
                            <th>توضیح</th>
                            <th>حذف</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="item-row">
                            <td>
                                <select name="items[0][item_id]" class="form-select product-select" required>
                                    <option value="">انتخاب کالا</option>
                                    <?php foreach ($products as $p): ?>
                                        <option value="<?= (int)$p['id'] ?>"
                                                data-price="<?= htmlspecialchars((string)($p['price'] ?? 0), ENT_QUOTES, 'UTF-8') ?>"
                                                data-item-type="finished_product"
                                                data-unit="piece">
                                            <?= htmlspecialchars($p['title']) ?>
                                            <?php if (!empty($p['sku'])): ?>
                                                (<?= htmlspecialchars($p['sku']) ?>)
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><input type="text" class="form-control stock-box ltr-field" value="ابتدا انبار" readonly></td>
                            <td><input type="text" name="items[0][item_type]" class="form-control ltr-field item-type-box" value="finished_product" readonly></td>
                            <td><input type="number" min="0.001" step="0.001" name="items[0][quantity]" class="form-control ltr-field quantity-box" required></td>
                            <td><input type="text" name="items[0][unit]" class="form-control ltr-field unit-box" value="piece"></td>
                            <td><input type="number" min="0" step="0.01" name="items[0][unit_cost]" class="form-control ltr-field unit-cost-box" value="0"></td>
                            <td><input type="text" name="items[0][serial_number]" class="form-control"></td>
                            <td><input type="text" name="items[0][batch_number]" class="form-control"></td>
                            <td><input type="date" name="items[0][expire_date]" class="form-control"></td>
                            <td><input type="text" name="items[0][warehouse_location]" class="form-control"></td>
                            <td><input type="text" name="items[0][description]" class="form-control"></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="d-flex gap-2 mt-3">
                <button type="button" class="btn btn-outline-primary" onclick="addRow()">افزودن ردیف</button>
                <button type="submit" class="btn btn-success">ثبت</button>
            </div>
        </form>
    </div>
</div>

<style>
/* مقادیر فنی انگلیسی در صفحه RTL از سمت چپ نمایش داده شوند تا بریده/وارونه دیده نشوند. */
#itemsTable .ltr-field {
    direction: ltr;
    text-align: left;
    min-width: 105px;
}
#itemsTable .item-type-box { min-width: 155px; }
#itemsTable .quantity-box { min-width: 100px; }
#itemsTable .unit-box { min-width: 100px; }
#itemsTable .unit-cost-box { min-width: 115px; }
#itemsTable input[readonly] { background-color: #f4f6f8; }
</style>

<script>
let rowIndex = 1;
const getStockUrl = <?= json_encode($getStockUrl, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

function currentWarehouseId() {
    const el = document.getElementById('warehouse_id');
    return el ? el.value : '';
}

function selectedStatus() {
    return document.querySelector('[name="status"]')?.value || 'draft';
}

function updateWarehouseHelp() {
    const help = document.getElementById('warehouseHelp');
    if (!help) return;
    if (currentWarehouseId()) {
        help.className = 'alert alert-success py-2 mb-3';
        help.textContent = 'انبار انتخاب شد؛ موجودی هر کالا پس از انتخاب نمایش داده می‌شود.';
    } else {
        help.className = 'alert alert-warning py-2 mb-3';
        help.textContent = 'برای نمایش موجودی، ابتدا انبار را انتخاب کنید. قیمت، نوع و واحد با انتخاب کالا به‌صورت خودکار تکمیل می‌شوند.';
    }
}

function applyProductDefaults(selectEl) {
    const row = selectEl.closest('tr');
    const option = selectEl.options[selectEl.selectedIndex];
    const priceBox = row.querySelector('.unit-cost-box');
    const itemTypeBox = row.querySelector('.item-type-box');
    const unitBox = row.querySelector('.unit-box');

    if (!selectEl.value) return;

    if (itemTypeBox) itemTypeBox.value = option?.dataset.itemType || 'finished_product';
    if (unitBox) unitBox.value = option?.dataset.unit || 'piece';
    if (priceBox && (priceBox.value === '' || Number(priceBox.value) === 0)) {
        priceBox.value = option?.dataset.price || '0';
    }
}

async function loadStock(selectEl) {
    const row = selectEl.closest('tr');
    const stockBox = row.querySelector('.stock-box');
    const priceBox = row.querySelector('.unit-cost-box');
    const quantityBox = row.querySelector('.quantity-box');
    const itemId = selectEl.value;
    const warehouseId = currentWarehouseId();

    applyProductDefaults(selectEl);

    if (!itemId) {
        stockBox.value = warehouseId ? '0' : 'ابتدا انبار';
        quantityBox?.removeAttribute('max');
        return;
    }

    if (!warehouseId) {
        stockBox.value = 'ابتدا انبار';
        quantityBox?.removeAttribute('max');
        return;
    }

    stockBox.value = 'در حال دریافت...';

    try {
        const url = getStockUrl + '?item_id=' + encodeURIComponent(itemId) + '&warehouse_id=' + encodeURIComponent(warehouseId) + '&_=' + Date.now();
        const response = await fetch(url, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
            cache: 'no-store'
        });

        const raw = await response.text();
        let data;
        try {
            data = JSON.parse(raw);
        } catch (_) {
            throw new Error('پاسخ سرور JSON نیست: ' + raw.slice(0, 180));
        }

        if (!response.ok || !data.success) {
            throw new Error(data.message || 'خطا در دریافت اطلاعات کالا');
        }

        stockBox.value = data.stock ?? '0';
        if (priceBox && (priceBox.value === '' || Number(priceBox.value) === 0)) {
            priceBox.value = data.price ?? '0';
        }

        const stock = Number(data.stock);
        if (quantityBox && Number.isFinite(stock) && stock >= 0) {
            quantityBox.max = String(stock);
        } else {
            quantityBox?.removeAttribute('max');
        }
    } catch (error) {
        stockBox.value = 'خطا';
        quantityBox?.removeAttribute('max');
        console.error('get_stock error:', error);
        alert('دریافت موجودی ناموفق بود. جزئیات خطا در Console مرورگر ثبت شد.');
    }
}

document.addEventListener('change', function(e) {
    if (e.target.classList.contains('product-select')) {
        loadStock(e.target);
    }

    if (e.target.id === 'warehouse_id') {
        updateWarehouseHelp();
        document.querySelectorAll('#itemsTable tbody tr.item-row .product-select').forEach(loadStock);
    }
});

document.getElementById('voucherForm').addEventListener('submit', function(e) {
    const warehouseId = currentWarehouseId();
    if (!warehouseId) {
        e.preventDefault();
        document.getElementById('warehouse_id')?.focus();
        alert('ابتدا انبار را انتخاب کنید.');
        return;
    }

    // پیش‌نویس موجودی را کم نمی‌کند؛ کنترل موجودی فقط برای approved و issued انجام شود.
    if (!['approved', 'issued'].includes(selectedStatus())) return;

    for (const row of document.querySelectorAll('#itemsTable tbody tr.item-row')) {
        const product = row.querySelector('.product-select');
        const quantity = row.querySelector('.quantity-box');
        const stockValue = row.querySelector('.stock-box')?.value ?? '0';
        const stock = Number(stockValue);

        if (product?.value && quantity && Number.isFinite(stock) && Number(quantity.value) > stock) {
            e.preventDefault();
            quantity.focus();
            alert('تعداد واردشده بیشتر از موجودی این کالا است. موجودی فعلی: ' + stock);
            return;
        }
    }
});

function addRow() {
    const tbody = document.querySelector('#itemsTable tbody');
    const first = tbody.querySelector('tr.item-row');
    const clone = first.cloneNode(true);

    clone.querySelectorAll('input').forEach(i => {
        if (i.classList.contains('stock-box')) {
            i.value = currentWarehouseId() ? '0' : 'ابتدا انبار';
        } else if (i.name.includes('[item_type]')) {
            i.value = 'finished_product';
        } else if (i.name.includes('[unit]')) {
            i.value = 'piece';
        } else if (i.name.includes('[unit_cost]')) {
            i.value = '0';
        } else {
            i.value = '';
        }
        if (i.classList.contains('quantity-box')) i.removeAttribute('max');
    });

    clone.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
    clone.querySelectorAll('[name]').forEach(el => {
        el.name = el.name.replace(/\[\d+\]/, '[' + rowIndex + ']');
    });

    clone.lastElementChild.innerHTML = '<button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">×</button>';
    tbody.appendChild(clone);
    rowIndex++;
}

function removeRow(btn) {
    const tbody = document.querySelector('#itemsTable tbody');
    if (tbody.querySelectorAll('tr').length > 1) btn.closest('tr').remove();
}

updateWarehouseHelp();
</script>