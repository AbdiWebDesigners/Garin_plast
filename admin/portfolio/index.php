<?php
$rootPath = dirname(__DIR__, 2);
require_once $rootPath . '/includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();
if (!hasPermission('manage_admin')) {
    header("Location: " . BASE_URL . "admin/dashboard.php"); 
    exit;
}

$page_title = "نمونه‌کارها";

// دریافت همه نمونه‌کارها
$stmt = $pdo->query("SELECT * FROM portfolio ORDER BY created_at DESC");
$portfolios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php require_once $rootPath . '/includes/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-briefcase"></i> مدیریت نمونه‌کارها</h4>
        <a href="create.php" class="btn btn-primary">افزودن نمونه‌کار جدید</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>تصویر</th>
                            <th>عنوان</th>
                            <th>توضیحات</th>
                            <th>تاریخ ایجاد</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($portfolios as $p): ?>
                        <tr>
                            <td>
                                <?php if ($p['image']): ?>
                                    <img src="<?= BASE_URL . $p['image'] ?>" width="80" height="60" style="object-fit: cover;" alt="">
                                <?php else: ?>
                                    <span class="text-muted">بدون تصویر</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($p['title']) ?></td>
                            <td><?= mb_substr(htmlspecialchars($p['description'] ?? ''), 0, 80) ?>...</td>
                            <td><?= $p['created_at'] ?></td>
                            <td>
                                <a href="edit.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-warning">ویرایش</a>
                                <a href="delete.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-danger" 
                                   onclick="return confirm('آیا مطمئن هستید؟')">حذف</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once $rootPath . '/includes/footer.php'; ?>