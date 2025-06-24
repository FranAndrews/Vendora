<?php
// admin/dashboard.php
define('SECURE_ACCESS', true);
require_once '../includes/config.php';
require_once '../includes/db.php';
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

// Get statistics
try {
    // User statistics
    $userCount = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
    $sellerCount = $conn->query("SELECT COUNT(*) FROM users WHERE role='seller'")->fetch_row()[0];
    $buyerCount = $conn->query("SELECT COUNT(*) FROM users WHERE role='buyer'")->fetch_row()[0];
    
    // Product statistics
    $productCount = $conn->query("SELECT COUNT(*) FROM products")->fetch_row()[0];
    $activeProducts = $conn->query("SELECT COUNT(*) FROM products WHERE status='active'")->fetch_row()[0];
    
    // Order statistics
    $orderCount = $conn->query("SELECT COUNT(*) FROM orders")->fetch_row()[0];
    $totalSales = $conn->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status='delivered'")->fetch_row()[0];

    // Recent users
    $recentUsers = $conn->query("SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);

    // Recent products
    $recentProducts = $conn->query("SELECT p.*, u.name as seller_name 
                                   FROM products p 
                                   JOIN users u ON p.seller_id = u.id 
                                   ORDER BY p.created_at DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);

} catch (Exception $e) {
    error_log("Admin dashboard error: " . $e->getMessage());
    $error = "An error occurred while fetching statistics.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Vendora</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <header class="bg-white shadow p-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold text-gray-800">Admin Dashboard</h1>
            <a href="<?= BASE_URL ?>logout.php" class="text-sm text-red-600 hover:underline">Logout</a>
        </div>
    </header>

    <main class="max-w-7xl mx-auto p-6">
        <!-- Welcome Section -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-2">Welcome, <?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?></h2>
            <p class="text-gray-600">Manage your marketplace and monitor activity here.</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Users Stats -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-gray-500 text-sm">Total Users</h3>
                        <p class="text-2xl font-semibold text-gray-800"><?= number_format($userCount) ?></p>
                        <p class="text-sm text-gray-500"><?= number_format($sellerCount) ?> sellers, <?= number_format($buyerCount) ?> buyers</p>
                    </div>
                </div>
            </div>

            <!-- Products Stats -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-gray-500 text-sm">Total Products</h3>
                        <p class="text-2xl font-semibold text-gray-800"><?= number_format($productCount) ?></p>
                        <p class="text-sm text-gray-500"><?= number_format($activeProducts) ?> active</p>
                    </div>
                </div>
            </div>

            <!-- Orders Stats -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-gray-500 text-sm">Total Orders</h3>
                        <p class="text-2xl font-semibold text-gray-800"><?= number_format($orderCount) ?></p>
                    </div>
                </div>
            </div>

            <!-- Sales Stats -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-gray-500 text-sm">Total Sales</h3>
                        <p class="text-2xl font-semibold text-gray-800">R<?= number_format($totalSales, 2) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Users -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Recent Users</h3>
                    <a href="<?= BASE_URL ?>admin/users.php" class="text-sm text-blue-600 hover:underline">View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Joined</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($recentUsers as $user): ?>
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-900"><?= htmlspecialchars($user['name']) ?></td>
                                    <td class="px-4 py-2 text-sm text-gray-500"><?= ucfirst($user['role']) ?></td>
                                    <td class="px-4 py-2 text-sm text-gray-500"><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Products -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Recent Products</h3>
                    <a href="<?= BASE_URL ?>admin/products.php" class="text-sm text-blue-600 hover:underline">View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Seller</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($recentProducts as $product): ?>
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-900"><?= htmlspecialchars($product['name']) ?></td>
                                    <td class="px-4 py-2 text-sm text-gray-500"><?= htmlspecialchars($product['seller_name']) ?></td>
                                    <td class="px-4 py-2 text-sm text-gray-900">R<?= number_format($product['price'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="<?= BASE_URL ?>admin/users.php" class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-800">Manage Users</h3>
                        <p class="text-sm text-gray-500">View and manage user accounts</p>
                    </div>
                </div>
            </a>

            <a href="<?= BASE_URL ?>admin/products.php" class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-800">Manage Products</h3>
                        <p class="text-sm text-gray-500">View and manage product listings</p>
                    </div>
                </div>
            </a>

            <a href="<?= BASE_URL ?>admin/orders.php" class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-800">Manage Orders</h3>
                        <p class="text-sm text-gray-500">View and manage customer orders</p>
                    </div>
                </div>
            </a>
        </div>
    </main>

    <footer class="bg-white shadow p-4 text-center text-gray-600 text-sm mt-8">
        <?= SITE_NAME ?> &copy; <?= date('Y') ?>
    </footer>
</body>
</html>