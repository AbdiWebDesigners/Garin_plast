<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-info text-white py-3 rounded-top-4">
                    <h5 class="card-title mb-0 fw-bold small"><i class="fa fa-eye me-2"></i>جزئیات دسته‌بندی</h5>
                </div>
                <div class="card-body p-4">
                    
                    <table class="table table-bordered mb-4 align-middle">
                        <tbody>
                            <tr>
                                <th class="bg-light text-muted small" style="width: 35%;">شناسه (ID) سیستم</th>
                                <td class="fw-bold"><?= (int)$category['id'] ?></td>
                            </tr>
                            <tr>
                                <th class="bg-light text-muted small">عنوان دسته‌بندی</th>
                                <td class="fw-bold text-dark fs-5"><?= htmlspecialchars($category['title'] ?? '-') ?></td>
                            </tr>
                            <tr>
                                <th class="bg-light text-muted small">نامک (Slug)</th>
                                <td style="direction: ltr; text-align: right;" class="text-secondary">
                                    <?= htmlspecialchars($category['slug'] ?? '-') ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-between border-top pt-3">
                        <a href="index.php" class="btn btn-outline-secondary px-4 shadow-sm">
                            <i class="fa fa-arrow-right me-1"></i> بازگشت به لیست
                        </a>
                        <div>
                            <a href="edit.php?id=<?= (int)$category['id'] ?>" class="btn btn-warning px-3 shadow-sm me-2">
                                <i class="fa fa-pen me-1"></i> ویرایش
                            </a>
                            <a href="index.php?delete=<?= (int)$category['id'] ?>" class="btn btn-danger px-3 shadow-sm" onclick="return confirm('آیا از حذف این دسته‌بندی مطمئن هستید؟')">
                                <i class="fa fa-trash me-1"></i> حذف
                            </a>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>