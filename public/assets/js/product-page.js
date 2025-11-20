/**
 * Product Page JavaScript - Complete with Buy Now
 * Image gallery, zoom, quantity selector, reviews, buy now
 */

// ============================================
// IMAGE GALLERY
// ============================================
const ProductGallery = {
    currentIndex: 0,
    images: [],

    init() {
        this.images = document.querySelectorAll('.thumbnail');
        this.mainImage = document.querySelector('.main-image');
        this.mainImageContainer = document.querySelector('.main-image-container');

        if (!this.images.length || !this.mainImage) return;

        // Thumbnail click handlers
        this.images.forEach((thumb, index) => {
            thumb.addEventListener('click', () => this.changeImage(index));
        });

        // Zoom functionality
        if (this.mainImageContainer) {
            this.mainImageContainer.addEventListener('click', () => this.openZoom());
        }

        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') this.prevImage();
            if (e.key === 'ArrowRight') this.nextImage();
        });
    },

    changeImage(index) {
        this.currentIndex = index;
        const newSrc = this.images[index].src;

        // Fade out
        this.mainImage.style.opacity = '0';

        setTimeout(() => {
            this.mainImage.src = newSrc;
            this.mainImage.style.opacity = '1';
        }, 200);

        // Update active thumbnail
        this.images.forEach(img => img.classList.remove('active'));
        this.images[index].classList.add('active');
    },

    nextImage() {
        const nextIndex = (this.currentIndex + 1) % this.images.length;
        this.changeImage(nextIndex);
    },

    prevImage() {
        const prevIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
        this.changeImage(prevIndex);
    },

    openZoom() {
        const overlay = document.createElement('div');
        overlay.className = 'zoom-overlay active';
        overlay.innerHTML = `
            <img src="${this.mainImage.src}" class="zoom-image" alt="Zoomed product">
            <button class="zoom-close">×</button>
        `;

        document.body.appendChild(overlay);
        document.body.style.overflow = 'hidden';

        // Close handlers
        const closeBtn = overlay.querySelector('.zoom-close');
        closeBtn.addEventListener('click', () => this.closeZoom(overlay));
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) this.closeZoom(overlay);
        });

        // ESC key to close
        const escHandler = (e) => {
            if (e.key === 'Escape') {
                this.closeZoom(overlay);
                document.removeEventListener('keydown', escHandler);
            }
        };
        document.addEventListener('keydown', escHandler);
    },

    closeZoom(overlay) {
        overlay.classList.remove('active');
        setTimeout(() => {
            overlay.remove();
            document.body.style.overflow = '';
        }, 300);
    }
};

// ============================================
// QUANTITY SELECTOR
// ============================================
const QuantitySelector = {
    init() {
        const minusBtn = document.querySelector('.qty-minus');
        const plusBtn = document.querySelector('.qty-plus');
        const input = document.querySelector('.qty-input');

        if (!minusBtn || !plusBtn || !input) return;

        const max = parseInt(input.getAttribute('max')) || 99;
        const min = parseInt(input.getAttribute('min')) || 1;

        minusBtn.addEventListener('click', () => {
            let value = parseInt(input.value) || min;
            if (value > min) {
                input.value = value - 1;
                this.animateChange(input);
            }
        });

        plusBtn.addEventListener('click', () => {
            let value = parseInt(input.value) || min;
            if (value < max) {
                input.value = value + 1;
                this.animateChange(input);
            }
        });

        // Validate on input
        input.addEventListener('input', () => {
            let value = parseInt(input.value) || min;
            if (value < min) input.value = min;
            if (value > max) input.value = max;
        });
    },

    animateChange(input) {
        input.style.transform = 'scale(1.2)';
        setTimeout(() => {
            input.style.transform = 'scale(1)';
        }, 200);
    }
};

// ============================================
// ADD TO CART
// ============================================
const AddToCart = {
    init() {
        const btn = document.querySelector('.btn-add-cart');
        if (!btn) return;

        btn.addEventListener('click', async (e) => {
            e.preventDefault();

            const productId = btn.dataset.productId;
            const quantity = document.querySelector('.qty-input')?.value || 1;

            // Add loading state
            btn.classList.add('adding');
            btn.textContent = 'Adding...';

            try {
                // Simulate API call (replace with actual cart.php POST)
                await new Promise(resolve => setTimeout(resolve, 1000));

                // Success
                btn.classList.remove('adding');
                btn.textContent = '✓ Added!';
                btn.style.background = 'linear-gradient(135deg, var(--success) 0%, #059669 100%)';

                Toast.success(`Product added to cart! 🛒`);

                // Reset button after 2 seconds
                setTimeout(() => {
                    btn.textContent = 'Add to Cart';
                    btn.style.background = '';
                }, 2000);

            } catch (error) {
                btn.classList.remove('adding');
                btn.textContent = 'Add to Cart';
                Toast.error('Failed to add to cart. Please try again.');
            }
        });
    }
};

// ============================================
// BUY NOW (Direct to Checkout)
// ============================================
const BuyNow = {
    init() {
        const btn = document.querySelector('.btn-buy-now');
        if (!btn) return;

        btn.addEventListener('click', async (e) => {
            e.preventDefault();

            const productId = btn.dataset.productId;
            const quantity = document.querySelector('.qty-input')?.value || 1;

            // Add loading state
            btn.classList.add('processing');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<span>Processing...</span>';

            try {
                // Add to cart first (replace with actual cart.php POST)
                await new Promise(resolve => setTimeout(resolve, 800));

                // Show success toast
                Toast.success('Redirecting to checkout... ⚡');

                // Redirect to checkout after short delay
                setTimeout(() => {
                    window.location.href = 'checkout.php';
                }, 500);

            } catch (error) {
                btn.classList.remove('processing');
                btn.innerHTML = originalText;
                Toast.error('Failed to process. Please try again.');
            }
        });
    }
};

