/**
 * Premium Features
 * Wishlist, Quick View, Toast Notifications, Advanced Search
 */

// ============================================
// STATE MANAGEMENT
// ============================================
const AppState = {
    wishlist: JSON.parse(localStorage.getItem('wishlist') || '[]'),

    addToWishlist(productId) {
        if (!this.wishlist.includes(productId)) {
            this.wishlist.push(productId);
            this.saveWishlist();
            return true;
        }
        return false;
    },

    removeFromWishlist(productId) {
        const index = this.wishlist.indexOf(productId);
        if (index > -1) {
            this.wishlist.splice(index, 1);
            this.saveWishlist();
            return true;
        }
        return false;
    },

    isInWishlist(productId) {
        return this.wishlist.includes(productId);
    },

    saveWishlist() {
        localStorage.setItem('wishlist', JSON.stringify(this.wishlist));
        this.updateWishlistUI();
    },

    updateWishlistUI() {
        // Update wishlist counter
        const counter = document.querySelector('.wishlist-counter');
        if (counter) {
            counter.textContent = this.wishlist.length;
            counter.style.display = this.wishlist.length > 0 ? 'flex' : 'none';
        }

        // Update heart icons
        document.querySelectorAll('.wishlist-btn').forEach(btn => {
            const productId = btn.dataset.productId;
            const isInWishlist = this.isInWishlist(productId);
            btn.classList.toggle('active', isInWishlist);
            btn.innerHTML = isInWishlist ? '❤️' : '🤍';
        });
    }
};

// ============================================
// TOAST NOTIFICATIONS
// ============================================
const Toast = {
    container: null,

    init() {
        if (!this.container) {
            this.container = document.createElement('div');
            this.container.id = 'toast-container';
            document.body.appendChild(this.container);
        }
    },

    show(message, type = 'info', duration = 3000) {
        this.init();

        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;

        const icons = {
            success: '✓',
            error: '✕',
            warning: '⚠',
            info: 'ℹ'
        };

        toast.innerHTML = `
            <div class="toast-icon">${icons[type] || icons.info}</div>
            <div class="toast-content">
                <div class="toast-message">${message}</div>
            </div>
            <button class="toast-close" onclick="Toast.remove(this.parentElement)">×</button>
        `;

        this.container.appendChild(toast);

        // Auto remove after duration
        if (duration > 0) {
            setTimeout(() => this.remove(toast), duration);
        }

        return toast;
    },

    remove(toast) {
        toast.classList.add('removing');
        setTimeout(() => {
            if (toast.parentElement) {
                toast.parentElement.removeChild(toast);
            }
        }, 300);
    },

    success(message, duration) {
        return this.show(message, 'success', duration);
    },

    error(message, duration) {
        return this.show(message, 'error', duration);
    },

    warning(message, duration) {
        return this.show(message, 'warning', duration);
    },

    info(message, duration) {
        return this.show(message, 'info', duration);
    }
};

// ============================================
// MODAL SYSTEM
// ============================================
const Modal = {
    create(content, options = {}) {
        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop';

        const modal = document.createElement('div');
        modal.className = 'modal';

        const header = document.createElement('div');
        header.className = 'modal-header';
        header.innerHTML = `
            <h3 class="modal-title">${options.title || 'Modal'}</h3>
            <button class="modal-close" onclick="Modal.close(this)">×</button>
        `;

        const body = document.createElement('div');
        body.className = 'modal-body';
        body.innerHTML = content;

        modal.appendChild(header);
        modal.appendChild(body);

        if (options.footer) {
            const footer = document.createElement('div');
            footer.className = 'modal-footer';
            footer.innerHTML = options.footer;
            modal.appendChild(footer);
        }

        backdrop.appendChild(modal);
        document.body.appendChild(backdrop);

        // Close on backdrop click
        backdrop.addEventListener('click', (e) => {
            if (e.target === backdrop) {
                this.close(backdrop);
            }
        });

        // Prevent body scroll
        document.body.style.overflow = 'hidden';

        return backdrop;
    },

    close(element) {
        const backdrop = element.closest('.modal-backdrop');
        if (backdrop) {
            backdrop.classList.add('removing');
            backdrop.querySelector('.modal').classList.add('removing');

            setTimeout(() => {
                backdrop.remove();
                document.body.style.overflow = '';
            }, 300);
        }
    }
};

