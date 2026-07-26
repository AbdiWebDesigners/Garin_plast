<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $capacity = (int)$_POST['capacity_per_hour'];
    $status = $_POST['status'] ?? 'active';

    try {
        $stmt = $pdo->prepare("INSERT INTO production_lines (name, capacity_per_hour, status) VALUES (?, ?, ?)");
        $stmt->execute([$name, $capacity, $status]);

        header("Location: index.php");
        exit;
    } catch (Exception $e) {
        $error = "خطا: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>ایجاد خط تولید جدید</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4>خط تولید جدید</h4>
        </div>
        <div class="card-body">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label>نام خط تولید</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>ظرفیت ساعتی (واحد در ساعت)</label>
                    <input type="number" name="capacity_per_hour" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>وضعیت خط</label>
                    <select name="status" class="form-select">
                        <option value="active">فعال</option>
                        <option value="maintenance">در حال تعمیر</option>
                        <option value="inactive">غیرفعال</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">ثبت خط تولید</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>