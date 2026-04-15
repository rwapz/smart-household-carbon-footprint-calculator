/**
 * Project: Smart Household
 * File: scripts/login-script.js
 * Description: Manages form toggling and error messages for Auth.
 */

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

const ERROR_MESSAGES = {
    empty:    'Please fill in all fields.',
    invalid:  'Incorrect username or password.',
    db:       'Database not available yet. Try again later.',
    email:    'Please enter a valid email address.',
    shortpass:'Password must be at least 6 characters.',
    taken:    'That username or email is already registered.',
    household: 'Please select a household.',
    password_mismatch: 'Passwords do not match.',
    terms_required: 'Please agree to the Terms & Conditions.'
};

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
    input.classList.add('input-error');
}

function removeErrorFromField(input) {
    input.classList.remove('input-error');
}

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
    const form = passwordInput.closest('form');
    if (!form) return;
    
    const strengthFill = form.querySelector('.strength-fill');
    const strengthLabel = form.querySelector('#strength-label');
    
    if (!strengthFill || !strengthLabel) return;

    const password = passwordInput.value;
    const strength = calculatePasswordStrength(password);
    
    const widths = [0, 17, 33, 50, 67, 83, 100];
    const labels = ['Weak', 'Weak', 'Fair', 'Good', 'Strong', 'Very Strong', 'Very Strong'];
    const colors = ['#ef4444', '#ef4444', '#f97316', '#eab308', '#84cc16', '#22c55e', '#22c55e'];
    
    strengthFill.style.width = widths[strength] + '%';
    strengthFill.style.backgroundColor = colors[strength];
    strengthLabel.textContent = labels[strength];
    strengthLabel.style.color = colors[strength];
}

