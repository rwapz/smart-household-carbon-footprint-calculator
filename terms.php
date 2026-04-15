<?php
session_start();
require_once 'connect.php';
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms & Conditions | Smart Household</title>
    <link rel="stylesheet" href="stylesheets/terms.css">
    <link rel="stylesheet" href="stylesheets/accessibility-global.css">

    <!-- Apply saved theme BEFORE paint to prevent flash -->
    <script>
        (function() {
            const theme    = localStorage.getItem('eco-theme')    || 'light';
            const contrast = localStorage.getItem('eco-contrast') === 'true' ? 'high' : 'normal';
            const font     = localStorage.getItem('eco-fontsize') || 'normal';
            const fontMap  = { small: '14px', normal: '16px', large: '19px' };
            document.documentElement.setAttribute('data-theme', theme);
            document.documentElement.setAttribute('data-contrast', contrast);
            document.documentElement.style.fontSize = fontMap[font] || '16px';
        })();
    </script>
</head>
<body>

<div class="terms-container">
    <!-- Header -->
    <header class="terms-header">
        <div class="header-content">
            <h1>Terms & Conditions</h1>
            <p class="last-updated">Last updated: April 15, 2026</p>
        </div>
        <a href="index.php" class="back-link">← Back to Login</a>
    </header>

    <!-- Main Content -->
    <main class="terms-content">
        
        <!-- Table of Contents -->
        <nav class="table-of-contents">
            <h2>Contents</h2>
            <ul>
                <li><a href="#acceptance">Acceptance of Terms</a></li>
                <li><a href="#use-license">Use License</a></li>
                <li><a href="#disclaimer">Disclaimer</a></li>
                <li><a href="#limitations">Limitations of Liability</a></li>
                <li><a href="#accuracy">Accuracy of Content</a></li>
                <li><a href="#modifications">Modifications to Terms</a></li>
                <li><a href="#governing-law">Governing Law</a></li>
                <li><a href="#contact">Contact Us</a></li>
            </ul>
        </nav>

        <!-- Section 1 -->
        <section id="acceptance" class="terms-section">
            <h2>1. Acceptance of Terms</h2>
            <p>
                By accessing and using the Smart Household Carbon Footprint Calculator ("the Platform"), you accept and agree to be bound by the terms and provision of this agreement. If you do not agree to abide by the above, please do not use this service.
            </p>
        </section>

        <!-- Section 2 -->
        <section id="use-license" class="terms-section">
            <h2>2. Use License</h2>
            <p>
                Permission is granted to temporarily download one copy of the materials (information or software) on Smart Household's Platform for personal, non-commercial transitory viewing only. This is the grant of a license, not a transfer of title, and under this license you may not:
            </p>
            <ul class="terms-list">
                <li>Modify or copying the materials</li>
                <li>Using the materials for any commercial purpose or for any public display</li>
                <li>Attempting to decompile or reverse engineer any software contained on the Platform</li>
                <li>Removing any copyright or other proprietary notations from the materials</li>
                <li>Transferring the materials to another person or "mirroring" the materials on any other server</li>
                <li>Violating any applicable laws or regulations</li>
            </ul>
            <p>
                This license shall automatically terminate if you violate any of these restrictions and may be terminated by Smart Household at any time. Upon terminating your viewing of these materials or upon the termination of this license, you must destroy any downloaded materials in your possession whether in electronic or printed format.
            </p>
        </section>

        <!-- Section 3 -->
        <section id="disclaimer" class="terms-section">
            <h2>3. Disclaimer</h2>
            <p>
                The materials on Smart Household's Platform are provided "as is". Smart Household makes no warranties, expressed or implied, and hereby disclaims and negates all other warranties including, without limitation, implied warranties or conditions of merchantability, fitness for a particular purpose, or non-infringement of intellectual property or other violation of rights.
            </p>
            <p>
                The Platform provides information and calculations for carbon footprint estimation. These calculations are based on average data and may not reflect your exact carbon footprint. For precise environmental impact assessments, please consult with environmental professionals.
            </p>
        </section>

        <!-- Section 4 -->
        <section id="limitations" class="terms-section">
            <h2>4. Limitations of Liability</h2>
            <p>
                In no event shall Smart Household or its suppliers be liable for any damages (including, without limitation, damages for loss of data or profit, or due to business interruption) arising out of the use or inability to use the materials on Smart Household's Platform, even if Smart Household or a Smart Household authorized representative has been notified orally or in writing of the possibility of such damage.
            </p>
            <p>
                Because some jurisdictions do not allow limitations on implied warranties, or limitations of liability for consequential or incidental damages, these limitations may not apply to you.
            </p>
        </section>

        <!-- Section 5 -->
        <section id="accuracy" class="terms-section">
            <h2>5. Accuracy of Content</h2>
            <p>
                The materials appearing on Smart Household's Platform could include technical, typographical, or photographic errors. Smart Household does not warrant that any of the materials on its Platform are accurate, complete, or current.
            </p>
            <p>
                Smart Household may make changes to the materials contained on its Platform at any time without notice. However, Smart Household does not make any commitment to update the materials.
            </p>
            <p>
                Users are responsible for maintaining the accuracy and confidentiality of their household data and account information. You agree to keep your password and other account information confidential.
            </p>
        </section>

        <!-- Section 6 -->
        <section id="modifications" class="terms-section">
            <h2>6. Modifications to Terms</h2>
            <p>
                Smart Household may revise these terms of service for its Platform at any time without notice. By using this Platform, you are agreeing to be bound by the then current version of these terms of service.
            </p>
            <p>
                We will notify users of any significant changes via email or through a prominent notice on the Platform. Your continued use of the Platform following the posting of revised Terms means that you accept and agree to the changes.
            </p>
        </section>

        <!-- Section 7 -->
        <section id="governing-law" class="terms-section">
            <h2>7. Governing Law</h2>
            <p>
                These terms and conditions are governed by and construed in accordance with the laws of the United Kingdom, and you irrevocably submit to the exclusive jurisdiction of the courts located in the UK.
            </p>
            <p>
                If any provision of these terms is found to be invalid or unenforceable, that provision will be limited or eliminated to the minimum extent necessary, and all other provisions will remain in full force and effect.
            </p>
        </section>

        <!-- Section 8 -->
        <section id="contact" class="terms-section">
            <h2>8. Contact Us</h2>
            <p>
                If you have any questions about these Terms and Conditions, please contact us at:
            </p>
            <div class="contact-info">
                <p><strong>Smart Household</strong></p>
                <p>📧 Email: support@smarthousehold.com</p>
                <p>💬 Contact Form: Available on our website</p>
            </div>
        </section>

        <!-- Additional Policies -->
        <section class="terms-section additional-policies">
            <h2>Privacy & Data Protection</h2>
            <p>
                Your use of the Smart Household Platform is also governed by our Privacy Policy. Please review our <a href="#">Privacy Policy</a> to understand our practices regarding the collection and use of your information.
            </p>
            <p>
                We take data protection seriously and comply with relevant data protection regulations to ensure your personal information is handled securely.
            </p>
        </section>

        <!-- Acceptance Checkbox Section -->
        <section class="terms-section acceptance-section">
            <h2>Acceptance</h2>
            <p>
                By creating an account and using the Smart Household Platform, you confirm that you have read and understand these Terms and Conditions, and that you agree to be bound by them.
            </p>
            <a href="index.php#signupTab" class="btn-accept">Return to Sign Up</a>
        </section>

    </main>

    <!-- Footer -->
    <footer class="terms-footer">
        <p>&copy; 2026 Smart Household. All rights reserved.</p>
        <p>Carbon Footprint Calculator | Sustainability for Households</p>
    </footer>

</div>

</body>
</html>
