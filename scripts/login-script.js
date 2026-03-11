/**
 * Project: Smart Household
 * File: scripts/login-script.js
 * Description: Handles the interactive toggling between Login and Sign Up forms.
 * Using 'defer' in HTML ensures this runs after the DOM is ready.
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Grab all necessary UI elements
    const loginTab = document.getElementById('loginTab');
    const signupTab = document.getElementById('signupTab');
    const loginForm = document.getElementById('loginForm');
    const signupForm = document.getElementById('signupForm');

    // 2. Safety Check: Only run logic if all elements are found on the page
    if (loginTab && signupTab && loginForm && signupForm) {
        
        // --- FUNCTIONALITY FOR THE SIGN UP TAB ---
        signupTab.addEventListener('click', () => {
            console.log("Navigating to Signup...");
            
            // Hide Login, Show Signup
            loginForm.style.display = 'none';
            signupForm.style.display = 'flex'; // Uses flex to maintain card alignment
            
            // Update Tab Styling
            signupTab.classList.add('active');
            loginTab.classList.remove('active');
        });

        // --- FUNCTIONALITY FOR THE LOGIN TAB ---
        loginTab.addEventListener('click', () => {
            console.log("Navigating to Login...");
            
            // Hide Signup, Show Login
            signupForm.style.display = 'none';
            loginForm.style.display = 'flex';
            
            // Update Tab Styling
            loginTab.classList.add('active');
            signupTab.classList.remove('active');
        });

    } else {
        // This helps you debug in the browser console if you accidentally change an ID
        console.error("Smart Household Error: One or more form elements were not found in the DOM.");
    }
});