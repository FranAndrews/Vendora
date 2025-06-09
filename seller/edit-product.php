<?php
session_start();

// Require seller role
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'seller') {
    header('Location: /login.php');
    exit();
}

$products = $_SESSION['products'] ?? [];
$productId = $_GET['id'] ?? null;
$targetProduct = null;

if (!$productId) {
    header('Location: /seller/dashboard.php');
    exit();
}

// Find the product
foreach ($products as &$p) {
    if ($p['id'] == $productId) {
        $targetProduct = &$p;
        break;
    }
}

if (!$targetProduct) {
    echo "Product not found.";
    exit();
}

$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $targetProduct['name'] = $_POST['name'];
    $targetProduct['price'] = (float) $_POST['price'];
    $targetProduct['image'] = $_POST['image'] ?? $targetProduct['image']; // Assume string URL for now

    $_SESSION['products'] = $products; // Update session

    $success = "Product updated successfully!";
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Edit Product | Vendora</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen p-6">

  <header class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-gray-800">Edit Product</h1>
    <a href="/seller/dashboard.php" class="text-sm text-blue-600 hover:underline">← Back to Dashboard</a>
  </header>

  <div class="max-w-xl mx-auto bg-white p-6 rounded shadow space-y-4">
    <?php if ($success): ?>
      <div class="bg-green-100 text-green-700 p-3 rounded"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST">
      <div>
        <label class="block mb-1 font-medium text-gray-700">Product Name</label>
        <input type="text" name="name" value="<?= htmlspecialchars($targetProduct['name']) ?>" required
               class="w-full border border-gray-300 rounded px-3 py-2" />
      </div>

      <div>
        <label class="block mb-1 font-medium text-gray-700">Price (USD)</label>
        <input type="number" name="price" step="0.01" value="<?= htmlspecialchars($targetProduct['price']) ?>" required
               class="w-full border border-gray-300 rounded px-3 py-2" />
      </div>

      <div>
        <label class="block mb-1 font-medium text-gray-700">Image URL (optional)</label>
        <input type="text" name="image" value="<?= htmlspecialchars($targetProduct['image']) ?>"
               class="w-full border border-gray-300 rounded px-3 py-2" />
      </div>

      <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 mt-4 rounded font-semibold">
        Save Changes
      </button>
    </form>
  </div>

</body>
</html>
