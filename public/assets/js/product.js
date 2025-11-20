/**
 * Product detail page JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    initializeProductPage();
});

function initializeProductPage() {
    const addToCartForm = document.querySelector('form[action="cart.php"]');
    if (addToCartForm) {
        addToCartForm.addEventListener('submit', handleAddToCart);
    }

    // Image gallery functionality
    const images = document.querySelectorAll('.product-image img');
    if (images.length > 0) {
        images.forEach((img, index) => {
            img.addEventListener('click', function() {
                showImageModal(img.src);
            });
        });
    }
}

function handleAddToCart(event) {
    const quantityInput = document.querySelector('input[name="quantity"]');
    const quantity = parseInt(quantityInput.value);
    const stock = parseInt(quantityInput.getAttribute('max'));
    
    if (quantity > stock) {
        event.preventDefault();
        alert('Not enough stock available');
        return;
    }
    
    showNotification('Product added to cart!', 'success');
}

function showImageModal(imageSrc) {
    const modal = document.createElement('div');
    modal.className = 'image-modal';
    modal.innerHTML = `
        <div class="modal-content">
            <span class="close">&times;</span>
            <img src="${imageSrc}" alt="Product Image">
        </div>
    `;
    
    document.body.appendChild(modal);
    
    modal.querySelector('.close').addEventListener('click', function() {
        modal.remove();
    });
    
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.remove();
        }
    });
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
