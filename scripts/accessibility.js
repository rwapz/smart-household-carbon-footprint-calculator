/**
 * ECOTRACKER - Accessibility & Dev Console Commands
 * Type commands in browser console e.g. eco('/darkmode')
 * Will hook into Settings/Dashboard in future.
 */

const EcoAccess = {

    darkMode: false,

    setDark(on) {
        this.darkMode = on;
        document.documentElement.setAttribute('data-theme', on ? 'dark' : 'light');
        const btn = document.getElementById('dark-btn');
        if (btn) btn.textContent = on ? '☀️ Light' : '🌙 Dark';
        localStorage.setItem('eco-darkmode', on);
    },

    setFontSize(size) {
        // size: 'small' | 'normal' | 'large'
        const map = { small: '14px', normal: '16px', large: '19px' };
        document.documentElement.style.fontSize = map[size] || '16px';
        localStorage.setItem('eco-fontsize', size);
    },

    setContrast(on) {
        document.documentElement.setAttribute('data-contrast', on ? 'high' : 'normal');
        localStorage.setItem('eco-contrast', on);
    },

    help() {
        console.log(`
%cEcoTracker Console Commands
%ceco('/darkmode')       — toggle dark mode
eco('/darkmode on')    — force dark
eco('/darkmode off')   — force light
eco('/fontsize large') — bigger text (small | normal | large)
eco('/contrast')       — toggle high contrast
eco('/reset')          — reset all accessibility settings
eco('/help')           — show this list
        `, 'font-weight:bold;color:#10b981;font-size:14px;', 'color:#aaa;');
    },

    reset() {
        this.setDark(false);
        this.setFontSize('normal');
        this.setContrast(false);
        console.log('%cAccessibility settings reset.', 'color:#10b981;');
    },

    // Restore saved prefs on load
    init() {
        const dark = localStorage.getItem('eco-darkmode') === 'true';
        const font = localStorage.getItem('eco-fontsize') || 'normal';
        const contrast = localStorage.getItem('eco-contrast') === 'true';
        if (dark) this.setDark(true);
        if (font !== 'normal') this.setFontSize(font);
        if (contrast) this.setContrast(true);
        console.log('%cEcoTracker accessibility loaded. Type eco(\'/help\') for commands.', 'color:#10b981;');
    }
};

// Global console command function
window.eco = function(cmd) {
    if (!cmd) return EcoAccess.help();
    const [command, arg] = cmd.split(' ');
    switch (command) {
        case '/darkmode':
            if (arg === 'on')       EcoAccess.setDark(true);
            else if (arg === 'off') EcoAccess.setDark(false);
            else                    EcoAccess.setDark(!EcoAccess.darkMode);
            break;
        case '/fontsize':   EcoAccess.setFontSize(arg || 'normal'); break;
        case '/contrast':   EcoAccess.setContrast(!document.documentElement.getAttribute('data-contrast') === 'high'); break;
        case '/reset':      EcoAccess.reset(); break;
        case '/help':       EcoAccess.help(); break;
        default: console.warn(`Unknown command: ${cmd}. Try eco('/help')`);
    }
};

// Also wire up the dark mode button in the header
window.toggleDarkMode = () => EcoAccess.setDark(!EcoAccess.darkMode);

document.addEventListener('DOMContentLoaded', () => EcoAccess.init());