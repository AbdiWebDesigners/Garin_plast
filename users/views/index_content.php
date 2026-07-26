<?php

require_once __DIR__ . '/../../includes/components/stat_card.php';
require_once __DIR__ . '/../../includes/components/search.php';
require_once __DIR__ . '/../../includes/components/table.php';
require_once __DIR__ . '/../../includes/components/button.php';
require_once __DIR__ . '/../../includes/components/badge.php';
require_once __DIR__ . '/../../includes/components/empty_state.php';

?>

<div class="container-fluid">

    <!-- Statistics -->

    <div class="row mb-4">

        <div class="col-lg-3 col-md-6 mb-3">

            <?php
            renderStatCard([
                'title' => 'کل کاربران',
                'value' => number_format($totalUsers),
                'icon'  => 'fa-users',
                'color' => 'primary'
            ]);
            ?>

        </div>

        <div class="col-lg-3 col-md-6 mb-3">

            <?php
            renderStatCard([
                'title' => 'کاربران فعال',
                'value' => number_format($activeUsers),
                'icon'  => 'fa-user-check',
                'color' => 'success'
            ]);
            ?>

        </div>

        <div class="col-lg-3 col-md-6 mb-3">

            <?php
            renderStatCard([
                'title' => 'غیرفعال',
                'value' => number_format($inactiveUsers),
                'icon'  => 'fa-user-slash',
                'color' => 'danger'
            ]);
            ?>

        </div>

        <div class="col-lg-3 col-md-6 mb-3">

            <?php
            renderStatCard([
                'title' => 'مدیران',
                'value' => number_format($adminUsers),
                'icon'  => 'fa-user-shield',
                'color' => 'warning'
            ]);
            ?>

        </div>

    </div>

    <!-- Search -->

    <div class="card shadow-sm border-0 mb-4">

        <div class="card-body">

            <form method="get">

                <div class="row">

                    <div class="col-lg-4">

                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="جستجوی کاربران..."
                            value="<?= htmlspecialchars($search) ?>"
                        >

                    </div>

                    <div class="col-lg-3">

                        <select
                            class="form-select"
                            name="role"
                        >

                            <option value="">همه نقش‌ها</option>

                            <?php foreach($roles as $r): ?>

                                <option
                                    value="<?= htmlspecialchars($r) ?>"
                                    <?= $role==$r?'selected':'' ?>
                                >

                                    <?= htmlspecialchars($r) ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <div class="col-lg-2">

                        <select
                            class="form-select"
                            name="status"
                        >

                            <option value="">همه وضعیت‌ها</option>

                            <option value="1" <?= $status==='1'?'selected':'' ?>>
                                فعال
                            </option>

                            <option value="0" <?= $status==='0'?'selected':'' ?>>
                                غیرفعال
                            </option>

                        </select>

                    </div>

                    <div class="col-lg-2">

                        <button class="btn btn-primary w-100">

                            <i class="fa fa-search"></i>

                            جستجو

                        </button>

                    </div>

                    <div class="col-lg-1">

                        <a href="index.php" class="btn btn-secondary w-100">

                            <i class="fa fa-rotate-left"></i>

                        </a>

                    </div>

                </div>

            </form>

        </div>

    </div>

    <!-- Table -->
    <div class="d-flex justify-content-between align-items-center mb-3">

<h5 class="mb-0">

    <i class="fa fa-users text-primary"></i>

    لیست کاربران

</h5>

<a href="create.php" class="btn btn-success">

    <i class="fa fa-user-plus"></i>

    افزودن کاربر

</a>

</div>


    <div class="card shadow border-0">

        <div class="card-header bg-white">

            <h4 class="mb-0">

                <i class="fa fa-users text-primary"></i>

                لیست کاربران

            </h4>

        </div>

        <div class="card-body">

            <?php if(empty($users)): ?>

                <?php

                renderEmptyState([
                    'icon'=>'fa-users',
                    'color'=>'secondary',
                    'title'=>'کاربری یافت نشد',
                    'message'=>'هیچ کاربری برای نمایش وجود ندارد.'
                ]);

                ?>

            <?php else: ?>

                <div class="table-responsive">

                    <table class="table table-hover align-middle">

                        <thead>

                        <tr>

                            <th>#</th>
                            <th>نام</th>
                            <th>ایمیل</th>
                            <th>موبایل</th>
                            <th>نقش</th>
                            <th>وضعیت</th>
                            <th>آخرین ورود</th>
                            <th width="180">عملیات</th>

                        </tr>

                        </thead>

                        <tbody>

                        <?php foreach($users as $user): ?>

                            <tr>

                                <td><?= $user['id'] ?></td>

                                <td><?= htmlspecialchars($user['fullname']) ?></td>

                                <td><?= htmlspecialchars($user['email']) ?></td>

                                <td><?= htmlspecialchars($user['mobile']) ?></td>

                                <td><?= htmlspecialchars($user['role']) ?></td>

                                <td>

                                    <?php
                                    renderBadge([
                                        'text' => $user['status'] ? 'فعال' : 'غیرفعال',
                                        'color'=> $user['status'] ? 'success' : 'danger'
                                    ]);
                                    ?>

                                </td>

                                <td>

                                    <?= $user['last_login'] ?: '-' ?>

                                </td>

                                <td>

                                    <a href="view.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-info">

                                        <i class="fa fa-eye"></i>

                                    </a>

                                    <a href="edit.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-warning">

                                        <i class="fa fa-edit"></i>

                                    </a>

                                    <a
                                        href="delete.php?id=<?= $user['id'] ?>"
                                        class="btn btn-sm btn-danger"
                                        onclick="return confirm('حذف شود؟')"
                                    >

                                        <i class="fa fa-trash"></i>

                                    </a>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php endif; ?>

        </div>

    </div>

</div>