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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request. Please try again.';
        error_log("Login failed: Invalid CSRF token");
    } else {
        $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'];

        if ($email === false) {
            $error = 'Invalid email format.';
            error_log("Login failed: Invalid email format - " . $_POST['email']);
        } else {
            try {
                error_log("Attempting login for email: " . $email);
                $stmt = safeQuery($conn, 
                    "SELECT id, name, email, password, role, status, last_login 
                     FROM users 
                     WHERE email = ? AND status = 'active'", 
                    "s", 
                    [$email]
                );
                
                $user = safeFetch($stmt)[0] ?? null;
                error_log("User found: " . ($user ? "Yes" : "No"));

                if ($user && password_verify($password, $user['password'])) {
                    error_log("Password verified successfully for user: " . $user['id']);
                    // Update last login
                    safeQuery($conn,
                        "UPDATE users SET last_login = NOW() WHERE id = ?",
                        "i",
                        [$user['id']]
                    );

                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_role'] = $user['role'];
                    $_SESSION['username'] = $user['name'];
                    $_SESSION['last_activity'] = time();

                    // Redirect based on role
                    switch ($user['role']) {
                        case 'admin':
                            header('Location: ' . BASE_URL . 'admin/dashboard.php');
                            break;
                        case 'seller':
                            header('Location: ' . BASE_URL . 'seller/dashboard.php');
                            break;
                        default:
                            header('Location: ' . BASE_URL . 'index.php');
                    }
                    exit();
                } else {
                    $error = 'Invalid email or password.';
                }
            } catch (Exception $e) {
                error_log("Login error: " . $e->getMessage());
                $error = 'An error occurred. Please try again later.';
            }
        }
    }
}

// Generate new CSRF token
$csrf_token = generateCSRFToken();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | <?= SITE_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full space-y-8 p-8 bg-white rounded-lg shadow-md">
        <div>
            <h2 class="text-center text-3xl font-extrabold text-gray-900">Sign in to your account</h2>
        </div>
        
        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline"><?= xssClean($error) ?></span>
            </div>
        <?php endif; ?>

        <form class="mt-8 space-y-6" method="POST" action="" novalidate>
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="email" class="sr-only">Email address</label>
                    <input id="email" name="email" type="email" required 
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                           placeholder="Email address"
                           value="<?= isset($_POST['email']) ? xssClean($_POST['email']) : '' ?>">
                </div>
                <div>
                    <label for="password" class="sr-only">Password</label>
                    <input id="password" name="password" type="password" required 
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                           placeholder="Password">
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember_me" name="remember_me" type="checkbox" 
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="remember_me" class="ml-2 block text-sm text-gray-900">
                        Remember me
                    </label>
                </div>

                <div class="text-sm">
                    <a href="forgot-password.php" class="font-medium text-blue-600 hover:text-blue-500">
                        Forgot your password?
                    </a>
                </div>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                    Sign in
                </button>
            </div>
        </form>

        <div class="text-center mt-4">
            <p class="text-sm text-gray-600">
                Don't have an account? 
                <a href="register.php" class="font-medium text-blue-600 hover:text-blue-500">
                    Register here
                </a>
            </p>
        </div>
    </div>

    <script>
    // Client-side validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        
        if (!email || !password) {
            e.preventDefault();
            alert('Please fill in all fields');
            return;
        }
        
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            e.preventDefault();
            alert('Please enter a valid email address');
            return;
        }
    });
    </script>
</body>
</html>
