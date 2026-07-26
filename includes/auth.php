<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

function isLoggedIn(): bool
{
    return !empty($_SESSION['user_id']);
}

function currentUserId(): int
{
    return (int)($_SESSION['user_id'] ?? 0);
}

function currentUserRole(): string
{
    return strtolower(trim($_SESSION['role'] ?? ''));
}

function userRole(): string
{
    return currentUserRole();
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? '';
        header('Location: ' . BASE_URL . 'login.php');
        exit;
    }
}

function getRolePermissions(string $roleName): array
{
    static $cache = [];

    $roleName = strtolower(trim($roleName));
    if (empty($roleName)) {
        return [];
    }

    if (isset($cache[$roleName])) {
        return $cache[$roleName];
    }

    global $pdo;

    try {
        $stmt = $pdo->prepare("
            SELECT p.name
            FROM permissions p
            INNER JOIN role_permissions rp ON rp.permission_id = p.id
            INNER JOIN roles r ON r.id = rp.role_id
            WHERE r.name = ?
        ");
        $stmt->execute([$roleName]);
        $cache[$roleName] = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return $cache[$roleName];
    } catch (PDOException $e) {
        error_log("خطا در دریافت مجوزها: " . $e->getMessage());
        return [];
    }
}

function hasPermission(string $permission): bool
{
    $role = currentUserRole();

    if ($role === 'admin') {
        return true;
    }

    return in_array($permission, getRolePermissions($role), true);
}

function requirePermission(string $permission): void
{
    if (!hasPermission($permission)) {
        http_response_code(403);
        echo "شما مجوز دسترسی به این صفحه را ندارید.";
        exit;
    }
}
?>