// ============================================
// WISHLIST TOGGLE
// ============================================
const WishlistToggle = {
    init() {
        const btn = document.querySelector('.btn-wishlist');
        if (!btn) return;

        const productId = btn.dataset.productId;
        const productName = btn.dataset.productName;

        // Check if already in wishlist
        if (AppState.isInWishlist(productId)) {
            btn.classList.add('active');
            btn.innerHTML = '❤️';
        }

        btn.addEventListener('click', () => {
            const isInWishlist = AppState.isInWishlist(productId);

            if (isInWishlist) {
                AppState.removeFromWishlist(productId);
                btn.classList.remove('active');
                btn.innerHTML = '🤍';
                Toast.info(`Removed from wishlist`);
            } else {
                AppState.addToWishlist(productId);
                btn.classList.add('active');
                btn.innerHTML = '❤️';
                Toast.success(`${productName} added to wishlist! ❤️`);

                // Animate
                btn.style.transform = 'scale(1.3)';
                setTimeout(() => {
                    btn.style.transform = '';
                }, 300);
            }
        });
    }
};

// ============================================
// SHARE FUNCTIONALITY
// ============================================
const ShareProduct = {
    init() {
        const shareButtons = document.querySelectorAll('.share-btn');
        const url = window.location.href;
        const title = document.querySelector('.product-title')?.textContent || 'Check out this product!';

        shareButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                const platform = btn.dataset.platform;

                switch (platform) {
                    case 'facebook':
                        window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`, '_blank');
                        break;
                    case 'twitter':
                        window.open(`https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}`, '_blank');
                        break;
                    case 'pinterest':
                        const image = document.querySelector('.main-image')?.src || '';
                        window.open(`https://pinterest.com/pin/create/button/?url=${encodeURIComponent(url)}&media=${encodeURIComponent(image)}&description=${encodeURIComponent(title)}`, '_blank');
                        break;
                    case 'whatsapp':
                        window.open(`https://wa.me/?text=${encodeURIComponent(title + ' ' + url)}`, '_blank');
                        break;
                    case 'copy':
                        navigator.clipboard.writeText(url).then(() => {
                            Toast.success('Link copied to clipboard! 📋');
                            btn.innerHTML = '✓';
                            setTimeout(() => {
                                btn.innerHTML = '🔗';
                            }, 2000);
                        });
                        break;
                }
            });
        });
    }
};

// ============================================
// TABS
// ============================================
const ProductTabs = {
    init() {
        const tabButtons = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');

        if (!tabButtons.length) return;

        tabButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                const targetTab = btn.dataset.tab;

                // Remove active from all
                tabButtons.forEach(b => b.classList.remove('active'));
                tabContents.forEach(c => c.classList.remove('active'));

                // Add active to clicked
                btn.classList.add('active');
                document.getElementById(targetTab)?.classList.add('active');
            });
        });
    }
};

// ============================================
// RELATED PRODUCTS CAROUSEL
// ============================================
const RelatedCarousel = {
    init() {
        const track = document.querySelector('.carousel-track');
        const prevBtn = document.querySelector('.carousel-btn.prev');
        const nextBtn = document.querySelector('.carousel-btn.next');

        if (!track) return;

        const scrollAmount = 350;

        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                track.scrollBy({
                    left: -scrollAmount,
                    behavior: 'smooth'
                });
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                track.scrollBy({
                    left: scrollAmount,
                    behavior: 'smooth'
                });
            });
        }
    }
};

// ============================================
// STAR RATING (for review form)
// ============================================
const StarRating = {
    init() {
        const ratingInputs = document.querySelectorAll('.rating-input input[type="radio"]');
        const stars = document.querySelectorAll('.rating-star');

        if (!stars.length) return;

        stars.forEach((star, index) => {
            star.addEventListener('click', () => {
                // Set the radio input
                if (ratingInputs[index]) {
                    ratingInputs[index].checked = true;
                }

                // Update visual stars
                stars.forEach((s, i) => {
                    if (i <= index) {
                        s.textContent = '★';
                        s.style.color = 'var(--accent-500)';
                    } else {
                        s.textContent = '☆';
                        s.style.color = 'var(--gray-300)';
                    }
                });
            });
        });
    }
};

// ============================================
// INITIALIZATION
// ============================================
document.addEventListener('DOMContentLoaded', () => {
    ProductGallery.init();
    QuantitySelector.init();
    AddToCart.init();
    BuyNow.init();
    WishlistToggle.init();
    ShareProduct.init();
    ProductTabs.init();
    RelatedCarousel.init();
    StarRating.init();

    // Animate elements on load
    const elements = document.querySelectorAll('.product-info > *');
    elements.forEach((el, index) => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        setTimeout(() => {
            el.style.transition = 'all 0.5s ease';
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        }, index * 100);
    });
});

// Export for use in other scripts
window.ProductPage = {
    ProductGallery,
    QuantitySelector,
    AddToCart,
    BuyNow,
    WishlistToggle,
    ShareProduct,
    ProductTabs,
    RelatedCarousel,
    StarRating
};
