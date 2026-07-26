<?php

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();
requirePermission('view_inventory');

$page_title = 'داشبورد انبار';

$contentFile = __DIR__ . '/views/dashboard_content.php';

require_once __DIR__ . '/../includes/layout.php';