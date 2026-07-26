<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();

$error = null;

// دریافت لیست حساب‌های موجود برای فیلد "حساب پدر"
$parentAccounts = $pdo->query("SELECT id, code, name, level FROM accounting_accounts ORDER BY code ASC")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code']);
    $name = trim($_POST['name']);
    $type = $_POST['type'];
    // اگر حساب پدری انتخاب نشده بود، مقدار null در دیتابیس ثبت می‌شود
    $parent_id = !empty($_POST['parent_id']) ? $_POST['parent_id'] : null; 
    $level = (int)$_POST['level'];

    // اعتبارسنجی ساده
    if (empty($code) || empty($name) || empty($type) || empty($level)) {
        $error = "لطفاً تمام فیلدهای ستاره‌دار را پر کنید.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO accounting_accounts (code, name, type, parent_id, level) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$code, $name, $type, $parent_id, $level]);
            
            // در صورت موفقیت، بازگشت به لیست سرفصل‌ها
            header("Location: accounts.php");
            exit;
        } catch (PDOException $e) {
            // کد 23000 مربوط به خطای تکراری بودن مقادیر Unique (مثل کد حساب) است
            if ($e->getCode() == 23000) {
                $error = "کد حساب وارد شده تکراری است. لطفاً کد دیگری انتخاب کنید.";
            } else {
                $error = "خطا در ثبت اطلاعات: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>ایجاد سرفصل جدید</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0">تعریف سرفصل حساب جدید</h5>
                    <a href="accounts.php" class="btn btn-light btn-sm">انصراف و بازگشت</a>
                </div>
                <div class="card-body p-4">
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">کد حساب <span class="text-danger">*</span></label>
                                <input type="text" name="code" class="form-control" placeholder="مثلاً: 101" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">نام حساب <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="مثلاً: صندوق ریالی" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">ماهیت (نوع حساب) <span class="text-danger">*</span></label>
                                <select name="type" class="form-select" required>
                                    <option value="">انتخاب کنید...</option>
                                    <option value="asset">دارایی (Asset)</option>
                                    <option value="liability">بدهی (Liability)</option>
                                    <option value="equity">حقوق صاحبان سهام (Equity)</option>
                                    <option value="revenue">درآمد (Revenue)</option>
                                    <option value="expense">هزینه (Expense)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">سطح حساب <span class="text-danger">*</span></label>
                                <select name="level" class="form-select" required>
                                    <option value="1">سطح ۱ (گروه)</option>
                                    <option value="2">سطح ۲ (کل)</option>
                                    <option value="3">سطح ۳ (معین)</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">حساب پدر (گروه اصلی)</label>
                            <select name="parent_id" class="form-select">
                                <option value="">بدون حساب پدر (حساب مستقل / سطح ۱)</option>
                                <?php foreach ($parentAccounts as $parent): ?>
                                    <option value="<?= $parent['id'] ?>">
                                        <?= htmlspecialchars($parent['code'] . ' - ' . $parent['name']) ?> 
                                        (سطح <?= $parent['level'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">اگر این حساب زیرمجموعه‌ی حساب دیگری است، آن را انتخاب کنید.</div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg">ذخیره سرفصل</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>