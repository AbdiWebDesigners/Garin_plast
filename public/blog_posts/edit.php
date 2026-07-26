<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/upload.php';

requireLogin();

$id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $summary = trim($_POST['summary'] ?? '');
    $content = $_POST['content'] ?? '';

    $image = $_POST['old_image'] ?? '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $newImage = uploadImage($_FILES['image']);
        if ($newImage) {
            $image = $newImage;
        }
    }

    if ($title && $slug && $content) {
        $stmt = $pdo->prepare("UPDATE blog_posts SET title=?, slug=?, image=?, summary=?, content=? WHERE id=?");
        $stmt->execute([$title, $slug, $image, $summary, $content, $id]);
        header("Location: index.php");
        exit;
    }
}

// دریافت اطلاعات مقاله
$stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) {
    die("مقاله یافت نشد.");
}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <title>ویرایش مقاله</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card shadow">
        <div class="card-header bg-warning text-dark">
            <h4>ویرایش مقاله</h4>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="old_image" value="<?= htmlspecialchars($post['image'] ?? '') ?>">

                <div class="mb-3">
                    <label>عنوان</label>
                    <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($post['title']) ?>" required>
                </div>
                <div class="mb-3">
                    <label>اسلاگ</label>
                    <input type="text" name="slug" class="form-control" value="<?= htmlspecialchars($post['slug']) ?>" required>
                </div>
                <div class="mb-3">
                    <label>تصویر فعلی</label><br>
                    <?php if ($post['image']): ?>
                        <img src="../uploads/<?= htmlspecialchars($post['image']) ?>" width="200" class="mb-2"><br>
                    <?php endif; ?>
                    <input type="file" name="image" class="form-control">
                </div>
                <div class="mb-3">
                    <label>خلاصه</label>
                    <textarea name="summary" class="form-control" rows="3"><?= htmlspecialchars($post['summary'] ?? '') ?></textarea>
                </div>
                <div class="mb-3">
                    <label>محتوا</label>
                    <textarea name="content" class="form-control" rows="12"><?= htmlspecialchars($post['content'] ?? '') ?></textarea>
                </div>
                <button type="submit" class="btn btn-warning">به‌روزرسانی مقاله</button>
                <a href="index.php" class="btn btn-secondary">انصراف</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>