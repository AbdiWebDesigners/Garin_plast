<?php
require_once __DIR__ . '/../../includes/header.php';
requireLogin();
requirePermission('manage_roles');

$page_title = 'مدیریت نقش‌ها';
?>

<div class="container-fluid px-4 py-4">
    <h3>مدیریت نقش‌ها</h3>

    <table class="table table-hover">
        <thead>
            <tr>
                <th>نقش</th>
                <th>توضیحات</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $pdo->query("SELECT * FROM roles");
            while ($role = $stmt->fetch()):
            ?>
            <tr>
                <td><?= htmlspecialchars($role['name']) ?></td>
                <td><?= htmlspecialchars($role['label']) ?></td>
                <td>
                    <a href="edit.php?id=<?= $role['id'] ?>" class="btn btn-sm btn-primary">ویرایش مجوزها</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>