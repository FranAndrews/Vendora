<?php

require_once 'includes/db.php';

$error ="";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email']);
  $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
  $password = $_POST['password'];

  $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password'])) {
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['user_role'] = $user['role'];
      $_SESSION['username'] = $user['name']; // ✅ FIXED

      // Redirect based on role
      switch ($user['role']) {
        case 'seller':
          header('Location:' . BASE_URL . ' /seller/dashboard.php');
          break;
        default:
          header('Location: index.php');
      }
      exit();
    }
  }

  $error = 'Invalid email or password.';
}
?>


<!-- HTML form -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Login | Vendora</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex justify-center items-center min-h-screen">
  <form method="POST" class="bg-white p-6 rounded shadow w-full max-w-sm space-y-4">
    <h2 class="text-xl font-bold text-gray-800">Login</h2>

    <?php if ($error): ?>
      <p class="text-red-600 text-sm"><?= $error ?></p>
    <?php endif; ?>

    <input name="email" type="email" placeholder="Email" required class="w-full border border-gray-300 px-3 py-2 rounded" />
    <input name="password" type="password" placeholder="Password" required class="w-full border border-gray-300 px-3 py-2 rounded" />

    <button type="submit" class="bg-blue-600 text-white w-full py-2 rounded hover:bg-blue-700">Login</button>
<a href="register.php" 
   class="text-sm text-blue-600 hover:underline text-center block">
   Create an account
</a>


  </form>
</body>
</html>
