<?php
$rootPath = dirname(__DIR__, 2);
require_once $rootPath . '/includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();
if (!hasPermission('manage_admin')) {
    header("Location: " . BASE_URL . "admin/dashboard.php"); 
    exit;
}

$page_title = "مدیریت مجوزهای دسترسی";

// دریافت همه مجوزها
$stmt = $pdo->query("SELECT * FROM permissions ORDER BY name ASC");
$permissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php require_once $rootPath . '/includes/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-key"></i> مدیریت مجوزهای دسترسی</h4>
        <a href="create.php" class="btn btn-primary">افزودن مجوز جدید</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>نام مجوز</th>
                            <th>برچسب فارسی</th>
                            <th>ماژول</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($permissions as $p): ?>
                        <tr>
                            <td><?= $p['id'] ?></td>
                            <td><code><?= htmlspecialchars($p['name']) ?></code></td>
                            <td><?= htmlspecialchars($p['label']) ?></td>
                            <td><?= htmlspecialchars($p['module'] ?? '-') ?></td>
                            <td>
                                <a href="edit.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-warning">ویرایش</a>
                                <a href="delete.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-danger" 
                                   onclick="return confirm('آیا از حذف این مجوز مطمئن هستید؟')">حذف</a>
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