<?php
require_once __DIR__ . '/../includes/db.php';
$pageTitle = 'تماس با ما | گَرین پلاست';
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
        .contact-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }
    </style>
</head>
<body>

<?php include '../includes/navbar.php'; ?>

<div class="container py-5">
    <h1 class="text-center display-5 fw-bold text-success mb-5">تماس با ما</h1>

    <div class="row g-5">
        <!-- اطلاعات تماس -->
        <div class="col-lg-5">
            <div class="contact-card bg-white p-5 h-100">
                <h3 class="fw-bold mb-4">در ارتباط با ما</h3>
                
                <div class="d-flex mb-4">
                    <i class="fas fa-phone-alt fa-2x text-success me-4"></i>
                    <div>
                        <strong>تلفن / واتساپ</strong><br>
                        09122908344
                    </div>
                </div>

                <div class="d-flex mb-4">
                    <i class="fas fa-envelope fa-2x text-success me-4"></i>
                    <div>
                        <strong>ایمیل</strong><br>
                        info@garinplast.com
                    </div>
                </div>

                <div class="d-flex mb-4">
                    <i class="fas fa-map-marker-alt fa-2x text-success me-4"></i>
                    <div>
                        <strong>آدرس کارخانه</strong><br>
                        تهران- ری- باقرشهر-شهرک صنعتی فرخی- خیابان یکم-پلاک 51- قطعه5
                    </div>
                </div>
            </div>
        </div>

        <!-- فرم تماس -->
        <div class="col-lg-7">
            <div class="contact-card bg-white p-5">
                <h3 class="fw-bold mb-4">ارسال پیام</h3>
                <form id="contactForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <input type="text" class="form-control" placeholder="نام و نام خانوادگی" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <input type="tel" class="form-control" placeholder="شماره تماس" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <input type="email" class="form-control" placeholder="ایمیل">
                    </div>
                    <div class="mb-3">
                        <select class="form-select">
                            <option>موضوع پیام</option>
                            <option>استعلام قیمت</option>
                            <option>سفارش عمده</option>
                            <option>همکاری</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <textarea class="form-control" rows="6" placeholder="پیام شما..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-success btn-lg px-5">ارسال پیام</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('contactForm').addEventListener('submit', function(e) {
        e.preventDefault();
        alert('✅ پیام شما با موفقیت ارسال شد!');
        this.reset();
    });
</script>
</body>
</html>