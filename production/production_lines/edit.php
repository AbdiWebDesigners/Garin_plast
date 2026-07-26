<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireLogin();

$id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $capacity = (int)$_POST['capacity_per_hour'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE production_lines SET name=?, capacity_per_hour=?, status=? WHERE id=?");
    $stmt->execute([$name, $capacity, $status, $id]);

    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM production_lines WHERE id = ?");
$stmt->execute([$id]);
$line = $stmt->fetch();

if (!$line) {
    die("خط تولید یافت نشد.");
}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>ویرایش خط تولید</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card shadow">
        <div class="card-header bg-warning text-dark">
            <h4>ویرایش خط تولید</h4>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label>نام خط تولید</label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($line['name']) ?>" required>
                </div>
                <div class="mb-3">
                    <label>ظرفیت ساعتی (واحد)</label>
                    <input type="number" name="capacity_per_hour" class="form-control" value="<?= $line['capacity_per_hour'] ?>" required>
                </div>
                <div class="mb-3">
                    <label>وضعیت</label>
                    <select name="status" class="form-select">
                        <option value="active" <?= $line['status']=='active' ? 'selected' : '' ?>>فعال</option>
                        <option value="maintenance" <?= $line['status']=='maintenance' ? 'selected' : '' ?>>در حال تعمیر</option>
                        <option value="inactive" <?= $line['status']=='inactive' ? 'selected' : '' ?>>غیرفعال</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-warning">به‌روزرسانی</button>
                <a href="index.php" class="btn btn-secondary">انصراف</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>