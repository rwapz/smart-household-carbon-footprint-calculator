/**
 * Dashboard - Clean version
 */

const TIPS = [
    "Use LED bulbs - 75% less energy!",
    "Carpooling saves 2kg CO2 per trip.",
    "Unplug devices when not in use.",
    "Use public transport.",
    "Eat one vegetarian meal per week.",
    "Air dry your clothes."
];

let currentData = null;

function loadDashboardData() {
    fetch('dashboard-api.php').then(r => r.json()).then(data => {
        if (data.success) {
            currentData = data;
            updateDisplay(data);
        }
    });
}

function updateDisplay(data) {
    const total = data.monthlyTotal || 0;
    const avg = data.monthlyAvg || 0;
    const goal = data.goalTarget || 30;
    const rank = data.rank || 1;
    const hh = data.household_id || 1;
    const count = data.activityCount || 0;
    
    document.getElementById('monthly-co2').textContent = total || '0';
    document.getElementById('avg-co2').textContent = avg || '0';
    document.getElementById('your-rank').textContent = rank;
    
    const pct = goal > 0 ? Math.round((total / goal) * 100) : 0;
    document.getElementById('goal-progress').textContent = Math.min(pct, 100) + '%';
    
    // Environmental
    document.getElementById('trees-equiv').textContent = total > 0 ? Math.round(total / 21) : '0';
    document.getElementById('water-saved').textContent = total > 0 ? Math.round(total * 1000) : '0';
    document.getElementById('clean-energy').textContent = total > 0 ? Math.round(total / 0.4) : '0';
    document.getElementById('impact-note').textContent = 'Household ' + hh + ' total: ' + total + ' kg';
    
    // Goal
    document.getElementById('goal-target-display').textContent = 'Target: ' + goal + ' kg CO₂';
    document.getElementById('goal-pct-text').textContent = Math.min(pct, 100) + '%';
    document.getElementById('goal-bar').style.width = Math.min(pct, 100) + '%';
    
    let status = total > goal ? '🎉 Goal Met!' : (pct >= 80 ? '⚠️ Getting close!' : (total > 0 ? '✓ On track' : 'Start logging!'));
    document.getElementById('goal-status').textContent = status;
    
    document.getElementById('daily-tip').textContent = TIPS[Math.floor(Math.random() * TIPS.length)];
}

function openGoalModal() {
    document.getElementById('goalModal').style.display = 'flex';
    if (currentData) document.getElementById('customGoalInput').value = currentData.goalTarget || 30;
}

function closeGoalModal() {
    document.getElementById('goalModal').style.display = 'none';
}

function saveGoal() {
    const val = parseInt(document.getElementById('customGoalInput').value);
    if (isNaN(val) || val < 5 || val > 200) { alert('Enter 5-200 kg'); return; }
    
    fetch('dashboard-api.php?action=setGoal', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({target: val})
    }).then(r => r.json()).then(data => {
        if (data.success) {
            closeGoalModal();
            location.reload();
        } else { alert('Error saving'); }
    });
}

document.addEventListener('click', e => { if (e.target.id === 'goalModal') closeGoalModal(); });
// Load user settings from database
fetch('user-preferences.php?action=get').then(r=>r.json()).then(s=>{
    if(s.theme) document.documentElement.setAttribute('data-theme', s.theme);
    if(s.font) document.documentElement.style.fontSize = {small:'14px',normal:'16px',large:'19px'}[s.font]||'16px';
    if(s.contrast) document.documentElement.setAttribute('data-contrast', s.contrast);
}).catch(()=>{});

document.addEventListener('DOMContentLoaded', loadDashboardData);