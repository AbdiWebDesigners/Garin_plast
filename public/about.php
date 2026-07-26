<?php
require_once __DIR__ . '/../includes/db.php';
$pageTitle = 'درباره ما | گَرین پلاست';
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        .about-img {
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<?php include '../includes/navbar.php'; ?>

<div class="container py-5">
    <div class="row align-items-center g-5">
        <!-- تصویر -->
        <div class="col-lg-6">
            <img src="../attachments/3-9.jpg" class="about-img img-fluid" alt="گارین پلاست">
        </div>
        
        <!-- متن -->
        <div class="col-lg-6">
            <h1 class="display-5 fw-bold text-success mb-4">درباره گارین پلاست</h1>
            <p class="lead mb-4">
                شرکت تولیدی گارین پلاست با بیش از ۱۵ سال تجربه در زمینه تولید نایلون، نایلکس، کیسه‌های خرید و ظروف یکبار مصرف، متعهد به ارائه محصولاتی با کیفیت صادراتی است.
            </p>
            <p>
                ما با استفاده از مواد اولیه مرغوب و ماشین‌آلات مدرن، محصولات متنوعی را با بالاترین استانداردهای کیفی تولید می‌کنیم. رضایت مشتریان و حفظ محیط زیست از اصول اساسی ماست.
            </p>

            <div class="row mt-5">
                <div class="col-md-6 mb-4">
                    <h5><i class="fas fa-check text-success me-2"></i> مواد اولیه درجه یک</h5>
                </div>
                <div class="col-md-6 mb-4">
                    <h5><i class="fas fa-check text-success me-2"></i> چاپ فلکسو و افست</h5>
                </div>
                <div class="col-md-6 mb-4">
                    <h5><i class="fas fa-check text-success me-2"></i> ارسال سریع به سراسر کشور</h5>
                </div>
                <div class="col-md-6 mb-4">
                    <h5><i class="fas fa-check text-success me-2"></i> پشتیبانی ۲۴ ساعته</h5>
                </div>
            </div>

            <a href="contact.php" class="btn btn-success btn-lg px-5 mt-3">تماس با ما</a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>