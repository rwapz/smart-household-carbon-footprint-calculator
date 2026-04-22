

function showStatus(msg) {
    const el = document.getElementById('status-msg');
    el.textContent = msg;
    setTimeout(() => el.textContent = '', 1800);
}

function toggleDarkMode() {
    EcoAccess.setDark(!EcoAccess.darkMode);
    syncUI();
    showStatus(EcoAccess.darkMode ? 'Dark mode on ✓' : 'Dark mode off');
}

function toggleContrast() {
    const isHigh = document.documentElement.getAttribute('data-contrast') === 'high';
    const next = !isHigh;
    EcoAccess.setContrast(next);
    syncUI();
    showStatus(next ? 'High contrast on ✓' : 'High contrast off');
}

function setFont(size) {
    EcoAccess.setFontSize(size);
    ['small', 'normal', 'large'].forEach(s => {
        document.getElementById('fs-' + s).classList.toggle('active', s === size);
    });
    showStatus('Text size: ' + size + ' ✓');
}

function syncUI() {
    const dark = localStorage.getItem('eco-theme') === 'dark';
    const isHigh = localStorage.getItem('eco-contrast') === 'true';
    const savedFont = localStorage.getItem('eco-fontsize') || 'normal';
    
    const darkBtn = document.getElementById('btn-dark');
    darkBtn.classList.toggle('off', !dark);
    document.getElementById('ind-dark').textContent = dark ? 'ON' : 'OFF';

    const contrastBtn = document.getElementById('btn-contrast');
    contrastBtn.classList.toggle('off', !isHigh);
    document.getElementById('ind-contrast').textContent = isHigh ? 'ON' : 'OFF';

    ['small', 'normal', 'large'].forEach(s => {
        const el = document.getElementById('fs-' + s);
        if (el) el.classList.toggle('active', s === savedFont);
    });
}

// Wait for both DOM and EcoAccess to be ready
window.addEventListener('load', () => syncUI());
