/**
 * Project: Smart Household
 * File: scripts/login-script.js
 */

document.addEventListener('DOMContentLoaded', () => {
    const loginTab = document.getElementById('loginTab');
    const signupTab = document.getElementById('signupTab');
    const loginForm = document.getElementById('loginForm');
    const signupForm = document.getElementById('signupForm');

    if (loginTab && signupTab && loginForm && signupForm) {
        
        signupTab.addEventListener('click', () => {
            console.log("Navigating to Signup...");
            loginForm.style.display = 'none';
            signupForm.style.display = 'flex';
            signupTab.classList.add('active');
            loginTab.classList.remove('active');
        });

        loginTab.addEventListener('click', () => {
            console.log("Navigating to Login...");
            signupForm.style.display = 'none';
            loginForm.style.display = 'flex';
            loginTab.classList.add('active');
            signupTab.classList.remove('active');
        });

    } else {
        console.error("Smart Household: UI components missing from DOM.");
    }
});