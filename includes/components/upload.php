<?php
/**
 * ---------------------------------------------------------
 * Garin ERP
 * Upload Component
 * includes/components/upload.php
 * ---------------------------------------------------------
 */

if (!function_exists('renderUpload')) {

    function renderUpload(array $options = []): void
    {
        $name        = $options['name'] ?? 'file';
        $id          = $options['id'] ?? $name;
        $label       = $options['label'] ?? 'انتخاب فایل';
        $accept      = $options['accept'] ?? '*/*';
        $required    = $options['required'] ?? false;
        $multiple    = $options['multiple'] ?? false;
        $preview     = $options['preview'] ?? false;
        $previewId   = $options['previewId'] ?? 'uploadPreview';
        $help        = $options['help'] ?? '';
?>

<div class="mb-3">

    <label
        for="<?= htmlspecialchars($id) ?>"
        class="form-label fw-bold">

        <?= htmlspecialchars($label) ?>

    </label>

    <input

        type="file"

        class="form-control"

        id="<?= htmlspecialchars($id) ?>"

        name="<?= htmlspecialchars($name) ?><?= $multiple ? '[]' : '' ?>"

        accept="<?= htmlspecialchars($accept) ?>"

        <?= $required ? 'required' : '' ?>

        <?= $multiple ? 'multiple' : '' ?>

    >

    <?php if($help!=''): ?>

        <div class="form-text">

            <?= htmlspecialchars($help) ?>

        </div>

    <?php endif; ?>

    <?php if($preview): ?>

        <div class="mt-3">

            <img

                id="<?= htmlspecialchars($previewId) ?>"

                src=""

                class="img-thumbnail"

                style="display:none;max-width:220px;"

            >

        </div>

        <script>

        document.addEventListener("DOMContentLoaded",function(){

            const input=document.getElementById("<?= $id ?>");

            const preview=document.getElementById("<?= $previewId ?>");

            if(!input) return;

            input.addEventListener("change",function(){

                if(this.files.length===0){

                    preview.style.display="none";

                    return;

                }

                const file=this.files[0];

                if(!file.type.startsWith("image/")){

                    preview.style.display="none";

                    return;

                }

                const reader=new FileReader();

                reader.onload=function(e){

                    preview.src=e.target.result;

                    preview.style.display="block";

                };

                reader.readAsDataURL(file);

            });

        });

        </script>

    <?php endif; ?>

</div>

<?php

    }

}