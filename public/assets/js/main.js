/**
 * Main JavaScript file for the online shop
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize event listeners
    initializeEventListeners();
    loadCart();
});

function initializeEventListeners() {
    // Add event listeners for common actions
    const addToCartButtons = document.querySelectorAll('.btn-add-cart');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', handleAddToCart);
    });
}

function handleAddToCart(event) {
    event.preventDefault();
    const form = event.target.closest('form');
    if (form) {
        form.submit();
    }
}

function loadCart() {
    // Load cart data from session/local storage
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    updateCartCount(cart.length);
}

function updateCartCount(count) {
    const cartCount = document.querySelector('.cart-count');
    if (cartCount) {
        cartCount.textContent = count;
    }
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
