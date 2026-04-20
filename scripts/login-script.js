/**
 * Smart Household - Login Script
 * Handles form toggling, validation, and error messages
 */

function getCookie(name) {
    const cookies = document.cookie.split(';');
    for (let cookie of cookies) {
        cookie = cookie.trim();
        if (cookie.startsWith(name + '=')) {
            return decodeURIComponent(cookie.substring(name.length + 1));
        }
    }
    return '';
}

function createErrorBanner(message) {
    const banner = document.createElement('div');
    banner.className = 'error-banner';
    banner.textContent = message;
    return banner;
}

function removeErrorBanners(form) {
    form.querySelectorAll('.error-banner').forEach(b => b.remove());
}

function addErrorToField(input) {
    if (input) input.classList.add('input-error');
}

function removeErrorFromField(input) {
    if (input) input.classList.remove('input-error');
}

const ERROR_MESSAGES = {
    empty: 'Please fill in all fields.',
    invalid: 'Incorrect username or password.',
    db: 'Database error. Please try again.',
    shortpass: 'Password must be at least 6 characters.',
    taken: 'Username already taken.',
    household: 'Please select a household.',
    password_mismatch: 'Passwords do not match.',
    terms_required: 'Please agree to the Terms & Conditions.'
};

function calculatePasswordStrength(password) {
    let strength = 0;
    if (password.length >= 8) strength++;
    if (password.length >= 12) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^a-zA-Z0-9]/.test(password)) strength++;
    return strength;
}

function updatePasswordStrength(passwordInput) {
    if (!passwordInput) return;
    const form = passwordInput.closest('form');
    if (!form) return;
    
    const strengthFill = form.querySelector('.strength-fill');
    const strengthLabel = form.querySelector('#strength-label');
    if (!strengthFill || !strengthLabel) return;

    const strength = calculatePasswordStrength(passwordInput.value || '');
    const widths = [0, 17, 33, 50, 67, 83, 100];
    const labels = ['Weak', 'Weak', 'Fair', 'Good', 'Strong', 'Very Strong'];
    const colors = ['#ef4444', '#ef4444', '#f97316', '#eab308', '#84cc16', '#2b8ad9'];
    
    strengthFill.style.width = widths[strength] + '%';
    strengthFill.style.backgroundColor = colors[strength];
    strengthLabel.textContent = labels[strength];
    strengthLabel.style.color = colors[strength];
}

function togglePasswordVisibility(button) {
    const wrapper = button.closest('.password-input-wrapper');
    if (!wrapper) return;
    const input = wrapper.querySelector('input');
    if (!input) return;
    
    input.type = input.type === 'password' ? 'text' : 'password';
    button.textContent = input.type === 'password' ? '👁️' : '👁️‍🗨️';
}

document.addEventListener('DOMContentLoaded', function() {
    const loginTab = document.getElementById('loginTab');
    const signupTab = document.getElementById('signupTab');
    const loginForm = document.getElementById('loginForm');
    const signupForm = document.getElementById('signupForm');

    if (!loginTab || !signupTab || !loginForm || !signupForm) return;

    // Password toggles
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            togglePasswordVisibility(this);
        });
    });

    // Password strength
    const signupPass = signupForm.querySelector('input[name="PASSWORD"]');
    if (signupPass) {
        signupPass.addEventListener('input', function() {
            updatePasswordStrength(this);
        });
    }

    // Tab switching
    signupTab.addEventListener('click', function(e) {
        e.preventDefault();
        loginForm.style.display = 'none';
        signupForm.style.display = 'flex';
        signupTab.classList.add('active');
        loginTab.classList.remove('active');
        removeErrorBanners(loginForm);
    });

    loginTab.addEventListener('click', function(e) {
        e.preventDefault();
        signupForm.style.display = 'none';
        loginForm.style.display = 'flex';
        loginTab.classList.add('active');
        signupTab.classList.remove('active');
        removeErrorBanners(signupForm);
    });

    // Handle URL errors
    const params = new URLSearchParams(window.location.search);
    const errorCode = params.get('error');
    const tab = params.get('tab');

    if (tab === 'signup') signupTab.click();

    if (errorCode && ERROR_MESSAGES[errorCode]) {
        const activeForm = tab === 'signup' ? signupForm : loginForm;
        const errorBanner = createErrorBanner(ERROR_MESSAGES[errorCode]);
        activeForm.prepend(errorBanner);
    }

    // Remember username
    const loginUser = loginForm.querySelector('input[name="USERNAME"]');
    const loginPass = loginForm.querySelector('input[name="PASSWORD"]');
    const rememberMe = loginForm.querySelector('input[name="REMEMBER_ME"]');
    const rememberedUsername = getCookie('remembered_username');
    
    if (rememberedUsername && loginUser) {
        loginUser.value = rememberedUsername;
        if (rememberMe) rememberMe.checked = true;
    }

    // Login validation
    [loginUser, loginPass].forEach(input => {
        if (!input) return;
        input.addEventListener('input', function() {
            removeErrorBanners(loginForm);
            removeErrorFromField(this);
        });
    });

    loginForm.addEventListener('submit', function(e) {
        removeErrorBanners(loginForm);
        let hasError = false;

        if (!loginUser?.value.trim()) {
            addErrorToField(loginUser);
            hasError = true;
        }
        if (!loginPass?.value.trim()) {
            addErrorToField(loginPass);
            hasError = true;
        }

        if (hasError) {
            e.preventDefault();
            loginForm.prepend(createErrorBanner(ERROR_MESSAGES.empty));
        }
    });

    // Signup validation
    const signupUser = signupForm.querySelector('input[name="USERNAME"]');
    const signupPassword = signupForm.querySelector('input[name="PASSWORD"]');
    const signupPasswordConfirm = signupForm.querySelector('input[name="PASSWORD_CONFIRM"]');

    [signupUser, signupPassword, signupPasswordConfirm].forEach(input => {
        if (!input) return;
        input.addEventListener('input', function() {
            removeErrorBanners(signupForm);
            removeErrorFromField(this);
        });
    });

    signupForm.addEventListener('submit', function(e) {
        removeErrorBanners(signupForm);
        let hasError = false;
        let firstError = 'empty';

        if (!signupUser?.value.trim()) {
            addErrorToField(signupUser);
            hasError = true;
        }
        if (!signupPassword?.value.trim()) {
            addErrorToField(signupPassword);
            hasError = true;
        }
        if (!signupPasswordConfirm?.value.trim()) {
            addErrorToField(signupPasswordConfirm);
            hasError = true;
        }

        if (signupPassword?.value && signupPasswordConfirm?.value && 
            signupPassword.value !== signupPasswordConfirm.value) {
            addErrorToField(signupPassword);
            addErrorToField(signupPasswordConfirm);
            hasError = true;
            firstError = 'password_mismatch';
        }

        const termsCheckbox = signupForm.querySelector('input[name="TERMS_AGREED"]');
        if (!termsCheckbox?.checked) {
            hasError = true;
            firstError = 'terms_required';
        }

        const householdRadios = signupForm.querySelectorAll('input[name="HOUSEHOLD_ID"]');
        if (![...householdRadios].some(r => r.checked)) {
            hasError = true;
            firstError = 'household';
        }

        if (hasError) {
            e.preventDefault();
            signupForm.prepend(createErrorBanner(ERROR_MESSAGES[firstError] || ERROR_MESSAGES.empty));
        }
    });
});
