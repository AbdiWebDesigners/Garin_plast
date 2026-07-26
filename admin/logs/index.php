<?php
$rootPath = dirname(__DIR__, 2);
require_once $rootPath . '/includes/auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!hasPermission('manage_admin') && !hasPermission('view_logs')) {
    header("Location: " . BASE_URL . "admin/dashboard.php");
    exit;
}

$page_title = "مدیریت لاگ‌ها";

// دریافت لاگ‌ها از دیتابیس
try {
    $stmt = $pdo->prepare("SELECT * FROM activity_logs ORDER BY created_at DESC LIMIT 100");
    $stmt->execute();
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $logs = [];
    $error = "خطا در دریافت لاگ‌ها: " . $e->getMessage();
}

require_once $rootPath . '/includes/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-history"></i> مدیریت لاگ‌ها</h4>
        <button class="btn btn-sm btn-outline-danger" onclick="clearLogs()">پاک کردن لاگ‌ها</button>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>زمان</th>
                            <th>کاربر</th>
                            <th>ماژول</th>
                            <th>عملیات</th>
                            <th>توضیحات</th>
                            <th>آی‌پی</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($logs)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">هیچ لاگی یافت نشد.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td><?= htmlspecialchars($log['created_at']) ?></td>
                                    <td><?= htmlspecialchars($log['user_id'] ?? 'ناشناس') ?></td>
                                    <td><span class="badge bg-info"><?= htmlspecialchars($log['module']) ?></span></td>
                                    <td><strong><?= htmlspecialchars($log['action']) ?></strong></td>
                                    <td><?= htmlspecialchars($log['description'] ?? '-') ?></td>
                                    <td><small><?= htmlspecialchars($log['ip_address'] ?? '-') ?></small></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function clearLogs() {
    if (confirm('آیا مطمئن هستید که می‌خواهید همه لاگ‌ها پاک شوند؟')) {
        // بعداً می‌توانیم AJAX برای پاک کردن اضافه کنیم
        alert('این قابلیت بعداً اضافه خواهد شد.');
    }
}
</script>

<?php require_once $rootPath . '/includes/footer.php'; ?>