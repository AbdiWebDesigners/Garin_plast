<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

$pageTitle = 'کارشناسان فروش';
$message = '';

if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("SELECT user_id FROM sales_agents WHERE id = ? LIMIT 1");
        $stmt->execute([$deleteId]);
        $agent = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$agent) {
            throw new RuntimeException('کارشناس فروش موردنظر یافت نشد.');
        }

        $stmt = $pdo->prepare("DELETE FROM sales_agents WHERE id = ?");
        $stmt->execute([$deleteId]);

        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([(int)$agent['user_id']]);

        $pdo->commit();

        header("Location: index.php?deleted=1");
        exit;
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        $message = 'خطا در حذف کارشناس فروش: ' . $e->getMessage();
    }
}

$q = trim($_GET['q'] ?? '');

try {
    $sql = "
        SELECT
            sa.*,
            u.fullname,
            u.mobile,
            u.email,
            u.role,
            u.status AS user_status
        FROM sales_agents sa
        LEFT JOIN users u ON u.id = sa.user_id
    ";

    $params = [];

    if ($q !== '') {
        $sql .= "
            WHERE
                u.fullname LIKE ?
                OR u.mobile LIKE ?
                OR u.email LIKE ?
                OR sa.position LIKE ?
                OR sa.bio LIKE ?
        ";

        $like = "%{$q}%";
        $params = [$like, $like, $like, $like, $like];
    }

    $sql .= " ORDER BY sa.id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $agents = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('خطا در دریافت کارشناسان فروش: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-body-secondary text-dark">

<div class="py-3 mb-4 bg-light border-bottom shadow-sm">
    <div class="container-fluid px-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h4 class="mb-0 text-dark">
                    <i class="fas fa-user-tie me-2"></i>
                    کارشناسان فروش
                </h4>
            </div>

            <div class="col-md-6 text-end">
                <a href="../dashboard.php" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-arrow-right me-1"></i>
                    بازگشت به داشبورد
                </a>

                <a href="add.php" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>
                    افزودن
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-4 py-2">

    <?php if (isset($_GET['added'])): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm">
            کارشناس فروش با موفقیت اضافه شد.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['updated'])): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm">
            اطلاعات کارشناس فروش با موفقیت ویرایش شد.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm">
            کارشناس فروش با موفقیت حذف شد.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($message): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm">
            <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-4 shadow-sm p-4">

        <div class="row mb-3">
            <div class="col-md-6">
                <form method="get" class="d-flex gap-2">
                    <input
                        type="text"
                        name="q"
                        class="form-control"
                        placeholder="جستجو در نام، موبایل، ایمیل، سمت یا بیو..."
                        value="<?= htmlspecialchars($q) ?>"
                    >

                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>

                    <?php if ($q !== ''): ?>
                        <a href="index.php" class="btn btn-outline-secondary">
                            پاک کردن
                        </a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" width="60">#</th>
                        <th>نام و نام خانوادگی</th>
                        <th>موبایل</th>
                        <th>ایمیل</th>
                        <th>سمت</th>
                        <th>کمیسیون</th>
                        <th>وضعیت</th>
                        <th>عکس</th>
                        <th class="text-center" width="180">عملیات</th>
                    </tr>
                </thead>

                <tbody>
                <?php if (empty($agents)): ?>
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="fas fa-folder-open fa-3x d-block mb-3"></i>
                            هیچ کارشناس فروشی یافت نشد.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($agents as $agent): ?>
                        <tr>
                            <td class="text-center fw-bold text-muted">
                                <?= (int)$agent['id'] ?>
                            </td>

                            <td class="fw-semibold">
                                <?= htmlspecialchars($agent['fullname'] ?? '-') ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($agent['mobile'] ?? '-') ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($agent['email'] ?? '-') ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($agent['position'] ?? '-') ?>
                            </td>

                            <td>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                    <?= number_format((float)($agent['commission_rate'] ?? 0), 2) ?>%
                                </span>
                            </td>

                            <td>
                                <?php if ((int)($agent['user_status'] ?? 0) === 1): ?>
                                    <span class="badge bg-success">فعال</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">غیرفعال</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if (!empty($agent['photo'])): ?>
                                    <img
                                        src="<?= htmlspecialchars($agent['photo']) ?>"
                                        width="50"
                                        height="50"
                                        class="rounded-circle object-fit-cover"
                                        alt=""
                                    >
                                <?php else: ?>
                                    <span class="text-muted">ندارد</span>
                                <?php endif; ?>
                            </td>

                            <td class="text-center">
                                <a href="view.php?id=<?= (int)$agent['id'] ?>" class="btn btn-sm btn-outline-primary me-1">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <a href="edit.php?id=<?= (int)$agent['id'] ?>" class="btn btn-sm btn-outline-warning me-1">
                                    <i class="fas fa-pen"></i>
                                </a>

                                <a
                                    href="?delete=<?= (int)$agent['id'] ?>"
                                    class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('آیا از حذف این کارشناس فروش مطمئن هستید؟')"
                                >
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
