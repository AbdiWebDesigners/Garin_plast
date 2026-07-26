<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/includes/db.php';

echo "<h3>تست اتصال دیتابیس و کاربران</h3>";

try {
    $users = $pdo->query("SELECT id, fullname, email, role, status, LEFT(password, 50) as pass_preview, LENGTH(password) as pass_len FROM users")->fetchAll();
    echo "<pre>";
    print_r($users);
    echo "</pre>";

    if (empty($users)) {
        echo "<h4 style='color:red'>هیچ کاربری در جدول users وجود ندارد!</h4>";
    }
} catch (Exception $e) {
    echo "خطا: " . $e->getMessage();
}
?>