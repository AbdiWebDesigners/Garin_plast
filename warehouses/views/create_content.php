<?php
/**
 * -------------------------------------------------------
 * Garin ERP
 * Warehouses
 * Create Warehouse
 * warehouses/views/create_content.php
 * -------------------------------------------------------
 */

global $pdo;

$error = '';
$success = '';

$warehouse_code = '';
$warehouse_name = '';
$warehouse_type = '';
$manager_id = '';
$province = '';
$city = '';
$address = '';
$phone = '';
$status = 'active';

/*
|--------------------------------------------------------------------------
| دریافت لیست کاربران جهت مدیر انبار
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
| ثبت فرم
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $warehouse_code = trim($_POST['warehouse_code'] ?? '');
    $warehouse_name = trim($_POST['warehouse_name'] ?? '');
    $warehouse_type = trim($_POST['warehouse_type'] ?? '');
    $manager_id = $_POST['manager_id'] ?? '';
    $province = trim($_POST['province'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $status = $_POST['status'] ?? 'active';

    /*
    |--------------------------------------------------------------------------
    | اعتبارسنجی
    |--------------------------------------------------------------------------
    */

    if ($warehouse_name == '') {

        $error = "نام انبار الزامی است.";

    }

    elseif ($warehouse_type == '') {

        $error = "نوع انبار را انتخاب کنید.";

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
                    LIMIT 1
                ");

                $check->execute([$warehouse_code]);

                if ($check->fetch()) {

                    throw new Exception("کد انبار قبلاً ثبت شده است.");

                }

            }

            /*
            |--------------------------------------------------------------------------
            | ثبت اطلاعات
            |--------------------------------------------------------------------------
            */

            $stmt = $pdo->prepare("
                INSERT INTO warehouses
                (
                    warehouse_code,
                    warehouse_name,
                    warehouse_type,
                    manager_id,
                    province,
                    city,
                    address,
                    phone,
                    status
                )
                VALUES
                (
                    :warehouse_code,
                    :warehouse_name,
                    :warehouse_type,
                    :manager_id,
                    :province,
                    :city,
                    :address,
                    :phone,
                    :status
                )
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

                ':status' => $status

            ]);

            $_SESSION['success'] = "انبار با موفقیت ثبت شد.";

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

                    <i class="fa fa-warehouse text-primary"></i>

                    ثبت انبار جدید

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

            <form method="POST" autocomplete="off">

                <div class="row g-4">

                    <!-- کد انبار -->

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

                    <!-- نام انبار -->

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

                    <!-- نوع انبار -->

                    <div class="col-md-4">

                        <label class="form-label">

                            نوع انبار

                            <span class="text-danger">*</span>

                        </label>

                        <select
                            name="warehouse_type"
                            class="form-select"
                            required>

                            <option value="">انتخاب نمایید</option>

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

                    <!-- مدیر انبار -->

                    <div class="col-md-6">

                        <label class="form-label">

                            مدیر انبار

                        </label>

                        <select
                            name="manager_id"
                            class="form-select">

                            <option value="">

                                --- انتخاب مدیر ---

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

                    <!-- وضعیت -->

                    <div class="col-md-3">

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
                                        <!-- استان -->

                    <div class="col-md-3">

                        <label class="form-label">

                            استان

                        </label>

                        <input
                            type="text"
                            name="province"
                            class="form-control"
                            maxlength="100"
                            value="<?= htmlspecialchars($province) ?>">

                    </div>

                    <!-- شهر -->

                    <div class="col-md-3">

                        <label class="form-label">

                            شهر

                        </label>

                        <input
                            type="text"
                            name="city"
                            class="form-control"
                            maxlength="100"
                            value="<?= htmlspecialchars($city) ?>">

                    </div>

                    <!-- تلفن -->

                    <div class="col-md-3">

                        <label class="form-label">

                            تلفن

                        </label>

                        <input
                            type="text"
                            name="phone"
                            class="form-control"
                            maxlength="30"
                            value="<?= htmlspecialchars($phone) ?>">

                    </div>

                    <!-- آدرس -->

                    <div class="col-md-12">

                        <label class="form-label">

                            آدرس انبار

                        </label>

                        <textarea
                            name="address"
                            rows="4"
                            class="form-control"><?= htmlspecialchars($address) ?></textarea>

                    </div>

                    <hr class="mt-4 mb-3">

                    <div class="col-12">

                        <div class="d-flex justify-content-end gap-2">

                            <button
                                type="submit"
                                class="btn btn-success">

                                <i class="fa fa-save ms-1"></i>

                                ذخیره اطلاعات

                            </button>

                            <button
                                type="reset"
                                class="btn btn-warning">

                                <i class="fa fa-eraser ms-1"></i>

                                پاک کردن فرم

                            </button>

                            <a
                                href="index.php"
                                class="btn btn-secondary">

                                <i class="fa fa-times ms-1"></i>

                                انصراف

                            </a>

                        </div>

                    </div>

                </div>

            </form>

        </div>

    </div>

</div>