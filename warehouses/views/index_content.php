<?php
global $pdo;

$error = '';
$success = '';

try {

    $stmt = $pdo->query("
        SELECT
            w.id,
            w.warehouse_code,
            w.warehouse_name,
            w.warehouse_type,
            w.province,
            w.city,
            w.phone,
            w.status,
            u.fullname AS manager_name
        FROM warehouses w
        LEFT JOIN users u
            ON w.manager_id = u.id
        ORDER BY w.id DESC
    ");

    $warehouses = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {

    $warehouses = [];
    $error = $e->getMessage();

}
?>

<div class="container-fluid py-4">
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">انبارها</h3>
            <div class="text-muted">مدیریت انبارهای سیستم</div>
        </div>
        <div>
            <a href="create.php" class="btn btn-success">
                <i class="fa fa-plus ms-1"></i>
                ثبت انبار جدید
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white">
            <h5 class="fw-bold mb-0">لیست انبارها</h5>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>کد انبار</th>
                        <th>نام انبار</th>
                        <th>نوع</th>
                        <th>مدیر</th>
                        <th>استان</th>
                        <th>شهر</th>
                        <th>تلفن</th>
                        <th>وضعیت</th>
                        <th width="160">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($warehouses)): ?>
                        <tr>
                            <td colspan="10" class="text-center text-muted py-5">هیچ انباری ثبت نشده است.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($warehouses as $row): ?>
                            <tr>
                                <td><?= (int)$row['id'] ?></td>
                                <td><?= htmlspecialchars($row['warehouse_code'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['warehouse_name']) ?></td>
                                <td>
                                    <?php
                                    switch ($row['warehouse_type']) {
                                        case 'raw_material':
                                            echo '<span class="badge bg-primary">مواد اولیه</span>';
                                            break;
                                        case 'production':
                                            echo '<span class="badge bg-success">تولید</span>';
                                            break;
                                        case 'finished_goods':
                                            echo '<span class="badge bg-info">کالای ساخته شده</span>';
                                            break;
                                        case 'general':
                                            echo '<span class="badge bg-secondary">عمومی</span>';
                                            break;
                                        case 'consignment':
                                            echo '<span class="badge bg-warning text-dark">امانی</span>';
                                            break;
                                        default:
                                            echo '<span class="badge bg-dark">نامشخص</span>';
                                    }
                                    ?>
                                </td>
                                <td><?= htmlspecialchars($row['manager_name'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['province'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['city'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['phone'] ?? '-') ?></td>
                                <td>
                                    <?php if (($row['status'] ?? 'active') === 'active'): ?>
                                        <span class="badge bg-success">فعال</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">غیرفعال</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('حذف شود؟')">
                                        <i class="fa fa-trash"></i>
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