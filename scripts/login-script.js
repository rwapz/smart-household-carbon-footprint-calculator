/**
 * Project: Smart Household
 * File: scripts/login-script.js
 * Description: Manages form toggling and error messages for Auth.
 */

const ERROR_MESSAGES = {
    empty:    'Please fill in all fields.',
    invalid:  'Incorrect username or password.',
    db:       'Database not available yet. Try again later.',
    email:    'Please enter a valid email address.',
    shortpass:'Password must be at least 6 characters.',
    taken:    'That username or email is already registered.'
};

document.addEventListener('DOMContentLoaded', () => {
    const loginTab   = document.getElementById('loginTab');
    const signupTab  = document.getElementById('signupTab');
    const loginForm  = document.getElementById('loginForm');
    const signupForm = document.getElementById('signupForm');

    if (loginTab && signupTab && loginForm && signupForm) {

        // Tab switching
        signupTab.addEventListener('click', () => {
            loginForm.style.display  = 'none';
            signupForm.style.display = 'flex';
            signupTab.classList.add('active');
            loginTab.classList.remove('active');
        });

        loginTab.addEventListener('click', () => {
            signupForm.style.display = 'none';
            loginForm.style.display  = 'flex';
            loginTab.classList.add('active');
            signupTab.classList.remove('active');
        });

        // Read URL params — show correct tab and error on redirect from PHP
        const params    = new URLSearchParams(window.location.search);
        const errorCode = params.get('error');
        const tab       = params.get('tab');

        if (tab === 'signup') signupTab.click();

        if (errorCode && ERROR_MESSAGES[errorCode]) {
            const banner = document.createElement('div');
            banner.style.cssText = `
                background:#fef2f2;color:#991b1b;border:1px solid #fecaca;
                padding:10px 16px;border-radius:8px;font-size:0.85rem;
                font-weight:600;margin-bottom:12px;text-align:center;
            `;
            banner.textContent = ERROR_MESSAGES[errorCode];
            const activeForm = tab === 'signup' ? signupForm : loginForm;
            activeForm.prepend(banner);
        }

    } else {
        console.log("Smart Household: Script active, Auth UI not present on this page.");
    }
});
