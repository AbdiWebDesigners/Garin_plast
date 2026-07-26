<?php

require_once __DIR__ . '/../../includes/components/alert.php';
require_once __DIR__ . '/../../includes/components/upload.php';
require_once __DIR__ . '/../../includes/components/form.php';

?>

<div class="container-fluid">

    <?php if (!empty($errors)): ?>

        <?php
        renderAlert([
            'type'    => 'danger',
            'title'   => 'خطا در ثبت اطلاعات',
            'message' => implode('<br>', $errors)
        ]);
        ?>

    <?php endif; ?>

    <?php if (!empty($success)): ?>

        <?php
        renderAlert([
            'type'    => 'success',
            'title'   => 'موفق',
            'message' => $success
        ]);
        ?>

    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">

        <div class="card shadow border-0">

            <div class="card-header bg-white">

                <h4 class="mb-0">

                    <i class="fa fa-user-plus text-success"></i>

                    ایجاد کاربر جدید

                </h4>

            </div>

            <div class="card-body">

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
                            value="<?= htmlspecialchars($_POST['fullname'] ?? '') ?>"
                        >

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
                            value="<?= htmlspecialchars($_POST['mobile'] ?? '') ?>"
                        >

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
                            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                        >

                    </div>

                    <!-- رمز -->

                    <div class="col-lg-6 mb-3">

                        <label class="form-label">

                            رمز عبور

                        </label>

                        <input
                            type="password"
                            name="password"
                            class="form-control"
                            required
                        >

                    </div>

                    <!-- نقش -->

                    <div class="col-lg-6 mb-3">

                        <label class="form-label">

                            نقش کاربر

                        </label>

                        <select
                            name="role"
                            class="form-select"
                        >

                            <option value="admin">مدیر سیستم</option>

                            <option value="sales_manager">مدیر فروش</option>

                            <option value="sales_agent">کارشناس فروش</option>

                            <option value="anbar_manager">مدیر انبار</option>

                            <option value="production_worker">کارگر تولید</option>
                            <option value="production">مدیر تولید</option>

                            <option value="accountant">حسابدار</option>

                            <option value="support">پشتیبانی</option>

                            <option value="user" selected>کاربر</option>

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
                            value="<?= htmlspecialchars($_POST['job_title'] ?? '') ?>"
                        >

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
                            value="<?= htmlspecialchars($_POST['department'] ?? '') ?>"
                        >

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
                            value="<?= htmlspecialchars($_POST['national_code'] ?? '') ?>"
                        >

                    </div>

                    <!-- آدرس -->

                    <div class="col-12 mb-3">

                        <label class="form-label">

                            آدرس

                        </label>

                        <textarea
                            name="address"
                            class="form-control"
                            rows="3"
                        ><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>

                    </div>

                    <!-- توضیحات -->

                    <div class="col-12 mb-3">

                        <label class="form-label">

                            توضیحات

                        </label>

                        <textarea
                            name="description"
                            class="form-control"
                            rows="4"
                        ><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>

                    </div>

                    <!-- آواتار -->

                    <div class="col-lg-6 mb-4">

                        <label class="form-label">

                            تصویر پروفایل

                        </label>

                        <input
                            type="file"
                            class="form-control"
                            name="avatar"
                            accept=".jpg,.jpeg,.png,.webp"
                        >

                        <div class="form-text">

                            فرمت‌های مجاز:
                            JPG - PNG - WEBP

                        </div>

                    </div>

                    <!-- وضعیت -->

                    <div class="col-lg-6 mb-4 d-flex align-items-center">

                        <div class="form-check form-switch mt-4">

                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="status"
                                checked
                            >

                            <label class="form-check-label">

                                کاربر فعال باشد

                            </label>

                        </div>

                    </div>
                    </div>

</div>

<div class="card-footer bg-white">

    <div class="d-flex justify-content-between">

        <a
            href="index.php"
            class="btn btn-secondary"
        >

            <i class="fa fa-arrow-right"></i>

            بازگشت

        </a>

        <div>

            <button
                type="reset"
                class="btn btn-warning"
            >

                <i class="fa fa-rotate-left"></i>

                پاک کردن

            </button>

            <button
                type="submit"
                class="btn btn-success"

            >

                <i class="fa fa-save"></i>

                ثبت کاربر

            </button>

        </div>

    </div>

</div>

</div>

</form>

</div>

<script>

document.addEventListener("DOMContentLoaded",function(){

/*
|--------------------------------------------------------------------------
| Preview Avatar
|--------------------------------------------------------------------------
*/

const avatarInput=document.querySelector('input[name="avatar"]');

if(avatarInput){

avatarInput.addEventListener("change",function(){

const file=this.files[0];

if(!file) return;

const reader=new FileReader();

reader.onload=function(e){

    let preview=document.getElementById("avatar-preview");

    if(!preview){

        preview=document.createElement("img");

        preview.id="avatar-preview";

        preview.className="img-thumbnail mt-3";

        preview.style.maxWidth="160px";

        preview.style.maxHeight="160px";

        avatarInput.parentNode.appendChild(preview);

    }

    preview.src=e.target.result;

};

reader.readAsDataURL(file);

});

}

/*
|--------------------------------------------------------------------------
| Password Strength
|--------------------------------------------------------------------------
*/

const pass=document.querySelector('input[name="password"]');

if(pass){

const info=document.createElement("small");

info.className="form-text mt-2";

pass.parentNode.appendChild(info);

pass.addEventListener("keyup",function(){

let score=0;

if(this.value.length>=8) score++;

if(/[A-Z]/.test(this.value)) score++;

if(/[a-z]/.test(this.value)) score++;

if(/[0-9]/.test(this.value)) score++;

if(/[^A-Za-z0-9]/.test(this.value)) score++;

switch(score){

    case 0:
    case 1:

        info.className="text-danger";

        info.innerHTML="رمز عبور بسیار ضعیف";

    break;

    case 2:

        info.className="text-warning";

        info.innerHTML="رمز عبور ضعیف";

    break;

    case 3:

        info.className="text-info";

        info.innerHTML="رمز عبور متوسط";

    break;

    case 4:

        info.className="text-primary";

        info.innerHTML="رمز عبور خوب";

    break;

    default:

        info.className="text-success";

        info.innerHTML="رمز عبور بسیار قوی";

}

});

}

});
</script>