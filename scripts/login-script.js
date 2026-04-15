/**
 * Project: Smart Household
 * File: scripts/login-script.js
 * Description: Manages form toggling and error messages for Auth.
 */

// ============ UTILITY FUNCTIONS ============

function getCookie(name) {
    const nameEQ = name + "=";
    const cookies = document.cookie.split(';');
    for (let i = 0; i < cookies.length; i++) {
        let cookie = cookies[i].trim();
        if (cookie.indexOf(nameEQ) === 0) {
            return decodeURIComponent(cookie.substring(nameEQ.length));
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
    form.querySelectorAll('.error-banner').forEach(banner => banner.remove());
}

function addErrorToField(input) {
    if (input) input.classList.add('input-error');
}

function removeErrorFromField(input) {
    if (input) input.classList.remove('input-error');
}

// ============ ERROR MESSAGES ============

const ERROR_MESSAGES = {
    empty:    'Please fill in all fields.',
    invalid:  'Incorrect username or password.',
    db:       'Database not available yet. Try again later.',
    email:    'Please enter a valid email address.',
    shortpass:'Password must be at least 6 characters.',
    taken:    'That username or email is already registered.',
    household: 'Please select a household.',
    password_mismatch: 'Passwords do not match.',
    terms_required: 'Please agree to the Terms & Conditions.',
    unauthorized: 'You must sign in to use this application.'
};

// ============ PASSWORD FUNCTIONS ============

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

    const password = passwordInput.value || '';
    const strength = calculatePasswordStrength(password);
    
    const widths = [0, 17, 33, 50, 67, 83, 100];
    const labels = ['Weak', 'Weak', 'Fair', 'Good', 'Strong', 'Very Strong', 'Very Strong'];
    const colors = ['#ef4444', '#ef4444', '#f97316', '#eab308', '#84cc16', '#2b8ad9', '#2b8ad9'];
    
    strengthFill.style.width = widths[strength] + '%';
    strengthFill.style.backgroundColor = colors[strength];
    strengthLabel.textContent = labels[strength];
    strengthLabel.style.color = colors[strength];
}

function togglePasswordVisibility(button) {
    if (!button) return;
    
    const wrapper = button.closest('.password-input-wrapper');
    if (!wrapper) return;
    
    const input = wrapper.querySelector('input');
    if (!input) return;
    
    if (input.type === 'password') {
        input.type = 'text';
        button.textContent = '👁️‍🗨️';
    } else {
        input.type = 'password';
        button.textContent = '👁️';
    }
}

// ============ FORM INITIALIZATION ============

document.addEventListener('DOMContentLoaded', () => {
    const loginTab   = document.getElementById('loginTab');
    const signupTab  = document.getElementById('signupTab');
    const loginForm  = document.getElementById('loginForm');
    const signupForm = document.getElementById('signupForm');

    // Exit early if we are not on the Auth page
    if (!loginTab || !signupTab || !loginForm || !signupForm) {
        console.log("Smart Household: Auth UI not present on this page.");
        return;
    }

    console.log("✓ Auth forms initialized");

    // ============ PASSWORD TOGGLE SETUP ============
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            togglePasswordVisibility(button);
        });
    });
    console.log("✓ Password toggles ready");

    // ============ PASSWORD STRENGTH SETUP ============
    const signupPass = signupForm.querySelector('input[name="PASSWORD"]');
    if (signupPass) {
        signupPass.addEventListener('input', () => {
            updatePasswordStrength(signupPass);
        });
    }

    // ============ TAB SWITCHING ============
    signupTab.addEventListener('click', (e) => {
        e.preventDefault();
        loginForm.style.display  = 'none';
        signupForm.style.display = 'flex';
        signupTab.classList.add('active');
        loginTab.classList.remove('active');
        removeErrorBanners(loginForm);
    });

    loginTab.addEventListener('click', (e) => {
        e.preventDefault();
        signupForm.style.display = 'none';
        loginForm.style.display  = 'flex';
        loginTab.classList.add('active');
        signupTab.classList.remove('active');
        removeErrorBanners(signupForm);
    });

    // ============ ERROR HANDLING FROM URL ============
    const params    = new URLSearchParams(window.location.search);
    const errorCode = params.get('error');
    const tab       = params.get('tab');

    if (tab === 'signup') signupTab.click();

    if (errorCode && ERROR_MESSAGES[errorCode]) {
        const activeForm = tab === 'signup' ? signupForm : loginForm;
        const errorBanner = createErrorBanner(ERROR_MESSAGES[errorCode]);
        activeForm.prepend(errorBanner);
        
        // Highlight relevant fields
        if (errorCode === 'empty') {
            activeForm.querySelectorAll('input[type="text"], input[type="password"]').forEach(input => {
                if (input.name && !['REMEMBER_ME', 'TERMS_AGREED', 'HOUSEHOLD_ID'].includes(input.name)) {
                    if (!input.value.trim()) addErrorToField(input);
                }
            });
        } else if (errorCode === 'invalid') {
            activeForm.querySelectorAll('input[type="password"]').forEach(input => {
                addErrorToField(input);
            });
        } else if (errorCode === 'shortpass') {
            addErrorToField(activeForm.querySelector('input[name="PASSWORD"]'));
        } else if (errorCode === 'taken') {
            addErrorToField(activeForm.querySelector('input[name="USERNAME"]'));
        } else if (errorCode === 'password_mismatch') {
            addErrorToField(activeForm.querySelector('input[name="PASSWORD"]'));
            addErrorToField(activeForm.querySelector('input[name="PASSWORD_CONFIRM"]'));
        }
    }

    // ============ LOGIN FORM ============
    const loginUser = loginForm.querySelector('input[name="USERNAME"]');
    const loginPass = loginForm.querySelector('input[name="PASSWORD"]');
    const rememberMe = loginForm.querySelector('input[name="REMEMBER_ME"]');

    // Restore remembered username
    const rememberedUsername = getCookie('remembered_username');
    if (rememberedUsername && loginUser) {
        loginUser.value = rememberedUsername;
        if (rememberMe) rememberMe.checked = true;
    }

    // Real-time validation
    [loginUser, loginPass].forEach(input => {
        if (!input) return;
        
        input.addEventListener('input', () => {
            removeErrorBanners(loginForm);
            if (input.value.trim()) {
                removeErrorFromField(input);
            }
        });

        input.addEventListener('focus', () => {
            removeErrorFromField(input);
        });
    });

    // Login submit
    loginForm.addEventListener('submit', (e) => {
        removeErrorBanners(loginForm);
        let hasError = false;

        if (!loginUser || !loginUser.value.trim()) {
            addErrorToField(loginUser);
            hasError = true;
        }
        if (!loginPass || !loginPass.value.trim()) {
            addErrorToField(loginPass);
            hasError = true;
        }

        if (hasError) {
            e.preventDefault();
            const errorBanner = createErrorBanner(ERROR_MESSAGES.empty);
            loginForm.prepend(errorBanner);
        }
    });

    // ============ SIGNUP FORM ============
    const signupUser = signupForm.querySelector('input[name="USERNAME"]');
    const signupPassword = signupForm.querySelector('input[name="PASSWORD"]');
    const signupPasswordConfirm = signupForm.querySelector('input[name="PASSWORD_CONFIRM"]');

    // Real-time validation
    [signupUser, signupPassword, signupPasswordConfirm].forEach(input => {
        if (!input) return;
        
        input.addEventListener('input', () => {
            removeErrorBanners(signupForm);
            if (input.value.trim()) {
                removeErrorFromField(input);
                
                // Check password match in real-time
                if (signupPassword && signupPasswordConfirm && 
                    signupPassword.value && signupPasswordConfirm.value) {
                    if (signupPassword.value === signupPasswordConfirm.value) {
                        removeErrorFromField(signupPassword);
                        removeErrorFromField(signupPasswordConfirm);
                    }
                }
            }
        });

        input.addEventListener('focus', () => {
            removeErrorFromField(input);
        });
    });

    // Signup submit
    signupForm.addEventListener('submit', (e) => {
        removeErrorBanners(signupForm);
        let hasError = false;
        let firstError = null;

        if (!signupUser || !signupUser.value.trim()) {
            addErrorToField(signupUser);
            hasError = true;
            if (!firstError) firstError = 'empty';
        }
        if (!signupPassword || !signupPassword.value.trim()) {
            addErrorToField(signupPassword);
            hasError = true;
            if (!firstError) firstError = 'empty';
        }
        if (!signupPasswordConfirm || !signupPasswordConfirm.value.trim()) {
            addErrorToField(signupPasswordConfirm);
            hasError = true;
            if (!firstError) firstError = 'empty';
        }

        if (signupPassword && signupPasswordConfirm && 
            signupPassword.value && signupPasswordConfirm.value && 
            signupPassword.value !== signupPasswordConfirm.value) {
            addErrorToField(signupPassword);
            addErrorToField(signupPasswordConfirm);
            hasError = true;
            firstError = 'password_mismatch';
        }

        const termsCheckbox = signupForm.querySelector('input[name="TERMS_AGREED"]');
        if (!termsCheckbox || !termsCheckbox.checked) {
            hasError = true;
            if (!firstError) firstError = 'terms_required';
        }

        const householdRadios = signupForm.querySelectorAll('input[name="HOUSEHOLD_ID"]');
        const householdSelected = Array.from(householdRadios).some(radio => radio.checked);
        if (!householdSelected) {
            hasError = true;
            if (!firstError) firstError = 'household';
        }

        if (hasError) {
            e.preventDefault();
            if (firstError && ERROR_MESSAGES[firstError]) {
                const errorBanner = createErrorBanner(ERROR_MESSAGES[firstError]);
                signupForm.prepend(errorBanner);
            }
        }
    });

    console.log("✓ All forms ready");
});
