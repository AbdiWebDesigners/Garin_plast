<?php
$rootPath = dirname(__DIR__, 2);
require_once $rootPath . '/includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();
if (!hasPermission('manage_admin')) exit;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $site_name = trim($_POST['site_name'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $mobile    = trim($_POST['mobile'] ?? '');
    $whatsapp  = trim($_POST['whatsapp'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $address   = trim($_POST['address'] ?? '');
    $instagram = trim($_POST['instagram'] ?? '');
    $telegram  = trim($_POST['telegram'] ?? '');
    $linkedin  = trim($_POST['linkedin'] ?? '');
    $bale      = trim($_POST['bale'] ?? '');
    $eitaa     = trim($_POST['eitaa'] ?? '');

    $logo = $_POST['old_logo'] ?? '';

    // آپلود لوگو
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $target_dir = "../uploads/settings/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);
        
        $logo_name = time() . '_' . basename($_FILES['logo']['name']);
        $target_file = $target_dir . $logo_name;
        
        if (move_uploaded_file($_FILES['logo']['tmp_name'], $target_file)) {
            $logo = 'uploads/settings/' . $logo_name;
        }
    }

    $stmt = $pdo->prepare("UPDATE settings SET 
        site_name=?, logo=?, phone=?, mobile=?, whatsapp=?, email=?, 
        address=?, instagram=?, telegram=?, linkedin=?, bale=?, eitaa=? 
        LIMIT 1");

    $stmt->execute([$site_name, $logo, $phone, $mobile, $whatsapp, $email, 
                    $address, $instagram, $telegram, $linkedin, $bale, $eitaa]);

    header("Location: index.php?success=1");
    exit;
}

header("Location: index.php");
exit;