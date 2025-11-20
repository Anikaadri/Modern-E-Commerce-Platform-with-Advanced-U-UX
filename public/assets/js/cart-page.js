/**
 * Shopping Cart Page JavaScript
 * Real-time updates, animations, promo codes
 */

// ============================================
// CART STATE MANAGEMENT
// ============================================
const CartManager = {
    init() {
        this.attachEventListeners();
        this.updateCartTotals();
        this.initPromoCode();
    },

    attachEventListeners() {
        // Quantity controls
        document.querySelectorAll('.qty-btn-cart').forEach(btn => {
            btn.addEventListener('click', (e) => this.handleQuantityChange(e));
        });

        // Remove item buttons
        document.querySelectorAll('.remove-item-btn').forEach(btn => {
            btn.addEventListener('click', (e) => this.handleRemoveItem(e));
        });

        // Quantity input direct change
        document.querySelectorAll('.qty-input-cart').forEach(input => {
            input.addEventListener('change', (e) => this.handleQuantityInput(e));
        });
    },

    handleQuantityChange(e) {
        const btn = e.currentTarget;
        const controls = btn.closest('.quantity-controls-cart');
        const input = controls.querySelector('.qty-input-cart');
        const currentQty = parseInt(input.value) || 1;
        const isIncrement = btn.classList.contains('qty-plus');

        let newQty = isIncrement ? currentQty + 1 : currentQty - 1;
        newQty = Math.max(1, Math.min(99, newQty));

        if (newQty !== currentQty) {
            input.value = newQty;
            this.animateQuantityChange(input);
            this.updateItemTotal(btn.closest('.cart-item'));
            this.updateCartTotals();
        }
    },

    handleQuantityInput(e) {
        const input = e.currentTarget;
        let value = parseInt(input.value) || 1;
        value = Math.max(1, Math.min(99, value));
        input.value = value;

        this.updateItemTotal(input.closest('.cart-item'));
        this.updateCartTotals();
    },

    animateQuantityChange(input) {
        input.style.transform = 'scale(1.2)';
        setTimeout(() => {
            input.style.transform = 'scale(1)';
        }, 200);
    },

    handleRemoveItem(e) {
        e.preventDefault();
        const item = e.currentTarget.closest('.cart-item');
        const productName = item.querySelector('.cart-item-name').textContent;

        // Add removing animation
        item.classList.add('removing');

        setTimeout(() => {
            item.remove();
            this.updateCartTotals();
            this.checkEmptyCart();
            Toast.info(`${productName} removed from cart`);
        }, 300);
    },

    updateItemTotal(item) {
        const price = parseFloat(item.dataset.price) || 0;
        const quantity = parseInt(item.querySelector('.qty-input-cart').value) || 1;
        const total = price * quantity;

        const priceElement = item.querySelector('.cart-item-price');
        if (priceElement) {
            priceElement.textContent = `$${total.toFixed(2)}`;

            // Animate price change
            priceElement.style.color = 'var(--success)';
            setTimeout(() => {
                priceElement.style.color = 'var(--accent-600)';
            }, 500);
        }
    },

    updateCartTotals() {
        const items = document.querySelectorAll('.cart-item:not(.removing)');
        let subtotal = 0;

        items.forEach(item => {
            const price = parseFloat(item.dataset.price) || 0;
            const quantity = parseInt(item.querySelector('.qty-input-cart').value) || 1;
            subtotal += price * quantity;
        });

        const shipping = subtotal > 0 ? 10 : 0; // $10 flat shipping
        const discount = this.getDiscountAmount(subtotal);
        const total = subtotal + shipping - discount;

        // Update summary
        this.updateSummaryValue('subtotal', subtotal);
        this.updateSummaryValue('shipping', shipping);
        this.updateSummaryValue('discount', discount);
        this.updateSummaryValue('total', total);

        // Update cart count
        this.updateCartCount(items.length);
    },

    updateSummaryValue(type, amount) {
        const element = document.querySelector(`.summary-${type} .summary-value`);
        if (element) {
            element.textContent = `$${amount.toFixed(2)}`;
        }
    },

    updateCartCount(count) {
        const countElement = document.querySelector('.cart-count');
        if (countElement) {
            countElement.textContent = `${count} item${count !== 1 ? 's' : ''}`;
        }
    },

    checkEmptyCart() {
        const items = document.querySelectorAll('.cart-item:not(.removing)');
        if (items.length === 0) {
            this.showEmptyCart();
        }
    },

    showEmptyCart() {
        const cartItems = document.querySelector('.cart-items');
        const cartSummary = document.querySelector('.cart-summary');

        if (cartItems) {
            cartItems.innerHTML = `
                <div class="empty-cart">
                    <div class="empty-cart-icon">🛒</div>
                    <h2 class="empty-cart-title">Your cart is empty</h2>
                    <p class="empty-cart-message">Looks like you haven't added anything to your cart yet.</p>
                    <a href="index.php" class="continue-shopping-btn">Continue Shopping</a>
                </div>
            `;
        }

        if (cartSummary) {
            cartSummary.style.display = 'none';
        }
    },

    // Promo Code System
    initPromoCode() {
        const promoToggle = document.querySelector('.promo-toggle');
        const promoInputGroup = document.querySelector('.promo-input-group');
        const promoApplyBtn = document.querySelector('.promo-apply-btn');

        if (promoToggle && promoInputGroup) {
            promoToggle.addEventListener('click', () => {
                promoInputGroup.classList.toggle('active');
            });
        }

        if (promoApplyBtn) {
            promoApplyBtn.addEventListener('click', () => this.applyPromoCode());
        }
    },

    applyPromoCode() {
        const input = document.querySelector('.promo-input');
        const code = input.value.trim().toUpperCase();

        if (!code) {
            Toast.error('Please enter a promo code');
            return;
        }

        // Simulate promo code validation
        const validCodes = {
            'SAVE10': 10,
            'SAVE20': 20,
            'WELCOME': 15
        };

        if (validCodes[code]) {
            this.currentDiscount = validCodes[code];
            Toast.success(`Promo code applied! ${validCodes[code]}% off`);
            this.updateCartTotals();
            input.value = '';
            document.querySelector('.promo-input-group').classList.remove('active');
        } else {
            Toast.error('Invalid promo code');
        }
    },

    currentDiscount: 0,

    getDiscountAmount(subtotal) {
        return (subtotal * this.currentDiscount) / 100;
    }
};

