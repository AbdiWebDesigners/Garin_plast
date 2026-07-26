<?php
/**
 * ---------------------------------------------------------
 * Garin ERP
 * Form Component
 * includes/components/form.php
 * ---------------------------------------------------------
 */

if (!function_exists('renderFormField')) {

    function renderFormField(array $options = []): void
    {
        $type        = $options['type'] ?? 'text';
        $name        = $options['name'] ?? '';
        $id          = $options['id'] ?? $name;
        $label       = $options['label'] ?? '';
        $value       = $options['value'] ?? '';
        $placeholder = $options['placeholder'] ?? '';
        $required    = $options['required'] ?? false;
        $readonly    = $options['readonly'] ?? false;
        $disabled    = $options['disabled'] ?? false;
        $rows        = $options['rows'] ?? 4;
        $optionsList = $options['options'] ?? [];
        $help        = $options['help'] ?? '';
        $class       = $options['class'] ?? '';
?>

<div class="mb-3">

    <?php if($label!=''): ?>

        <label
            for="<?= htmlspecialchars($id) ?>"
            class="form-label fw-bold">

            <?= htmlspecialchars($label) ?>

            <?php if($required): ?>

                <span class="text-danger">*</span>

            <?php endif; ?>

        </label>

    <?php endif; ?>

    <?php
    switch($type){

        case 'textarea':
    ?>

            <textarea

                id="<?= htmlspecialchars($id) ?>"

                name="<?= htmlspecialchars($name) ?>"

                class="form-control <?= htmlspecialchars($class) ?>"

                rows="<?= $rows ?>"

                placeholder="<?= htmlspecialchars($placeholder) ?>"

                <?= $required?'required':'' ?>

                <?= $readonly?'readonly':'' ?>

                <?= $disabled?'disabled':'' ?>

            ><?= htmlspecialchars($value) ?></textarea>

    <?php
        break;

        case 'select':
    ?>

            <select

                id="<?= htmlspecialchars($id) ?>"

                name="<?= htmlspecialchars($name) ?>"

                class="form-select <?= htmlspecialchars($class) ?>"

                <?= $required?'required':'' ?>

                <?= $disabled?'disabled':'' ?>

            >

                <?php foreach($optionsList as $key=>$text): ?>

                    <option

                        value="<?= htmlspecialchars($key) ?>"

                        <?= ($value==$key)?'selected':'' ?>

                    >

                        <?= htmlspecialchars($text) ?>

                    </option>

                <?php endforeach; ?>

            </select>

    <?php
        break;

        default:
    ?>

            <input

                type="<?= htmlspecialchars($type) ?>"

                id="<?= htmlspecialchars($id) ?>"

                name="<?= htmlspecialchars($name) ?>"

                class="form-control <?= htmlspecialchars($class) ?>"

                value="<?= htmlspecialchars($value) ?>"

                placeholder="<?= htmlspecialchars($placeholder) ?>"

                <?= $required?'required':'' ?>

                <?= $readonly?'readonly':'' ?>

                <?= $disabled?'disabled':'' ?>

            >

    <?php
    }
    ?>

    <?php if($help!=''): ?>

        <div class="form-text">

            <?= htmlspecialchars($help) ?>

        </div>

    <?php endif; ?>

</div>

<?php
    }

}