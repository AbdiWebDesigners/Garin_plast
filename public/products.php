<?php
// ۱. فعال‌سازی نمایش خطاها برای دیباگ
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ۲. اتصال به دیتابیس (یک پوشه عقب می‌رویم تا به includes برسیم)
require_once __DIR__ . '/../includes/db.php'; 

$pageTitle = 'محصولات |  گَرین پلاست';
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://v1.fontapi.ir/css/Vazir');
        body { font-family: 'Vazir', sans-serif; }
        .product-card {
            transition: all 0.4s ease;
            border: none;
            border-radius: 15px;
            overflow: hidden;
        }
        .product-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 20px 40px rgba(0, 200, 83, 0.25);
        }
        .product-img {
            height: 240px;
            object-fit: cover;
        }
    </style>
</head>
<body>

<?php 
// لود نوار ناوبری (یک پوشه عقب‌تر)
include __DIR__ . '/../includes/navbar.php'; 
?>

<div class="container py-5">
    <h1 class="display-5 fw-bold text-success mb-5 text-center">تمام محصولات</h1>

    <div class="row g-4" id="productsContainer">
        <?php
        try {
            $stmt = $pdo->prepare("SELECT id, title, price, image FROM products ORDER BY id DESC");
            $stmt->execute();
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($products)) {
                echo '<div class="col-12 text-center text-muted">هیچ محصولی در دیتابیس یافت نشد.</div>';
            }

            foreach ($products as $p):
                // مسیر عکس‌ها چون پوشه uploads در ریشه است باید با ../ فراخوانی شود
                $img = !empty($p['image']) ? '../uploads/' . htmlspecialchars($p['image']) : '../attachments/loop-handle-bags-250x250.webp';
        ?>
        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="card product-card h-100 shadow-sm">
                <img src="<?= $img ?>" class="card-img-top product-img" alt="<?= htmlspecialchars($p['title']) ?>">
                <div class="card-body text-center d-flex flex-column">
                    <h6 class="fw-bold"><?= htmlspecialchars($p['title']) ?></h6>
                    <p class="text-success fw-bold fs-5"><?= number_format($p['price']) ?> تومان</p>
                    
                    <form action="add_to_cart.php" method="POST" class="mt-auto">
                        <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-cart-plus"></i> اضافه به سبد خرید
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <?php 
            endforeach; 
        } catch (PDOException $e) {
            echo '<div class="col-12 alert alert-danger">خطا در اجرای کوئری دیتابیس: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
        ?>
    </div>
</div>

<?php 
// لود فوتر (یک پوشه عقب‌تر)
include __DIR__ . '/../includes/footer.php'; 
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>