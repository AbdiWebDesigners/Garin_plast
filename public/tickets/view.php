<?php
require_once __DIR__ . '/../../includes/auth.php';
requireLogin();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    die('تیکت نامعتبر است.');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message'] ?? '');
    if ($message !== '') {
        try {
            $stmt = $pdo->prepare("INSERT INTO ticket_messages (ticket_id, user_id, message) VALUES (?, ?, ?)");
            $stmt->execute([$id, currentUserId(), $message]);
            header("Location: view.php?id=$id");
            exit;
        } catch (Throwable $e) {
            $error = 'خطا در ارسال پیام.';
        }
    } else {
        $error = 'متن پیام نمی‌تواند خالی باشد.';
    }
}

try {
    $ticketStmt = $pdo->prepare("SELECT * FROM tickets WHERE id = ? LIMIT 1");
    $ticketStmt->execute([$id]);
    $ticket = $ticketStmt->fetch(PDO::FETCH_ASSOC);

    if (!$ticket) {
        die('تیکت یافت نشد.');
    }

    $messagesStmt = $pdo->prepare("
        SELECT m.*, u.fullname
        FROM ticket_messages m
        LEFT JOIN users u ON u.id = m.user_id
        WHERE m.ticket_id = ?
        ORDER BY m.created_at ASC
    ");
    $messagesStmt->execute([$id]);
    $messages = $messagesStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    die('خطا در بارگذاری تیکت.');
}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تیکت #<?= $id ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <h2>تیکت #<?= $ticket['id'] ?> - <?= htmlspecialchars($ticket['subject'] ?? '') ?></h2>
    <p>وضعیت: <strong><?= $ticket['status'] ?></strong> | اولویت: <strong><?= $ticket['priority'] ?></strong></p>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="border p-4 mb-4 bg-light" style="max-height: 500px; overflow-y: auto;">
        <?php foreach ($messages as $m): ?>
            <div class="mb-4 p-3 rounded <?= $m['user_id'] == $_SESSION['user_id'] ? 'bg-white border' : 'bg-success text-white' ?>">
                <strong><?= htmlspecialchars($m['fullname'] ?? 'کاربر') ?>:</strong><br>
                <?= nl2br(htmlspecialchars($m['message'])) ?>
                <small class="text-muted d-block mt-2"><?= $m['created_at'] ?></small>
            </div>
        <?php endforeach; ?>
    </div>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label fw-bold">پاسخ جدید</label>
            <textarea name="message" class="form-control" rows="4" required></textarea>
        </div>
        <button type="submit" class="btn btn-success">ارسال پیام</button>
        <a href="index.php" class="btn btn-secondary">بازگشت به لیست</a>
    </form>
</div>
</body>
</html>