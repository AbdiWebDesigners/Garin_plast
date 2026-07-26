<?php

if (!function_exists('logActivity')) {

    function logActivity($module, $action, $recordId = null, $description = null)
    {
        global $pdo;

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            return;
        }

        $ip = $_SERVER['REMOTE_ADDR'] ?? '';

        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        $stmt = $pdo->prepare("
            INSERT INTO activity_logs
            (
                user_id,
                module,
                action,
                record_id,
                description,
                ip_address,
                user_agent
            )
            VALUES
            (
                ?,?,?,?,?,?,?
            )
        ");

        $stmt->execute([
            $userId,
            $module,
            $action,
            $recordId,
            $description,
            $ip,
            $userAgent
        ]);
    }

}