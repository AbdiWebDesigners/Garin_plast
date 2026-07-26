<?php
/**
 * ---------------------------------------------------------
 * Garin ERP
 * Button Component
 * includes/components/button.php
 * ---------------------------------------------------------
 */

if (!function_exists('renderButton')) {

    function renderButton(array $options = []): void
    {
        $text     = $options['text'] ?? 'Button';
        $type     = $options['type'] ?? 'primary';
        $icon     = $options['icon'] ?? '';
        $href     = $options['href'] ?? '';
        $submit   = $options['submit'] ?? false;
        $outline  = $options['outline'] ?? false;
        $size     = $options['size'] ?? '';
        $block    = $options['block'] ?? false;
        $target   = $options['target'] ?? '';
        $id       = $options['id'] ?? '';
        $class    = $options['class'] ?? '';
        $disabled = $options['disabled'] ?? false;

        $btnClass = 'btn ';

        if ($outline) {
            $btnClass .= 'btn-outline-' . $type;
        } else {
            $btnClass .= 'btn-' . $type;
        }

        if ($size == 'sm') {
            $btnClass .= ' btn-sm';
        }

        if ($size == 'lg') {
            $btnClass .= ' btn-lg';
        }

        if ($block) {
            $btnClass .= ' w-100';
        }

        $btnClass .= ' ' . $class;

        if ($href != '') {

            ?>

            <a
                href="<?= htmlspecialchars($href) ?>"
                class="<?= htmlspecialchars($btnClass) ?>"
                <?= $target ? 'target="'.htmlspecialchars($target).'"' : '' ?>
                <?= $id ? 'id="'.htmlspecialchars($id).'"' : '' ?>
                <?= $disabled ? 'onclick="return false;"' : '' ?>
            >

                <?php if($icon!=''): ?>

                    <i class="fa <?= htmlspecialchars($icon) ?> me-2"></i>

                <?php endif; ?>

                <?= htmlspecialchars($text) ?>

            </a>

            <?php

        } else {

            ?>

            <button

                type="<?= $submit ? 'submit' : 'button' ?>"

                class="<?= htmlspecialchars($btnClass) ?>"

                <?= $id ? 'id="'.htmlspecialchars($id).'"' : '' ?>

                <?= $disabled ? 'disabled' : '' ?>

            >

                <?php if($icon!=''): ?>

                    <i class="fa <?= htmlspecialchars($icon) ?> me-2"></i>

                <?php endif; ?>

                <?= htmlspecialchars($text) ?>

            </button>

            <?php
        }

    }

}