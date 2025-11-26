// Async JavaScript for DIM.RIA project
document.addEventListener('DOMContentLoaded', function() {
    console.log('DIM.RIA JavaScript loaded');

    // Initialize features
    initializeAuthCheck();
    initializeFormValidation();
});

// Check authentication status
function initializeAuthCheck() {
    // Check auth status every 5 minutes
    setInterval(checkAuthStatus, 5 * 60 * 1000);

    // Initial check
    checkAuthStatus();
}

async function checkAuthStatus() {
    try {
        const response = await fetch('/auth?action=check');
        const data = await response.json();

        updateAuthUI(data);
    } catch (error) {
        console.error('Auth check failed:', error);
    }
}

function updateAuthUI(authData) {
    const loginLinks = document.querySelectorAll('.login-link');
    const userMenus = document.querySelectorAll('.user-menu');

    if (authData.authenticated) {
        loginLinks.forEach(link => link.style.display = 'none');
        userMenus.forEach(menu => menu.style.display = 'block');
    } else {
        loginLinks.forEach(link => link.style.display = 'block');
        userMenus.forEach(menu => menu.style.display = 'none');
    }
}

// Form validation
function initializeFormValidation() {
    const forms = document.querySelectorAll('form[data-validate]');

    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
            }
        });
    });
}

function validateForm(form) {
    let isValid = true;
    const inputs = form.querySelectorAll('input, textarea, select');

    inputs.forEach(input => {
        if (!validateField(input)) {
            isValid = false;
        }
    });

    return isValid;
}

function validateField(field) {
    const value = field.value.trim();
    const type = field.type;
    let isValid = true;
    let errorMessage = '';

    // Clear previous errors
    clearFieldError(field);

    // Required validation
    if (field.hasAttribute('required') && !value) {
        isValid = false;
        errorMessage = 'Це поле обов\'язкове';
    }

    // Email validation
    if (type === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            isValid = false;
            errorMessage = 'Введіть правильну email адресу';
        }
    }

    if (!isValid) {
        showFieldError(field, errorMessage);
    }

    return isValid;
}

function showFieldError(field, message) {
    field.classList.add('error');

    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error';
    errorDiv.textContent = message;

    field.parentNode.insertBefore(errorDiv, field.nextSibling);
}

function clearFieldError(field) {
    field.classList.remove('error');

    const errorDiv = field.parentNode.querySelector('.field-error');
    if (errorDiv) {
        errorDiv.remove();
    }
}

// Export functions for global use
window.DIMRIA = {
    checkAuthStatus,
    validateForm
};
