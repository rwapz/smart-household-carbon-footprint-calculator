/**
 * Project: Smart Household Carbon Footprint Calculator
 * Script: Auth Toggle Logic
 * Purpose: Handles the smooth transition between Login and Signup forms.
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Element Selectors
    const loginTab = document.getElementById('loginTab');
    const signupTab = document.getElementById('signupTab');
    const loginForm = document.getElementById('loginForm');
    const signupForm = document.getElementById('signupForm');

    // 2. Safety Check: Ensure elements exist before adding listeners
    if (loginTab && signupTab && loginForm && signupForm) {
        
        // --- SWITCH TO SIGNUP ---
        signupTab.addEventListener('click', () => {
            // UI Feedback
            console.log("Navigating to Signup...");
            
            // Toggle form visibility
            loginForm.style.display = 'none';
            signupForm.style.display = 'flex';
            
            // Toggle active tab classes for CSS styling
            signupTab.classList.add('active');
            loginTab.classList.remove('active');
        });

        // --- SWITCH TO LOGIN ---
        loginTab.addEventListener('click', () => {
            // UI Feedback
            console.log("Navigating to Login...");
            
            // Toggle form visibility
            signupForm.style.display = 'none';
            loginForm.style.display = 'flex';
            
            // Toggle active tab classes for CSS styling
            loginTab.classList.add('active');
            signupTab.classList.remove('active');
        });

    } else {
        console.error("EcoTracker Error: One or more Auth elements missing from the DOM.");
    }
});