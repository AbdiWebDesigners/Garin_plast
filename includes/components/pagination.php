<?php
/**
 * ---------------------------------------------------------
 * Garin ERP
 * Pagination Component
 * includes/components/pagination.php
 * ---------------------------------------------------------
 */

if (!function_exists('renderPagination')) {

    function renderPagination(array $options = [])
    {
        $page       = max(1, (int)($options['page'] ?? 1));
        $totalPages = max(1, (int)($options['totalPages'] ?? 1));
        $url        = $options['url'] ?? '';
        $params     = $options['params'] ?? [];

        if ($totalPages <= 1) {
            return;
        }

        ?>

        <nav class="mt-4">

            <ul class="pagination justify-content-center">

                <!-- Previous -->

                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">

                    <a class="page-link"

                       href="<?= htmlspecialchars(buildPaginationUrl($url, $params, $page - 1)) ?>">

                        <i class="fa fa-angle-right"></i>

                    </a>

                </li>

                <?php

                $start = max(1, $page - 2);
                $end   = min($totalPages, $page + 2);

                for ($i = $start; $i <= $end; $i++):

                ?>

                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">

                        <a class="page-link"

                           href="<?= htmlspecialchars(buildPaginationUrl($url, $params, $i)) ?>">

                            <?= $i ?>

                        </a>

                    </li>

                <?php endfor; ?>

                <!-- Next -->

                <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">

                    <a class="page-link"

                       href="<?= htmlspecialchars(buildPaginationUrl($url, $params, $page + 1)) ?>">

                        <i class="fa fa-angle-left"></i>

                    </a>

                </li>

            </ul>

        </nav>

        <?php
    }

}

if (!function_exists('buildPaginationUrl')) {

    function buildPaginationUrl($url, array $params, $page)
    {
        $params['page'] = $page;

        return $url . '?' . http_build_query($params);
    }

}