// ============================================
// PROGRESS INDICATOR
// ============================================
const CheckoutProgress = {
    init() {
        this.updateProgress('cart');
    },

    updateProgress(currentStep) {
        const steps = ['cart', 'checkout', 'complete'];
        const currentIndex = steps.indexOf(currentStep);

        steps.forEach((step, index) => {
            const stepElement = document.querySelector(`.progress-step[data-step="${step}"]`);
            if (stepElement) {
                if (index < currentIndex) {
                    stepElement.classList.add('completed');
                    stepElement.classList.remove('active');
                } else if (index === currentIndex) {
                    stepElement.classList.add('active');
                    stepElement.classList.remove('completed');
                } else {
                    stepElement.classList.remove('active', 'completed');
                }
            }
        });
    }
};

// ============================================
// INITIALIZATION
// ============================================
document.addEventListener('DOMContentLoaded', () => {
    CartManager.init();
    CheckoutProgress.init();

    // Animate cart items on load
    const cartItems = document.querySelectorAll('.cart-item');
    cartItems.forEach((item, index) => {
        item.style.opacity = '0';
        item.style.transform = 'translateY(20px)';
        setTimeout(() => {
            item.style.transition = 'all 0.3s ease';
            item.style.opacity = '1';
            item.style.transform = 'translateY(0)';
        }, index * 100);
    });
});

// Export for use in other scripts
window.CartPage = {
    CartManager,
    CheckoutProgress
};
