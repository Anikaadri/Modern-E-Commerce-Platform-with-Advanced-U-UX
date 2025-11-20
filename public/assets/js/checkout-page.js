/**
 * Checkout Page JavaScript
 * Multi-step form, validation, payment processing
 */

// ============================================
// CHECKOUT MANAGER
// ============================================
const CheckoutManager = {
    currentStep: 1,
    totalSteps: 3,
    formData: {},

    init() {
        this.attachEventListeners();
        this.updateStepDisplay();
    },

    attachEventListeners() {
        // Navigation buttons
        const nextBtns = document.querySelectorAll('.btn-next');
        const backBtns = document.querySelectorAll('.btn-back');
        const submitBtn = document.querySelector('.btn-submit');

        nextBtns.forEach(btn => {
            btn.addEventListener('click', () => this.nextStep());
        });

        backBtns.forEach(btn => {
            btn.addEventListener('click', () => this.prevStep());
        });

        if (submitBtn) {
            submitBtn.addEventListener('click', (e) => this.submitOrder(e));
        }

        // Payment method selection
        document.querySelectorAll('.payment-method').forEach(method => {
            method.addEventListener('click', () => this.selectPaymentMethod(method));
        });

        // Real-time validation
        document.querySelectorAll('.form-input').forEach(input => {
            input.addEventListener('blur', () => this.validateField(input));
        });
    },

    nextStep() {
        if (this.validateCurrentStep()) {
            this.saveStepData();
            this.currentStep++;
            this.updateStepDisplay();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    },

    prevStep() {
        this.currentStep--;
        this.updateStepDisplay();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    },

    updateStepDisplay() {
        // Update progress bar
        const stepsContainer = document.querySelector('.checkout-steps');
        if (stepsContainer) {
            stepsContainer.setAttribute('data-step', this.currentStep);
        }

        // Update step items
        document.querySelectorAll('.step-item').forEach((item, index) => {
            const stepNum = index + 1;
            item.classList.remove('active', 'completed');

            if (stepNum < this.currentStep) {
                item.classList.add('completed');
            } else if (stepNum === this.currentStep) {
                item.classList.add('active');
            }
        });

        // Show/hide form steps
        document.querySelectorAll('.form-step').forEach((step, index) => {
            step.classList.remove('active');
            if (index + 1 === this.currentStep) {
                step.classList.add('active');
            }
        });
    },

    validateCurrentStep() {
        const currentStepElement = document.querySelector(`.form-step:nth-child(${this.currentStep})`);
        if (!currentStepElement) return true;

        const inputs = currentStepElement.querySelectorAll('.form-input[required]');
        let isValid = true;

        inputs.forEach(input => {
            if (!this.validateField(input)) {
                isValid = false;
            }
        });

        if (!isValid) {
            Toast.error('Please fill in all required fields');
        }

        return isValid;
    },

    validateField(input) {
        const value = input.value.trim();
        const type = input.type;
        let isValid = true;
        let errorMessage = '';

        // Required check
        if (input.hasAttribute('required') && !value) {
            isValid = false;
            errorMessage = 'This field is required';
        }

        // Email validation
        else if (type === 'email' && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                isValid = false;
                errorMessage = 'Please enter a valid email';
            }
        }

        // Phone validation
        else if (input.name === 'phone' && value) {
            const phoneRegex = /^\d{10,}$/;
            if (!phoneRegex.test(value.replace(/\D/g, ''))) {
                isValid = false;
                errorMessage = 'Please enter a valid phone number';
            }
        }

        // Card number validation
        else if (input.classList.contains('card-number-input') && value) {
            const cardRegex = /^\d{13,19}$/;
            if (!cardRegex.test(value.replace(/\s/g, ''))) {
                isValid = false;
                errorMessage = 'Please enter a valid card number';
            }
        }

        // CVV validation
        else if (input.name === 'cvv' && value) {
            const cvvRegex = /^\d{3,4}$/;
            if (!cvvRegex.test(value)) {
                isValid = false;
                errorMessage = 'Please enter a valid CVV';
            }
        }

        // Update UI
        const errorElement = input.nextElementSibling;
        if (isValid) {
            input.classList.remove('error');
            if (errorElement && errorElement.classList.contains('error-message')) {
                errorElement.classList.remove('show');
            }
        } else {
            input.classList.add('error');
            if (errorElement && errorElement.classList.contains('error-message')) {
                errorElement.textContent = errorMessage;
                errorElement.classList.add('show');
            }
        }

        return isValid;
    },

    saveStepData() {
        const currentStepElement = document.querySelector(`.form-step:nth-child(${this.currentStep})`);
        if (!currentStepElement) return;

        const inputs = currentStepElement.querySelectorAll('.form-input');
        inputs.forEach(input => {
            this.formData[input.name] = input.value;
        });
    },

    selectPaymentMethod(methodElement) {
        // Remove selected from all
        document.querySelectorAll('.payment-method').forEach(m => {
            m.classList.remove('selected');
        });

        // Add selected to clicked
        methodElement.classList.add('selected');

        // Check radio button
        const radio = methodElement.querySelector('.payment-radio');
        if (radio) {
            radio.checked = true;
        }

        // Show/hide card input
        const cardInput = document.querySelector('.card-input-group');
        if (cardInput) {
            if (methodElement.dataset.method === 'card') {
                cardInput.classList.add('active');
            } else {
                cardInput.classList.remove('active');
            }
        }
    },

    async submitOrder(e) {
        e.preventDefault();

        if (!this.validateCurrentStep()) {
            return;
        }

        this.saveStepData();

        // Show loading
        const submitBtn = e.currentTarget;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Processing...';

        // Simulate order processing
        await new Promise(resolve => setTimeout(resolve, 2000));

        // Show success modal
        this.showSuccessModal();
    },

    showSuccessModal() {
        const modal = document.querySelector('.success-modal');
        if (modal) {
            modal.classList.add('active');

            // Auto redirect after 5 seconds
            setTimeout(() => {
                window.location.href = 'index.php';
            }, 5000);
        }
    }
};

