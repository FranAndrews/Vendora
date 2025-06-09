<?php
session_start();

// Auth check for seller
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'seller') {
  header('Location: /login.php');
  exit();
}

// Dummy seller name (Replace later with DB session data)
$sellerName = "John Seller";

// Use session products if set, otherwise show test data (until DB setup)
$products = $_SESSION['products'] ?? [
  ['id' => 1, 'name' => 'Sample Product 1', 'price' => 49.99, 'image' => 'https://via.placeholder.com/150'],
  ['id' => 2, 'name' => 'Sample Product 2', 'price' => 25.00, 'image' => 'https://via.placeholder.com/150'],
  ['id' => 3, 'name' => 'Sample Product 3', 'price' => 99.99, 'image' => 'https://via.placeholder.com/150'],
  ['id' => 4, 'name' => 'Sample Product 4', 'price' => 15.50, 'image' => 'https://via.placeholder.com/150'],
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Seller Dashboard | Vendora</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">

  <!-- HEADER -->
  <header class="bg-white shadow p-4 flex justify-between items-center">
    <h1 class="text-xl font-bold text-gray-800">Seller Dashboard</h1>
    <a href="/logout.php" class="text-sm text-gray-600 hover:underline">Logout</a>
  </header>

  <main class="max-w-7xl mx-auto p-6">
    <h2 class="text-2xl font-semibold mb-4">Welcome, <?= htmlspecialchars($sellerName) ?>!</h2>

    <!-- Add Product CTA -->
    <div class="mb-6">
      <a href="/seller/add-product.php"
         class="inline-block bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded">
        + Add New Product
      </a>
    </div>

    <!-- Product List -->
    <section>
      <h3 class="text-xl font-semibold mb-6">Your Products</h3>

      <?php if (empty($products)): ?>
        <p class="text-gray-600">You haven't added any products yet.</p>
      <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          <?php foreach ($products as $prod): ?>
            <div class="bg-white rounded shadow hover:shadow-lg transition-shadow border border-gray-200 overflow-hidden flex flex-col">
              <img src="<?= htmlspecialchars($prod['image']) ?>" alt="<?= htmlspecialchars($prod['name']) ?>" class="w-full h-40 object-cover" />
              <div class="p-4 flex-1 flex flex-col">
                <h4 class="font-semibold text-gray-800 mb-2"><?= htmlspecialchars($prod['name']) ?></h4>
                <p class="text-gray-700 mb-4">$<?= number_format($prod['price'], 2) ?></p>
                <div class="mt-auto flex justify-between text-sm">
                  <a href="/seller/edit-product.php?id=<?= $prod['id'] ?>" class="text-blue-600 hover:underline">Edit</a>
                  <a href="/seller/delete-product.php?id=<?= $prod['id'] ?>" class="text-red-600 hover:underline" onclick="return confirm('Delete this product?');">Delete</a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>
  </main>

  <footer class="bg-white shadow p-4 text-center text-gray-600 text-sm">
    Vendora &copy; <?= date('Y') ?>
  </footer>

</body>
</html>