// ============================================
// QUICK VIEW
// ============================================
const QuickView = {
    async show(productId) {
        try {
            // Show loading state
            const loadingContent = `
                <div class="skeleton-card">
                    <div class="skeleton skeleton-image"></div>
                    <div class="skeleton skeleton-title"></div>
                    <div class="skeleton skeleton-text"></div>
                    <div class="skeleton skeleton-text"></div>
                </div>
            `;

            const modal = Modal.create(loadingContent, {
                title: 'Quick View'
            });

            // Fetch product details (simulated - replace with actual API call)
            const response = await fetch(`product.php?id=${productId}&ajax=1`);
            const product = await response.json();

            // Update modal with product details
            const content = `
                <div class="quick-view-content">
                    <div class="quick-view-image">
                        <div class="placeholder-image" style="width: 100%; height: 300px; background: linear-gradient(135deg, var(--primary-500) 0%, var(--secondary-500) 100%); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 4rem; color: white;">
                            ${product.name ? product.name.charAt(0) : 'P'}
                        </div>
                    </div>
                    <h3 style="font-size: 1.5rem; font-weight: 800; margin: 1.5rem 0 1rem 0;">${product.name || 'Product'}</h3>
                    <p style="color: var(--gray-600); margin-bottom: 1rem;">${product.description || 'No description available'}</p>
                    <div style="font-size: 2rem; font-weight: 900; color: var(--accent-500); margin: 1.5rem 0;">
                        ৳${product.price ? Number(product.price).toLocaleString() : '0'}
                    </div>
                    <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                        <button class="btn btn-primary" onclick="window.location.href='product.php?id=${productId}'" style="flex: 1;">
                            View Full Details
                        </button>
                        <button class="btn btn-secondary" onclick="addToCart(${productId})" style="flex: 1;">
                            Add to Cart
                        </button>
                    </div>
                </div>
            `;

            modal.querySelector('.modal-body').innerHTML = content;

        } catch (error) {
            console.error('Error loading product:', error);
            Toast.error('Failed to load product details');
        }
    }
};

// ============================================
// WISHLIST FUNCTIONALITY
// ============================================
function toggleWishlist(productId, productName) {
    const isAdded = AppState.addToWishlist(productId);

    if (isAdded) {
        Toast.success(`${productName} added to wishlist! ❤️`);
    } else {
        AppState.removeFromWishlist(productId);
        Toast.info(`${productName} removed from wishlist`);
    }
}

