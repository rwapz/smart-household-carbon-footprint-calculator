const EcoAccess = {
    saveTimer: null,
    
    setDark(on) {
        this.darkMode = on;
        document.documentElement.setAttribute('data-theme', on ? 'dark' : 'light');
        const btn = document.getElementById('dark-btn');
        if (btn) btn.textContent = on ? '☀️ Light' : '🌙 Dark';
        this.saveSettings({theme: on ? 'dark' : 'light'});
    },

    setFontSize(size) {
        const map = { small: '14px', normal: '16px', large: '19px' };
        document.documentElement.style.fontSize = map[size] || '16px';
        this.saveSettings({font: size});
    },

    setContrast(on) {
        document.documentElement.setAttribute('data-contrast', on ? 'high' : 'normal');
        this.saveSettings({contrast: on ? 'high' : 'normal'});
    },

    saveSettings(settings) {
        clearTimeout(this.saveTimer);
        this.saveTimer = setTimeout(() => {
            fetch('api-settings.php?action=save', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(settings)
            });
        }, 500);
    },

    reset() {
        this.setDark(false);
        this.setFontSize('normal');
        this.setContrast(false);
    },

    init(settings) {
        if (settings) {
            this.setDark(settings.theme === 'dark');
            if (settings.font) this.setFontSize(settings.font);
            if (settings.contrast === 'high') this.setContrast(true);
        }
    }
};

window.eco = function(cmd) {
    if (!cmd) return EcoAccess.help();
    const [command, arg] = cmd.split(' ');
    switch (command) {
        case '/darkmode': EcoAccess.setDark(arg === 'on' ? true : (arg === 'off' ? false : !EcoAccess.darkMode)); break;
        case '/fontsize': if (arg) EcoAccess.setFontSize(arg); break;
        case '/contrast': EcoAccess.setContrast(arg === 'on'); break;
        case '/reset': EcoAccess.reset(); break;
        default: console.log('Commands: /darkmode [on|off], /fontsize [small|normal|large], /contrast [on|off], /reset');
    }
};

EcoAccess.help = function() {
    console.log('%cAccessibility Commands', 'font-weight:bold');
    console.log('/darkmode - toggle dark mode');
    console.log('/fontsize [size] - set font size');
    console.log('/contrast - toggle high contrast');
    console.log('/reset - reset to defaults');
};