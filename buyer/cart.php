<?php
session_start();
require '../includes/db.php';



// Handle remove/update POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['remove_product_id'])) {
        $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("ii", $user_id, $_POST['remove_product_id']);
        $stmt->execute();
    }
    if (isset($_POST['update_product_id']) && isset($_POST['quantity'])) {
        $qty = max(1, intval($_POST['quantity']));
        $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("iii", $qty, $user_id, $_POST['update_product_id']);
        $stmt->execute();
    }
    if (isset($_POST['checkout'])) {
        // Place orders for all cart items
        $cart_items = $conn->prepare("SELECT product_id, quantity FROM cart WHERE user_id = ?");
        $cart_items->bind_param("i", $user_id);
        $cart_items->execute();
        $result = $cart_items->get_result();

        $place_order = $conn->prepare("INSERT INTO orders (user_id, product_id, quantity, status) VALUES (?, ?, ?, 'pending')");
        while ($row = $result->fetch_assoc()) {
            $place_order->bind_param("iii", $user_id, $row['product_id'], $row['quantity']);
            $place_order->execute();
        }

        // Clear cart
        $clear_cart = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $clear_cart->bind_param("i", $user_id);
        $clear_cart->execute();

        header("Location: orders.php?checkout=success");
        exit();
    }
}

// Fetch cart items with product info
$stmt = $conn->prepare("SELECT c.product_id, c.quantity, p.name, p.price, p.image_path FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$cart_items = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Your Cart | Vendora</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 p-6 min-h-screen">
  <h1 class="text-2xl font-bold mb-4">Your Cart</h1>

  <?php if (empty($cart_items)): ?>
    <p class="text-gray-600">Your cart is empty.</p>
  <?php else: ?>
    <form method="POST" class="space-y-4">
      <?php foreach ($cart_items as $item): ?>
        <div class="bg-white border rounded p-4 shadow flex items-center justify-between gap-4">
          <div class="flex items-center gap-4">
            <?php if ($item['image_path']): ?>
              <img src="<?= htmlspecialchars($item['image_path']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="w-16 h-16 object-cover rounded" />
            <?php else: ?>
              <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center text-gray-500">No Image</div>
            <?php endif; ?>
            <div>
              <p class="font-semibold text-gray-800"><?= htmlspecialchars($item['name']) ?></p>
              <p class="text-sm text-gray-600">$<?= number_format($item['price'], 2) ?></p>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <input type="hidden" name="update_product_id" value="<?= $item['product_id'] ?>" />
            <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" class="w-16 border rounded px-2 py-1" />
            <button type="submit" class="bg-blue-600 text-white px-4 py-1 rounded">Update</button>
          </div>
          <div>
            <button type="submit" name="remove_product_id" value="<?= $item['product_id'] ?>" class="text-red-600 hover:underline text-sm">Remove</button>
          </div>
        </div>
      <?php endforeach; ?>

      <button type="submit" name="checkout" class="mt-6 bg-green-600 text-white px-6 py-2 rounded font-semibold">Checkout</button>
    </form>
  <?php endif; ?>

</body>
</html>
