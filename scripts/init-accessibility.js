/**
 * Shared accessibility init - load settings from DB on page load
 * Prevents flash of wrong theme
 */
(function() {
    // Apply localStorage immediately to prevent flash
    const savedTheme = localStorage.getItem('eco-theme') || 'light';
    const savedFont = localStorage.getItem('eco-fontsize') || 'normal';
    const savedContrast = localStorage.getItem('eco-contrast') || 'normal';
    
    document.documentElement.setAttribute('data-theme', savedTheme);
    document.documentElement.setAttribute('data-contrast', savedContrast);
    const fontMap = { small: '14px', normal: '16px', large: '19px' };
    document.documentElement.style.fontSize = fontMap[savedFont] || '16px';
    
    // Then sync with DB (if logged in)
    fetch('user-preferences.php?action=get')
        .then(r => r.ok ? r.json() : null)
        .then(s => {
            if (s && !s.error) {
                localStorage.setItem('eco-theme', s.theme);
                localStorage.setItem('eco-fontsize', s.font);
                localStorage.setItem('eco-contrast', s.contrast === 'high' ? 'true' : 'false');
                
                document.documentElement.setAttribute('data-theme', s.theme);
                document.documentElement.setAttribute('data-contrast', s.contrast);
                document.documentElement.style.fontSize = fontMap[s.font] || '16px';
            }
        })
        .catch(() => {});
})();