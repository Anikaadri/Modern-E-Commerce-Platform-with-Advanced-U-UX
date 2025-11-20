/**
 * Advanced Animations Controller
 * Intersection Observer, 3D Effects, Page Transitions
 */

// ============================================
// INTERSECTION OBSERVER - SCROLL ANIMATIONS
// ============================================
const ScrollAnimations = {
    observer: null,

    init() {
        const options = {
            root: null,
            rootMargin: '0px',
            threshold: 0.1
        };

        this.observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fadeInUp');
                    entry.target.style.opacity = '1';
                }
            });
        }, options);

        // Observe all product cards
        document.querySelectorAll('.product-card').forEach((card, index) => {
            card.style.opacity = '0';
            card.style.animationDelay = `${index * 0.1}s`;
            this.observer.observe(card);
        });

        // Observe sections
        document.querySelectorAll('.categories, .products').forEach(section => {
            section.style.opacity = '0';
            this.observer.observe(section);
        });
    },

    destroy() {
        if (this.observer) {
            this.observer.disconnect();
        }
    }
};

// ============================================
// 3D CARD TILT EFFECT
// ============================================
const CardTilt = {
    init() {
        document.querySelectorAll('.product-card').forEach(card => {
            card.addEventListener('mousemove', this.handleMouseMove.bind(this));
            card.addEventListener('mouseleave', this.handleMouseLeave.bind(this));
        });
    },

    handleMouseMove(e) {
        const card = e.currentTarget;
        const rect = card.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        const centerX = rect.width / 2;
        const centerY = rect.height / 2;

        const rotateX = (y - centerY) / 10;
        const rotateY = (centerX - x) / 10;

        card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-12px)`;
    },

    handleMouseLeave(e) {
        const card = e.currentTarget;
        card.style.transform = '';
    }
};

// ============================================
// PARALLAX SCROLLING
// ============================================
const Parallax = {
    elements: [],

    init() {
        this.elements = document.querySelectorAll('[data-parallax]');
        if (this.elements.length > 0) {
            window.addEventListener('scroll', this.handleScroll.bind(this), { passive: true });
        }
    },

    handleScroll() {
        const scrolled = window.pageYOffset;

        this.elements.forEach(element => {
            const speed = element.dataset.parallax || 0.5;
            const yPos = -(scrolled * speed);
            element.style.transform = `translateY(${yPos}px)`;
        });
    }
};

// ============================================
// BUTTON RIPPLE EFFECT
// ============================================
const RippleEffect = {
    init() {
        document.querySelectorAll('.btn, .product-card .btn').forEach(button => {
            button.addEventListener('click', this.createRipple.bind(this));
        });
    },

    createRipple(e) {
        const button = e.currentTarget;
        const ripple = document.createElement('span');
        const rect = button.getBoundingClientRect();

        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;

        ripple.style.cssText = `
            position: absolute;
            width: ${size}px;
            height: ${size}px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.6);
            left: ${x}px;
            top: ${y}px;
            pointer-events: none;
            transform: scale(0);
            animation: ripple 0.6s ease-out;
        `;

        button.style.position = 'relative';
        button.style.overflow = 'hidden';
        button.appendChild(ripple);

        setTimeout(() => ripple.remove(), 600);
    }
};

// Add ripple animation to CSS
const style = document.createElement('style');
style.textContent = `
    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// ============================================
// LOADING SPINNER
// ============================================
const LoadingSpinner = {
    create() {
        const spinner = document.createElement('div');
        spinner.className = 'loading-spinner';
        spinner.innerHTML = `
            <div style="
                width: 50px;
                height: 50px;
                border: 4px solid var(--gray-200);
                border-top-color: var(--primary-500);
                border-radius: 50%;
                animation: spin 0.8s linear infinite;
            "></div>
        `;
        return spinner;
    },

    show(container) {
        const spinner = this.create();
        spinner.style.cssText = `
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4rem;
        `;
        container.innerHTML = '';
        container.appendChild(spinner);
    }
};

// ============================================
// PAGE TRANSITION
// ============================================
const PageTransition = {
    init() {
        // Add transition overlay
        const overlay = document.createElement('div');
        overlay.id = 'page-transition';
        overlay.style.cssText = `
            position: fixed;
            inset: 0;
            background: linear-gradient(135deg, var(--primary-500) 0%, var(--secondary-500) 100%);
            z-index: 9999;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        `;
        document.body.appendChild(overlay);

        // Intercept link clicks
        document.querySelectorAll('a:not([target="_blank"])').forEach(link => {
            link.addEventListener('click', (e) => {
                const href = link.getAttribute('href');
                if (href && !href.startsWith('#') && !href.startsWith('javascript:')) {
                    e.preventDefault();
                    this.transition(href);
                }
            });
        });
    },

    transition(url) {
        const overlay = document.getElementById('page-transition');
        overlay.style.opacity = '1';

        setTimeout(() => {
            window.location.href = url;
        }, 300);
    }
};

// ============================================
// SMOOTH SCROLL
// ============================================
const SmoothScroll = {
    init() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', (e) => {
                e.preventDefault();
                const target = document.querySelector(anchor.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }
};

// ============================================
// IMAGE LAZY LOADING
// ============================================
const LazyLoad = {
    observer: null,

    init() {
        const options = {
            root: null,
            rootMargin: '50px',
            threshold: 0.01
        };

        this.observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                        this.observer.unobserve(img);
                    }
                }
            });
        }, options);

        document.querySelectorAll('img[data-src]').forEach(img => {
            this.observer.observe(img);
        });
    }
};

// ============================================
// STAGGER ANIMATION
// ============================================
function staggerAnimation(selector, delay = 100) {
    const elements = document.querySelectorAll(selector);
    elements.forEach((element, index) => {
        element.style.animationDelay = `${index * delay}ms`;
        element.classList.add('animate-fadeInUp');
    });
}

// ============================================
// HOVER GLOW EFFECT
// ============================================
const HoverGlow = {
    init() {
        document.querySelectorAll('.product-card').forEach(card => {
            card.addEventListener('mouseenter', (e) => {
                card.style.boxShadow = '0 25px 50px rgba(99, 102, 241, 0.3), 0 0 40px rgba(99, 102, 241, 0.2)';
            });

            card.addEventListener('mouseleave', (e) => {
                card.style.boxShadow = '';
            });
        });
    }
};

// ============================================
// INITIALIZATION
// ============================================
document.addEventListener('DOMContentLoaded', () => {
    // Initialize all animation modules
    ScrollAnimations.init();
    CardTilt.init();
    Parallax.init();
    RippleEffect.init();
    SmoothScroll.init();
    LazyLoad.init();
    HoverGlow.init();

    // Stagger product cards
    setTimeout(() => {
        staggerAnimation('.product-card', 100);
    }, 100);

    // Page entrance animation
    document.body.style.opacity = '0';
    setTimeout(() => {
        document.body.style.transition = 'opacity 0.5s ease';
        document.body.style.opacity = '1';
    }, 50);
});

// Cleanup on page unload
window.addEventListener('beforeunload', () => {
    ScrollAnimations.destroy();
});

// Export for use in other scripts
window.ScrollAnimations = ScrollAnimations;
window.CardTilt = CardTilt;
window.Parallax = Parallax;
window.LoadingSpinner = LoadingSpinner;
window.staggerAnimation = staggerAnimation;
