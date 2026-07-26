<div class="container-fluid">

    <div class="row">

        <!-- پروفایل -->

        <div class="col-lg-4 mb-4">

            <div class="card shadow border-0">

                <div class="card-body text-center">

                    <?php

                    $avatar = !empty($user['avatar'])
                        ? BASE_URL . $user['avatar']
                        : BASE_URL . 'assets/images/default-avatar.png';

                    ?>

                    <img
                        src="<?= htmlspecialchars($avatar) ?>"
                        class="rounded-circle border shadow-sm mb-3"
                        style="width:160px;height:160px;object-fit:cover;"
                        alt="Avatar">

                    <h3 class="fw-bold">

                        <?= htmlspecialchars($user['fullname']) ?>

                    </h3>

                    <p class="text-muted mb-3">

                        <?= htmlspecialchars($user['email']) ?>

                    </p>

                    <?php if($user['status']): ?>

                        <span class="badge bg-success px-3 py-2">

                            <i class="fa fa-check-circle"></i>

                            فعال

                        </span>

                    <?php else: ?>

                        <span class="badge bg-danger px-3 py-2">

                            <i class="fa fa-times-circle"></i>

                            غیرفعال

                        </span>

                    <?php endif; ?>

                    <hr>

                    <div class="text-start">

                        <p class="mb-2">

                            <strong>

                                <i class="fa fa-phone text-primary"></i>

                                موبایل :

                            </strong>

                            <?= htmlspecialchars($user['mobile'] ?: '-') ?>

                        </p>

                        <p class="mb-2">

                            <strong>

                                <i class="fa fa-envelope text-primary"></i>

                                ایمیل :

                            </strong>

                            <?= htmlspecialchars($user['email']) ?>

                        </p>

                        <p class="mb-2">

                            <strong>

                                <i class="fa fa-user-tag text-primary"></i>

                                نقش :

                            </strong>

                            <?= htmlspecialchars($user['role']) ?>

                        </p>

                    </div>

                </div>

            </div>

        </div>

        <!-- اطلاعات اصلی -->

        <div class="col-lg-8">

            <div class="card shadow border-0">

                <div class="card-header bg-white">

                    <h4 class="mb-0">

                        <i class="fa fa-address-card text-primary"></i>

                        اطلاعات کاربر

                    </h4>

                </div>

                <div class="card-body">

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label text-muted">

                                نام و نام خانوادگی

                            </label>

                            <div class="fw-bold">

                                <?= htmlspecialchars($user['fullname']) ?>

                            </div>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label text-muted">

                                کد ملی

                            </label>

                            <div class="fw-bold">

                                <?= htmlspecialchars($user['national_code'] ?: '-') ?>

                            </div>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label text-muted">

                                سمت سازمانی

                            </label>

                            <div class="fw-bold">

                                <?= htmlspecialchars($user['job_title'] ?: '-') ?>

                            </div>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label text-muted">

                                واحد

                            </label>

                            <div class="fw-bold">

                                <?= htmlspecialchars($user['department'] ?: '-') ?>

                            </div>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label text-muted">

                                آخرین ورود

                            </label>

                            <div class="fw-bold">

                                <?= !empty($user['last_login']) ? htmlspecialchars($user['last_login']) : '-' ?>

                            </div>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label text-muted">

                                آخرین IP

                            </label>

                            <div class="fw-bold">

                                <?= !empty($user['last_ip']) ? htmlspecialchars($user['last_ip']) : '-' ?>

                            </div>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label text-muted">

                                تاریخ ایجاد

                            </label>

                            <div class="fw-bold">

                                <?= htmlspecialchars($user['created_at']) ?>

                            </div>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label text-muted">

                                آخرین بروزرسانی

                            </label>

                            <div class="fw-bold">

                                <?= htmlspecialchars($user['updated_at']) ?>

                            </div>

                        </div>

                    </div>

                    <hr>

                    <div class="mb-3">

                        <label class="form-label text-muted">

                            آدرس

                        </label>

                        <div class="border rounded p-3 bg-light">

                            <?= !empty($user['address']) ? nl2br(htmlspecialchars($user['address'])) : '-' ?>

                        </div>

                    </div>

                    <div class="mb-4">

                        <label class="form-label text-muted">

                            توضیحات

                        </label>

                        <div class="border rounded p-3 bg-light">

                            <?= !empty($user['description']) ? nl2br(htmlspecialchars($user['description'])) : '-' ?>

                        </div>

                    </div>

                    <div class="d-flex gap-2 flex-wrap">

                        <a
                            href="edit.php?id=<?= (int)$user['id'] ?>"
                            class="btn btn-warning">

                            <i class="fa fa-edit"></i>

                            ویرایش

                        </a>

                        <a
                            href="delete.php?id=<?= (int)$user['id'] ?>"
                            class="btn btn-danger"
                            onclick="return confirm('آیا از حذف این کاربر مطمئن هستید؟');">

                            <i class="fa fa-trash"></i>

                            حذف

                        </a>

                        <a
                            href="index.php"
                            class="btn btn-secondary">

                            <i class="fa fa-arrow-right"></i>

                            بازگشت

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<style>

.card{

    border-radius:14px;

}

.card-header{

    border-bottom:1px solid #ececec;

}

.rounded-circle{

    border:4px solid #f8f9fa;

}

.form-label{

    font-weight:600;

    margin-bottom:4px;

}

.bg-light{

    background:#f8f9fb !important;

}

</style>