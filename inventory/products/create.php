<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$page_title = "افزودن محصول جدید";

require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/header.php';
require_once '../../includes/db.php';

$errors = [];
$old = [
    'category_id' => '',
    'title'       => '',
    'sku'         => '',
    'price'       => '',
    'stock'       => '',
    'description' => '',
    'status'      => 'active',
];

$categories = $pdo->query("SELECT id, title FROM categories ORDER BY title ASC")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $old['category_id'] = $_POST['category_id'] ?? '';
    $old['title']       = trim($_POST['title'] ?? '');
    $old['sku']         = trim($_POST['sku'] ?? '');
    $old['price']       = $_POST['price'] ?? '';
    $old['stock']       = $_POST['stock'] ?? '';
    $old['description'] = trim($_POST['description'] ?? '');
    $old['status']      = ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active';

    /* اعتبارسنجی */
    if ($old['title'] === '') {
        $errors[] = 'عنوان محصول الزامی است.';
    }

    if ($old['price'] === '' || !is_numeric($old['price']) || (float)$old['price'] < 0) {
        $errors[] = 'قیمت باید یک عدد معتبر و غیرمنفی باشد.';
    }

    if ($old['stock'] === '' || !ctype_digit((string)$old['stock'])) {
        $errors[] = 'موجودی باید یک عدد صحیح و غیرمنفی باشد.';
    }

    if ($old['category_id'] !== '' && !ctype_digit((string)$old['category_id'])) {
        $errors[] = 'دسته‌بندی نامعتبر است.';
    }

    /* بررسی تکراری نبودن کد محصول (sku) */
    if ($old['sku'] !== '') {
        $stmt = $pdo->prepare("SELECT id FROM products WHERE sku = ?");
        $stmt->execute([$old['sku']]);
        if ($stmt->fetch()) {
            $errors[] = 'این کد محصول (SKU) قبلاً ثبت شده است.';
        }
    }

    /* آپلود تصویر (اختیاری) */
    $image_name = null;

    if (!empty($_FILES['image']['name'])) {

        $allowed_ext  = ['jpg', 'jpeg', 'png', 'webp'];
        $allowed_mime = ['image/jpeg', 'image/png', 'image/webp'];
        $max_size     = 3 * 1024 * 1024; // 3MB

        $file = $_FILES['image'];
        $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'خطا در آپلود تصویر.';
        } elseif (!in_array($ext, $allowed_ext, true) || !in_array(mime_content_type($file['tmp_name']), $allowed_mime, true)) {
            $errors[] = 'فرمت تصویر مجاز نیست (فقط jpg, jpeg, png, webp).';
        } elseif ($file['size'] > $max_size) {
            $errors[] = 'حجم تصویر نباید بیشتر از ۳ مگابایت باشد.';
        } else {
            $image_name = 'prod_' . bin2hex(random_bytes(8)) . '.' . $ext;
            $upload_dir = __DIR__ . '/../../assets/uploads/products/';

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            if (!move_uploaded_file($file['tmp_name'], $upload_dir . $image_name)) {
                $errors[] = 'ذخیره تصویر با خطا مواجه شد.';
                $image_name = null;
            }
        }
    }

    if (empty($errors)) {

        $stmt = $pdo->prepare("
            INSERT INTO products (category_id, title, description, image, price, stock, status, sku)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $old['category_id'] !== '' ? (int)$old['category_id'] : null,
            $old['title'],
            $old['description'] !== '' ? $old['description'] : null,
            $image_name,
            (float)$old['price'],
            (int)$old['stock'],
            $old['status'],
            $old['sku'] !== '' ? $old['sku'] : null,
        ]);

        header("Location: index.php?success=created");
        exit;
    }
}
?>

<h2>افزودن محصول جدید</h2>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">

    <div class="mb-3">
        <label class="form-label">عنوان محصول *</label>
        <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($old['title']) ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">کد محصول (SKU)</label>
        <input type="text" name="sku" class="form-control" value="<?= htmlspecialchars($old['sku']) ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">دسته‌بندی</label>
        <select name="category_id" class="form-select">
            <option value="">بدون دسته</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= (string)$old['category_id'] === (string)$cat['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['title']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">قیمت (تومان) *</label>
            <input type="number" step="1" min="0" name="price" class="form-control" value="<?= htmlspecialchars($old['price']) ?>" required>
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label">موجودی *</label>
            <input type="number" step="1" min="0" name="stock" class="form-control" value="<?= htmlspecialchars($old['stock']) ?>" required>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">توضیحات</label>
        <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($old['description']) ?></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">تصویر محصول</label>
        <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.webp">
        <small class="text-muted">فرمت‌های مجاز: jpg, jpeg, png, webp — حداکثر ۳ مگابایت</small>
    </div>

    <div class="mb-3">
        <label class="form-label">وضعیت</label>
        <select name="status" class="form-select">
            <option value="active"   <?= $old['status'] === 'active'   ? 'selected' : '' ?>>فعال</option>
            <option value="inactive" <?= $old['status'] === 'inactive' ? 'selected' : '' ?>>غیرفعال</option>
        </select>
    </div>

    <button type="submit" class="btn btn-success">ثبت محصول</button>
    <a href="index.php" class="btn btn-secondary">انصراف</a>

</form>

<?php require_once '../../includes/footer.php'; ?>
