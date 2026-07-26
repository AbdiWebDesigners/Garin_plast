<?php
$rootPath = dirname(__DIR__, 2);
require_once $rootPath . '/includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();
if (!hasPermission('manage_admin')) exit;

$page_title = "افزودن سوال جدید";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question = trim($_POST['question']);
    $answer   = trim($_POST['answer']);

    $stmt = $pdo->prepare("INSERT INTO faq (question, answer) VALUES (?, ?)");
    $stmt->execute([$question, $answer]);

    header("Location: index.php?success=1");
    exit;
}

require_once $rootPath . '/includes/header.php';
?>

<div class="container mt-4">
    <h4>افزودن سوال جدید</h4>
    <div class="card">
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label>سوال</label>
                    <textarea name="question" class="form-control" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label>پاسخ</label>
                    <textarea name="answer" class="form-control" rows="8" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">ذخیره سوال</button>
                <a href="index.php" class="btn btn-secondary">انصراف</a>
            </form>
        </div>
    </div>
</div>

<?php require_once $rootPath . '/includes/footer.php'; ?>