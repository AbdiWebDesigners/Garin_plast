<?php
require_once __DIR__ . '/../../includes/header.php';
requireLogin();
requirePermission('manage_roles');

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: index.php");
    exit;
}

$page_title = 'ویرایش نقش';

try {
    $roleStmt = $pdo->prepare("SELECT * FROM roles WHERE id = ?");
    $roleStmt->execute([$id]);
    $role = $roleStmt->fetch();

    if (!$role) {
        die('نقش یافت نشد.');
    }

    $allPermissions = $pdo->query("SELECT id, name, title, section FROM permissions ORDER BY section, name")->fetchAll();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $pdo->prepare("DELETE FROM role_permissions WHERE role_id = ?")->execute([$id]);

        if (isset($_POST['permissions']) && is_array($_POST['permissions'])) {
            $stmt = $pdo->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)");
            foreach ($_POST['permissions'] as $perm_id) {
                $stmt->execute([$id, (int)$perm_id]);
            }
        }

        $_SESSION['success'] = 'مجوزهای نقش با موفقیت بروزرسانی شد.';
        header("Location: index.php");
        exit;
    }

    $currentPermissions = $pdo->prepare("SELECT permission_id FROM role_permissions WHERE role_id = ?");
    $currentPermissions->execute([$id]);
    $current = $currentPermissions->fetchAll(PDO::FETCH_COLUMN);

} catch (Exception $e) {
    die('خطا: ' . $e->getMessage());
}
?>

<div class="container-fluid px-4 py-4">
    <h3>ویرایش نقش: <?= htmlspecialchars($role['label']) ?></h3>

    <form method="POST">
        <div class="row g-3">
            <?php foreach ($allPermissions as $perm): ?>
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="form-check border p-3 rounded">
                        <input class="form-check-input" type="checkbox" 
                               name="permissions[]" value="<?= $perm['id'] ?>"
                               <?= in_array($perm['id'], $current) ? 'checked' : '' ?>>
                        <label class="form-check-label ms-2">
                            <?= htmlspecialchars($perm['title'] ?? $perm['name']) ?> 
                            <small class="text-muted d-block">(<?= htmlspecialchars($perm['section'] ?? '') ?>)</small>
                        </label>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-success btn-lg">ذخیره تغییرات</button>
            <a href="index.php" class="btn btn-secondary btn-lg">انصراف</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>