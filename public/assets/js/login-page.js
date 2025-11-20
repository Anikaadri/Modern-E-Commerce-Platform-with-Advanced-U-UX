/**
 * Login/Register Page JavaScript
 * Form toggle, validation, password features, and AJAX submission
 */

// ============================================
// LOGIN MANAGER
// ============================================
const LoginManager = {
    init() {
        this.attachEventListeners();
        this.initPasswordFeatures();
    },

    attachEventListeners() {
        // Form toggle
        document.querySelectorAll('.toggle-btn').forEach(btn => {
            btn.addEventListener('click', () => this.toggleForm(btn));
        });

        // Form submissions
        const loginForm = document.querySelector('.login-form');
        const registerForm = document.querySelector('.register-form');

        if (loginForm) {
            loginForm.addEventListener('submit', (e) => this.handleLogin(e));
        }

        if (registerForm) {
            registerForm.addEventListener('submit', (e) => this.handleRegister(e));
        }

        // Real-time validation
        document.querySelectorAll('.form-input-login').forEach(input => {
            input.addEventListener('blur', () => this.validateField(input));
        });
    },

    toggleForm(btn) {
        const formType = btn.dataset.form;

        // Update toggle buttons
        document.querySelectorAll('.toggle-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        // Show/hide forms
        document.querySelectorAll('.login-form, .register-form').forEach(form => {
            form.classList.remove('active');
        });

        document.querySelector(`.${formType}-form`).classList.add('active');
    },

    initPasswordFeatures() {
        // Password toggle
        document.querySelectorAll('.password-toggle').forEach(toggle => {
            toggle.addEventListener('click', () => this.togglePasswordVisibility(toggle));
        });

        // Password strength
        const passwordInput = document.querySelector('input[name="register_password"]');
        if (passwordInput) {
            passwordInput.addEventListener('input', () => this.checkPasswordStrength(passwordInput));
        }
    },

    togglePasswordVisibility(toggle) {
        const input = toggle.previousElementSibling;
        const type = input.type === 'password' ? 'text' : 'password';
        input.type = type;
        toggle.textContent = type === 'password' ? '👁️' : '🙈';
    },

    checkPasswordStrength(input) {
        const password = input.value;
        const strengthIndicator = document.querySelector('.password-strength');
        const strengthFill = document.querySelector('.strength-fill');
        const strengthText = document.querySelector('.strength-text');

        if (!strengthIndicator) return;

        if (password.length === 0) {
            strengthIndicator.classList.remove('show');
            return;
        }

        strengthIndicator.classList.add('show');

        let strength = 0;
        if (password.length >= 8) strength++;
        if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
        if (/\d/.test(password)) strength++;
        if (/[^a-zA-Z\d]/.test(password)) strength++;

        strengthFill.className = 'strength-fill';
        strengthText.className = 'strength-text';

        if (strength <= 1) {
            strengthFill.classList.add('weak');
            strengthText.classList.add('weak');
            strengthText.textContent = 'Weak password';
        } else if (strength <= 3) {
            strengthFill.classList.add('medium');
            strengthText.classList.add('medium');
            strengthText.textContent = 'Medium password';
        } else {
            strengthFill.classList.add('strong');
            strengthText.classList.add('strong');
            strengthText.textContent = 'Strong password';
        }
    },

    validateField(input) {
        const value = input.value.trim();
        const type = input.type;
        let isValid = true;

        if (input.hasAttribute('required') && !value) {
            isValid = false;
        } else if (type === 'email' && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            isValid = emailRegex.test(value);
        }

        input.classList.toggle('error', !isValid);
        return isValid;
    },

    async handleLogin(e) {
        e.preventDefault();

        const form = e.currentTarget;
        const submitBtn = form.querySelector('.btn-login');
        const email = form.querySelector('input[name="email"]').value;
        const password = form.querySelector('input[name="password"]').value;

        // Validate
        if (!email || !password) {
            Toast.error('Please fill in all fields');
            return;
        }

        // Show loading
        submitBtn.classList.add('btn-loading');
        submitBtn.disabled = true;

        try {
            const formData = new FormData();
            formData.append('email', email);
            formData.append('password', password);

            const response = await fetch('login.php?mode=login', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                Toast.success(data.message);
                setTimeout(() => {
                    window.location.href = data.redirect || 'index.php';
                }, 1000);
            } else {
                Toast.error(data.message || 'Login failed');
                submitBtn.classList.remove('btn-loading');
                submitBtn.disabled = false;
            }
        } catch (error) {
            console.error('Login error:', error);
            Toast.error('An error occurred. Please try again.');
            submitBtn.classList.remove('btn-loading');
            submitBtn.disabled = false;
        }
    },

    async handleRegister(e) {
        e.preventDefault();

        const form = e.currentTarget;
        const submitBtn = form.querySelector('.btn-register');
        const name = form.querySelector('input[name="name"]').value;
        const email = form.querySelector('input[name="register_email"]').value;
        const password = form.querySelector('input[name="register_password"]').value;
        const confirmPassword = form.querySelector('input[name="confirm_password"]').value;

        // Validate
        if (!name || !email || !password || !confirmPassword) {
            Toast.error('Please fill in all fields');
            return;
        }

        if (password !== confirmPassword) {
            Toast.error('Passwords do not match');
            return;
        }

        // Show loading
        submitBtn.classList.add('btn-loading');
        submitBtn.disabled = true;

        try {
            const formData = new FormData();
            formData.append('name', name);
            formData.append('email', email);
            formData.append('password', password);
            formData.append('confirm_password', confirmPassword);

            const response = await fetch('login.php?mode=register', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                Toast.success(data.message);
                setTimeout(() => {
                    // Switch to login form or redirect
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        // Switch to login tab automatically
                        const loginTab = document.querySelector('.toggle-btn[data-form="login"]');
                        if (loginTab) loginTab.click();
                    }
                }, 1500);
            } else {
                Toast.error(data.message || 'Registration failed');
                submitBtn.classList.remove('btn-loading');
                submitBtn.disabled = false;
            }
        } catch (error) {
            console.error('Registration error:', error);
            Toast.error('An error occurred. Please try again.');
            submitBtn.classList.remove('btn-loading');
            submitBtn.disabled = false;
        }
    }
};

// ============================================
// INITIALIZATION
// ============================================
document.addEventListener('DOMContentLoaded', () => {
    LoginManager.init();

    // Animate card on load
    const card = document.querySelector('.login-card');
    if (card) {
        card.style.opacity = '0';
        card.style.transform = 'scale(0.9)';
        setTimeout(() => {
            card.style.transition = 'all 0.4s ease';
            card.style.opacity = '1';
            card.style.transform = 'scale(1)';
        }, 100);
    }
});

// Export
window.LoginPage = {
    LoginManager
};
