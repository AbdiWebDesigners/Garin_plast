<?php
require_once __DIR__ . '/../../includes/components/stat_card.php';
require_once __DIR__ . '/../../includes/components/search.php';
require_once __DIR__ . '/../../includes/components/empty_state.php';
require_once __DIR__ . '/../../includes/components/timeline.php';
?>

<div class="container-fluid">

    <!-- ===================== -->
    <!-- کارت های آماری -->
    <!-- ===================== -->

    <div class="row mb-4">

        <div class="col-lg-3 col-md-6 mb-3">

            <?php
            renderStatCard([
                'title' => 'کل فعالیت‌ها',
                'value' => number_format($totalRows),
                'icon'  => 'fa-clock',
                'color' => 'primary'
            ]);
            ?>

        </div>

        <div class="col-lg-3 col-md-6 mb-3">

            <?php
            renderStatCard([
                'title' => 'آخرین فعالیت',
                'value' => !empty($activities)
                    ? date('Y/m/d', strtotime($activities[0]['created_at']))
                    : '-',
                'icon'  => 'fa-calendar',
                'color' => 'success'
            ]);
            ?>

        </div>

        <div class="col-lg-3 col-md-6 mb-3">

            <?php
            renderStatCard([
                'title' => 'ماژول‌ها',
                'value' => count($modules),
                'icon'  => 'fa-layer-group',
                'color' => 'info'
            ]);
            ?>

        </div>

        <div class="col-lg-3 col-md-6 mb-3">

            <?php
            renderStatCard([
                'title' => 'IP فعلی',
                'value' => $_SERVER['REMOTE_ADDR'],
                'icon'  => 'fa-network-wired',
                'color' => 'warning'
            ]);
            ?>

        </div>

    </div>


    <!-- ===================== -->
    <!-- فرم جستجو -->
    <!-- ===================== -->

    <div class="card shadow-sm border-0 mb-4">

        <div class="card-body">

            <form method="GET">

                <div class="row g-3">

                    <div class="col-lg-5">

                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="جستجو در عملیات..."
                            value="<?= htmlspecialchars($search) ?>"
                        >

                    </div>

                    <div class="col-lg-3">

                        <select
                            name="module"
                            class="form-select"
                        >

                            <option value="">
                                همه ماژول‌ها
                            </option>

                            <?php foreach($modules as $m): ?>

                                <option
                                    value="<?= htmlspecialchars($m) ?>"
                                    <?= $module==$m ? 'selected' : '' ?>
                                >

                                    <?= ucfirst($m) ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <div class="col-lg-2">

                        <button
                            class="btn btn-primary w-100"
                            type="submit"
                        >

                            <i class="fa fa-search"></i>

                            جستجو

                        </button>

                    </div>

                    <div class="col-lg-2">

                        <a
                            href="activity.php"
                            class="btn btn-secondary w-100"
                        >

                            پاک کردن

                        </a>

                    </div>

                </div>

            </form>

        </div>

    </div>


    <!-- ===================== -->
    <!-- Timeline -->
    <!-- ===================== -->

    <div class="card shadow border-0">

        <div class="card-header bg-white">

            <h5 class="mb-0">

                <i class="fa fa-clock text-primary"></i>

                تاریخچه فعالیت‌ها

            </h5>

        </div>

        <div class="card-body">

            <?php if(empty($activities)): ?>

                <?php

                renderEmptyState([

                    'icon'       => 'fa-clock',

                    'title'      => 'فعالیتی یافت نشد',

                    'message'    => 'هیچ فعالیتی برای نمایش وجود ندارد.',

                    'color'      => 'info'

                ]);

                ?>

            <?php else: ?>

                <div class="timeline">

                    <?php foreach($activities as $row): ?>

                        <?php renderTimelineItem($row); ?>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </div>
        <?php if($totalPages > 1): ?>

<div class="card-footer bg-white">

    <nav>

        <ul class="pagination justify-content-center mb-0">

            <?php if($page > 1): ?>

                <li class="page-item">

                    <a
                        class="page-link"
                        href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>&module=<?= urlencode($module) ?>"
                    >
                        قبلی
                    </a>

                </li>

            <?php endif; ?>

            <?php

            $start = max(1, $page - 2);
            $end   = min($totalPages, $page + 2);

            for($i = $start; $i <= $end; $i++):

            ?>

                <li class="page-item <?= $i == $page ? 'active' : '' ?>">

                    <a
                        class="page-link"
                        href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&module=<?= urlencode($module) ?>"
                    >

                        <?= $i ?>

                    </a>

                </li>

            <?php endfor; ?>

            <?php if($page < $totalPages): ?>

                <li class="page-item">

                    <a
                        class="page-link"
                        href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>&module=<?= urlencode($module) ?>"
                    >
                        بعدی
                    </a>

                </li>

            <?php endif; ?>

        </ul>

    </nav>

</div>

<?php endif; ?>

</div>

</div>


<style>

.timeline{

position:relative;

}

.timeline-item{

transition:.25s;

}

.timeline-item:hover{

transform:translateY(-3px);

}

.card{

border-radius:14px;

}

.card-header{

border-bottom:1px solid #ececec;

}

.pagination .page-link{

border-radius:8px;

margin:0 3px;

}

</style>


<script>

document.addEventListener("DOMContentLoaded",function(){

document.querySelectorAll(".timeline-item").forEach(function(item){

item.addEventListener("mouseenter",function(){

this.style.transform="translateY(-3px)";

});

item.addEventListener("mouseleave",function(){

this.style.transform="translateY(0px)";

});

});

});

</script>