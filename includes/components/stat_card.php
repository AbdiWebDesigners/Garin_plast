<?php
/**
 * ---------------------------------------------------------
 * Garin ERP
 * Statistic Card Component
 * includes/components/stat_card.php
 * ---------------------------------------------------------
 */

if (!function_exists('renderStatCard')) {

    function renderStatCard(array $options = []): void
    {
        $title   = $options['title']   ?? '';
        $value   = $options['value']   ?? '-';
        $icon    = $options['icon']    ?? 'fa-chart-bar';
        $color   = $options['color']   ?? 'primary';
        $footer  = $options['footer']  ?? '';
        $class   = $options['class']   ?? '';

        ?>

        <div class="card shadow-sm border-0 h-100 <?= htmlspecialchars($class) ?>">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center">

                    <div>

                        <small class="text-muted d-block mb-2">

                            <?= htmlspecialchars($title) ?>

                        </small>

                        <h3 class="fw-bold text-<?= htmlspecialchars($color) ?> mb-0">

                            <?= $value ?>

                        </h3>

                    </div>

                    <div>

                        <i class="fa <?= htmlspecialchars($icon) ?> fa-2x text-<?= htmlspecialchars($color) ?>"></i>

                    </div>

                </div>

            </div>

            <?php if ($footer != ''): ?>

                <div class="card-footer bg-white">

                    <?= $footer ?>

                </div>

            <?php endif; ?>

        </div>

        <?php
    }

}