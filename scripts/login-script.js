

document.addEventListener('DOMContentLoaded', () => {
    // Select elements
    const loginTab = document.getElementById('loginTab');
    const signupTab = document.getElementById('signupTab');
    const loginForm = document.getElementById('loginForm');
    const signupForm = document.getElementById('signupForm');

    // Only execute if the Auth components exist (prevents errors on other pages)
    if (loginTab && signupTab && loginForm && signupForm) {
        
        // Handle Switching to Signup
        signupTab.addEventListener('click', () => {
            console.log("UI: Switching to Signup View");
            loginForm.style.display = 'none';
            signupForm.style.display = 'flex';
            
            signupTab.classList.add('active');
            loginTab.classList.remove('active');
        });

        // Handle Switching to Login
        loginTab.addEventListener('click', () => {
            console.log("UI: Switching to Login View");
            signupForm.style.display = 'none';
            loginForm.style.display = 'flex';
            
            loginTab.classList.add('active');
            signupTab.classList.remove('active');
        });

    } else {
        // This log confirms the script loaded but recognized it's not on the Auth page.
        console.log("Smart Household: Script active, Auth UI not present on this page.");
    }
});
