/**
 * Dashboard - Household version
 */

const TIPS = [
    "Use LED bulbs - 75% less energy!",
    "Carpooling saves 2kg CO2 per trip.",
    "Unplug devices when not in use.",
    "Use public transport.",
    "Eat one vegetarian meal per week.",
    "Air dry your clothes.",
    "Lower water heater temperature."
];

let currentData = null;

function loadDashboardData() {
    fetch('dashboard-api.php')
        .then(r => r.json())
        .then(data => {
            if (data.error) return;
            currentData = data;
            updateDisplay(data);
        });
}

function updateDisplay(data) {
    const monthlyTotal = data.monthlyTotal || 0;
    const monthlyAvg = data.monthlyAvg || 0;
    const goalTarget = data.goalTarget || 30;
    const rank = data.rank || 1;
    const household = data.household_id || 1;
    const activityCount = data.activityCount || 0;
    
    // Show household SHARED data
    document.getElementById('monthly-co2').textContent = monthlyTotal || '0';
    document.getElementById('avg-co2').textContent = monthlyAvg || '0';
    document.getElementById('your-rank').textContent = rank;
    
    const goalPct = goalTarget > 0 ? Math.round((monthlyTotal / goalTarget) * 100) : 0;
    document.getElementById('goal-progress').textContent = Math.min(goalPct, 100) + '%';
    
    document.getElementById('trees-equiv').textContent = monthlyTotal > 0 ? Math.round(monthlyTotal / 21) : '0';
    document.getElementById('water-saved').textContent = monthlyTotal > 0 ? Math.round(monthlyTotal * 1000) : '0';
    document.getElementById('clean-energy').textContent = monthlyTotal > 0 ? Math.round(monthlyTotal / 0.4) : '0';
    document.getElementById('impact-note').textContent = 'Household ' + household + ' total: ' + monthlyTotal + ' kg (' + activityCount + ' activities)';
    
    document.getElementById('goal-target-display').textContent = 'Target: ' + goalTarget + ' kg CO₂';
    document.getElementById('goal-pct-text').textContent = Math.min(goalPct, 100) + '%';
    document.getElementById('goal-bar').style.width = Math.min(goalPct, 100) + '%';
    
    let status = 'Start logging!';
    const card = document.querySelector('.goal-card');
    card.classList.remove('goal-exceeded', 'goal-warning');
    
    if (monthlyTotal > goalTarget) {
        status = '🎉 Goal Met!';
        card.classList.add('goal-exceeded');
    } else if (goalPct >= 80) {
        status = '⚠️ Getting close!';
        card.classList.add('goal-warning');
    } else if (monthlyTotal > 0) {
        status = '✓ On track';
    }
    document.getElementById('goal-status').textContent = status;
    
    document.getElementById('daily-tip').textContent = TIPS[Math.floor(Math.random() * TIPS.length)];
}

function openGoalModal() {
    document.getElementById('goalModal').style.display = 'flex';
    if (currentData) {
        document.getElementById('customGoalInput').value = currentData.goalTarget || 30;
    }
}

function closeGoalModal() {
    document.getElementById('goalModal').style.display = 'none';
}

function saveGoal() {
    const value = parseInt(document.getElementById('customGoalInput').value);
    if (isNaN(value) || value < 5 || value > 200) {
        alert('Enter 5-200 kg');
        return;
    }
    
    fetch('dashboard-api.php?action=setGoal', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({target: value})
    })
    .then(r => r.json())
    .then(data => {
        console.log('Save result:', data);
        if (data.success) {
            closeGoalModal();
            loadDashboardData();
            // Update local cache too
            if (currentData) currentData.goalTarget = value;
            alert('Goal saved: ' + value + ' kg!');
            location.reload();
        } else {
            alert('Error saving goal');
        }
    })
    .catch(e => {
        console.error(e);
        alert('Error: ' + e);
    });
}

document.addEventListener('click', e => {
    if (e.target.id === 'goalModal') closeGoalModal();
});

document.addEventListener('DOMContentLoaded', loadDashboardData);