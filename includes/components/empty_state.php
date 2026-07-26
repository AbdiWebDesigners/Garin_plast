<?php
/**
 * ---------------------------------------------------------
 * Garin ERP
 * Empty State Component
 * includes/components/empty_state.php
 * ---------------------------------------------------------
 */

if (!function_exists('renderEmptyState')) {

    function renderEmptyState(array $options = []): void
    {
        $icon       = $options['icon'] ?? 'fa-folder-open';
        $title      = $options['title'] ?? 'اطلاعاتی یافت نشد';
        $message    = $options['message'] ?? '';
        $buttonText = $options['buttonText'] ?? '';
        $buttonLink = $options['buttonLink'] ?? '#';
        $color      = $options['color'] ?? 'secondary';
?>

<div class="card shadow-sm border-0">

    <div class="card-body text-center py-5">

        <i class="fa <?= htmlspecialchars($icon) ?> fa-4x text-<?= htmlspecialchars($color) ?> mb-4"></i>

        <h4 class="fw-bold mb-3">

            <?= htmlspecialchars($title) ?>

        </h4>

        <?php if($message!=''): ?>

            <p class="text-muted mb-4">

                <?= htmlspecialchars($message) ?>

            </p>

        <?php endif; ?>

        <?php if($buttonText!=''): ?>

            <a
                href="<?= htmlspecialchars($buttonLink) ?>"
                class="btn btn-primary">

                <i class="fa fa-plus-circle"></i>

                <?= htmlspecialchars($buttonText) ?>

            </a>

        <?php endif; ?>

    </div>

</div>

<?php
    }

}