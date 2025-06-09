<?php 
require_once 'includes/db.php'; 
session_start();

// Fetch all products from DB (with seller info if needed)
$stmt = $conn->prepare("SELECT p.id, p.name, p.price, p.image_path, u.name AS seller_name 
                        FROM products p 
                        JOIN users u ON p.seller_id = u.id 
                        ORDER BY p.id DESC LIMIT 20");
$stmt->execute();
$result = $stmt->get_result();

$products = [];
while ($row = $result->fetch_assoc()) 
    $products[] = $row;


if (isset($_SESSION['user_id'])) {
    echo "Welcome back, User #" . htmlspecialchars($_SESSION['user_id']) . "!";
    // You can also fetch user info from the database to show username or email here
} else {
    echo "You are not logged in. <a href='login.php'>Login here</a>";
}
?>


?>

<?php include 'includes/header.php'; ?>

<!-- HERO SECTION -->
<section class="bg-gradient-to-r from-blue-50 to-blue-100 py-16 px-6 mb-10">
  <div class="max-w-7xl mx-auto grid md:grid-cols-2 gap-10 items-center">
    <div>
      <h1 class="text-4xl font-extrabold text-blue-800 mb-4">Welcome to <span class="text-blue-600">Vendora</span></h1>
      <p class="text-gray-700 text-lg mb-6 leading-relaxed">
        Discover a world of products, sellers, and unbeatable deals. Shop confidently and sell effortlessly on the marketplace made for everyone.
      </p>
      <div class="flex flex-col sm:flex-row gap-4">
        <a href="/register" class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 transition">
          Start Shopping
        </a>
        <a href="/register" class="border border-blue-600 text-blue-600 px-6 py-3 rounded-md hover:bg-blue-50 transition">
          Become a Seller
        </a>
      </div>
    </div>
    <div class="flex justify-center">
      <img src="https://via.placeholder.com/400x300?text=Vendora+Hero+Image" alt="Marketplace Preview" class="rounded-lg shadow-md">
    </div>
  </div>
</section>

<!-- Search + Dropdown Container -->
<section class="bg-white sticky top-0 z-50 border-b shadow-sm">
  <div class="max-w-7xl mx-auto flex items-center gap-4 px-4 py-3 relative">
    <div class="relative">
      <button id="deptDropdownBtn" class="bg-blue-600 text-white px-4 py-2 rounded-md">
        Shop by Department
      </button>
      <div id="deptDropdown" class="absolute left-0 mt-2 w-56 bg-white border rounded shadow-lg hidden z-50">
        <ul class="text-sm text-gray-700 divide-y">
          <li><a href="#" class="block px-4 py-2 hover:bg-blue-50">Health & Wellness</a></li>
          <li><a href="#" class="block px-4 py-2 hover:bg-blue-50">Electronics</a></li>
          <li><a href="#" class="block px-4 py-2 hover:bg-blue-50">Home & Kitchen</a></li>
          <li><a href="#" class="block px-4 py-2 hover:bg-blue-50">Clothing</a></li>
        </ul>
      </div>
    </div>
    <form action="/buyer/products.php" method="GET" class="flex-1">
      <input type="text" name="search" placeholder="Search products..." class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
    </form>
  </div>
</section>

<script>
  const deptBtn = document.getElementById('deptDropdownBtn');
  const deptDropdown = document.getElementById('deptDropdown');

  deptBtn.addEventListener('click', () => {
    deptDropdown.classList.toggle('hidden');
  });

  document.addEventListener('click', (e) => {
    if (!deptBtn.contains(e.target) && !deptDropdown.contains(e.target)) {
      deptDropdown.classList.add('hidden');
    }
  });
</script>

<!-- Products Section -->
<section class="mb-10 max-w-7xl mx-auto px-4">
  <h2 class="text-xl font-semibold mb-4">Latest Products</h2>

  <?php if (empty($products)): ?>
    <div class="text-gray-400 italic">No products found.</div>
  <?php else: ?>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
      <?php foreach ($products as $product): ?>
        <div class="bg-white border rounded-lg p-4 shadow-sm hover:shadow-md transition">
          <img src="<?= htmlspecialchars($product['image_path']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-full h-32 object-cover mb-3 rounded">
          <h3 class="text-gray-800 font-semibold"><?= htmlspecialchars($product['name']) ?></h3>
          <p class="text-blue-600 font-bold mt-1">R<?= number_format($product['price'], 2) ?></p>
          <p class="text-sm text-gray-500">Sold by: <?= htmlspecialchars($product['seller_name']) ?></p>
          <!-- ✅ Updated button with link to product.php -->
          <a href="../../buyer/product.php?id=<?= $product['id'] ?>" class="mt-3 inline-block bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 text-sm">
            View
          </a>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<?php include 'includes/footer.php'; ?>
