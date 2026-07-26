<?php

// ====================== اتصال به دیتابیس ======================
$host = 'localhost';
$db   = 'garinpl1_garin';
$user = 'garinpl1_abdi';
$pass = 'Arnika@love99';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("خطا: " . $e->getMessage());
}

// داده‌ها
$categories = $pdo->query("SELECT * FROM categories ORDER BY id LIMIT 8")->fetchAll(PDO::FETCH_ASSOC);
$featured_products = $pdo->query("SELECT * FROM products WHERE status = 'active' ORDER BY id DESC LIMIT 8")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>گَرین پلاست | تولیدکننده انواع نایلون و نایلکس</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        @import url('https://v1.fontapi.ir/css/Vazir');
        body { font-family: 'Vazir', sans-serif; }
        .hero { height: 520px; object-fit: cover; }
        .section-title { position: relative; display: inline-block; }
        .section-title:after {
            content: '';
            position: absolute;
            width: 60px;
            height: 3px;
            background: #198754;
            bottom: -8px;
            right: 0;
        }
    </style>
</head>
<body>

    <!-- هدر -->
    <header class="bg-white shadow sticky-top">
        <div class="container py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <img src="public/uploads/logo.png" alt="لوگو" style="height:65px;">
                    <h4 class="fw-bold text-success mb-0">گَرین پلاست</h4>
                </div>
                           <nav class="d-flex gap-4 fs-5">
    <a href="index.php" class="text-dark fw-bold">صفحه اصلی</a>
    <a href="public/products.php" class="text-dark fw-bold">محصولات</a>
    <a href="public/categories.php" class="text-dark fw-bold">دسته‌بندی‌ها</a>
    <a href="public/special_offers.php" class="text-danger fw-bold">پیشنهادات ویژه</a>
    <a href="public/about.php" class="text-dark fw-bold">درباره ما</a>
    <a href="public/contact.php" class="text-dark fw-bold">تماس</a>
</nav>
                 <!-- دکمه ورود و سبد خرید -->
                 <div class="d-flex align-items-center gap-3">
                    <a href="login.php" class="btn btn-success rounded-pill px-4">ورود / ثبت نام</a>
                    <a href="public/cart.php" class="position-relative text-dark fs-4">
                        <i class="bi bi-basket3"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger fs-6">0</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- اسلایدر -->
    <div id="mainSlider" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="public/uploads/slider-main.jpg" class="d-block w-100 hero" alt="اسلایدر">
                <div class="carousel-caption text-start" style="background:rgba(0,0,0,0.45); padding: 40px 30px; border-radius: 15px; max-width: 550px; right: 8%;">
                    <h1 class="display-4 fw-bold">تولیدکننده انواع نایلون و نایلکس</h1>
                    <p class="lead">کیفیت صادراتی • قیمت رقابتی • مواد اولیه مرغوب</p>
                    <a href="public/product.php" class="btn btn-success btn-lg px-5">مشاهده محصولات</a>
                </div>
            </div>
        </div>
    </div>

    <!-- دسته‌بندی محصولات -->
    <section class="py-5">
        <div class="container">
            <h2 class="section-title text-success fw-bold fs-3 mb-5">دسته‌بندی محصولات</h2>
            <div class="row g-4">
                <?php foreach($categories as $cat): ?>
                <div class="col-6 col-md-3 col-lg-2">
                    <div class="text-center p-3 border rounded-4 shadow-sm h-100">
                        <img src="<?= htmlspecialchars($cat['image'] ?? 'public/uploads/default.png') ?>" 
                             class="img-fluid mb-3" style="height:120px;" alt="">
                        <h6 class="fw-bold"><?= htmlspecialchars($cat['title']) ?></h6>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- محصولات ویژه / پیشنهادات ویژه -->
    <!-- محصولات ویژه (مهم‌ترین بخش) -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center fw-bold text-success mb-5">پیشنهادات ویژه</h2>
            <div class="row g-4">
                <?php
                $products = $pdo->query("SELECT * FROM products WHERE status = 'active' LIMIT 8")->fetchAll();
                foreach ($products as $p):
                    $imagePath = !empty($p['image'])
    ? 'public/uploads/' . basename($p['image'])
    : 'public/uploads/default-1.png';
                ?>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="card h-100 shadow-sm">
                        <img src="<?= htmlspecialchars($imagePath) ?>" 
                             class="card-img-top product-img p-3" 
                             alt="<?= htmlspecialchars($p['title']) ?>">
                        <div class="card-body text-center">
                            <h6 class="fw-bold"><?= htmlspecialchars($p['title']) ?></h6>
                            <p class="text-success fw-bold"><?= number_format($p['price']) ?> تومان</p>
                            
                            <form action="public/add_to_cart.php" method="POST">
                                <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                                <button type="submit" class="btn btn-success w-100">
                                    اضافه به سبد خرید
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <!-- درباره ما -->
    <section class="py-5">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <img src="public/uploads/factory.jpg" class="img-fluid rounded-4 shadow" alt="کارخانه گَرین پلاست">
                </div>
                <div class="col-lg-6">
                    <h2 class="fw-bold text-success mb-4">درباره گَرین پلاست</h>
                    
                    <p class="lead">مجموعه گَرین پلاس از ۱۵ سال تجربه در تولید انواع نایلون، نایلکس و ظروف یکبار مصرف فعالیت می‌کند.</p>
                    <p>ما با استفاده از بهترین مواد اولیه و دستگاه‌های پیشرفته، محصولاتی با کیفیت صادراتی و قیمت کاملاً رقابتی تولید می‌کنیم.</p>
                    <a href="public/about.php" class="btn btn-outline-success btn-lg">بیشتر درباره ما</a>
                </div>
            </div>
        </div>
    </section>

    <!-- فوتر -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5 class="text-success">گَرین پلاست</h5>
                    <p>تولیدکننده حرفه‌ای نایلون، نایلکس و ظروف یکبار مصرف</p>
                </div>
                <div class="col-md-4">
                    <h5>دسترسی سریع</h5>
                  <ul class="list-unstyled">
                        <li><a href="public/products.php" class="text-white-50">محصولات</a></li>
                        <li><a href="public/categories.php" class="text-white-50">دسته‌بندی‌ها</a></li>
                        <li><a href="public/offers.php" class="text-white-50">پیشنهادات ویژه</a></li>
                        <li><a href="public/about.php" class="text-white-50">درباره ما</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>تماس با ما</h5>
                    <p>09122908344<br>info@garinplast.com</p>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center small">
                © ۱۴۰۴ - تمامی حقوق محفوظ است
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>