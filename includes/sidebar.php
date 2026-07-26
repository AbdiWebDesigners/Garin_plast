<?php
require_once __DIR__ . '/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function navActive($path) {
    $current = $_SERVER['REQUEST_URI'] ?? '';
    return strpos($current, $path) !== false ? 'active' : '';
}