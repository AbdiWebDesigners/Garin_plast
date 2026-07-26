<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';


$pageTitle = 'مشتریان بالقوه';

try {
    if (isset($_GET['delete'])) {
        $deleteId = (int) $_GET['delete'];
        $stmt = $pdo->prepare("DELETE FROM sales_leads WHERE id = ?");
        $stmt->execute([$deleteId]);
        header("Location: index.php?deleted=1");
        exit;
    }

    if (isset($_GET['status'], $_GET['id'])) {
        $id = (int) $_GET['id'];
        $status = $_GET['status'];

        $allowedStatuses = ['new', 'contacted', 'quotation_sent', 'negotiation', 'won', 'lost'];
        if (in_array($status, $allowedStatuses, true)) {
            $stmt = $pdo->prepare("UPDATE sales_leads SET status = ? WHERE id = ?");
            $stmt->execute([$status, $id]);
            header("Location: index.php?updated=1");
            exit;
        }
    }

    $q = trim($_GET['q'] ?? '');

    $sql = "
        SELECT sl.*, sa.position AS agent_position
        FROM sales_leads sl
        LEFT JOIN sales_agents sa ON sa.id = sl.sales_agent_id
    ";
    $params = [];

    if ($q !== '') {
        $sql .= " WHERE sl.customer_name LIKE ? OR sl.company_name LIKE ? OR sl.phone LIKE ? OR sl.email LIKE ? OR sl.source LIKE ?";
        $like = "%$q%";
        $params = [$like, $like, $like, $like, $like];
    }

    $sql .= " ORDER BY sl.id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $leads = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("خطا در دریافت مشتریان بالقوه: " . $e->getMessage());
}

function leadBadge($status) {
    if ($status === 'new') return '<span class="badge bg-primary-subtle text-primary border border-primary-subtle">جدید</span>';
    if ($status === 'contacted') return '<span class="badge bg-info-subtle text-info border border-info-subtle">تماس گرفته شده</span>';
    if ($status === 'quotation_sent') return '<span class="badge bg-warning-subtle text-warning border border-warning-subtle">پیش‌فاکتور ارسال شد</span>';
    if ($status === 'negotiation') return '<span class="badge bg-success-subtle text-success border border-success-subtle">مذاکره</span>';
    if ($status === 'won') return '<span class="badge bg-success text-white">برنده</span>';
    if ($status === 'lost') return '<span class="badge bg-danger text-white">از دست رفته</span>';
    return '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">نامشخص</span>';
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
                <h4 class="mb-0 text-dark"><i class="fas fa-bullhorn me-2"></i>مشتریان بالقوه</h4>
            </div>
            <div class="col-md-6 text-end">
                <a href="../dashboard.php" class="btn btn-sm btn-outline-secondary me-1">داشبورد</a>
                <a href="add.php" class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i>افزودن</a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-4 py-2">
    <?php if (isset($_GET['added'])): ?>
        <div class="alert alert-success alert-dismissible fade show border-0" role="alert">مشتری بالقوه با موفقیت اضافه شد.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>
    <?php if (isset($_GET['updated'])): ?>
        <div class="alert alert-success alert-dismissible fade show border-0" role="alert">اطلاعات با موفقیت بروزرسانی شد.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>
    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success alert-dismissible fade show border-0" role="alert">رکورد با موفقیت حذف شد.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>

    <div class="bg-white rounded-4 shadow-sm p-4">
        <div class="row mb-3">
            <div class="col-md-6">
                <form method="get" class="d-flex gap-2">
                    <input type="text" name="q" class="form-control" placeholder="جستجو در نام، شرکت، تلفن، ایمیل یا منبع..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                    <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" width="60">#</th>
                        <th>نام مشتری</th>
                        <th>شرکت</th>
                        <th>تلفن</th>
                        <th>ایمیل</th>
                        <th>منبع</th>
                        <th>وضعیت</th>
                        <th>کارشناس فروش</th>
                        <th>پیگیری بعدی</th>
                        <th>تاریخ ثبت</th>
                        <th class="text-center" width="220">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($leads)): ?>
                        <tr><td colspan="11" class="text-center py-5 text-muted"><i class="fas fa-folder-open fa-3x d-block mb-3"></i>موردی یافت نشد.</td></tr>
                    <?php else: ?>
                        <?php foreach ($leads as $lead): ?>
                            <tr>
                                <td class="text-center fw-bold text-muted"><?= (int)$lead['id'] ?></td>
                                <td class="fw-semibold"><?= htmlspecialchars($lead['customer_name'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($lead['company_name'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($lead['phone'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($lead['email'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($lead['source'] ?? '-') ?></td>
                                <td><?= leadBadge($lead['status'] ?? 'new') ?></td>
                                <td><?= htmlspecialchars($lead['agent_position'] ?? '-') ?></td>
                                <td class="text-muted small"><?= htmlspecialchars($lead['next_followup'] ?? '-') ?></td>
                                <td class="text-muted small"><?= htmlspecialchars($lead['created_at'] ?? '-') ?></td>
                                <td class="text-center">
                                    <a href="?id=<?= (int)$lead['id'] ?>&status=contacted" class="btn btn-sm btn-outline-info me-1"><i class="fas fa-phone"></i></a>
                                    <a href="?id=<?= (int)$lead['id'] ?>&status=quotation_sent" class="btn btn-sm btn-outline-warning me-1"><i class="fas fa-file-invoice"></i></a>
                                    <a href="?id=<?= (int)$lead['id'] ?>&status=negotiation" class="btn btn-sm btn-outline-success me-1"><i class="fas fa-handshake"></i></a>
                                    <a href="view.php?id=<?= (int)$lead['id'] ?>" class="btn btn-sm btn-outline-primary me-1"><i class="fas fa-eye"></i></a>
                                    <a href="?delete=<?= (int)$lead['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('آیا از حذف این رکورد مطمئن هستید؟')"><i class="fas fa-trash"></i></a>
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