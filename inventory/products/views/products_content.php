<?php
global $pdo;

// ==================== هندل کردن ثبت و حذف محصول ====================
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // --- افزودن محصول ---
    if ($action === 'add') {
        $title       = trim($_POST['title'] ?? '');
        $category_id = (int)($_POST['category_id'] ?? 0);
        $price       = (int)($_POST['price'] ?? 0);

        if (empty($title)) {
            $error = "نام محصول نمی‌تواند خالی باشد.";
        } elseif ($category_id <= 0) {
            $error = "دسته‌بندی را انتخاب کنید.";
        } elseif ($price <= 0) {
            $error = "قیمت باید بزرگتر از صفر باشد.";
        } else {
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO products (title, category_id, price, created_at) 
                    VALUES (:title, :category_id, :price, NOW())
                ");
                $stmt->execute([
                    ':title'       => $title,
                    ':category_id' => $category_id,
                    ':price'       => $price
                ]);
                $success = "محصول با موفقیت ثبت شد!";
                // رفرش لیست محصولات
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            } catch (Exception $e) {
                $error = "خطا در ثبت محصول: " . $e->getMessage();
            }
        }
    }

    // --- حذف محصول ---
    if ($action === 'delete') {
        $product_id = (int)($_POST['product_id'] ?? 0);
        if ($product_id > 0) {
            try {
                $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
                $stmt->execute([':id' => $product_id]);
                $success = "محصول حذف شد.";
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            } catch (Exception $e) {
                $error = "خطا در حذف: " . $e->getMessage();
            }
        }
    }
}

// ۱. خواندن لیست محصولات ...
try {
    $stmt = $pdo->query("
        SELECT products.*, categories.title AS category_name 
        FROM products 
        LEFT JOIN categories ON products.category_id = categories.id 
        ORDER BY products.id DESC
    ");
    $productsList = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $productsList = [];
    $error = "خطا در دریافت لیست محصولات: " . $e->getMessage();
}

// ۲. خواندن لیست دسته‌بندی‌ها ...
try {
    $catStmt = $pdo->query("SELECT id, title FROM categories ORDER BY id ASC");
    $categoriesList = $catStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $categoriesList = [];
}
?>

<div class="container-fluid py-4">
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="card-title mb-0 fw-bold small text-uppercase">افزودن محصول جدید</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="add">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small">نام محصول</label>
                            <input type="text" name="title" class="form-control" required placeholder="مثال: سفره یکبار مصرف">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small">دسته‌بندی</label>
                            <select name="category_id" class="form-select" required>
                                <option value="" disabled selected>انتخاب کنید...</option>
                                <?php foreach ($categoriesList as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['title']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small">قیمت (تومان)</label>
                            <input type="number" name="price" class="form-control text-start" required placeholder="مثال: 285000">
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-bold">ثبت محصول</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-bold text-dark">لیست محصولات (<?= count($productsList) ?> قلم)</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-center small text-muted text-uppercase">
                                <tr>
                                    <th style="width: 80px;">کد (ID)</th>
                                    <th class="text-start ps-3">نام محصول</th>
                                    <th>دسته‌بندی</th>
                                    <th>قیمت (تومان)</th>
                                    <th style="width: 150px;">عملیات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($productsList)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">هیچ محصولی در سیستم یافت نشد.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($productsList as $item): ?>
                                        <tr class="text-center">
                                            <td class="text-muted fw-bold"><?= $item['id'] ?></td>
                                            
                                            <td class="text-start ps-3 fw-bold text-dark"><?= htmlspecialchars($item['title'] ?? 'بدون نام') ?></td>
                                            
                                            <td>
                                                <span class="badge bg-light text-secondary border">
                                                    <?= htmlspecialchars($item['category_name'] ?? 'بدون دسته') ?>
                                                </span>
                                            </td>
                                            
                                            <td class="text-success fw-bold"><?= number_format($item['price'] ?? 0) ?></td>
                                            
                                            <td>
                                                <a href="view.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-outline-success me-1" title="مشاهده کالا">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                
                                                <a href="edit.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-outline-info me-1" title="ویرایش کالا">
                                                    <i class="fa fa-edit"></i>
                                                </a>

                                                <form method="POST" onsubmit="return confirm('آیا از حذف این محصول اطمینان دارید؟');" style="display:inline-block;">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف کالا">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>