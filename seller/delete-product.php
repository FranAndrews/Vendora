<?php
session_start();

// Require seller role
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'seller') {
    header('Location: /login.php');
    exit();
}

$productId = $_GET['id'] ?? null;

if (!$productId || !isset($_SESSION['products'])) {
    header('Location: /seller/dashboard.php');
    exit();
}

foreach ($_SESSION['products'] as $index => $prod) {
    if ($prod['id'] == $productId) {
        unset($_SESSION['products'][$index]);
        $_SESSION['products'] = array_values($_SESSION['products']); // Re-index array
        break;
    }
}

header('Location: /seller/dashboard.php');
exit();
