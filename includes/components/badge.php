<?php
/**
 * ---------------------------------------------------------
 * Garin ERP
 * Badge Component
 * includes/components/badge.php
 * ---------------------------------------------------------
 */

if (!function_exists('renderBadge')) {

    function renderBadge(array $options = []): void
    {
        $text    = $options['text'] ?? '';
        $type    = $options['type'] ?? 'secondary';
        $icon    = $options['icon'] ?? '';
        $pill    = $options['pill'] ?? false;
        $class   = $options['class'] ?? '';

        ?>

        <span class="
            badge
            bg-<?= htmlspecialchars($type) ?>
            <?= $pill ? 'rounded-pill' : '' ?>
            <?= htmlspecialchars($class) ?>
        ">

            <?php if($icon != ''): ?>

                <i class="fa <?= htmlspecialchars($icon) ?> me-1"></i>

            <?php endif; ?>

            <?= htmlspecialchars($text) ?>

        </span>

        <?php
    }

}