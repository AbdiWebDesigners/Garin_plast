<?php

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();
requirePermission('view_inventory');

$page_title = 'ثبت انبار جدید';

$contentFile = __DIR__ . '/views/create_content.php';

require_once __DIR__ . '/../includes/layout.php';