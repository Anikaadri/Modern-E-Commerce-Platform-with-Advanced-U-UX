/**
 * Profile Page JavaScript
 * Tab switching and AJAX form handling
 */

document.addEventListener('DOMContentLoaded', () => {
    initTabs();
    initForms();
});

// Tab Switching
function initTabs() {
    const tabs = document.querySelectorAll('.tab-btn');
    const panels = document.querySelectorAll('.tab-panel');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            // Remove active class
            tabs.forEach(t => t.classList.remove('active'));
            panels.forEach(p => p.classList.remove('active'));

            // Add active class
            tab.classList.add('active');
            const targetId = tab.dataset.tab;
            document.getElementById(targetId).classList.add('active');

            // Update URL hash without scrolling
            history.replaceState(null, null, `#${targetId}`);
        });
    });

    // Check hash on load
    const hash = window.location.hash.substring(1);
    if (hash) {
        const targetTab = document.querySelector(`.tab-btn[data-tab="${hash}"]`);
        if (targetTab) targetTab.click();
    }
}

// Form Handling
function initForms() {
    const profileForm = document.getElementById('profile-form');
    const passwordForm = document.getElementById('password-form');

    if (profileForm) {
        profileForm.addEventListener('submit', (e) => handleFormSubmit(e, profileForm));
    }

    if (passwordForm) {
        passwordForm.addEventListener('submit', (e) => handleFormSubmit(e, passwordForm));
    }
}

async function handleFormSubmit(e, form) {
    e.preventDefault();

    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;

    // Validate password match if applicable
    if (form.id === 'password-form') {
        const newPass = form.querySelector('input[name="new_password"]').value;
        const confirmPass = form.querySelector('input[name="confirm_password"]').value;

        if (newPass !== confirmPass) {
            Toast.error('New passwords do not match');
            return;
        }
    }

    // Show loading state
    submitBtn.textContent = 'Saving...';
    submitBtn.disabled = true;
    submitBtn.classList.add('btn-loading');

    try {
        const formData = new FormData(form);

        const response = await fetch('profile.php', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            Toast.success(data.message);
            if (form.id === 'password-form') {
                form.reset();
            }
        } else {
            Toast.error(data.message || 'An error occurred');
        }
    } catch (error) {
        console.error('Error:', error);
        Toast.error('Connection error. Please try again.');
    } finally {
        // Reset button state
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
        submitBtn.classList.remove('btn-loading');
    }
}

// View Order Details (Placeholder)
function viewOrder(orderId) {
    // This could open a modal or redirect to order details page
    Toast.info(`Viewing order #${orderId} details...`);
    // window.location.href = `order.php?id=${orderId}`;
}
