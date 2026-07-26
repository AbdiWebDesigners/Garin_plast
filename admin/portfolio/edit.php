<?php
$rootPath = dirname(__DIR__, 2);
require_once $rootPath . '/includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();
if (!hasPermission('manage_admin')) exit;

$page_title = "ویرایش نمونه‌کار";

$id = $_GET['id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title']);
    $description = trim($_POST['description'] ?? '');

    $image = $_POST['old_image'] ?? '';

    // آپلود تصویر جدید
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../uploads/portfolio/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);
        
        $image_name = time() . '_' . basename($_FILES['image']['name']);
        $target_file = $target_dir . $image_name;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image = 'uploads/portfolio/' . $image_name;
        }
    }

    $stmt = $pdo->prepare("UPDATE portfolio SET title=?, image=?, description=? WHERE id=?");
    $stmt->execute([$title, $image, $description, $id]);

    header("Location: index.php?success=1");
    exit;
}

// دریافت اطلاعات
$stmt = $pdo->prepare("SELECT * FROM portfolio WHERE id = ?");
$stmt->execute([$id]);
$portfolio = $stmt->fetch();

require_once $rootPath . '/includes/header.php';
?>

<div class="container mt-4">
    <h4>ویرایش نمونه‌کار</h4>
    <div class="card">
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="old_image" value="<?= htmlspecialchars($portfolio['image'] ?? '') ?>">
                
                <div class="mb-3">
                    <label>عنوان</label>
                    <input type="text" name="title" value="<?= htmlspecialchars($portfolio['title']) ?>" class="form-control" required>
                </div>
                
                <?php if ($portfolio['image']): ?>
                <div class="mb-3">
                    <img src="<?= BASE_URL . $portfolio['image'] ?>" width="200" alt="">
                </div>
                <?php endif; ?>
                
                <div class="mb-3">
                    <label>تصویر جدید (اختیاری)</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>
                
                <div class="mb-3">
                    <label>توضیحات</label>
                    <textarea name="description" class="form-control" rows="6"><?= htmlspecialchars($portfolio['description'] ?? '') ?></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">به‌روزرسانی</button>
                <a href="index.php" class="btn btn-secondary">انصراف</a>
            </form>
        </div>
    </div>
</div>

<?php require_once $rootPath . '/includes/footer.php'; ?>