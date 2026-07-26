<?php
require_once __DIR__ . '/../includes/db.php';
$pageTitle = 'پیشنهادات ویژه |  گَرین پلاس';
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
        .product-card {
            transition: all 0.4s ease;
            border: none;
            border-radius: 15px;
            overflow: hidden;
            position: relative;
        }
        .product-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 20px 40px rgba(0, 200, 83, 0.25);
        }
        .offer-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: #d32f2f;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: bold;
            z-index: 10;
        }
        .product-img {
            height: 240px;
            object-fit: cover;
        }
    </style>
</head>
<body>

<?php include '../includes/navbar.php'; ?>

<div class="container py-5">
    <h1 class="text-center display-5 fw-bold text-danger mb-5">
        <i class="fas fa-fire"></i> پیشنهادات ویژه
    </h1>

    <div class="row g-4">
        <?php
        $offers = $pdo->query("SELECT * FROM products ORDER BY id DESC LIMIT 12")->fetchAll();
        foreach ($offers as $p) {
            $img = !empty($p['image']) ? '../uploads/' . htmlspecialchars($p['image']) : '../attachments/loop-handle-bags-250x250.webp';
            echo '
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="card product-card h-100 shadow-sm">
                    <div class="offer-badge">ویژه</div>
                    <img src="'.$img.'" class="card-img-top product-img" alt="'.htmlspecialchars($p['title']).'">
                    <div class="card-body text-center d-flex flex-column">
                        <h6 class="fw-bold mb-2">'.htmlspecialchars($p['title']).'</h6>
                        <p class="text-success fw-bold fs-5 mt-auto">'.number_format($p['price']).' تومان</p>
                        <a href="#" class="btn btn-outline-danger btn-sm mt-3">افزودن به سبد</a>
                    </div>
                </div>
            </div>';
        }
        ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>