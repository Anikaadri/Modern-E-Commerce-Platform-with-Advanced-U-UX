/**
 * Shopping cart related JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    initializeCart();
});

function initializeCart() {
    const updateButtons = document.querySelectorAll('button[name="update_quantity"]');
    updateButtons.forEach(button => {
        button.addEventListener('click', handleUpdateQuantity);
    });

    const removeButtons = document.querySelectorAll('.btn-remove');
    removeButtons.forEach(button => {
        button.addEventListener('click', handleRemoveItem);
    });
}

function handleUpdateQuantity(event) {
    event.preventDefault();
    const form = event.target.closest('form');
    const quantity = form.querySelector('input[name="quantity"]').value;
    
    if (quantity < 1) {
        alert('Quantity must be at least 1');
        return;
    }
    
    form.submit();
}

function handleRemoveItem(event) {
    if (!confirm('Are you sure you want to remove this item?')) {
        event.preventDefault();
    }
}

function calculateTotal() {
    const cartItems = document.querySelectorAll('.cart-table tbody tr');
    let total = 0;
    
    cartItems.forEach(row => {
        const subtotal = parseFloat(row.querySelector('td:nth-child(4)').textContent.replace('$', ''));
        total += subtotal;
    });
    
    const totalElement = document.querySelector('.cart-summary .total');
    if (totalElement) {
        totalElement.textContent = 'Total: $' + total.toFixed(2);
    }
}

// Recalculate total on page load
calculateTotal();
