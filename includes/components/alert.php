<?php
/**
 * ---------------------------------------------------------
 * Garin ERP
 * Alert Component
 * includes/components/alert.php
 * ---------------------------------------------------------
 */

if (!function_exists('renderAlert')) {

    function renderAlert(array $options = []): void
    {
        $type        = $options['type'] ?? 'info';
        $title       = $options['title'] ?? '';
        $message     = $options['message'] ?? '';
        $icon        = $options['icon'] ?? '';
        $dismissible = $options['dismissible'] ?? true;
        $class       = $options['class'] ?? '';

        if ($icon == '') {

            switch ($type) {

                case 'success':
                    $icon = 'fa-circle-check';
                    break;

                case 'danger':
                    $icon = 'fa-circle-xmark';
                    break;

                case 'warning':
                    $icon = 'fa-triangle-exclamation';
                    break;

                case 'info':
                    $icon = 'fa-circle-info';
                    break;

                default:
                    $icon = 'fa-bell';
            }
        }

        ?>

        <div class="alert alert-<?= htmlspecialchars($type) ?>

            <?= $dismissible ? 'alert-dismissible fade show' : '' ?>

            shadow-sm <?= htmlspecialchars($class) ?>"

             role="alert">

            <div class="d-flex align-items-start">

                <i class="fa <?= htmlspecialchars($icon) ?> me-3 mt-1"></i>

                <div class="flex-grow-1">

                    <?php if ($title != ''): ?>

                        <h6 class="fw-bold mb-1">

                            <?= htmlspecialchars($title) ?>

                        </h6>

                    <?php endif; ?>

                    <?= $message ?>

                </div>

            </div>

            <?php if ($dismissible): ?>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
                </button>

            <?php endif; ?>

        </div>

        <?php
    }

}