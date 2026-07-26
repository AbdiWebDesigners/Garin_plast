<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-warning text-dark py-3 rounded-top-4">
                    <h5 class="card-title mb-0 fw-bold small"><i class="fa fa-pen me-2"></i>ویرایش دسته‌بندی</h5>
                </div>
                <div class="card-body p-4">
                    <?php if (!empty($message)): ?>
                        <div class="alert alert-danger small"><?= htmlspecialchars($message) ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small">عنوان دسته‌بندی *</label>
                            <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($_POST['title'] ?? $category['title'] ?? '') ?>">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted small">نامک (Slug)</label>
                            <input type="text" name="slug" class="form-control text-start" style="direction: ltr;" value="<?= htmlspecialchars($_POST['slug'] ?? $category['slug'] ?? '') ?>">
                        </div>

                        <div class="d-flex justify-content-between border-top pt-3">
                            <a href="index.php" class="btn btn-outline-secondary px-4">انصراف</a>
                            <button type="submit" class="btn btn-warning px-4 fw-bold shadow-sm">
                                <i class="fa fa-save me-1"></i> بروزرسانی دسته‌بندی
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>