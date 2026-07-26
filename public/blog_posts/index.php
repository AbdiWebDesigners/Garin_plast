<?php
require_once __DIR__ . '/../../includes/auth.php';
requireLogin();

$posts = [];
try {
    $stmt = $pdo->query("SELECT * FROM blog_posts ORDER BY created_at DESC");
    $posts = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
} catch (Throwable $e) {
    $posts = [];
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>مدیریت مقالات</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    
    <div class="d-flex justify-content-between mb-4">
        
<a href="<?= BASE_URL ?>admin/dashboard.php" class="btn btn-secondary">داشبورد</a>
        <h2>مقالات</h2>
        <a href="create.php" class="btn btn-success">مقاله جدید</a>
    </div>

    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>عنوان</th>
                <th>تصویر</th>
                <th>خلاصه</th>
                <th>تاریخ انتشار</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($posts as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['title'] ?? '') ?></td>
                <td>
                    <?php if (!empty($p['image'])): ?>
                        <img src="../uploads/<?= htmlspecialchars($p['image']) ?>" width="80" height="50" style="object-fit:cover;">
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars(mb_substr($p['summary'] ?? '', 0, 80)) ?>...</td>
                <td><?= htmlspecialchars($p['created_at'] ?? '') ?></td>
                <td>
                    <a href="view.php?id=<?= (int)$p['id'] ?>" class="btn btn-sm btn-info">مشاهده</a>
                    <a href="edit.php?id=<?= (int)$p['id'] ?>" class="btn btn-sm btn-primary">ویرایش</a>
                    <a href="delete.php?id=<?= (int)$p['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('مطمئن هستید؟')">حذف</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>