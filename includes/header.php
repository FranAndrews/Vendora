<!-- includes/header.php -->
<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Vendora</title>
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800">

<!-- Header Nav -->
<header class="bg-white border-b border-gray-200 shadow-sm">
  <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
    <a href="../../index.php" class="text-2xl font-bold text-blue-600">Vendora </a>
    <nav class="flex items-center gap-4">
      <!-- TODO: Add role-based nav links here -->
       <a href="../buyer/cart.php" class="relative text-sm text-gray-700 hover:text-blue-600 ml-4">
  🛒 Cart
  <span id="cart-count" class="absolute -top-2 -right-2 bg-blue-600 text-white text-xs rounded-full px-2 py-0.5">
    0
  </span>
</a>


<!-- CART JS INCREMENTATION -->
 <script>
  function getCart() {
    return JSON.parse(localStorage.getItem('cart')) || [];
  }

  function updateCartCount() {
    const count = getCart().length;
    const badge = document.getElementById('cart-count');
    if (badge) badge.textContent = count;
  }

  document.addEventListener('DOMContentLoaded', updateCartCount);
</script>


     <a href="/Vendora/register.php" class="text-blue-600 hover:underline">Sell</a>
  

      <?php if (isset($_SESSION['user'])): ?>
        <span class="text-sm text-gray-500">Hi, <?= htmlspecialchars($_SESSION['user']['name']) ?></span>
        <a href="/logout.php" class="text-blue-600 hover:underline text-sm">Logout</a>
      <?php else: ?>
        <a href="./login.php" class="text-blue-600 hover:underline text-sm">Login</a>
      <?php endif; ?>
    </nav>
  </div>
</header>

<!-- Page Content Container Start -->
<main class="max-w-7xl mx-auto px-4 py-6">
