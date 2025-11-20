/**
 * Smart Auto-Hide Header
 * Hides when scrolling down, shows when scrolling up
 */

const SmartHeader = {
    lastScrollTop: 0,
    header: null,
    scrollThreshold: 5,

    init() {
        this.header = document.querySelector('header');
        if (!this.header) return;

        // Make header fixed
        this.header.style.position = 'fixed';
        this.header.style.top = '0';
        this.header.style.left = '0';
        this.header.style.right = '0';
        this.header.style.zIndex = '9999';
        this.header.style.transition = 'transform 0.3s ease';

        // Listen to scroll
        window.addEventListener('scroll', () => this.handleScroll(), { passive: true });
    },

    handleScroll() {
        const currentScroll = window.pageYOffset || document.documentElement.scrollTop;

        // Scrolling down - hide header
        if (currentScroll > this.lastScrollTop && currentScroll > 100) {
            this.hideHeader();
        }
        // Scrolling up - show header
        else if (currentScroll < this.lastScrollTop) {
            this.showHeader();
        }

        // At top of page - always show
        if (currentScroll <= 0) {
            this.showHeader();
        }

        this.lastScrollTop = currentScroll <= 0 ? 0 : currentScroll;
    },

    hideHeader() {
        const headerHeight = this.header.offsetHeight;
        this.header.style.transform = `translateY(-${headerHeight}px)`;
    },

    showHeader() {
        this.header.style.transform = 'translateY(0)';
    }
};

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    SmartHeader.init();
});

// Export
window.SmartHeader = SmartHeader;
