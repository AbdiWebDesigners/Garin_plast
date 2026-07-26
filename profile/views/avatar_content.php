<div class="row">

    <!-- کارت پروفایل -->
    <div class="col-lg-4 mb-4">

        <div class="card shadow border-0">

            <div class="card-body text-center">

                <?php

                if (!empty($user['avatar']) && file_exists(__DIR__ . '/../../uploads/avatars/' . $user['avatar'])) {

                    $avatar = BASE_URL . 'uploads/avatars/' . $user['avatar'];

                } else {

                    $avatar = BASE_URL . 'uploads/avatars/default.png';

                }

                ?>

                <img

                    id="avatarPreview"

                    src="<?= $avatar ?>"

                    class="rounded-circle shadow"

                    style="width:200px;height:200px;object-fit:cover;border:5px solid #f5f5f5;"

                >

                <h4 class="mt-4">

                    <?= htmlspecialchars($user['fullname']) ?>

                </h4>

                <p class="text-muted">

                    تصویر پروفایل

                </p>

            </div>

        </div>

    </div>

    <!-- فرم -->
    <div class="col-lg-8">

        <div class="card shadow border-0">

            <div class="card-header bg-white">

                <h4 class="mb-0">

                    <i class="fa fa-camera text-primary"></i>

                    مدیریت تصویر پروفایل

                </h4>

            </div>

            <div class="card-body">

                <?php if ($success): ?>

                    <div class="alert alert-success">

                        <?= htmlspecialchars($success) ?>

                    </div>

                <?php endif; ?>

                <?php if ($error): ?>

                    <div class="alert alert-danger">

                        <?= htmlspecialchars($error) ?>

                    </div>

                <?php endif; ?>

                <form

                    method="post"

                    enctype="multipart/form-data"

                >

                    <div class="mb-4">

                        <label class="form-label">

                            انتخاب تصویر

                        </label>

                        <input

                            id="avatarInput"

                            type="file"

                            name="avatar"

                            class="form-control"

                            accept=".jpg,.jpeg,.png,.webp"

                            required

                        >

                        <div class="form-text">

                            فرمت‌های مجاز:

                            JPG - PNG - WEBP

                            <br>

                            حداکثر حجم:

                            2MB

                        </div>

                    </div>

                    <hr>

                    <div class="d-flex gap-2">

                        <button

                            class="btn btn-primary"

                            type="submit"

                        >

                            <i class="fa fa-upload"></i>

                            آپلود تصویر

                        </button>

                        <a

                            href="index.php"

                            class="btn btn-secondary"

                        >

                            بازگشت

                        </a>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

<script>

const input = document.getElementById('avatarInput');

const preview = document.getElementById('avatarPreview');

input.addEventListener('change', function(){

    const file = this.files[0];

    if(!file) return;

    const reader = new FileReader();

    reader.onload = function(e){

        preview.src = e.target.result;

    }

    reader.readAsDataURL(file);

});

</script>