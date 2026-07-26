<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/upload.php';

requireLogin();

$error = '';
$title = '';
$slug = '';
$summary = '';
$content = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $summary = trim($_POST['summary'] ?? '');
    $content = trim($_POST['content'] ?? '');

    $image = '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image = uploadImage($_FILES['image']);
        if (!$image) {
            $error = 'آپلود تصویر با مشکل مواجه شد.';
        }
    }

    if ($error === '' && $title !== '' && $slug !== '' && $content !== '') {
        try {
            $stmt = $pdo->prepare("INSERT INTO blog_posts (title, slug, image, summary, content) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$title, $slug, $image, $summary, $content]);
            header("Location: index.php?created=1");
            exit;
        } catch (Throwable $e) {
            $error = 'خطا در ذخیره مقاله.';
        }
    } elseif ($error === '') {
        $error = 'لطفاً فیلدهای ضروری را پر کنید.';
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>ایجاد مقاله جدید</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">مقاله جدید</h4>
            <a href="index.php" class="btn btn-light btn-sm">بازگشت</a>
        </div>
        <div class="card-body">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">عنوان مقاله</label>
                    <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($title) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">اسلاگ (لینک URL)</label>
                    <input type="text" name="slug" class="form-control" required value="<?= htmlspecialchars($slug) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">تصویر مقاله</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>
                <div class="mb-3">
                    <label class="form-label">خلاصه مقاله</label>
                    <textarea name="summary" class="form-control" rows="3"><?= htmlspecialchars($summary) ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">محتوای کامل مقاله</label>
                    <textarea name="content" class="form-control" rows="12" required><?= htmlspecialchars($content) ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">ذخیره مقاله</button>
                <a href="index.php" class="btn btn-secondary">انصراف</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>