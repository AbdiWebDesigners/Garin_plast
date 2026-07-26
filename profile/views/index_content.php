<div class="row">

    <div class="col-lg-12">

        <div class="card shadow-sm border-0">

            <div class="card-header bg-white">

                <h3 class="mb-0">
                    پروفایل من
                </h3>

            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-lg-3 text-center">

                        <img
                            src="<?= BASE_URL ?>assets/img/avatar.png"
                            class="rounded-circle shadow mb-3"
                            width="140"
                            height="140"
                        >

                        <h4 class="mt-2">
                            <?= htmlspecialchars($user['fullname']) ?>
                        </h4>

                        <span class="badge bg-primary">
                            <?= htmlspecialchars($user['role']) ?>
                        </span>

                    </div>

                    <div class="col-lg-9">

                        <table class="table table-bordered table-hover align-middle">

                            <tr>
                                <th width="220">نام و نام خانوادگی</th>
                                <td><?= htmlspecialchars($user['fullname']) ?></td>
                            </tr>

                            <tr>
                                <th>موبایل</th>
                                <td><?= htmlspecialchars($user['mobile']) ?></td>
                            </tr>

                            <tr>
                                <th>ایمیل</th>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                            </tr>

                            <tr>
                                <th>نقش</th>
                                <td><?= htmlspecialchars($user['role']) ?></td>
                            </tr>

                            <tr>
                                <th>وضعیت</th>
                                <td><?= $status ?></td>
                            </tr>

                            <tr>
                                <th>تاریخ عضویت</th>
                                <td><?= htmlspecialchars($user['created_at']) ?></td>
                            </tr>

                        </table>

                    </div>

                </div>

            </div>

            <div class="card-footer bg-white">

                <a href="edit.php" class="btn btn-primary">
                    <i class="fa fa-edit"></i>
                    ویرایش اطلاعات
                </a>

                <a href="avatar.php" class="btn btn-success">
                    <i class="fa fa-image"></i>
                    تصویر پروفایل
                </a>

                <a href="activity.php" class="btn btn-info text-white">
                    <i class="fa fa-history"></i>
                    فعالیت‌ها
                </a>

                <a href="change_password.php" class="btn btn-warning">
                    <i class="fa fa-key"></i>
                    تغییر رمز عبور
                </a>

            </div>

        </div>

    </div>

</div>