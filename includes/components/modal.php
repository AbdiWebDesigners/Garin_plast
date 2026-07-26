<?php
/**
 * ---------------------------------------------------------
 * Garin ERP
 * Modal Component
 * includes/components/modal.php
 * ---------------------------------------------------------
 */

if (!function_exists('renderModal')) {

    function renderModal(array $options = []): void
    {
        $id          = $options['id'] ?? 'garinModal';
        $title       = $options['title'] ?? 'Modal';
        $body        = $options['body'] ?? '';
        $footer      = $options['footer'] ?? '';
        $size        = $options['size'] ?? '';
        $centered    = $options['centered'] ?? true;
        $scrollable  = $options['scrollable'] ?? false;
        $static      = $options['static'] ?? false;

        $dialogClass = '';

        switch ($size) {

            case 'sm':
                $dialogClass .= ' modal-sm';
                break;

            case 'lg':
                $dialogClass .= ' modal-lg';
                break;

            case 'xl':
                $dialogClass .= ' modal-xl';
                break;
        }

        if ($centered) {
            $dialogClass .= ' modal-dialog-centered';
        }

        if ($scrollable) {
            $dialogClass .= ' modal-dialog-scrollable';
        }

?>

<div class="modal fade"

     id="<?= htmlspecialchars($id) ?>"

     tabindex="-1"

     <?= $static ? 'data-bs-backdrop="static" data-bs-keyboard="false"' : '' ?>

>

    <div class="modal-dialog<?= $dialogClass ?>">

        <div class="modal-content shadow">

            <div class="modal-header">

                <h5 class="modal-title">

                    <?= htmlspecialchars($title) ?>

                </h5>

                <button

                    type="button"

                    class="btn-close"

                    data-bs-dismiss="modal">

                </button>

            </div>

            <div class="modal-body">

                <?= $body ?>

            </div>

            <?php if($footer!=''): ?>

            <div class="modal-footer">

                <?= $footer ?>

            </div>

            <?php endif; ?>

        </div>

    </div>

</div>

<?php

    }

}