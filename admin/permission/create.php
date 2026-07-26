<?php
$rootPath = dirname(__DIR__, 2);
require_once $rootPath . '/includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();
if (!hasPermission('manage_admin')) exit;

$page_title = "افزودن مجوز جدید";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name   = trim($_POST['name']);
    $label  = trim($_POST['label']);
    $module = trim($_POST['module'] ?? '');

    $stmt = $pdo->prepare("INSERT INTO permissions (name, label, module) VALUES (?, ?, ?)");
    $stmt->execute([$name, $label, $module]);

    header("Location: index.php?success=1");
    exit;
}

require_once $rootPath . '/includes/header.php';
?>

<div class="container mt-4">
    <h4>افزودن مجوز جدید</h4>
    <div class="card">
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label>نام مجوز (انگلیسی)</label>
                    <input type="text" name="name" class="form-control" placeholder="view_products" required>
                </div>
                <div class="mb-3">
                    <label>برچسب فارسی</label>
                    <input type="text" name="label" class="form-control" placeholder="مشاهده محصولات" required>
                </div>
                <div class="mb-3">
                    <label>ماژول (اختیاری)</label>
                    <input type="text" name="module" class="form-control" placeholder="inventory">
                </div>
                <button type="submit" class="btn btn-primary">ذخیره مجوز</button>
                <a href="index.php" class="btn btn-secondary">انصراف</a>
            </form>
        </div>
    </div>
</div>

<?php require_once $rootPath . '/includes/footer.php'; ?>