/**
 * Mobile Menu
 * Responsive hamburger navigation with touch gestures
 */

const MobileMenu = {
    isOpen: false,
    menuElement: null,
    backdropElement: null,
    toggleButton: null,

    init() {
        this.createMenu();
        this.createToggleButton();
        this.setupEventListeners();
    },

    createMenu() {
        // Create backdrop
        this.backdropElement = document.createElement('div');
        this.backdropElement.className = 'mobile-menu-backdrop';
        this.backdropElement.style.cssText = `
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(8px);
            z-index: var(--z-modal-backdrop);
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        `;

        // Create menu
        this.menuElement = document.createElement('div');
        this.menuElement.className = 'mobile-menu';
        this.menuElement.style.cssText = `
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            width: 280px;
            max-width: 80vw;
            background: linear-gradient(135deg, var(--gray-900) 0%, var(--gray-800) 100%);
            z-index: var(--z-modal);
            transform: translateX(100%);
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            padding: 2rem;
            overflow-y: auto;
            box-shadow: -10px 0 40px rgba(0, 0, 0, 0.3);
        `;

        // Get navigation links
        const nav = document.querySelector('header nav');
        if (nav) {
            const links = Array.from(nav.querySelectorAll('a'));
            const menuContent = `
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                    <h3 style="color: white; font-size: 1.5rem; font-weight: 800;">Menu</h3>
                    <button class="mobile-menu-close" style="background: rgba(255,255,255,0.1); border: none; color: white; width: 40px; height: 40px; border-radius: 50%; cursor: pointer; font-size: 1.5rem; display: flex; align-items: center; justify-content: center;">
                        ×
                    </button>
                </div>
                <nav style="display: flex; flex-direction: column; gap: 0.5rem;">
                    ${links.map(link => `
                        <a href="${link.href}" style="
                            color: white;
                            text-decoration: none;
                            padding: 1rem;
                            border-radius: 12px;
                            background: rgba(255, 255, 255, 0.05);
                            transition: all 0.3s ease;
                            font-weight: 600;
                            display: block;
                        " onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'">
                            ${link.textContent}
                        </a>
                    `).join('')}
                </nav>
            `;
            this.menuElement.innerHTML = menuContent;
        }

        document.body.appendChild(this.backdropElement);
        document.body.appendChild(this.menuElement);

        // Setup close button
        const closeBtn = this.menuElement.querySelector('.mobile-menu-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => this.close());
        }
    },

    createToggleButton() {
        this.toggleButton = document.createElement('button');
        this.toggleButton.className = 'mobile-menu-toggle';
        this.toggleButton.innerHTML = `
            <span></span>
            <span></span>
            <span></span>
        `;
        this.toggleButton.style.cssText = `
            display: none;
            flex-direction: column;
            gap: 5px;
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px;
            position: relative;
            z-index: ${parseInt(getComputedStyle(document.documentElement).getPropertyValue('--z-modal')) + 10};
        `;

        // Style hamburger lines
        this.toggleButton.querySelectorAll('span').forEach(span => {
            span.style.cssText = `
                display: block;
                width: 25px;
                height: 3px;
                background: white;
                border-radius: 2px;
                transition: all 0.3s ease;
            `;
        });

        // Add to header
        const header = document.querySelector('header');
        if (header) {
            const nav = header.querySelector('nav');
            if (nav) {
                nav.appendChild(this.toggleButton);
            }
        }

        // Show on mobile
        const mediaQuery = window.matchMedia('(max-width: 768px)');
        const handleMediaChange = (e) => {
            this.toggleButton.style.display = e.matches ? 'flex' : 'none';
        };
        mediaQuery.addListener(handleMediaChange);
        handleMediaChange(mediaQuery);
    },

    setupEventListeners() {
        // Toggle button
        this.toggleButton.addEventListener('click', () => {
            if (this.isOpen) {
                this.close();
            } else {
                this.open();
            }
        });

        // Backdrop click
        this.backdropElement.addEventListener('click', () => this.close());

        // Touch gestures
        let touchStartX = 0;
        let touchEndX = 0;

        this.menuElement.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });

        this.menuElement.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            if (touchEndX - touchStartX > 50) {
                this.close();
            }
        }, { passive: true });
    },

    open() {
        this.isOpen = true;
        this.backdropElement.style.opacity = '1';
        this.backdropElement.style.pointerEvents = 'all';
        this.menuElement.style.transform = 'translateX(0)';
        document.body.style.overflow = 'hidden';

        // Animate hamburger to X
        const spans = this.toggleButton.querySelectorAll('span');
        spans[0].style.transform = 'rotate(45deg) translateY(8px)';
        spans[1].style.opacity = '0';
        spans[2].style.transform = 'rotate(-45deg) translateY(-8px)';
    },

    close() {
        this.isOpen = false;
        this.backdropElement.style.opacity = '0';
        this.backdropElement.style.pointerEvents = 'none';
        this.menuElement.style.transform = 'translateX(100%)';
        document.body.style.overflow = '';

        // Animate X back to hamburger
        const spans = this.toggleButton.querySelectorAll('span');
        spans[0].style.transform = '';
        spans[1].style.opacity = '1';
        spans[2].style.transform = '';
    }
};

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    MobileMenu.init();
});

// Export
window.MobileMenu = MobileMenu;
