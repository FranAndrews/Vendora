/**
 * Cart Management JavaScript
 * Handles shopping cart functionality
 */

const Cart = {
    // Get cart from localStorage
    getCart: function() {
        return JSON.parse(localStorage.getItem('cart')) || [];
    },

    // Save cart to localStorage
    saveCart: function(cart) {
        localStorage.setItem('cart', JSON.stringify(cart));
        this.updateCartCount();
    },

    // Add item to cart
    addItem: function(productId, name, price, quantity = 1) {
        const cart = this.getCart();
        const existingItem = cart.find(item => item.id === productId);
        
        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            cart.push({
                id: productId,
                name: name,
                price: price,
                quantity: quantity
            });
        }
        
        this.saveCart(cart);
        Vendora.showNotification('Item added to cart', 'success');
    },

    // Remove item from cart
    removeItem: function(productId) {
        const cart = this.getCart();
        const filteredCart = cart.filter(item => item.id !== productId);
        this.saveCart(filteredCart);
        Vendora.showNotification('Item removed from cart', 'info');
    },

    // Update item quantity
    updateQuantity: function(productId, quantity) {
        const cart = this.getCart();
        const item = cart.find(item => item.id === productId);
        
        if (item) {
            if (quantity <= 0) {
                this.removeItem(productId);
            } else {
                item.quantity = quantity;
                this.saveCart(cart);
            }
        }
    },

    // Get cart total
    getTotal: function() {
        const cart = this.getCart();
        return cart.reduce((total, item) => total + (item.price * item.quantity), 0);
    },

    // Get cart count
    getCount: function() {
        const cart = this.getCart();
        return cart.reduce((count, item) => count + item.quantity, 0);
    },

    // Update cart count display
    updateCartCount: function() {
        const count = this.getCount();
        const badge = document.getElementById('cart-count');
        if (badge) {
            badge.textContent = count;
            badge.style.display = count > 0 ? 'block' : 'none';
        }
    },

    // Clear cart
    clearCart: function() {
        localStorage.removeItem('cart');
        this.updateCartCount();
        Vendora.showNotification('Cart cleared', 'info');
    },

    // Checkout
    checkout: function() {
        const cart = this.getCart();
        if (cart.length === 0) {
            Vendora.showNotification('Cart is empty', 'error');
            return;
        }
        
        // Redirect to checkout page
        window.location.href = BASE_URL + 'buyer/checkout.php';
    }
};

// Initialize cart functionality
document.addEventListener('DOMContentLoaded', function() {
    Cart.updateCartCount();
    
    // Add event listeners for cart buttons
    document.addEventListener('click', function(e) {
        if (e.target.matches('.add-to-cart')) {
            const productId = e.target.dataset.productId;
            const name = e.target.dataset.productName;
            const price = parseFloat(e.target.dataset.productPrice);
            
            Cart.addItem(productId, name, price);
        }
        
        if (e.target.matches('.remove-from-cart')) {
            const productId = e.target.dataset.productId;
            Cart.removeItem(productId);
        }
    });
}); 