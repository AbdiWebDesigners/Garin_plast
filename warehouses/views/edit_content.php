<?php
/**
 * -------------------------------------------------------
 * Garin ERP
 * Warehouses
 * Edit Warehouse
 * warehouses/views/edit_content.php
 * -------------------------------------------------------
 */

global $pdo;

$error   = '';
$success = '';

/*
|--------------------------------------------------------------------------
| دریافت کاربران (مدیر انبار)
|--------------------------------------------------------------------------
*/

try {

    $stmtUsers = $pdo->prepare("
        SELECT id, fullname
        FROM users
        WHERE status = 1
        ORDER BY fullname
    ");

    $stmtUsers->execute();

    $users = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {

    $users = [];

}


/*
|--------------------------------------------------------------------------
| ذخیره ویرایش
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $warehouse_code = trim($_POST['warehouse_code'] ?? '');
    $warehouse_name = trim($_POST['warehouse_name'] ?? '');
    $warehouse_type = trim($_POST['warehouse_type'] ?? '');
    $manager_id     = $_POST['manager_id'] ?? '';
    $province       = trim($_POST['province'] ?? '');
    $city           = trim($_POST['city'] ?? '');
    $address        = trim($_POST['address'] ?? '');
    $phone          = trim($_POST['phone'] ?? '');
    $status         = $_POST['status'] ?? 'active';


    /*
    |--------------------------------------------------------------------------
    | اعتبارسنجی
    |--------------------------------------------------------------------------
    */

    if ($warehouse_name == '') {

        $error = "نام انبار الزامی است.";

    }

    elseif ($warehouse_type == '') {

        $error = "نوع انبار الزامی است.";

    }

    else {

        try {

            /*
            |--------------------------------------------------------------------------
            | بررسی تکراری نبودن کد انبار
            |--------------------------------------------------------------------------
            */

            if ($warehouse_code != '') {

                $check = $pdo->prepare("
                    SELECT id
                    FROM warehouses
                    WHERE warehouse_code = ?
                    AND id <> ?
                    LIMIT 1
                ");

                $check->execute([
                    $warehouse_code,
                    $id
                ]);

                if ($check->fetch()) {

                    throw new Exception("کد انبار قبلاً ثبت شده است.");

                }

            }

            /*
            |--------------------------------------------------------------------------
            | بروزرسانی
            |--------------------------------------------------------------------------
            */

            $stmt = $pdo->prepare("
                UPDATE warehouses
                SET

                    warehouse_code = :warehouse_code,
                    warehouse_name = :warehouse_name,
                    warehouse_type = :warehouse_type,
                    manager_id     = :manager_id,
                    province       = :province,
                    city           = :city,
                    address        = :address,
                    phone          = :phone,
                    status         = :status

                WHERE id = :id
            ");

            $stmt->execute([

                ':warehouse_code' =>
                    $warehouse_code != ''
                    ? $warehouse_code
                    : null,

                ':warehouse_name' => $warehouse_name,

                ':warehouse_type' => $warehouse_type,

                ':manager_id' =>
                    $manager_id != ''
                    ? $manager_id
                    : null,

                ':province' =>
                    $province != ''
                    ? $province
                    : null,

                ':city' =>
                    $city != ''
                    ? $city
                    : null,

                ':address' =>
                    $address != ''
                    ? $address
                    : null,

                ':phone' =>
                    $phone != ''
                    ? $phone
                    : null,

                ':status' => $status,

                ':id' => $id

            ]);

            $_SESSION['success'] = "اطلاعات انبار با موفقیت ویرایش شد.";

            header("Location: index.php");

            exit;

        } catch (Exception $e) {

            $error = $e->getMessage();

        }

    }

}
?>
<div class="container-fluid py-4">

    <div class="card shadow-sm border-0">

        <div class="card-header bg-white">

            <div class="d-flex justify-content-between align-items-center">

                <h4 class="mb-0 fw-bold">

                    <i class="fa fa-edit text-warning"></i>

                    ویرایش انبار

                </h4>

                <a href="index.php" class="btn btn-secondary">

                    <i class="fa fa-arrow-right"></i>

                    بازگشت

                </a>

            </div>

        </div>

        <div class="card-body">

            <?php if($error): ?>

                <div class="alert alert-danger">

                    <?= htmlspecialchars($error) ?>

                </div>

            <?php endif; ?>

            <form method="POST">

                <div class="row g-4">

                    <div class="col-md-3">

                        <label class="form-label">

                            کد انبار

                        </label>

                        <input
                            type="text"
                            name="warehouse_code"
                            class="form-control"
                            maxlength="30"
                            value="<?= htmlspecialchars($warehouse_code) ?>">

                    </div>

                    <div class="col-md-5">

                        <label class="form-label">

                            نام انبار

                            <span class="text-danger">*</span>

                        </label>

                        <input
                            type="text"
                            name="warehouse_name"
                            class="form-control"
                            required
                            maxlength="150"
                            value="<?= htmlspecialchars($warehouse_name) ?>">

                    </div>

                    <div class="col-md-4">

                        <label class="form-label">

                            نوع انبار

                        </label>

                        <select
                            name="warehouse_type"
                            class="form-select"
                            required>

                            <option value="">انتخاب کنید</option>

                            <option value="raw_material"
                                <?= $warehouse_type=='raw_material'?'selected':'' ?>>

                                مواد اولیه

                            </option>

                            <option value="production"
                                <?= $warehouse_type=='production'?'selected':'' ?>>

                                تولید

                            </option>

                            <option value="finished_goods"
                                <?= $warehouse_type=='finished_goods'?'selected':'' ?>>

                                کالای ساخته شده

                            </option>

                            <option value="general"
                                <?= $warehouse_type=='general'?'selected':'' ?>>

                                عمومی

                            </option>

                            <option value="consignment"
                                <?= $warehouse_type=='consignment'?'selected':'' ?>>

                                امانی

                            </option>

                        </select>

                    </div>

                    <div class="col-md-4">

                        <label class="form-label">

                            مدیر انبار

                        </label>

                        <select
                            name="manager_id"
                            class="form-select">

                            <option value="">

                                بدون مدیر

                            </option>

                            <?php foreach($users as $user): ?>

                                <option
                                    value="<?= $user['id'] ?>"
                                    <?= ($manager_id==$user['id'])?'selected':'' ?>>

                                    <?= htmlspecialchars($user['fullname']) ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <div class="col-md-2">

                        <label class="form-label">

                            وضعیت

                        </label>

                        <select
                            name="status"
                            class="form-select">

                            <option value="active"
                                <?= $status=='active'?'selected':'' ?>>

                                فعال

                            </option>

                            <option value="inactive"
                                <?= $status=='inactive'?'selected':'' ?>>

                                غیرفعال

                            </option>

                        </select>

                    </div>

                    <div class="col-md-3">

                        <label class="form-label">

                            استان

                        </label>

                        <input
                            type="text"
                            name="province"
                            class="form-control"
                            value="<?= htmlspecialchars($province) ?>">

                    </div>

                    <div class="col-md-3">

                        <label class="form-label">

                            شهر

                        </label>

                        <input
                            type="text"
                            name="city"
                            class="form-control"
                            value="<?= htmlspecialchars($city) ?>">

                    </div>

                    <div class="col-md-3">

                        <label class="form-label">

                            تلفن

                        </label>

                        <input
                            type="text"
                            name="phone"
                            class="form-control"
                            value="<?= htmlspecialchars($phone) ?>">

                    </div>

                    <div class="col-md-12">

                        <label class="form-label">

                            آدرس

                        </label>

                        <textarea
                            name="address"
                            rows="4"
                            class="form-control"><?= htmlspecialchars($address) ?></textarea>

                    </div>