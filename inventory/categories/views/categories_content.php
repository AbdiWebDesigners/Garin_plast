<div class="py-3 mb-4 bg-light border-bottom shadow-sm">
    <div class="container-fluid px-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h4 class="mb-0 text-dark fw-bold">
                    <i class="fas fa-tags me-2 text-primary"></i>دسته‌بندی‌ها
                </h4>
            </div>
            <div class="col-md-6 text-end">
                <a href="<?= BASE_URL ?? '../../' ?>admin/dashboard.php" class="text-secondary text-decoration-none me-3 small fw-bold">
                    <i class="fas fa-arrow-right me-1"></i>بازگشت به داشبورد 
                </a>
                <a href="add.php" class="btn btn-primary shadow-sm fw-bold">
                    <i class="fas fa-plus me-1"></i>افزودن دسته‌بندی
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-4 py-2">
    <?php if (isset($_GET['added'])): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="fa fa-check-circle me-1"></i> دسته‌بندی با موفقیت اضافه شد.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['updated'])): ?>
        <div class="alert alert-info alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="fa fa-check-circle me-1"></i> دسته‌بندی با موفقیت ویرایش شد.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="fa fa-trash me-1"></i> دسته‌بندی با موفقیت حذف شد.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($message)): ?>
        <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm" role="alert">
            <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            <div class="row mb-4">
                <div class="col-md-6">
                    <form method="get" class="d-flex gap-2">
                        <input type="text" name="q" class="form-control" placeholder="جستجو در عنوان یا slug..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                        <button class="btn btn-primary px-4" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" width="60">#</th>
                            <th>عنوان دسته‌بندی</th>
                            <th>نامک (Slug)</th>
                            <th class="text-center" width="180">عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($categories)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="fas fa-folder-open fa-3x d-block mb-3 text-secondary opacity-50"></i>
                                    هیچ دسته‌بندی‌ای یافت نشد.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td class="text-center fw-bold text-muted"><?= (int)$category['id'] ?></td>
                                    <td class="fw-bold text-dark"><?= htmlspecialchars($category['title'] ?? '-') ?></td>
                                    <td class="text-muted" style="direction: ltr; text-align: right;"><?= htmlspecialchars($category['slug'] ?? '-') ?></td>
                                    <td class="text-center">
                                        <a href="view.php?id=<?= (int)$category['id'] ?>" class="btn btn-sm btn-outline-info me-1" title="مشاهده">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="edit.php?id=<?= (int)$category['id'] ?>" class="btn btn-sm btn-outline-warning me-1" title="ویرایش">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        <a href="?delete=<?= (int)$category['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('آیا از حذف این دسته‌بندی مطمئن هستید؟ اطلاعات مرتبط با آن ممکن است آسیب ببیند.')" title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>