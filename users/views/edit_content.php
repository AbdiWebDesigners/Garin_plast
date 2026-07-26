<div class="container-fluid">

<?php if($error): ?>

<?php
renderAlert([
    'type'    => 'danger',
    'title'   => 'خطا',
    'message' => $error
]);
?>

<?php endif; ?>


<?php if($success): ?>

<?php
renderAlert([
    'type'    => 'success',
    'title'   => 'موفق',
    'message' => $success
]);
?>

<?php endif; ?>


<div class="card shadow-sm border-0">

<div class="card-header bg-white">

<h4 class="mb-0">

<i class="fa fa-user-pen text-primary"></i>

ویرایش کاربر

</h4>

</div>

<div class="card-body">

<form method="post"
      enctype="multipart/form-data">

<div class="row">

<!-- نام -->

<div class="col-lg-6 mb-3">

<label class="form-label">

نام و نام خانوادگی

</label>

<input
type="text"
name="fullname"
class="form-control"
required
value="<?= htmlspecialchars($fullname) ?>">

</div>


<!-- موبایل -->

<div class="col-lg-6 mb-3">

<label class="form-label">

موبایل

</label>

<input
type="text"
name="mobile"
class="form-control"
value="<?= htmlspecialchars($mobile) ?>">

</div>


<!-- ایمیل -->

<div class="col-lg-6 mb-3">

<label class="form-label">

ایمیل

</label>

<input
type="email"
name="email"
class="form-control"
required
value="<?= htmlspecialchars($email) ?>">

</div>


<!-- رمز -->

<div class="col-lg-6 mb-3">

<label class="form-label">

رمز عبور جدید

</label>

<input
type="password"
name="password"
class="form-control">

<small class="text-muted">

در صورت عدم تغییر، خالی بگذارید.

</small>

</div>


<!-- نقش -->

<div class="col-lg-6 mb-3">

<label class="form-label">

نقش

</label>

<select
name="role"
class="form-select">

<option value="admin"
<?= $role=='admin'?'selected':'' ?>>

مدیر سیستم

</option>

<option value="sales_manager"
<?= $role=='sales_manager'?'selected':'' ?>>

مدیر فروش

</option>

<option value="sales_agent"
<?= $role=='sales_agent'?'selected':'' ?>>

کارشناس فروش

</option>
<option value="support"
<?= $role=='support'?'selected':'' ?>>

پشتیبانی

</option>
<option value="production"
<?= $role=='production'?'selected':'' ?>>

مدیر تولید

</option>
<option value="anbar_manager"
<?= $role=='anbar_manager'?'selected':'' ?>>

مدیر انبار 

</option>
<option value="accountant"
<?= $role=='accountant'?'selected':'' ?>>

 حسابدار 

</option>
<option value="production_worker"
<?= $role=='production_worker'?'selected':'' ?>>

 کارگر تولید

</option>

<option value="user"
<?= $role=='user'?'selected':'' ?>>

کاربر

</option>

</select>

</div>


<!-- وضعیت -->

<div class="col-lg-6 mb-3">

<label class="form-label">

وضعیت

</label>

<select
name="status"
class="form-select">

<option value="1"
<?= $status==1?'selected':'' ?>>

فعال

</option>

<option value="0"
<?= $status==0?'selected':'' ?>>

غیرفعال

</option>

</select>

</div>


<!-- عنوان شغلی -->

<div class="col-lg-6 mb-3">

<label class="form-label">

عنوان شغلی

</label>

<input
type="text"
name="job_title"
class="form-control"
value="<?= htmlspecialchars($job_title) ?>">

</div>


<!-- دپارتمان -->

<div class="col-lg-6 mb-3">

<label class="form-label">

دپارتمان

</label>

<input
type="text"
name="department"
class="form-control"
value="<?= htmlspecialchars($department) ?>">
</div>
<!-- کد ملی -->

<div class="col-lg-6 mb-3">

    <label class="form-label">

        کد ملی

    </label>

    <input
        type="text"
        name="national_code"
        class="form-control"
        value="<?= htmlspecialchars($national_code) ?>">

</div>


<!-- آواتار -->

<div class="col-lg-6 mb-3">

    <label class="form-label">

        آواتار

    </label>

    <?php if(!empty($avatar)): ?>

        <div class="mb-2">

            <img
                id="avatarPreview"
                src="<?= BASE_URL ?>uploads/avatars/<?= htmlspecialchars($avatar) ?>"
                class="rounded-circle border"
                width="90"
                height="90"
                style="object-fit:cover;">

        </div>

    <?php else: ?>

        <div class="mb-2">

            <img
                id="avatarPreview"
                src="<?= BASE_URL ?>assets/images/default-avatar.png"
                class="rounded-circle border"
                width="90"
                height="90"
                style="object-fit:cover;">

        </div>

    <?php endif; ?>

    <input
        type="file"
        name="avatar"
        id="avatarInput"
        class="form-control"
        accept="image/*">

</div>


<!-- آدرس -->

<div class="col-12 mb-3">

    <label class="form-label">

        آدرس

    </label>

    <textarea
        name="address"
        rows="3"
        class="form-control"><?= htmlspecialchars($address) ?></textarea>

</div>


<!-- توضیحات -->

<div class="col-12 mb-4">

    <label class="form-label">

        توضیحات

    </label>

    <textarea
        name="description"
        rows="4"
        class="form-control"><?= htmlspecialchars($description) ?></textarea>

</div>

</div>


<hr>

<div class="d-flex justify-content-between">

    <a
        href="index.php"
        class="btn btn-secondary">

        <i class="fa fa-arrow-right"></i>

        بازگشت

    </a>

    <button
        type="submit"
        class="btn btn-primary">

        <i class="fa fa-save"></i>

        ذخیره تغییرات

    </button>

</div>

</form>

</div>

</div>

</div>


<script>

const avatarInput = document.getElementById("avatarInput");
const avatarPreview = document.getElementById("avatarPreview");

if(avatarInput){

    avatarInput.addEventListener("change",function(){

        if(this.files.length){

            avatarPreview.src = URL.createObjectURL(this.files[0]);

        }

    });

}

</script>