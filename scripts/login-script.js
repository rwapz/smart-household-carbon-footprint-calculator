/**
 * Project: Smart Household
 * Script: login-script.js
 * Logic: Toggles between Login and Signup forms using Flexbox
 */

document.addEventListener('DOMContentLoaded', () => {
    // Select the tabs and forms
    const loginTab = document.getElementById('loginTab');
    const signupTab = document.getElementById('signupTab');
    const loginForm = document.getElementById('loginForm');
    const signupForm = document.getElementById('signupForm');

    // Verification check to prevent console errors
    if (loginTab && signupTab && loginForm && signupForm) {
        
        // --- SWITCH TO SIGNUP ---
        signupTab.addEventListener('click', () => {
            console.log("Navigating to Signup...");
            
            // Toggle visibility
            loginForm.style.display = 'none';
            signupForm.style.display = 'flex';
            
            // Toggle active styling
            signupTab.classList.add('active');
            loginTab.classList.remove('active');
        });

        // --- SWITCH TO LOGIN ---
        loginTab.addEventListener('click', () => {
            console.log("Navigating to Login...");
            
            // Toggle visibility
            signupForm.style.display = 'none';
            loginForm.style.display = 'flex';
            
            // Toggle active styling
            loginTab.classList.add('active');
            signupTab.classList.remove('active');
        });

    } else {
        console.error("Smart Household Error: Auth components not found in DOM.");
    }
});