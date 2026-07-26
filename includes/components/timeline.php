<?php
/**
 * --------------------------------------------------------
 * Timeline Component
 * Garin ERP
 * --------------------------------------------------------
 */

if (!function_exists('renderTimelineItem')) {

    function renderTimelineItem(array $item)
    {
        $action = $item['action'] ?? '';

        $icons = [
            'login'             => 'fa-right-to-bracket',
            'logout'            => 'fa-right-from-bracket',
            'edit'              => 'fa-pen',
            'create'            => 'fa-plus',
            'delete'            => 'fa-trash',
            'update'            => 'fa-pen',
            'change_avatar'     => 'fa-image',
            'change_password'   => 'fa-key',
            'upload'            => 'fa-upload',
            'download'          => 'fa-download'
        ];

        $colors = [
            'login'             => 'success',
            'logout'            => 'secondary',
            'edit'              => 'warning',
            'create'            => 'primary',
            'delete'            => 'danger',
            'update'            => 'info',
            'change_avatar'     => 'info',
            'change_password'   => 'danger',
            'upload'            => 'primary',
            'download'          => 'dark'
        ];

        $icon  = $icons[$action]  ?? 'fa-circle';
        $color = $colors[$action] ?? 'secondary';

        ?>

        <div class="timeline-item">

            <div class="timeline-line"></div>

            <div class="timeline-dot bg-<?= $color ?>">
                <i class="fa <?= $icon ?>"></i>
            </div>

            <div class="timeline-card shadow-sm">

                <div class="d-flex justify-content-between align-items-center">

                    <span class="badge bg-<?= $color ?>">
                        <i class="fa <?= $icon ?>"></i>
                        <?= htmlspecialchars($action) ?>
                    </span>

                    <small class="text-muted">
                        <?= htmlspecialchars($item['created_at'] ?? '') ?>
                    </small>

                </div>

                <div class="mt-3">

                    <?= nl2br(htmlspecialchars($item['description'] ?? '')) ?>

                </div>

                <hr>

                <div class="small text-muted">

                    <strong>Module :</strong>

                    <?= htmlspecialchars($item['module'] ?? '-') ?>

                    |

                    <strong>IP :</strong>

                    <?= htmlspecialchars($item['ip_address'] ?? '-') ?>

                    <?php if(!empty($item['user_agent'])): ?>

                        <br><br>

                        <small>

                            <?= htmlspecialchars(substr($item['user_agent'],0,120)) ?>

                            <?php if(strlen($item['user_agent'])>120): ?>

                                ...

                            <?php endif; ?>

                        </small>

                    <?php endif; ?>

                </div>

            </div>

        </div>

        <?php
    }

}
?>

<style>

.timeline{

    position:relative;

}

.timeline-item{

    position:relative;

    padding-right:55px;

    margin-bottom:28px;

}

.timeline-line{

    position:absolute;

    right:20px;

    top:0;

    bottom:-28px;

    width:3px;

    background:#dfe7ef;

}

.timeline-item:last-child .timeline-line{

    display:none;

}

.timeline-dot{

    position:absolute;

    right:8px;

    top:10px;

    width:28px;

    height:28px;

    border-radius:50%;

    color:#fff;

    display:flex;

    align-items:center;

    justify-content:center;

    font-size:12px;

    box-shadow:0 4px 12px rgba(0,0,0,.15);

}

.timeline-card{

    background:#fff;

    border-radius:12px;

    padding:18px;

    transition:.25s;

    border-right:4px solid #0d6efd;

}

.timeline-card:hover{

    transform:translateY(-3px);

    box-shadow:0 12px 25px rgba(0,0,0,.12);

}

</style>