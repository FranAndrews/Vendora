<?php
define('SECURE_ACCESS', true);
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
session_start();

// Get product ID from URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

try {
    // Fetch product details
    $stmt = $conn->prepare("
        SELECT p.*, u.name as seller_name, u.email as seller_email 
        FROM products p 
        JOIN users u ON p.seller_id = u.id 
        WHERE p.id = ?
    ");
    
    if (!$stmt) {
        throw new Exception("Failed to prepare product query: " . $conn->error);
    }
    
    $stmt->bind_param("i", $product_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to execute product query: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if (!$product) {
        header('Location: ' . BASE_URL . 'buyer/products.php');
        exit;
    }

    // Fetch related products
    $stmt = $conn->prepare("
        SELECT p.*, u.name as seller_name 
        FROM products p 
        JOIN users u ON p.seller_id = u.id 
        WHERE p.id != ? AND p.category = ? 
        LIMIT 4
    ");
    
    if (!$stmt) {
        throw new Exception("Failed to prepare related products query: " . $conn->error);
    }
    
    $stmt->bind_param("is", $product_id, $product['category']);
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to execute related products query: " . $stmt->error);
    }
    
    $related_products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

} catch (Exception $e) {
    error_log("Error in product.php: " . $e->getMessage());
    die("An error occurred while loading the product. Please try again later.");
}

include __DIR__ . '/../includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="flex text-sm mb-6">
        <a href="<?= BASE_URL ?>index.php" class="text-gray-500 hover:text-orange-600">Home</a>
        <span class="mx-2 text-gray-400">/</span>
        <a href="<?= BASE_URL ?>buyer/products.php" class="text-gray-500 hover:text-orange-600">Products</a>
        <span class="mx-2 text-gray-400">/</span>
        <span class="text-gray-800"><?= htmlspecialchars($product['name']) ?></span>
    </nav>

    <!-- Product Details -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Product Images -->
        <div class="space-y-4">
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <div class="product-image">
                    <?php if (strpos($product['image_path'], 'http') === 0): ?>
                        <img src="<?php echo htmlspecialchars($product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="img-fluid">
                    <?php else: ?>
                        <img src="<?php echo BASE_URL . '/' . htmlspecialchars($product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="img-fluid">
                    <?php endif; ?>
                </div>
            </div>
            <div class="grid grid-cols-4 gap-2">
                <!-- Additional product images can be added here -->
            </div>
        </div>

        <!-- Product Info -->
        <div class="space-y-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($product['name']) ?></h1>
                <p class="text-sm text-gray-500 mt-2">Sold by: <?= htmlspecialchars($product['seller_name']) ?></p>
            </div>

            <div class="border-t border-b border-gray-200 py-4">
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-orange-600">R<?= number_format($product['price'], 2) ?></span>
                    <?php if ($product['original_price']): ?>
                        <span class="text-lg text-gray-500 line-through">R<?= number_format($product['original_price'], 2) ?></span>
                    <?php endif; ?>
                </div>
                <?php if ($product['stock'] > 0): ?>
                    <p class="text-green-600 text-sm mt-2">In Stock (<?= $product['stock'] ?> available)</p>
                <?php else: ?>
                    <p class="text-red-600 text-sm mt-2">Out of Stock</p>
                <?php endif; ?>
            </div>

            <div class="space-y-4">
                <div>
                    <h3 class="font-semibold text-gray-800 mb-2">Description</h3>
                    <p class="text-gray-600"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                </div>

                <?php if ($product['features']): ?>
                    <div>
                        <h3 class="font-semibold text-gray-800 mb-2">Features</h3>
                        <ul class="list-disc list-inside text-gray-600">
                            <?php foreach (explode("\n", $product['features']) as $feature): ?>
                                <li><?= htmlspecialchars($feature) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Add to Cart Section -->
            <div class="mt-8">
                <div class="flex items-center justify-between">
                    <div class="text-3xl font-bold text-gray-900">R<?= number_format($product['price'], 2) ?></div>
                    <div class="text-sm text-gray-500">In Stock: <?= $product['stock'] ?></div>
                </div>
                
                <div class="mt-4">
                    <form action="<?= BASE_URL ?>buyer/cart.php" method="POST" class="flex items-center space-x-4">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        <div class="flex items-center border border-gray-300 rounded-md">
                            <button type="button" onclick="updateQuantity(-1)" class="px-3 py-2 text-gray-600 hover:text-orange-600">-</button>
                            <input type="number" name="quantity" id="quantity" value="1" min="1" max="<?= $product['stock'] ?>" 
                                   class="w-16 text-center border-0 focus:ring-0" onchange="validateQuantity(this)">
                            <button type="button" onclick="updateQuantity(1)" class="px-3 py-2 text-gray-600 hover:text-orange-600">+</button>
                        </div>
                        <button type="submit" class="flex-1 bg-orange-600 text-white px-6 py-3 rounded-md hover:bg-orange-700 transition">
                            Add to Cart
                        </button>
                    </form>
                </div>
                
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <div class="mt-4 text-sm text-gray-600">
                        <p>You can add items to cart without signing in. You'll be asked to sign in or register when checking out.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <?php if (!empty($related_products)): ?>
        <div class="mt-16">
            <h2 class="text-xl font-semibold mb-6">You May Also Like</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <?php foreach ($related_products as $related): ?>
                    <div class="bg-white border rounded-lg p-4 shadow-sm hover:shadow-md transition">
                        <img src="<?= htmlspecialchars($related['image_path']) ?>" 
                             alt="<?= htmlspecialchars($related['name']) ?>" 
                             class="w-full h-32 object-cover mb-3 rounded">
                        <h3 class="text-gray-800 font-semibold"><?= htmlspecialchars($related['name']) ?></h3>
                        <p class="text-orange-600 font-bold mt-1">R<?= number_format($related['price'], 2) ?></p>
                        <p class="text-sm text-gray-500">Sold by: <?= htmlspecialchars($related['seller_name']) ?></p>
                        <a href="<?= BASE_URL ?>buyer/product.php?id=<?= $related['id'] ?>" 
                           class="mt-3 inline-block bg-orange-600 text-white px-3 py-1 rounded hover:bg-orange-700 text-sm">
                            View
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function updateQuantity(change) {
    const input = document.getElementById('quantity');
    const newValue = parseInt(input.value) + change;
    const max = parseInt(input.max);
    
    if (newValue >= 1 && newValue <= max) {
        input.value = newValue;
    }
}

function addToCart() {
    const quantity = parseInt(document.getElementById('quantity').value);
    const product = {
        id: <?= $product['id'] ?>,
        name: "<?= addslashes($product['name']) ?>",
        price: <?= $product['price'] ?>,
        image: "<?= addslashes($product['image_path']) ?>",
        quantity: quantity
    };

    // Get existing cart
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    
    // Check if product already exists in cart
    const existingIndex = cart.findIndex(item => item.id === product.id);
    
    if (existingIndex > -1) {
        // Update quantity if product exists
        cart[existingIndex].quantity += quantity;
    } else {
        // Add new product if it doesn't exist
        cart.push(product);
    }

    // Save updated cart
    localStorage.setItem('cart', JSON.stringify(cart));
    
    // Update cart count
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    const badge = document.getElementById('cart-count');
    if (badge) badge.textContent = totalItems;

    // Show notification
    const notification = document.getElementById('cart-notification');
    notification.classList.remove('hidden');
    setTimeout(() => {
        notification.classList.add('hidden');
    }, 3000);
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
