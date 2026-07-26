<?php
/**
 * ---------------------------------------------------------
 * Garin ERP
 * Card Component
 * includes/components/card.php
 * ---------------------------------------------------------
 */

if (!function_exists('renderCard')) {

    function renderCard(array $options = []): void
    {
        $title      = $options['title']      ?? '';
        $icon       = $options['icon']       ?? '';
        $content    = $options['content']    ?? '';
        $footer     = $options['footer']     ?? '';
        $class      = $options['class']      ?? '';
        $headerClass= $options['headerClass']?? 'bg-white';
        $bodyClass  = $options['bodyClass']  ?? '';
        $footerClass= $options['footerClass']?? 'bg-white';

        ?>

        <div class="card shadow-sm border-0 <?= htmlspecialchars($class) ?>">

            <?php if($title!=''): ?>

                <div class="card-header <?= htmlspecialchars($headerClass) ?>">

                    <h5 class="mb-0">

                        <?php if($icon!=''): ?>

                            <i class="fa <?= htmlspecialchars($icon) ?> me-2"></i>

                        <?php endif; ?>

                        <?= htmlspecialchars($title) ?>

                    </h5>

                </div>

            <?php endif; ?>

            <div class="card-body <?= htmlspecialchars($bodyClass) ?>">

                <?= $content ?>

            </div>

            <?php if($footer!=''): ?>

                <div class="card-footer <?= htmlspecialchars($footerClass) ?>">

                    <?= $footer ?>

                </div>

            <?php endif; ?>

        </div>

        <?php
    }

}