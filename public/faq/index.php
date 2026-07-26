<?php
$rootPath = dirname(__DIR__, 2);
require_once $rootPath . '/includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();
if (!hasPermission('manage_admin')) {
    header("Location: " . BASE_URL . "admin/dashboard.php"); 
    exit;
}

$page_title = "سوالات متداول (FAQ)";

// دریافت همه سوالات
$stmt = $pdo->query("SELECT * FROM faq ORDER BY id DESC");
$faqs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php require_once $rootPath . '/includes/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-circle-question"></i> مدیریت سوالات متداول</h4>
        <a href="create.php" class="btn btn-primary">افزودن سوال جدید</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th width="40%">سوال</th>
                            <th>پاسخ</th>
                            <th width="160">عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($faqs)): ?>
                            <tr>
                                <td colspan="3" class="text-center py-4">هنوز سوالی ثبت نشده است.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($faqs as $faq): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($faq['question']) ?></strong></td>
                                <td><?= mb_substr(htmlspecialchars($faq['answer'] ?? ''), 0, 120) . '...' ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="view.php?id=<?= $faq['id'] ?>" class="btn btn-info text-white">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="edit.php?id=<?= $faq['id'] ?>" class="btn btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="delete.php?id=<?= $faq['id'] ?>" class="btn btn-danger"
                                           onclick="return confirm('آیا از حذف این سوال مطمئن هستید؟')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
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

<?php require_once $rootPath . '/includes/footer.php'; ?>