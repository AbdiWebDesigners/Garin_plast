<?php

require_once __DIR__ . '/config.php';

try {

    $dsn = "mysql:host=" . DB_HOST .
           ";dbname=" . DB_NAME .
           ";charset=utf8mb4";

    $pdo = new PDO(
        $dsn,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ]
    );

} catch (PDOException $e) {

    die(
        APP_DEBUG
            ? 'خطا در اتصال به پایگاه داده: ' . $e->getMessage()
            : 'خطا در اتصال به پایگاه داده.'
    );

}


/**
 * شمارش رکوردهای یک جدول
 */
function getCount(string $table): int
{
    global $pdo;

    try {

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM `$table`");
        $stmt->execute();

        return (int)$stmt->fetchColumn();

    } catch (PDOException $e) {

        error_log($e->getMessage());

        return 0;

    }
}