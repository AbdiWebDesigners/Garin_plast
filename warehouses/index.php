<?php

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();
requirePermission('view_inventory');

$page_title = 'مدیریت انبارها';

$contentFile = __DIR__ . '/views/index_content.php';

require_once __DIR__ . '/../includes/layout.php';