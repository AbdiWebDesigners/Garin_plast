<?php
$rootPath = dirname(__DIR__, 2);
require_once $rootPath . '/includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();
if (!hasPermission('manage_admin')) {
    header("Location: " . BASE_URL . "admin/dashboard.php"); 
    exit;
}

$page_title = "تنظیمات سایت";

// دریافت تنظیمات (معمولاً فقط یک رکورد)
$stmt = $pdo->query("SELECT * FROM settings LIMIT 1");
$settings = $stmt->fetch();

if (!$settings) {
    // اگر رکورد وجود نداشت، یکی بساز
    $pdo->query("INSERT INTO settings (site_name) VALUES ('سایت من')");
    $settings = $pdo->query("SELECT * FROM settings LIMIT 1")->fetch();
}
?>

<?php require_once $rootPath . '/includes/header.php'; ?>

<div class="container mt-4">
    <h4><i class="fas fa-cog"></i> تنظیمات سایت</h4>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="update.php" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>نام سایت</label>
                            <input type="text" name="site_name" value="<?= htmlspecialchars($settings['site_name'] ?? '') ?>" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>لوگو</label><br>
                            <?php if (!empty($settings['logo'])): ?>
                                <img src="<?= BASE_URL . $settings['logo'] ?>" width="120" class="mb-2 d-block" alt=""><br>
                            <?php endif; ?>
                            <input type="file" name="logo" class="form-control" accept="image/*">
                            <small class="text-muted">برای تغییر لوگو، تصویر جدید انتخاب کنید.</small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label>تلفن ثابت</label>
                            <input type="text" name="phone" value="<?= htmlspecialchars($settings['phone'] ?? '') ?>" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label>موبایل</label>
                            <input type="text" name="mobile" value="<?= htmlspecialchars($settings['mobile'] ?? '') ?>" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label>واتساپ</label>
                            <input type="text" name="whatsapp" value="<?= htmlspecialchars($settings['whatsapp'] ?? '') ?>" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label>ایمیل</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($settings['email'] ?? '') ?>" class="form-control">
                </div>

                <div class="mb-3">
                    <label>آدرس</label>
                    <textarea name="address" class="form-control" rows="3"><?= htmlspecialchars($settings['address'] ?? '') ?></textarea>
                </div>

                <hr>
                <h5>شبکه‌های اجتماعی</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label>اینستاگرام</label>
                            <input type="text" name="instagram" value="<?= htmlspecialchars($settings['instagram'] ?? '') ?>" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label>تلگرام</label>
                            <input type="text" name="telegram" value="<?= htmlspecialchars($settings['telegram'] ?? '') ?>" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label>لینکدین</label>
                            <input type="text" name="linkedin" value="<?= htmlspecialchars($settings['linkedin'] ?? '') ?>" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>بله</label>
                            <input type="text" name="bale" value="<?= htmlspecialchars($settings['bale'] ?? '') ?>" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>ایتا</label>
                            <input type="text" name="eitaa" value="<?= htmlspecialchars($settings['eitaa'] ?? '') ?>" class="form-control">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-success btn-lg">ذخیره تنظیمات</button>
            </form>
        </div>
    </div>
</div>

<?php require_once $rootPath . '/includes/footer.php'; ?>