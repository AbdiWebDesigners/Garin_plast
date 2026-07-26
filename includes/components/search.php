<?php
/**
 * ---------------------------------------------------------
 * Garin ERP
 * Search Component
 * includes/components/search.php
 * ---------------------------------------------------------
 */

if (!function_exists('renderSearch')) {

    function renderSearch(array $options = []): void
    {
        $action      = $options['action'] ?? '';
        $method      = $options['method'] ?? 'get';
        $placeholder = $options['placeholder'] ?? 'جستجو...';
        $name        = $options['name'] ?? 'search';
        $value       = $options['value'] ?? '';
        $buttonText  = $options['buttonText'] ?? 'جستجو';
        $buttonIcon  = $options['buttonIcon'] ?? 'fa-search';
        $resetUrl    = $options['resetUrl'] ?? '';
        $class       = $options['class'] ?? '';
        ?>

        <form
            action="<?= htmlspecialchars($action) ?>"
            method="<?= htmlspecialchars($method) ?>"
            class="<?= htmlspecialchars($class) ?>"
        >

            <div class="input-group">

                <span class="input-group-text">

                    <i class="fa fa-search"></i>

                </span>

                <input
                    type="text"
                    class="form-control"
                    name="<?= htmlspecialchars($name) ?>"
                    placeholder="<?= htmlspecialchars($placeholder) ?>"
                    value="<?= htmlspecialchars($value) ?>"
                >

                <button
                    class="btn btn-primary"
                    type="submit"
                >

                    <i class="fa <?= htmlspecialchars($buttonIcon) ?>"></i>

                    <?= htmlspecialchars($buttonText) ?>

                </button>

                <?php if($resetUrl!=''): ?>

                    <a
                        href="<?= htmlspecialchars($resetUrl) ?>"
                        class="btn btn-secondary"
                    >

                        <i class="fa fa-rotate-left"></i>

                        پاک کردن

                    </a>

                <?php endif; ?>

            </div>

        </form>

        <?php
    }

}