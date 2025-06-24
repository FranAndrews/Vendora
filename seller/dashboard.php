<?php
define('SECURE_ACCESS', true);
require_once '../includes/config.php';
require_once '../includes/db.php';
session_start();

// Auth check for seller
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'seller') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

// Fetch seller's products from database
$products = [];
$totalProducts = 0;
$totalSales = 0;
$activeProducts = 0;

try {
    // Get products
    $stmt = $conn->prepare("SELECT * FROM products WHERE seller_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
        $totalProducts++;
        if ($row['status'] === 'active') {
            $activeProducts++;
        }
    }

    // Get total sales (you can implement this when you add orders functionality)
    // $stmt = $conn->prepare("SELECT SUM(total_amount) as total FROM orders WHERE seller_id = ? AND status = 'delivered'");
    // $stmt->bind_param("i", $_SESSION['user_id']);
    // $stmt->execute();
    // $result = $stmt->get_result();
    // if ($row = $result->fetch_assoc()) {
    //     $totalSales = $row['total'] ?? 0;
    // }
} catch (Exception $e) {
    error_log("Error fetching seller data: " . $e->getMessage());
}

// Fetch seller's name
$sellerName = $_SESSION['username'] ?? "Seller";

include '../includes/header.php';
?>

<main class="max-w-7xl mx-auto p-6">
    <!-- Welcome Section -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800 mb-2">Welcome back, <?= htmlspecialchars($sellerName) ?>!</h2>
                <p class="text-gray-600">Manage your products and track your sales here.</p>
            </div>
            <a href="<?= BASE_URL ?>" class="inline-flex items-center bg-orange-600 text-white px-4 py-2 rounded-md hover:bg-orange-700 transition">
                <i class="fas fa-shopping-bag mr-2"></i>
                Shop as Buyer
            </a>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                    <i class="fas fa-box text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-gray-500 text-sm">Total Products</h3>
                    <p class="text-2xl font-semibold text-gray-800"><?= $totalProducts ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-gray-500 text-sm">Active Products</h3>
                    <p class="text-2xl font-semibold text-gray-800"><?= $activeProducts ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-chart-line text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-gray-500 text-sm">Total Sales</h3>
                    <p class="text-2xl font-semibold text-gray-800">R<?= number_format($totalSales, 2) ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Product CTA -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Your Products</h3>
                <p class="text-gray-600 text-sm">Manage your product listings</p>
            </div>
            <a href="<?= BASE_URL ?>seller/add-product.php"
               class="inline-flex items-center bg-orange-600 hover:bg-orange-700 text-white py-2 px-4 rounded-md transition">
                <i class="fas fa-plus mr-2"></i>
                Add New Product
            </a>
        </div>
    </div>

    <!-- Product List -->
    <?php if (empty($products)): ?>
        <div class="bg-white rounded-lg shadow-sm p-8 text-center">
            <i class="fas fa-box text-6xl text-gray-400 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No products yet</h3>
            <p class="text-gray-600 mb-4">Start by adding your first product to your store.</p>
            <a href="<?= BASE_URL ?>seller/add-product.php" class="inline-flex items-center text-orange-600 hover:text-orange-700">
                Add your first product
                <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php foreach ($products as $prod): ?>
                <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow border border-gray-200 overflow-hidden flex flex-col">
                    <?php if ($prod['image_path']): ?>
                        <img src="<?= htmlspecialchars($prod['image_path']) ?>" alt="<?= htmlspecialchars($prod['name']) ?>" class="w-full h-48 object-cover" />
                    <?php else: ?>
                        <div class="w-full h-48 bg-gray-100 flex items-center justify-center">
                            <i class="fas fa-image text-4xl text-gray-400"></i>
                        </div>
                    <?php endif; ?>
                    <div class="p-4 flex-1 flex flex-col">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="font-semibold text-gray-800"><?= htmlspecialchars($prod['name']) ?></h4>
                            <span class="px-2 py-1 text-xs rounded-full <?= $prod['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>">
                                <?= ucfirst($prod['status']) ?>
                            </span>
                        </div>
                        <p class="text-orange-600 font-bold mb-2">R<?= number_format($prod['price'], 2) ?></p>
                        <p class="text-gray-500 text-sm mb-4 line-clamp-2"><?= htmlspecialchars($prod['description'] ?? '') ?></p>
                        <div class="mt-auto flex justify-between text-sm">
                            <a href="<?= BASE_URL ?>seller/edit-product.php?id=<?= $prod['id'] ?>" 
                               class="text-orange-600 hover:text-orange-700 flex items-center">
                                <i class="fas fa-edit mr-1"></i>
                                Edit
                            </a>
                            <a href="<?= BASE_URL ?>seller/delete-product.php?id=<?= $prod['id'] ?>" 
                               class="text-red-600 hover:text-red-700 flex items-center"
                               onclick="return confirm('Are you sure you want to delete this product?');">
                                <i class="fas fa-trash mr-1"></i>
                                Delete
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?php include '../includes/footer.php'; ?>
