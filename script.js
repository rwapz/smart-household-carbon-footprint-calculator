/**
 * Carbon Tracker - Auth Toggle Logic
 * Sheffield Hallam University Project
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Grab the elements
    const loginTab = document.getElementById('loginTab');
    const signupTab = document.getElementById('signupTab');
    const loginForm = document.getElementById('loginForm');
    const signupForm = document.getElementById('signupForm');

    // 2. Function to switch to Signup
    signupTab.addEventListener('click', () => {
        console.log("Switching to Signup... Let's save some carbon!");
        
        loginForm.style.display = 'none';
        signupForm.style.display = 'flex';
        
        signupTab.classList.add('active');
        loginTab.classList.remove('active');
    });

    // 3. Function to switch to Login
    loginTab.addEventListener('click', () => {
        console.log("Switching to Login... Welcome back, twin.");
        
        signupForm.style.display = 'none';
        loginForm.style.display = 'flex';
        
        loginTab.classList.add('active');
        signupTab.classList.remove('active');
    });
});