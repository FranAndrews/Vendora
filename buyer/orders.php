<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'buyer') {
    header('Location: /login.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>My Orders | Vendora</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 p-6 min-h-screen">
  <h1 class="text-2xl font-bold mb-4">My Orders</h1>
  <p class="text-gray-600">No orders placed yet. This will be updated once checkout is implemented.</p>
</body>
</html>
