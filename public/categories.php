<?php
require_once __DIR__ . '/../includes/db.php';
$pageTitle = 'دسته بندی ها |گَرین پلاست';
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
        .category-card {
            transition: all 0.4s ease;
            border: none;
            border-radius: 15px;
            overflow: hidden;
        }
        .category-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 20px 40px rgba(0, 200, 83, 0.25);
        }
    </style>
</head>
<body>

<?php include '../includes/navbar.php'; ?>

<div class="container py-5">
    <h1 class="text-center display-5 fw-bold text-success mb-5">دسته‌بندی محصولات</h1>
    
    <div class="row g-4">
        <?php
        $cats = $pdo->query("SELECT * FROM categories")->fetchAll();
        foreach ($cats as $cat) {
            $name = $cat['name'] ?? $cat['title'] ?? $cat['cat_name'] ?? 'دسته‌بندی';
            $desc = $cat['description'] ?? $cat['desc'] ?? '';
            echo '
            <div class="col-md-4 col-sm-6">
                <div class="card category-card h-100 text-center shadow-sm p-5">
                    <i class="fas fa-box fa-4x text-success mb-4"></i>
                    <h4 class="fw-bold">'.htmlspecialchars($name).'</h4>
                    <p class="text-muted mb-4">'.htmlspecialchars($desc).'</p>
                    <a href="products.php?category=' . $cat['id'] . '" class="btn btn-outline-success">مشاهده محصولات</a>
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