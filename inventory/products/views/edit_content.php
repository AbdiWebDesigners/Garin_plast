<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-info text-white py-3">
                    <h5 class="card-title mb-0 fw-bold small text-uppercase">
                        <i class="fa fa-edit me-2"></i> ویرایش محصول: <?= htmlspecialchars($product['title'] ?? 'بدون نام') ?>
                    </h5>
                </div>
                <div class="card-body p-4">
                    
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                    <?php endif; ?>

                    <form method="POST" action="" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?= $product['id'] ?? '' ?>">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted small">عنوان محصول *</label>
                                <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($product['title'] ?? '') ?>">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted small">کد محصول (SKU)</label>
                                <input type="text" name="sku" class="form-control" style="direction: ltr;" value="<?= htmlspecialchars($product['sku'] ?? '') ?>">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted small">دسته‌بندی</label>
                                <select name="category_id" class="form-select" required>
                                    <option value="" disabled>انتخاب کنید...</option>
                                    <?php if (!empty($categoriesList)): ?>
                                        <?php foreach ($categoriesList as $cat): ?>
                                            <option value="<?= $cat['id'] ?>" <?= (isset($product['category_id']) && $product['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($cat['title']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted small">وضعیت</label>
                                <select name="status" class="form-select">
                                    <option value="active" <?= (isset($product['status']) && $product['status'] === 'active') ? 'selected' : '' ?>>فعال</option>
                                    <option value="inactive" <?= (isset($product['status']) && $product['status'] === 'inactive') ? 'selected' : '' ?>>غیرفعال</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted small">قیمت (تومان) *</label>
                                <input type="number" name="price" class="form-control text-start" required value="<?= htmlspecialchars($product['price'] ?? 0) ?>">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted small">موجودی *</label>
                                <input type="number" name="stock" class="form-control text-start" required value="<?= htmlspecialchars($product['stock'] ?? 0) ?>">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted small">توضیحات</label>
                            <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                        </div>

                        <div class="mb-4 border p-3 rounded bg-light">
                            <label class="form-label fw-bold text-muted small d-block">تصویر محصول</label>
                            
                            <?php if (!empty($product['image'])): ?>
                                <div class="mb-3 bg-white p-2 border rounded d-inline-block">
                                    <p class="small text-muted mb-2">تصویر فعلی:</p>
                                    <img src="../../<?= htmlspecialchars($product['image']) ?>" alt="تصویر کالا" class="img-thumbnail" style="max-height: 120px;">
                                    
                                    <div class="form-check mt-3">
                                        <input class="form-check-input" type="checkbox" name="remove_image" value="1" id="removeImage">
                                        <label class="form-check-label small text-danger fw-bold" for="removeImage">
                                            حذف تصویر فعلی (بدون جایگزینی)
                                        </label>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <label class="form-label small mt-2">آپلود تصویر جدید (اختیاری - جایگزین تصویر فعلی می‌شود)</label>
                            <input type="file" name="image_file" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                            <small class="text-muted d-block mt-1">فرمت‌های مجاز: jpg, jpeg, png, webp — حداکثر ۳ مگابایت</small>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <div>
                                <a href="index.php" class="btn btn-outline-secondary me-2">انصراف و بازگشت</a>
                            </div>
                            <button type="submit" class="btn btn-info text-white fw-bold px-4 shadow-sm">
                                <i class="fa fa-save me-1"></i> ذخیره تغییرات
                            </button>
                        </div>
                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>