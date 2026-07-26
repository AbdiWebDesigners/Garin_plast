<?php
$rootPath = dirname(__DIR__, 2);
require_once $rootPath . '/includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();
if (!hasPermission('manage_admin')) exit;

$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM faq WHERE id = ?");
$stmt->execute([$id]);
$faq = $stmt->fetch();

if (!$faq) {
    header("Location: index.php");
    exit;
}

$page_title = "مشاهده سوال";

require_once $rootPath . '/includes/header.php';
?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h5><?= htmlspecialchars($faq['question']) ?></h5>
        </div>
        <div class="card-body">
            <p><?= nl2br(htmlspecialchars($faq['answer'])) ?></p>
        </div>
        <div class="card-footer">
            <a href="index.php" class="btn btn-secondary">بازگشت</a>
            <a href="edit.php?id=<?= $faq['id'] ?>" class="btn btn-warning">ویرایش</a>
        </div>
    </div>
</div>

<?php require_once $rootPath . '/includes/footer.php'; ?>