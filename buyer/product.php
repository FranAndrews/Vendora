<?php
session_start();
require '../includes/db.php';
require '../includes/header.php';





// Fetch product from DB
$stmt = $conn->prepare("SELECT id, name, price, description, image_path FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    echo "Product not found.";
    exit();
}

$user_id = $_SESSION['user_id'] ?? 1; // fallback or dummy user

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $check = $conn->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?");
    $check->bind_param("ii", $user_id, $id);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $qty = $row['quantity'] + 1;
        $update = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
        $update->bind_param("iii", $qty, $user_id, $id);
        $update->execute();
    } else {
        $insert = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
        $insert->bind_param("ii", $user_id, $id);
        $insert->execute();
    }

    header("Location: product.php?id=$id&added=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($product['name']) ?> | Vendora</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900">

  <!-- Container -->
   <!-- Main Container -->
  <section class="min-h-screen py-12 px-4">
    <!-- Wrapper with border -->
    <div class="max-w-4xl mx-auto bg-white border border-gray-300 rounded-xl shadow-sm p-6 space-y-10">

      <!-- Breadcrumb -->
      <nav class="text-sm text-gray-500" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2">
          <li><a href="#" class="hover:underline text-gray-600">Shop</a></li>
          <li><span class="mx-1">/</span></li>
          <li class="text-gray-400"><?= htmlspecialchars($product['name']) ?></li>
        </ol>
      </nav>

      <!-- Main Content: Image + Info -->
      <div class="grid md:grid-cols-2 gap-6">
        
        <!-- Product Image -->
        <div class="border border-gray-200 rounded-lg overflow-hidden p-2 bg-gray-50">
          <img src="<?= htmlspecialchars($product['image_path']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-full h-[350px] object-contain rounded-md" />
        </div>

        <!-- Product Info -->
        <div class="flex flex-col justify-between border border-gray-200 rounded-lg p-4 bg-white shadow-sm">
          <div class="space-y-4">
            <h1 class="text-2xl font-semibold"><?= htmlspecialchars($product['name']) ?></h1>
            <p class="text-xl text-blue-600 font-bold">$<?= number_format($product['price'], 2) ?></p>
            <p class="text-gray-700 text-sm"><?= htmlspecialchars($product['description'] ?? 'No description provided.') ?></p>
          </div>

          <!-- Feedback -->
          <?php if (isset($_GET['added'])): ?>
            <div class="mt-4 text-green-600 font-medium text-sm">✔️ Product added to cart!</div>
          <?php endif; ?>

          <!-- Add to Cart -->
          <form method="POST" class="mt-6">
            <button type="submit" name="add_to_cart" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition">
              Add to Cart
            </button>
          </form>
        </div>
      </div>
    </div>
  </section>
</body>

<?php require '../includes/footer.php'; ?>
</html>
