<?php
define('SECURE_ACCESS', true);
require_once '../includes/config.php';
require_once '../includes/db.php';
session_start();

// Check if seller is logged in
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'seller') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $imagePath = null;

    if ($name === '' || $price === '') {
        $error = 'Please fill in all required fields (Name and Price).';
    } else {
        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../uploads/products/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $filename = uniqid() . '_' . basename($_FILES['image']['name']);
            $targetFile = $uploadDir . $filename;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $imagePath = 'uploads/products/' . $filename;
            } else {
                $error = 'Image upload failed.';
            }
        }

        if (!$error) {
            try {
                $stmt = $conn->prepare("INSERT INTO products (seller_id, name, description, price, image_path) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("issds", $_SESSION['user_id'], $name, $description, $price, $imagePath);
                
                if ($stmt->execute()) {
                    $success = "Product '$name' added successfully!";
                } else {
                    $error = "Failed to add product. Please try again.";
                }
            } catch (Exception $e) {
                $error = "An error occurred. Please try again.";
                error_log("Product addition error: " . $e->getMessage());
            }
        }
    }
}

// Fetch seller's products
$products = [];
try {
    $stmt = $conn->prepare("SELECT * FROM products WHERE seller_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
} catch (Exception $e) {
    error_log("Error fetching products: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Add New Product | Vendora</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">

<header class="bg-white shadow p-4 flex justify-between items-center">
    <h1 class="text-xl font-bold text-gray-800">Add New Product</h1>
    <a href="<?= BASE_URL ?>seller/dashboard.php" class="text-blue-600 hover:underline">Back to Dashboard</a>
</header>

<main class="max-w-3xl mx-auto p-6 flex-grow space-y-8">

    <form action="<?= BASE_URL ?>seller/add-product.php" method="POST" enctype="multipart/form-data" class="bg-white shadow rounded p-6 space-y-6">
        <?php if ($error): ?>
            <div class="bg-red-100 text-red-700 p-3 rounded"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="bg-green-100 text-green-700 p-3 rounded"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <div>
            <label for="name" class="block font-semibold mb-1 text-gray-700">Product Name <span class="text-red-500">*</span></label>
            <input type="text" id="name" name="name" required
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-blue-600 focus:outline-none" />
        </div>

        <div>
            <label for="price" class="block font-semibold mb-1 text-gray-700">Price (Rand) <span class="text-red-500">*</span></label>
            <input type="number" id="price" name="price" step="0.01" min="0" required
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-blue-600 focus:outline-none" />
        </div>

        <div>
            <label for="description" class="block font-semibold mb-1 text-gray-700">Description</label>
            <textarea id="description" name="description" rows="4"
                      class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-blue-600 focus:outline-none"></textarea>
        </div>

        <div>
            <label for="image" class="block font-semibold mb-1 text-gray-700">Product Image (optional)</label>
            <input type="file" id="image" name="image" accept="image/*"
                   class="w-full text-gray-700" />
        </div>

        <button type="submit"
                class="bg-orange-600 hover:bg-orange-700 text-white px-5 py-2 rounded font-semibold">
            Add Product
        </button>
    </form>

    <!-- Display added products -->
    <?php if (!empty($products)): ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-6">
            <?php foreach ($products as $product): ?>
                <div class="bg-white border border-gray-300 rounded shadow p-4">
                    <?php if ($product['image_path']): ?>
                        <img src="<?= htmlspecialchars($product['image_path']) ?>" alt="Product image" class="w-full h-40 object-cover rounded mb-3">
                    <?php endif; ?>
                    <h2 class="text-lg font-semibold text-gray-800"><?= htmlspecialchars($product['name']) ?></h2>
                    <p class="text-gray-600 text-sm">R<?= htmlspecialchars($product['price']) ?></p>
                    <p class="text-gray-500 text-sm mt-2"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</main>

<footer class="bg-white shadow p-4 text-center text-gray-600 text-sm">
    Vendora &copy; <?= date('Y') ?>
</footer>

</body>
</html>