function setupPasswordToggles(form) {
    const toggles = form.querySelectorAll('.toggle-password');
    toggles.forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            
            // Find input in the same wrapper
            const wrapper = button.closest('.password-input-wrapper');
            if (wrapper) {
                const input = wrapper.querySelector('input');
                if (input) {
                    const isHidden = input.type === 'password';
                    input.type = isHidden ? 'text' : 'password';
                    button.textContent = isHidden ? '👁️‍🗨️' : '👁️';
                }
            }
        });
    });
}

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

    console.log("✓ Auth forms loaded successfully");

    // Setup password toggles FIRST before anything else
    setupPasswordToggles(loginForm);
    setupPasswordToggles(signupForm);
    console.log("✓ Password toggles setup complete");

    // Setup password strength indicator for signup
    const signupPass = signupForm.querySelector('input[name="PASSWORD"]');
    if (signupPass) {
        signupPass.addEventListener('input', () => {
            updatePasswordStrength(signupPass);
        });
    }

    // Handle Tab Switching
    signupTab.addEventListener('click', () => {
        loginForm.style.display  = 'none';
        signupForm.style.display = 'flex';
        signupTab.classList.add('active');
        loginTab.classList.remove('active');
        removeErrorBanners(loginForm);
    });

    loginTab.addEventListener('click', () => {
        signupForm.style.display = 'none';
        loginForm.style.display  = 'flex';
        loginTab.classList.add('active');
        signupTab.classList.remove('active');
        removeErrorBanners(signupForm);
    });

    // Read URL params — show correct tab and error on redirect from PHP
    const params    = new URLSearchParams(window.location.search);
    const errorCode = params.get('error');
    const tab       = params.get('tab');

    if (tab === 'signup') signupTab.click();

    // Render error message if it exists
    if (errorCode && ERROR_MESSAGES[errorCode]) {
        const activeForm = tab === 'signup' ? signupForm : loginForm;
        const errorBanner = createErrorBanner(ERROR_MESSAGES[errorCode]);
        activeForm.prepend(errorBanner);
        
        // Highlight relevant fields
        if (errorCode === 'empty') {
            activeForm.querySelectorAll('input[type="text"], input[type="password"]').forEach(input => {
                if (input.name !== 'REMEMBER_ME' && input.name !== 'TERMS_AGREED' && input.name !== 'HOUSEHOLD_ID') {
                    if (!input.value.trim()) addErrorToField(input);
                }
            });
        } else if (errorCode === 'invalid') {
            activeForm.querySelectorAll('input[type="text"], input[type="password"]').forEach(input => {
                if (input.name !== 'REMEMBER_ME' && input.name !== 'TERMS_AGREED' && input.name !== 'HOUSEHOLD_ID') {
                    addErrorToField(input);
                }
            });
        } else if (errorCode === 'shortpass') {
            const passInput = activeForm.querySelector('input[name="PASSWORD"]');
            if (passInput) addErrorToField(passInput);
        } else if (errorCode === 'taken') {
            const userInput = activeForm.querySelector('input[name="USERNAME"]');
            if (userInput) addErrorToField(userInput);
        } else if (errorCode === 'password_mismatch') {
            const passInput = activeForm.querySelector('input[name="PASSWORD"]');
            const confirmInput = activeForm.querySelector('input[name="PASSWORD_CONFIRM"]');
            if (passInput) addErrorToField(passInput);
            if (confirmInput) addErrorToField(confirmInput);
        }
    }

    // Real-time validation for login form
    const loginUser = loginForm.querySelector('input[name="USERNAME"]');
    const loginPass = loginForm.querySelector('input[name="PASSWORD"]');
    const rememberMe = loginForm.querySelector('input[name="REMEMBER_ME"]');

    // Check for remembered username cookie
    const rememberedUsername = getCookie('remembered_username');
    if (rememberedUsername) {
        loginUser.value = rememberedUsername;
        if (rememberMe) rememberMe.checked = true;
    }

    [loginUser, loginPass].forEach(input => {
        if (input) {
            input.addEventListener('input', () => {
                removeErrorBanners(loginForm);
                if (input.value.trim()) {
                    removeErrorFromField(input);
                }
            });
            input.addEventListener('focus', () => {
                removeErrorFromField(input);
            });
        }
    });

    // Real-time validation for signup form
    const signupUser = signupForm.querySelector('input[name="USERNAME"]');
    const signupPass = signupForm.querySelector('input[name="PASSWORD"]');
    const signupPassConfirm = signupForm.querySelector('input[name="PASSWORD_CONFIRM"]');

    [signupUser, signupPass, signupPassConfirm].forEach(input => {
        if (input) {
            input.addEventListener('input', () => {
                removeErrorBanners(signupForm);
                if (input.value.trim()) {
                    removeErrorFromField(input);
                }
                // Check password match in real-time
                if (input.name === 'PASSWORD' || input.name === 'PASSWORD_CONFIRM') {
                    if (signupPass.value && signupPassConfirm.value && signupPass.value === signupPassConfirm.value) {
                        removeErrorFromField(signupPass);
                        removeErrorFromField(signupPassConfirm);
                    }
                }
            });
            input.addEventListener('focus', () => {
                removeErrorFromField(input);
            });
        }
    });

    // Login form validation
    loginForm.addEventListener('submit', (e) => {
        removeErrorBanners(loginForm);
        let hasError = false;

        if (!loginUser.value.trim()) {
            addErrorToField(loginUser);
            hasError = true;
        }
        if (!loginPass.value.trim()) {
            addErrorToField(loginPass);
            hasError = true;
        }

        if (hasError) {
            e.preventDefault();
            const errorBanner = createErrorBanner(ERROR_MESSAGES.empty);
            loginForm.prepend(errorBanner);
            return false;
        }
    });

    // Signup form validation
    signupForm.addEventListener('submit', (e) => {
        removeErrorBanners(signupForm);
        let hasError = false;

        if (!signupUser.value.trim()) {
            addErrorToField(signupUser);
            hasError = true;
        }
        if (!signupPass.value.trim()) {
            addErrorToField(signupPass);
            hasError = true;
        }

        if (!signupPassConfirm.value.trim()) {
            addErrorToField(signupPassConfirm);
            hasError = true;
        }

        if (signupPass.value && signupPassConfirm.value && signupPass.value !== signupPassConfirm.value) {
            addErrorToField(signupPass);
            addErrorToField(signupPassConfirm);
            hasError = true;
            const errorBanner = createErrorBanner(ERROR_MESSAGES.password_mismatch);
            signupForm.prepend(errorBanner);
        }

        const termsCheckbox = signupForm.querySelector('input[name="TERMS_AGREED"]');
        if (!termsCheckbox.checked) {
            hasError = true;
        }

        const householdRadios = signupForm.querySelectorAll('input[name="HOUSEHOLD_ID"]');
        const householdSelected = Array.from(householdRadios).some(radio => radio.checked);
        if (!householdSelected) {
            hasError = true;
        }

        if (hasError) {
            e.preventDefault();
            if (!signupUser.value.trim() || !signupPass.value.trim() || !signupPassConfirm.value.trim()) {
                const errorBanner = createErrorBanner(ERROR_MESSAGES.empty);
                signupForm.prepend(errorBanner);
            } else if (!termsCheckbox.checked) {
                const errorBanner = createErrorBanner(ERROR_MESSAGES.terms_required);
                signupForm.prepend(errorBanner);
            } else if (!householdSelected) {
                const errorBanner = createErrorBanner(ERROR_MESSAGES.household);
                signupForm.prepend(errorBanner);
            }
            return false;
        }
    });
});
