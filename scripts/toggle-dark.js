/**
 * Shared accessibility controls - adds toggleDarkMode and other controls
 */
function toggleDarkMode() {
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const newTheme = isDark ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', newTheme);
    localStorage.setItem('eco-theme', newTheme);
    
    // Update button if exists
    const btn = document.getElementById('dark-btn');
    if (btn) btn.textContent = isDark ? '🌙' : '☀️';
    
    // Save to DB
    fetch('user-preferences.php?action=save', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({theme: newTheme})
    });
}

function toggleContrast() {
    const isHigh = document.documentElement.getAttribute('data-contrast') === 'high';
    const newContrast = isHigh ? 'normal' : 'high';
    document.documentElement.setAttribute('data-contrast', newContrast);
    localStorage.setItem('eco-contrast', isHigh ? 'false' : 'true');
    
    fetch('user-preferences.php?action=save', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({contrast: newContrast})
    });
}

function setFontSize(size) {
    const map = { small: '14px', normal: '16px', large: '19px' };
    document.documentElement.style.fontSize = map[size] || '16px';
    localStorage.setItem('eco-fontsize', size);
    
    fetch('user-preferences.php?action=save', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({font: size})
    });
}