// ============================================
// ADVANCED SEARCH & FILTERING
// ============================================
const SearchFilter = {
    products: [],

    init(products) {
        this.products = products;
        this.setupEventListeners();
    },

    setupEventListeners() {
        const searchInput = document.querySelector('#search-input');
        const categoryFilter = document.querySelector('#category-filter');
        const priceMinInput = document.querySelector('#price-min');
        const priceMaxInput = document.querySelector('#price-max');

        if (searchInput) {
            searchInput.addEventListener('input', () => this.filter());
        }

        if (categoryFilter) {
            categoryFilter.addEventListener('change', () => this.filter());
        }

        if (priceMinInput || priceMaxInput) {
            [priceMinInput, priceMaxInput].forEach(input => {
                if (input) {
                    input.addEventListener('input', () => this.filter());
                }
            });
        }
    },

    filter() {
        const searchTerm = document.querySelector('#search-input')?.value.toLowerCase() || '';
        const category = document.querySelector('#category-filter')?.value || '';
        const priceMin = parseFloat(document.querySelector('#price-min')?.value) || 0;
        const priceMax = parseFloat(document.querySelector('#price-max')?.value) || Infinity;

        const filtered = this.products.filter(product => {
            const matchesSearch = product.name.toLowerCase().includes(searchTerm) ||
                product.description.toLowerCase().includes(searchTerm);
            const matchesCategory = !category || product.category === category;
            const matchesPrice = product.price >= priceMin && product.price <= priceMax;

            return matchesSearch && matchesCategory && matchesPrice;
        });

        this.renderProducts(filtered);
    },

    renderProducts(products) {
        const grid = document.querySelector('.product-grid');
        if (!grid) return;

        if (products.length === 0) {
            grid.innerHTML = '<p style="grid-column: 1/-1; text-align: center; padding: 4rem; color: var(--gray-600);">No products found</p>';
            return;
        }

        grid.innerHTML = products.map(product => `
            <div class="product-card" data-product-id="${product.id}">
                <div class="product-image-wrapper" style="position: relative;">
                    <button class="wishlist-btn" data-product-id="${product.id}" 
                            onclick="toggleWishlist('${product.id}', '${product.name}')"
                            style="position: absolute; top: 1rem; right: 1rem; background: white; border: none; width: 40px; height: 40px; border-radius: 50%; cursor: pointer; font-size: 1.25rem; box-shadow: 0 4px 15px rgba(0,0,0,0.1); transition: all 0.3s;">
                        ${AppState.isInWishlist(product.id) ? '❤️' : '🤍'}
                    </button>
                    <div class="placeholder-image" style="width: 100%; height: 250px; background: linear-gradient(135deg, var(--primary-500) 0%, var(--secondary-500) 100%); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 3rem; color: white;">
                        ${product.name.charAt(0)}
                    </div>
                </div>
                <h3>${product.name}</h3>
                <p>${product.description.substring(0, 100)}...</p>
                <p class="price">৳${Number(product.price).toLocaleString()}</p>
                <div style="display: flex; gap: 0.5rem; margin-top: 1rem;">
                    <button class="btn btn-primary" onclick="QuickView.show('${product.id}')" style="flex: 1; padding: 0.75rem;">
                        Quick View
                    </button>
                    <a href="product.php?id=${product.id}" class="btn btn-outline" style="flex: 1; padding: 0.75rem;">
                        Details
                    </a>
                </div>
            </div>
        `).join('');

        // Update wishlist UI after rendering
        AppState.updateWishlistUI();
    }
};

// ============================================
// SCROLL TO TOP
// ============================================
function initScrollToTop() {
    const fab = document.querySelector('.fab-scroll-top');
    if (!fab) return;

    window.addEventListener('scroll', () => {
        if (window.scrollY > 300) {
            fab.style.display = 'flex';
        } else {
            fab.style.display = 'none';
        }
    });

    fab.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

// ============================================
// INITIALIZATION
// ============================================
document.addEventListener('DOMContentLoaded', () => {
    // Initialize wishlist UI
    AppState.updateWishlistUI();

    // Initialize scroll to top
    initScrollToTop();

    // Show welcome toast
    setTimeout(() => {
        Toast.info('Welcome to our premium shop! 🛍️', 4000);
    }, 500);
});

// ============================================
// UTILITY FUNCTIONS
// ============================================
// ============================================
// UTILITY FUNCTIONS
// ============================================
async function addToCart(productId, quantity = 1, silent = false) {
    try {
        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('quantity', quantity);

        const response = await fetch('cart.php', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            if (!silent) {
                Toast.success('Product added to cart! 🛒');
            }
            return true;
        } else {
            Toast.error(data.message || 'Failed to add to cart');
            return false;
        }
    } catch (error) {
        console.error('Error:', error);
        Toast.error('Connection error');
        return false;
    }
}

async function buyNow(productId) {
    const btn = event.currentTarget;
    const originalContent = btn.innerHTML;
    btn.innerHTML = '⚡ Processing...';
    btn.disabled = true;

    const success = await addToCart(productId, 1, true);

    if (success) {
        Toast.success('Redirecting to checkout... ⚡');
        setTimeout(() => {
            window.location.href = 'checkout.php';
        }, 500);
    } else {
        btn.innerHTML = originalContent;
        btn.disabled = false;
    }
}

// Export for use in other scripts
window.AppState = AppState;
window.Toast = Toast;
window.Modal = Modal;
window.QuickView = QuickView;
window.SearchFilter = SearchFilter;
window.addToCart = addToCart;
window.buyNow = buyNow;
