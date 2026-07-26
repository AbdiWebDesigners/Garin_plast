<?php
/**
 * ---------------------------------------------------------
 * Garin ERP
 * Table Component
 * includes/components/table.php
 * ---------------------------------------------------------
 */

if (!function_exists('renderTable')) {

    function renderTable(array $options = []): void
    {
        $headers      = $options['headers'] ?? [];
        $rows         = $options['rows'] ?? [];
        $responsive   = $options['responsive'] ?? true;
        $striped      = $options['striped'] ?? true;
        $hover        = $options['hover'] ?? true;
        $bordered     = $options['bordered'] ?? false;
        $small        = $options['small'] ?? false;
        $class        = $options['class'] ?? '';

        $tableClass = "table align-middle mb-0";

        if ($striped) {
            $tableClass .= " table-striped";
        }

        if ($hover) {
            $tableClass .= " table-hover";
        }

        if ($bordered) {
            $tableClass .= " table-bordered";
        }

        if ($small) {
            $tableClass .= " table-sm";
        }

        $tableClass .= " " . $class;

        if ($responsive) {
            echo '<div class="table-responsive">';
        }

        ?>

        <table class="<?= htmlspecialchars($tableClass) ?>">

            <?php if (!empty($headers)): ?>

                <thead class="table-light">

                <tr>

                    <?php foreach ($headers as $header): ?>

                        <th>

                            <?= htmlspecialchars($header) ?>

                        </th>

                    <?php endforeach; ?>

                </tr>

                </thead>

            <?php endif; ?>

            <tbody>

            <?php if (empty($rows)): ?>

                <tr>

                    <td colspan="<?= count($headers) ?>"

                        class="text-center text-muted py-4">

                        اطلاعاتی برای نمایش وجود ندارد.

                    </td>

                </tr>

            <?php else: ?>

                <?php foreach ($rows as $row): ?>

                    <tr>

                        <?php foreach ($row as $cell): ?>

                            <td>

                                <?= $cell ?>

                            </td>

                        <?php endforeach; ?>

                    </tr>

                <?php endforeach; ?>

            <?php endif; ?>

            </tbody>

        </table>

        <?php

        if ($responsive) {
            echo '</div>';
        }
    }

}