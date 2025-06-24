<?php
define('SECURE_ACCESS', true);
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/security.php';

// Initialize session with security
secureSession();

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . 'index.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request. Please try again.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $role = $_POST['role'] ?? 'buyer';

        // Validate inputs
        if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
            $error = 'All fields are required.';
        } elseif (!validateUsername($username)) {
            $error = 'Username must be between ' . USERNAME_MIN_LENGTH . ' and ' . USERNAME_MAX_LENGTH . ' characters and contain only letters, numbers, and underscores.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Invalid email address.';
        } elseif (!validatePassword($password)) {
            $error = 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character.';
        } elseif ($password !== $confirm_password) {
            $error = 'Passwords do not match.';
        } elseif (!in_array($role, ['buyer', 'seller'])) {
            $error = 'Invalid role selected.';
        } else {
            try {
                // Check if email already exists
                $stmt = safeQuery($conn, 
                    "SELECT id FROM users WHERE email = ?", 
                    "s", 
                    [$email]
                );
                
                if (count(safeFetch($stmt)) > 0) {
                    $error = 'Email already exists.';
                } else {
                    // Check if username already exists
                    $stmt = safeQuery($conn, 
                        "SELECT id FROM users WHERE name = ?", 
                        "s", 
                        [$username]
                    );
                    
                    if (count(safeFetch($stmt)) > 0) {
                        $error = 'Username already exists.';
                    } else {
                        // Hash password
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        
                        // Insert new user
                        $stmt = safeQuery($conn,
                            "INSERT INTO users (name, email, password, role, status, created_at) 
                             VALUES (?, ?, ?, ?, 'active', NOW())",
                            "ssss",
                            [$username, $email, $hashed_password, $role]
                        );
                        
                        if ($stmt->affected_rows > 0) {
                            $success = 'Registration successful! You can now login.';
                            // Clear form data
                            $_POST = array();
                        } else {
                            $error = 'Registration failed. Please try again.';
                        }
                    }
                }
            } catch (Exception $e) {
                error_log("Registration error: " . $e->getMessage());
                $error = 'An error occurred. Please try again later.';
            }
        }
    }
}

// Generate new CSRF token
$csrf_token = generateCSRFToken();
?>

<!DOCTYPE html>
<html lang="en" class="bg-gray-50 min-h-screen flex items-center justify-center">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Register | <?= SITE_NAME ?></title>
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
    
    <?php if ($success): ?>
      <div class="mb-4 p-3 text-sm text-green-700 bg-green-100 rounded border border-green-300">
        <?= htmlspecialchars($success) ?>
      </div>
    <?php endif; ?>
    
    <form method="POST" action="" class="space-y-5" novalidate>
      <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
      
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
        <label for="confirm_password" class="block text-gray-700 mb-1">Confirm Password</label>
        <input
          type="password"
          id="confirm_password"
          name="confirm_password"
          class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-600"
          required
        />
      </div>

      <div>
        <label for="role" class="block text-gray-700 mb-1">Role</label>
        <select
          id="role"
          name="role"
          class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600"
        >
          <option value="buyer" <?= (($_POST['role'] ?? '') === 'buyer') ? 'selected' : '' ?>>Buyer</option>
          <option value="seller" <?= (($_POST['role'] ?? '') === 'seller') ? 'selected' : '' ?>>Seller</option>
        </select>
      </div>

      <button
        type="submit"
        class="w-full bg-orange-600 hover:bg-orange-700 text-white font-semibold py-2 rounded transition-colors"
      >
        Register
      </button>
    </form>

    <p class="mt-6 text-center text-gray-600 text-sm">
      Already have an account? 
      <a href="<?= BASE_URL ?>login.php" class="text-blue-600 hover:text-blue-700 font-semibold">Login here</a>.
    </p>
  </div>

  <script>
  // Client-side validation
  document.querySelector('form').addEventListener('submit', function(e) {
      const username = document.getElementById('username').value;
      const email = document.getElementById('email').value;
      const password = document.getElementById('password').value;
      const confirmPassword = document.getElementById('confirm_password').value;
      
      if (!username || !email || !password || !confirmPassword) {
          e.preventDefault();
          alert('Please fill in all fields');
          return;
      }
      
      if (username.length < <?= USERNAME_MIN_LENGTH ?> || username.length > <?= USERNAME_MAX_LENGTH ?>) {
          e.preventDefault();
          alert('Username must be between <?= USERNAME_MIN_LENGTH ?> and <?= USERNAME_MAX_LENGTH ?> characters');
          return;
      }
      
      if (!/^[a-zA-Z0-9_]+$/.test(username)) {
          e.preventDefault();
          alert('Username can only contain letters, numbers, and underscores');
          return;
      }
      
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
          e.preventDefault();
          alert('Please enter a valid email address');
          return;
      }
      
      if (password.length < <?= PASSWORD_MIN_LENGTH ?>) {
          e.preventDefault();
          alert('Password must be at least <?= PASSWORD_MIN_LENGTH ?> characters long');
          return;
      }
      
      if (!/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/.test(password)) {
          e.preventDefault();
          alert('Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character');
          return;
      }
      
      if (password !== confirmPassword) {
          e.preventDefault();
          alert('Passwords do not match');
          return;
      }
  });
  </script>
</body>
</html>
