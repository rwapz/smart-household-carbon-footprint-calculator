<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accessibility Settings | Smart Household</title>
    <link rel="stylesheet" href="stylesheets/accessibility-style.css">
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

<div class="card">
    <h1>Accessibility</h1>
    <p class="subtitle">Customise your Smart Household experience.</p>

    <div class="section-label">Display</div>

    <button class="setting-btn off" id="btn-dark" onclick="toggleDarkMode()">
        <span class="btn-label">🌙 Dark Mode</span>
        <span class="toggle-indicator" id="ind-dark">OFF</span>
    </button>

    <button class="setting-btn off" id="btn-contrast" onclick="toggleContrast()">
        <span class="btn-label">⚡ High Contrast</span>
        <span class="toggle-indicator" id="ind-contrast">OFF</span>
    </button>

    <div class="section-label" style="margin-top:16px;">Text Size</div>
    <div class="font-row">
        <button class="font-btn" id="fs-small" onclick="setFont('small')">A<br><small>Small</small></button>
        <button class="font-btn active" id="fs-normal" onclick="setFont('normal')">A<br><small>Normal</small></button>
        <button class="font-btn" id="fs-large" onclick="setFont('large')" style="font-size:1.1rem;">A<br><small>Large</small></button>
    </div>

    <div class="status-msg" id="status-msg"></div>

    <hr>
    <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
</div>

<script src="scripts/accessibility.js"></script>
<script src="scripts/accessibility-settings.js"></script>
</body>
</html>