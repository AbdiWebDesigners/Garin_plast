<?php
$rootPath = dirname(__DIR__, 2);
require_once $rootPath . '/includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();
if (!hasPermission('manage_admin')) exit;

$page_title = "ویرایش مجوز";

$id = $_GET['id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name   = trim($_POST['name']);
    $label  = trim($_POST['label']);
    $module = trim($_POST['module'] ?? '');

    $stmt = $pdo->prepare("UPDATE permissions SET name=?, label=?, module=? WHERE id=?");
    $stmt->execute([$name, $label, $module, $id]);

    header("Location: index.php?success=1");
    exit;
}

// دریافت اطلاعات فعلی
$stmt = $pdo->prepare("SELECT * FROM permissions WHERE id = ?");
$stmt->execute([$id]);
$perm = $stmt->fetch();

require_once $rootPath . '/includes/header.php';
?>

<div class="container mt-4">
    <h4>ویرایش مجوز</h4>
    <div class="card">
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label>نام مجوز</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($perm['name']) ?>" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>برچسب فارسی</label>
                    <input type="text" name="label" value="<?= htmlspecialchars($perm['label']) ?>" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>ماژول</label>
                    <input type="text" name="module" value="<?= htmlspecialchars($perm['module'] ?? '') ?>" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">به‌روزرسانی</button>
                <a href="index.php" class="btn btn-secondary">انصراف</a>
            </form>
        </div>
    </div>
</div>

<?php require_once $rootPath . '/includes/footer.php'; ?>