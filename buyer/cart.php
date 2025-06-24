<?php
define('SECURE_ACCESS', true);
require_once '../includes/config.php';
require_once '../includes/db.php';
session_start();

include '../includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-8">Shopping Cart</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Cart Items -->
        <div class="lg:col-span-2">
            <div id="cart-items" class="space-y-4">
                <!-- Cart items will be loaded here -->
            </div>

            <div id="empty-cart" class="hidden text-center py-12">
                <i class="fas fa-shopping-cart text-4xl text-gray-400 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">Your cart is empty</h3>
                <p class="text-gray-500 mb-6">Looks like you haven't added any items to your cart yet.</p>
                <a href="<?= BASE_URL ?>buyer/products.php" class="inline-block bg-orange-600 text-white px-6 py-3 rounded-md hover:bg-orange-700 transition">
                    Start Shopping
                </a>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="lg:col-span-1">
            <div class="bg-white border rounded-lg p-6 sticky top-24">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Order Summary</h2>
                
                <div class="space-y-3 mb-4">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span id="subtotal">R0.00</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Shipping</span>
                        <span id="shipping">R0.00</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Tax</span>
                        <span id="tax">R0.00</span>
                    </div>
                    <div class="border-t pt-3 flex justify-between font-semibold text-gray-800">
                        <span>Total</span>
                        <span id="total">R0.00</span>
                    </div>
                </div>

                <div class="mt-6">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="<?= BASE_URL ?>buyer/checkout.php" class="w-full bg-orange-600 text-white px-6 py-3 rounded-md hover:bg-orange-700 transition text-center block">
                            Proceed to Checkout
                        </a>
                    <?php else: ?>
                        <div class="space-y-4">
                            <a href="<?= BASE_URL ?>login.php" class="w-full bg-orange-600 text-white px-6 py-3 rounded-md hover:bg-orange-700 transition text-center block">
                                Sign in to Checkout
                            </a>
                            <p class="text-center text-sm text-gray-600">
                                Don't have an account? 
                                <a href="<?= BASE_URL ?>register.php" class="text-orange-600 hover:text-orange-700">Register here</a>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mt-4 text-center">
                    <a href="<?= BASE_URL ?>buyer/products.php" class="text-orange-600 hover:text-orange-700 text-sm">
                        Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function loadCart() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const cartItems = document.getElementById('cart-items');
    const emptyCart = document.getElementById('empty-cart');
    
    if (cart.length === 0) {
        cartItems.classList.add('hidden');
        emptyCart.classList.remove('hidden');
        updateSummary(0, 0, 0);
        return;
    }

    cartItems.classList.remove('hidden');
    emptyCart.classList.add('hidden');
    
    cartItems.innerHTML = cart.map(item => `
        <div class="bg-white border rounded-lg p-4 flex items-center gap-4" data-id="${item.id}">
            <img src="${item.image}" alt="${item.name}" class="w-24 h-24 object-cover rounded">
            <div class="flex-1">
                <h3 class="font-semibold text-gray-800">${item.name}</h3>
                <p class="text-orange-600 font-bold">R${item.price.toFixed(2)}</p>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="updateItemQuantity(${item.id}, -1)" class="px-2 py-1 text-gray-600 hover:text-orange-600">
                    <i class="fas fa-minus"></i>
                </button>
                <span class="w-8 text-center">${item.quantity}</span>
                <button onclick="updateItemQuantity(${item.id}, 1)" class="px-2 py-1 text-gray-600 hover:text-orange-600">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
            <button onclick="removeItem(${item.id})" class="text-gray-400 hover:text-red-600">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `).join('');

    updateSummary(cart);
}

function updateItemQuantity(id, change) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    const index = cart.findIndex(item => item.id === id);
    
    if (index > -1) {
        cart[index].quantity += change;
        
        if (cart[index].quantity <= 0) {
            cart.splice(index, 1);
        }
        
        localStorage.setItem('cart', JSON.stringify(cart));
        loadCart();
        updateCartCount();
    }
}

function removeItem(id) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    cart = cart.filter(item => item.id !== id);
    localStorage.setItem('cart', JSON.stringify(cart));
    loadCart();
    updateCartCount();
}

function updateSummary(cart) {
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const shipping = subtotal > 0 ? 50 : 0; // R50 shipping fee
    const tax = subtotal * 0.15; // 15% tax
    
    document.getElementById('subtotal').textContent = `R${subtotal.toFixed(2)}`;
    document.getElementById('shipping').textContent = `R${shipping.toFixed(2)}`;
    document.getElementById('tax').textContent = `R${tax.toFixed(2)}`;
    document.getElementById('total').textContent = `R${(subtotal + shipping + tax).toFixed(2)}`;
}

function proceedToCheckout() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    if (cart.length === 0) {
        alert('Your cart is empty!');
        return;
    }
    
    // Redirect to checkout page
    window.location.href = '<?= BASE_URL ?>buyer/checkout.php';
}

// Load cart when page loads
document.addEventListener('DOMContentLoaded', loadCart);
</script>

<?php include '../includes/footer.php'; ?>
