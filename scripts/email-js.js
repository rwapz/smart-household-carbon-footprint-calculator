/**
 * EcoTracker - EmailJS Integration
 * Handles welcome emails on signup
 * Requires: https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js
 */

// ⚠️ Replace these with your actual EmailJS credentials
const EMAILJS_PUBLIC_KEY  = '1_9lrPS1IktYIG7UD'; // your existing key
const EMAILJS_SERVICE_ID  = 'service_g9boubk';
const EMAILJS_TEMPLATE_ID = 'template_gwvqjtw';   // your existing template

document.addEventListener('DOMContentLoaded', function () {
    emailjs.init(EMAILJS_PUBLIC_KEY);

    // --- Welcome email after signup ---
    // Called from dashboard.php when ?new=1 is in the URL
    const params = new URLSearchParams(window.location.search);
    if (params.get('new') === '1') {
        // PHP session passes username — we read it from a hidden element
        // Add this to dashboard.php: <span id="eco-user" data-name="<?= $_SESSION['username'] ?>" data-email="<?= $_SESSION['email'] ?>"></span>
        const userEl = document.getElementById('eco-user');
        if (userEl) {
            sendWelcomeEmail(
                userEl.dataset.name,
                userEl.dataset.email
            );
        }
    }
});

function sendWelcomeEmail(name, email) {
    const loadingMsg = '📧 Sending welcome email...';
    console.log(loadingMsg);

    emailjs.send(EMAILJS_SERVICE_ID, EMAILJS_TEMPLATE_ID, {
        user_name:  name,
        user_email: email,
        message:    `Welcome to EcoTracker, ${name}! Start tracking your carbon footprint today.`
    }).then(() => {
        console.log(`✅ Welcome email sent to ${email}`);
        showEmailNotification(`Welcome ${name}! Check your inbox 📬`);
    }, (err) => {
        console.warn('❌ Email failed:', err);
    });
}

function showEmailNotification(msg) {
    // Reuse existing notification div if present, or create one
    let notif = document.getElementById('eco-notification');
    if (!notif) {
        notif = document.createElement('div');
        notif.id = 'eco-notification';
        notif.style.cssText = `
            position:fixed; top:0; left:0; right:0;
            background:#2ecc71; color:#fff;
            text-align:center; padding:14px;
            font-size:1rem; font-weight:600;
            z-index:9999; opacity:0;
            transition: opacity 0.4s;
        `;
        document.body.prepend(notif);
    }
    notif.textContent = msg;
    notif.style.opacity = 1;
    setTimeout(() => notif.style.opacity = 0, 3500);
}