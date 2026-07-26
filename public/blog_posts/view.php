<?php
require_once __DIR__ . '/../../includes/auth.php';

$id = (int)($_GET['id'] ?? 0);

if ($id === 0) {
    die("مقاله یافت نشد.");
}

$stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) {
    die("مقاله مورد نظر وجود ندارد.");
}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($post['title']) ?> - گارین پلاست</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        .article-content img { max-width: 100%; height: auto; border-radius: 8px; margin: 20px 0; }
        .article-content { line-height: 1.8; font-size: 1.1rem; }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <?php if ($post['image']): ?>
                <img src="../uploads/<?= htmlspecialchars($post['image']) ?>" class="img-fluid rounded mb-4" alt="<?= htmlspecialchars($post['title']) ?>">
            <?php endif; ?>

            <h1 class="mb-4"><?= htmlspecialchars($post['title']) ?></h1>
            <p class="text-muted">تاریخ انتشار: <?= $post['created_at'] ?></p>

            <div class="article-content">
                <?= nl2br($post['content']) ?>
            </div>

            <div class="mt-5">
                <a href="../index.php" class="btn btn-secondary">بازگشت به صفحه اصلی</a>
                <a href="index.php" class="btn btn-outline-primary">مشاهده همه مقالات</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>