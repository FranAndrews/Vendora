<!-- includes/header.php -->
<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Vendora</title>
  <!-- Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXX"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'G-XXXXXXXX');
  </script>
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 text-gray-800">

<!-- Header Nav -->
<header class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-50">
  <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
    <a href="<?= BASE_URL ?>" class="text-2xl font-bold text-orange-600 hover:text-orange-700 transition">
      <i class="fas fa-store mr-2"></i><?= SITE_NAME ?>
    </a>
    <nav class="flex items-center gap-6">
      <a href="<?= BASE_URL ?>" class="text-gray-700 hover:text-orange-600 transition">
        <i class="fas fa-home mr-1"></i>Home
      </a>
      <a href="<?= BASE_URL ?>buyer/products.php" class="text-gray-700 hover:text-orange-600 transition">
        <i class="fas fa-search mr-1"></i>Products
      </a>
      <a href="<?= BASE_URL ?>buyer/categories.php" class="text-gray-700 hover:text-orange-600 transition">
        <i class="fas fa-list mr-1"></i>Categories
      </a>
      <a href="<?= BASE_URL ?>buyer/cart.php" class="relative text-gray-700 hover:text-orange-600 transition">
        <i class="fas fa-shopping-cart mr-1"></i>Cart
        <span id="cart-count" class="absolute -top-2 -right-2 bg-orange-600 text-white text-xs rounded-full px-2 py-0.5">
          <?= isset($_SESSION['cart_count']) ? $_SESSION['cart_count'] : 0 ?>
        </span>
      </a>

      <?php if (isset($_SESSION['user_id'])): ?>
        <div class="relative group">
          <button class="text-gray-700 hover:text-orange-600 transition flex items-center gap-2">
            <i class="fas fa-user-circle"></i>
            <span><?= htmlspecialchars($_SESSION['username'] ?? 'Account') ?></span>
            <i class="fas fa-chevron-down text-xs"></i>
          </button>
          <div class="hidden group-hover:block absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-50">
            <?php if ($_SESSION['user_role'] === 'seller'): ?>
              <a href="<?= BASE_URL ?>seller/dashboard.php" class="block px-4 py-2 text-gray-700 hover:bg-orange-50 hover:text-orange-600">
                <i class="fas fa-store-alt mr-2"></i>Seller Dashboard
              </a>
              <a href="<?= BASE_URL ?>" class="block px-4 py-2 text-gray-700 hover:bg-orange-50 hover:text-orange-600">
                <i class="fas fa-shopping-bag mr-2"></i>Shop as Buyer
              </a>
            <?php endif; ?>
            <a href="<?= BASE_URL ?>buyer/orders.php" class="block px-4 py-2 text-gray-700 hover:bg-orange-50 hover:text-orange-600">
              <i class="fas fa-box mr-2"></i>My Orders
            </a>
            <a href="<?= BASE_URL ?>logout.php" class="block px-4 py-2 text-gray-700 hover:bg-orange-50 hover:text-orange-600">
              <i class="fas fa-sign-out-alt mr-2"></i>Logout
            </a>
          </div>
        </div>
      <?php else: ?>
        <a href="<?= BASE_URL ?>login.php" class="text-gray-700 hover:text-orange-600 transition">
          <i class="fas fa-sign-in-alt mr-1"></i>Login
        </a>
        <a href="<?= BASE_URL ?>register.php" class="bg-orange-600 text-white px-4 py-2 rounded-md hover:bg-orange-700 transition text-sm">
          <i class="fas fa-store-alt mr-1"></i>Register
        </a>
      <?php endif; ?>
    </nav>
  </div>
</header>

<!-- CART JS -->
<script>
  function getCart() {
    return JSON.parse(localStorage.getItem('cart')) || [];
  }

  function updateCartCount() {
    const count = getCart().length;
    const badge = document.getElementById('cart-count');
    if (badge) badge.textContent = count;
  }

  document.addEventListener('DOMContentLoaded', () => {
    updateCartCount();
  });
</script>

<!-- Page Content Container Start -->
<main class="max-w-7xl mx-auto px-4 py-6">
