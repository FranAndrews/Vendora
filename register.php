<?php
session_start();
require_once 'includes/db.php';      // make sure this sets $conn (mysqli)
require_once 'includes/config.php';  // make sure this defines BASE_URL with trailing slash

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username']);
  $email = trim($_POST['email']);
  $password = $_POST['password'];
  $role = $_POST['role'] ?? 'buyer';

  if ($username === '' || $email === '' || $password === '') {
    $error = 'All fields are required.';
  } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = 'Invalid email address.';
  } else {
    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
      $error = 'Email already exists.';
    } else {
      // Insert new user
      $hashed = password_hash($password, PASSWORD_DEFAULT);
      $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
      $stmt->bind_param("ssss", $username, $email, $hashed, $role);

      if ($stmt->execute()) {
        header('Location: ' . BASE_URL . 'login.php');  // fixed redirect URL
        exit();
      } else {
        $error = 'Registration failed. Please try again.';
      }
    }
    $stmt->close();
  }
}
?>

<!DOCTYPE html>
<html lang="en" class="bg-gray-50 min-h-screen flex items-center justify-center">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Register | Vendora</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
  <div class="max-w-md w-full bg-white border border-gray-300 rounded-lg shadow-md p-8">
    <h1 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Create an Account</h1>
    
    <?php if ($error): ?>
      <div class="mb-4 p-3 text-sm text-red-700 bg-red-100 rounded border border-red-300">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>
    
    <form method="POST" action="" class="space-y-5" novalidate>
      <div>
        <label for="username" class="block text-gray-700 mb-1">Username</label>
        <input
          type="text"
          id="username"
          name="username"
          class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-600"
          required
          value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
        />
      </div>

      <div>
        <label for="email" class="block text-gray-700 mb-1">Email</label>
        <input
          type="email"
          id="email"
          name="email"
          class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-600"
          required
          value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
        />
      </div>

      <div>
        <label for="password" class="block text-gray-700 mb-1">Password</label>
        <input
          type="password"
          id="password"
          name="password"
          class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-600"
          required
        />
      </div>

      <div>
        <label for="role" class="block text-gray-700 mb-1">Role</label>
        <select
          id="role"
          name="role"
          class="w-56 border border-gray-300 rounded px-3 py-2 text-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600"
        >
          <option value="buyer" <?= (($_POST['role'] ?? '') === 'buyer') ? 'selected' : '' ?>>Buyer</option>
          <option value="seller" <?= (($_POST['role'] ?? '') === 'seller') ? 'selected' : '' ?>>Seller</option>
        </select>
      </div>

      <button
        type="submit"
        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded transition-colors"
      >
        Register
      </button>
    </form>

    <p class="mt-6 text-center text-gray-600 text-sm">
      Already have an account? 
      <a href="<?= BASE_URL ?>login.php" class="text-blue-600 hover:text-blue-700 font-semibold">Login here</a>.
    </p>
  </div>
</body>
</html>
