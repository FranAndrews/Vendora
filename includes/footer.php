<!-- includes/footer.php -->
</main> <!-- Close page content container -->

<footer class="bg-white border-t border-gray-200 mt-10">
  <div class="max-w-7xl mx-auto px-4 py-8">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
      <!-- Company Info -->
      <div>
        <h3 class="text-lg font-semibold text-orange-600 mb-4"><?= SITE_NAME ?></h3>
        <p class="text-gray-600 text-sm mb-4">Your trusted marketplace for quality products and services.</p>
        <div class="flex space-x-4">
          <a href="#" class="text-gray-400 hover:text-orange-600 transition">
            <i class="fab fa-facebook-f"></i>
          </a>
          <a href="#" class="text-gray-400 hover:text-orange-600 transition">
            <i class="fab fa-twitter"></i>
          </a>
          <a href="#" class="text-gray-400 hover:text-orange-600 transition">
            <i class="fab fa-instagram"></i>
          </a>
        </div>
      </div>

      <!-- Quick Links -->
      <div>
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Links</h3>
        <ul class="space-y-2 text-sm">
          <li><a href="<?= BASE_URL ?>buyer/products.php" class="text-gray-600 hover:text-orange-600 transition">Browse Products</a></li>
          <li><a href="<?= BASE_URL ?>register.php" class="text-gray-600 hover:text-orange-600 transition">Become a Seller</a></li>
          <li><a href="<?= BASE_URL ?>buyer/cart.php" class="text-gray-600 hover:text-orange-600 transition">Shopping Cart</a></li>
          <li><a href="<?= BASE_URL ?>buyer/orders.php" class="text-gray-600 hover:text-orange-600 transition">My Orders</a></li>
        </ul>
      </div>

      <!-- Customer Service -->
      <div>
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Customer Service</h3>
        <ul class="space-y-2 text-sm">
          <li><a href="#" class="text-gray-600 hover:text-orange-600 transition">Contact Us</a></li>
          <li><a href="#" class="text-gray-600 hover:text-orange-600 transition">Shipping Policy</a></li>
          <li><a href="#" class="text-gray-600 hover:text-orange-600 transition">Returns & Refunds</a></li>
          <li><a href="#" class="text-gray-600 hover:text-orange-600 transition">FAQ</a></li>
        </ul>
      </div>

      <!-- Newsletter -->
      <div>
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Stay Updated</h3>
        <p class="text-gray-600 text-sm mb-4">Subscribe to our newsletter for the latest updates and offers.</p>
        <form class="flex gap-2">
          <input type="email" placeholder="Enter your email" class="flex-1 px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
          <button type="submit" class="bg-orange-600 text-white px-4 py-2 rounded-md hover:bg-orange-700 transition">
            Subscribe
          </button>
        </form>
      </div>
    </div>

    <div class="border-t border-gray-200 mt-8 pt-8 text-center text-sm text-gray-500">
      <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.</p>
      <div class="mt-2 space-x-4">
        <a href="#" class="hover:text-orange-600 transition">Privacy Policy</a>
        <a href="#" class="hover:text-orange-600 transition">Terms of Service</a>
        <a href="#" class="hover:text-orange-600 transition">Cookie Policy</a>
      </div>
    </div>
  </div>
</footer>

</body>
</html>
