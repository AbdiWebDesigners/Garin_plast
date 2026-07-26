<div class="row">

    <!-- اطلاعات کاربر -->
    <div class="col-lg-4 mb-4">

        <div class="card shadow-sm border-0">

            <div class="card-body text-center">

                <?php
                $avatar = !empty($user['avatar'])
                    ? BASE_URL . 'uploads/avatars/' . $user['avatar']
                    : BASE_URL . 'assets/img/avatar.png';
                ?>

                <img
                    src="<?= $avatar ?>"
                    class="rounded-circle shadow mb-3"
                    width="150"
                    height="150"
                    style="object-fit:cover;"
                >

                <h4 class="fw-bold mb-1">

                    <?= htmlspecialchars($user['fullname']) ?>

                </h4>

                <div class="text-muted mb-3">

                    <?= htmlspecialchars($user['job_title'] ?: 'بدون سمت') ?>

                </div>

                <span class="badge bg-primary">

                    <?= htmlspecialchars($user['role']) ?>

                </span>

                <hr>

                <table class="table table-sm">

                    <tr>

                        <th>وضعیت</th>

                        <td>

                            <?php if($user['status']) : ?>

                                <span class="badge bg-success">
                                    فعال
                                </span>

                            <?php else: ?>

                                <span class="badge bg-danger">
                                    غیرفعال
                                </span>

                            <?php endif; ?>

                        </td>

                    </tr>

                    <tr>

                        <th>آخرین ورود</th>

                        <td>

                            <?= $user['last_login'] ?: '-' ?>

                        </td>

                    </tr>

                    <tr>

                        <th>آخرین IP</th>

                        <td>

                            <?= htmlspecialchars($user['last_ip'] ?: '-') ?>

                        </td>

                    </tr>

                    <tr>

                        <th>تاریخ عضویت</th>

                        <td>

                            <?= htmlspecialchars($user['created_at']) ?>

                        </td>

                    </tr>

                </table>

            </div>

        </div>

    </div>

    <!-- فرم -->
    <div class="col-lg-8">

        <div class="card shadow-sm border-0">

            <div class="card-header bg-white">

                <h4 class="mb-0">

                    <i class="fa fa-user-edit text-primary"></i>

                    ویرایش اطلاعات

                </h4>

            </div>

            <div class="card-body">

                <?php if($success): ?>

                    <div class="alert alert-success">

                        <?= htmlspecialchars($success) ?>

                    </div>

                <?php endif; ?>

                <?php if($error): ?>

                    <div class="alert alert-danger">

                        <?= htmlspecialchars($error) ?>

                    </div>

                <?php endif; ?>

                <form method="post">

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                نام و نام خانوادگی

                            </label>

                            <input
                                type="text"
                                name="fullname"
                                class="form-control"
                                value="<?= htmlspecialchars($user['fullname']) ?>"
                                required
                            >

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                موبایل

                            </label>

                            <input
                                type="text"
                                name="mobile"
                                class="form-control"
                                value="<?= htmlspecialchars($user['mobile']) ?>"
                            >

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                ایمیل

                            </label>

                            <input
                                type="email"
                                name="email"
                                class="form-control"
                                value="<?= htmlspecialchars($user['email']) ?>"
                                required
                            >

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                سمت

                            </label>

                            <input
                                type="text"
                                name="job_title"
                                class="form-control"
                                value="<?= htmlspecialchars($user['job_title']) ?>"
                            >

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                واحد

                            </label>

                            <input
                                type="text"
                                name="department"
                                class="form-control"
                                value="<?= htmlspecialchars($user['department']) ?>"
                            >

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                کد ملی

                            </label>

                            <input
                                type="text"
                                name="national_code"
                                class="form-control"
                                value="<?= htmlspecialchars($user['national_code']) ?>"
                            >

                        </div>

                        <div class="col-md-12 mb-3">

                            <label class="form-label">

                                آدرس

                            </label>

                            <textarea
                                name="address"
                                class="form-control"
                                rows="3"
                            ><?= htmlspecialchars($user['address']) ?></textarea>

                        </div>

                        <div class="col-md-12 mb-3">

                            <label class="form-label">

                                توضیحات

                            </label>

                            <textarea
                                name="description"
                                class="form-control"
                                rows="4"
                            ><?= htmlspecialchars($user['description']) ?></textarea>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                نقش

                            </label>

                            <input
                                type="text"
                                class="form-control"
                                value="<?= htmlspecialchars($user['role']) ?>"
                                readonly
                            >

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                وضعیت

                            </label>

                            <input
                                type="text"
                                class="form-control"
                                value="<?= $user['status'] ? 'فعال' : 'غیرفعال' ?>"
                                readonly
                            >

                        </div>

                    </div>

                    <hr>

                    <div class="d-flex gap-2">

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >

                            <i class="fa fa-save"></i>

                            ذخیره اطلاعات

                        </button>

                        <a
                            href="index.php"
                            class="btn btn-secondary"
                        >

                            انصراف

                        </a>

                        <a
                            href="avatar.php"
                            class="btn btn-success"
                        >

                            تصویر پروفایل

                        </a>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>