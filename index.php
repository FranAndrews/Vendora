<?php 
define('SECURE_ACCESS', true);
require_once 'includes/config.php';
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

// Get user info if logged in
$userName = '';
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $userName = $row['name'];
    }
}
?>

<?php include 'includes/header.php'; ?>

<!-- HERO SECTION -->
<section class="bg-gradient-to-r from-orange-50 to-orange-100 py-16 px-6 mb-10">
  <div class="max-w-7xl mx-auto grid md:grid-cols-2 gap-10 items-center">
    <div>
      <?php if (isset($_SESSION['user_id'])): ?>
        <div class="mb-6">
          <h2 class="text-2xl font-semibold text-orange-800">Welcome back, <?= htmlspecialchars($userName) ?>!</h2>
          <p class="text-gray-600 mt-2">Ready to discover amazing products today?</p>
        </div>
      <?php else: ?>
        <h1 class="text-4xl font-extrabold text-orange-800 mb-4">Welcome to <span class="text-orange-600">Vendora</span></h1>
        <p class="text-gray-700 text-lg mb-6 leading-relaxed">
          You're not signed in. Join our marketplace to discover amazing products, connect with sellers, and start shopping today!
        </p>
      <?php endif; ?>
      <div class="flex flex-col sm:flex-row gap-4">
        <?php if (!isset($_SESSION['user_id'])): ?>
          <a href="<?= BASE_URL ?>register.php" class="bg-orange-600 text-white px-6 py-3 rounded-md hover:bg-orange-700 transition">
            Start Shopping
          </a>
          <a href="<?= BASE_URL ?>register.php" class="border border-orange-600 text-orange-600 px-6 py-3 rounded-md hover:bg-orange-50 transition">
            Become a Seller
          </a>
        <?php else: ?>
          <a href="<?= BASE_URL ?>buyer/products.php" class="bg-orange-600 text-white px-6 py-3 rounded-md hover:bg-orange-700 transition">
            Browse Products
          </a>
          <?php if ($_SESSION['user_role'] === 'seller'): ?>
            <a href="<?= BASE_URL ?>seller/dashboard.php" class="border border-orange-600 text-orange-600 px-6 py-3 rounded-md hover:bg-orange-50 transition">
              Seller Dashboard
            </a>
          <?php endif; ?>
        <?php endif; ?>
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
      <button id="deptDropdownBtn" class="bg-orange-600 text-white px-4 py-2 rounded-md" type="button">
        Shop by Department
      </button>
      <div id="deptDropdown" class="absolute left-0 mt-2 w-56 bg-white border rounded shadow-lg hidden z-50">
        <ul class="text-sm text-gray-700 divide-y">
          <li><a href="#" class="block px-4 py-2 hover:bg-orange-50">Health & Wellness</a></li>
          <li><a href="#" class="block px-4 py-2 hover:bg-orange-50">Electronics</a></li>
          <li><a href="#" class="block px-4 py-2 hover:bg-orange-50">Home & Kitchen</a></li>
          <li><a href="#" class="block px-4 py-2 hover:bg-orange-50">Clothing</a></li>
        </ul>
      </div>
    </div>
    <form action="<?= BASE_URL ?>buyer/products.php" method="GET" class="flex-1">
      <input type="text" name="search" placeholder="Search products..." class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
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
        <div class="product-card bg-white border rounded-lg p-4 shadow-sm flex flex-col h-full">
          <div class="product-image w-full h-40 flex items-center justify-center mb-3">
            <?php if (strpos($product['image_path'], 'http') === 0): ?>
              <img src="<?php echo htmlspecialchars($product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-full object-cover rounded">
            <?php else: ?>
              <img src="<?php echo BASE_URL . '/' . htmlspecialchars($product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-full object-cover rounded">
            <?php endif; ?>
          </div>
          <div class="product-info flex-1 flex flex-col">
            <h3 class="text-gray-800 font-semibold"><?php echo htmlspecialchars($product['name']); ?></h3>
            <p class="price text-orange-600 font-bold mt-1">R <?php echo number_format($product['price'], 2); ?></p>
            <div class="mt-auto pt-2">
              <a href="<?= BASE_URL ?>buyer/product.php?id=<?php echo $product['id']; ?>" class="btn btn-primary bg-orange-600 text-white px-3 py-1 rounded hover:bg-orange-700 text-sm w-full block text-center">View Details</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<!-- Category Sections -->
<?php
// Get all categories
$categories = [
    'Health & Wellness' => 'health',
    'Electronics' => 'electronics',
    'Home & Kitchen' => 'home',
    'Clothing' => 'clothing'
];

foreach ($categories as $categoryName => $categorySlug): 
    // Get products for this category
    $categoryProducts = array_filter($products, function($product) use ($categorySlug) {
        return strtolower($product['category']) === $categorySlug;
    });
    
    if (!empty($categoryProducts)):
?>
<section class="mb-16 max-w-7xl mx-auto px-4" id="<?= $categorySlug ?>">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-semibold text-gray-800"><?= $categoryName ?></h2>
        <a href="<?= BASE_URL ?>buyer/products.php?category=<?= $categorySlug ?>" class="text-orange-600 hover:text-orange-700 text-sm">
            View All <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>
    
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        <?php foreach (array_slice($categoryProducts, 0, 4) as $product): ?>
            <div class="product-card bg-white border rounded-lg p-4 shadow-sm flex flex-col h-full">
                <div class="product-image w-full h-40 flex items-center justify-center mb-3">
                    <?php if (strpos($product['image_path'], 'http') === 0): ?>
                        <img src="<?php echo htmlspecialchars($product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-full object-cover rounded">
                    <?php else: ?>
                        <img src="<?php echo BASE_URL . '/' . htmlspecialchars($product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-full object-cover rounded">
                    <?php endif; ?>
                </div>
                <div class="product-info flex-1 flex flex-col">
                    <h3 class="text-gray-800 font-semibold"><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p class="price text-orange-600 font-bold mt-1">R <?php echo number_format($product['price'], 2); ?></p>
                    <div class="mt-auto pt-2">
                        <a href="<?= BASE_URL ?>buyer/product.php?id=<?php echo $product['id']; ?>" class="btn btn-primary bg-orange-600 text-white px-3 py-1 rounded hover:bg-orange-700 text-sm w-full block text-center">View Details</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php 
    endif;
endforeach; 
?>

<!-- Intersection Observer for smooth reveal -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('opacity-100', 'translate-y-0');
                entry.target.classList.remove('opacity-0', 'translate-y-4');
            }
        });
    }, {
        threshold: 0.1
    });

    // Observe all category sections
    document.querySelectorAll('section[id]').forEach(section => {
        section.classList.add('opacity-0', 'translate-y-4', 'transition-all', 'duration-500');
        observer.observe(section);
    });
});
</script>

<?php include 'includes/footer.php'; ?>