// ============================================
// FORM AUTO-FORMATTING
// ============================================
const FormFormatting = {
    init() {
        this.setupCardNumberFormatting();
        this.setupExpiryFormatting();
        this.setupPhoneFormatting();
    },

    setupCardNumberFormatting() {
        const cardInput = document.querySelector('.card-number-input');
        if (!cardInput) return;

        cardInput.addEventListener('input', (e) => {
            let value = e.target.value.replace(/\s/g, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
            e.target.value = formattedValue;
        });
    },

    setupExpiryFormatting() {
        const expiryInput = document.querySelector('input[name="expiry"]');
        if (!expiryInput) return;

        expiryInput.addEventListener('input', (e) => {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.slice(0, 2) + '/' + value.slice(2, 4);
            }
            e.target.value = value;
        });
    },

    setupPhoneFormatting() {
        const phoneInput = document.querySelector('input[name="phone"]');
        if (!phoneInput) return;

        phoneInput.addEventListener('input', (e) => {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 10) {
                value = value.slice(0, 10);
            }
            e.target.value = value;
        });
    }
};

// ============================================
// INITIALIZATION
// ============================================
document.addEventListener('DOMContentLoaded', () => {
    CheckoutManager.init();
    FormFormatting.init();

    // Animate form on load
    const form = document.querySelector('.checkout-form');
    if (form) {
        form.style.opacity = '0';
        form.style.transform = 'translateY(20px)';
        setTimeout(() => {
            form.style.transition = 'all 0.5s ease';
            form.style.opacity = '1';
            form.style.transform = 'translateY(0)';
        }, 100);
    }
});

// Export for use in other scripts
window.CheckoutPage = {
    CheckoutManager,
    FormFormatting
};
