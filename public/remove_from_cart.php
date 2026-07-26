<?php
session_start();

if (isset($_GET['id']) && isset($_SESSION['cart'][$_GET['id']])) {
    unset($_SESSION['cart'][$_GET['id']]);
    $_SESSION['success_message'] = 'محصول از سبد خرید حذف شد.';
}

header("Location: cart.php");
exit;
?>