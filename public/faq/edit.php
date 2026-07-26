<?php
$rootPath = dirname(__DIR__, 2);
require_once $rootPath . '/includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();
if (!hasPermission('manage_admin')) exit;

$page_title = "ویرایش سوال";

$id = $_GET['id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question = trim($_POST['question']);
    $answer   = trim($_POST['answer']);

    $stmt = $pdo->prepare("UPDATE faq SET question=?, answer=? WHERE id=?");
    $stmt->execute([$question, $answer, $id]);

    header("Location: index.php?success=1");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM faq WHERE id = ?");
$stmt->execute([$id]);
$faq = $stmt->fetch();

require_once $rootPath . '/includes/header.php';
?>

<div class="container mt-4">
    <h4>ویرایش سوال</h4>
    <div class="card">
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label>سوال</label>
                    <textarea name="question" class="form-control" rows="3" required><?= htmlspecialchars($faq['question'] ?? '') ?></textarea>
                </div>
                <div class="mb-3">
                    <label>پاسخ</label>
                    <textarea name="answer" class="form-control" rows="10" required><?= htmlspecialchars($faq['answer'] ?? '') ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">به‌روزرسانی</button>
                <a href="index.php" class="btn btn-secondary">انصراف</a>
            </form>
        </div>
    </div>
</div>

<?php require_once $rootPath . '/includes/footer.php'; ?>