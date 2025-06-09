<?php
// admin/dashboard.php
session_start();
require_once __DIR__ . '/../includes/db.php';


// Get some basic statistics
$userCount = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
$sellerCount = $conn->query("SELECT COUNT(*) FROM users WHERE role='seller'")->fetch_row()[0];
$productCount = $conn->query("SELECT COUNT(*) FROM products")->fetch_row()[0];
$orderCount = $conn->query("SELECT COUNT(*) FROM orders")->fetch_row()[0];

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard | Vendora</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
  <header class="bg-white shadow p-4 flex justify-between items-center">
    <h1 class="text-xl font-bold text-gray-800">Admin Dashboard</h1>
    <a href="../logout.php" class="text-sm text-red-600 hover:underline">Logout</a>
  </header>

  <main class="max-w-5xl mx-auto p-6">
    <h2 class="text-2xl font-semibold mb-6">Welcome, Admin</h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="bg-white p-4 rounded shadow">
        <h3 class="text-lg font-semibold">Total Users</h3>
        <p class="text-2xl font-bold"><?= $userCount ?></p>
      </div>

      <div class="bg-white p-4 rounded shadow">
        <h3 class="text-lg font-semibold">Total Sellers</h3>
        <p class="text-2xl font-bold"><?= $sellerCount ?></p>
      </div>

      <div class="bg-white p-4 rounded shadow">
        <h3 class="text-lg font-semibold">Total Products</h3>
        <p class="text-2xl font-bold"><?= $productCount ?></p>
      </div>

      <div class="bg-white p-4 rounded shadow">
        <h3 class="text-lg font-semibold">Total Orders</h3>
        <p class="text-2xl font-bold"><?= $orderCount ?></p>
      </div>
    </div>
  </main>
</body>
</html>