<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireLogin(); // مطمئن شو این خط هست

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = $_SESSION['user_id'] ?? null;
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $priority = $_POST['priority'] ?? 'normal';

    if ($customer_id && $subject && $message) {
        try {
            $stmt = $pdo->prepare("INSERT INTO tickets (customer_id, subject, status, priority) VALUES (?, ?, 'open', ?)");
            $stmt->execute([$customer_id, $subject, $priority]);
            $ticket_id = $pdo->lastInsertId();

            $stmt2 = $pdo->prepare("INSERT INTO ticket_messages (ticket_id, message, sender_type) VALUES (?, ?, 'customer')");
            $stmt2->execute([$ticket_id, $message]);

            header("Location: view.php?id=$ticket_id");
            exit;
        } catch (Exception $e) {
            $error = "خطا: " . $e->getMessage();
        }
    } else {
        $error = "لطفاً همه فیلدها را پر کنید.";
    }
}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>ایجاد تیکت جدید</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h4>تیکت جدید</h4>
        </div>
        <div class="card-body">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label>موضوع تیکت</label>
                    <input type="text" name="subject" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>اولویت</label>
                    <select name="priority" class="form-select