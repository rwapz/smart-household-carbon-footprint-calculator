/**
 * DASHBOARD ENHANCED - Load and display dashboard widgets
 */

const TIPS = [
    "You used 10% more electricity this week. Try unplugging devices when not in use!",
    "Carpooling could save 2kg CO₂/week. Consider sharing rides with colleagues.",
    "Every kWh of renewable energy used reduces your carbon footprint by 0.5kg!",
    "Reducing water usage by 1 hour daily saves ~15kg CO₂/year through treatment.",
    "Using public transport instead of driving saves 90% CO₂! 🚌",
    "Air dry clothes when possible. Dryers account for 5-10% of household energy.",
    "LED bulbs use 75% less energy. Switch today and save money!",
    "Eating one vegetarian meal per week reduces your carbon footprint by 2kg/year.",
    "Regular maintenance of your car improves fuel efficiency by up to 15%.",
    "Close unused apps and tabs - each browser tab uses ~10-50MB RAM energy!"
];

// Load goal from localStorage
function getGoal() {
    const saved = localStorage.getItem('eco-monthly-goal');
    return saved ? parseInt(saved) : 30; // Default 30kg
}

function setGoal(value) {
    localStorage.setItem('eco-monthly-goal', value);
    closeGoalModal();
    loadDashboardData();
}

function setCustomGoal() {
    const input = document.getElementById('customGoalInput');
    const value = parseInt(input.value);
    
    if (isNaN(value) || value < 5 || value > 200) {
        alert('Please enter a value between 5 and 200 kg CO₂');
        return;
    }
    
    setGoal(value);
}

function openGoalModal() {
    document.getElementById('goalModal').style.display = 'flex';
    document.getElementById('customGoalInput').value = getGoal();
}

function closeGoalModal() {
    document.getElementById('goalModal').style.display = 'none';
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('goalModal');
    if (event.target === modal) {
        closeGoalModal();
    }
});

async function loadDashboardData() {
    try {
        // Fetch activity data from server
        const response = await fetch('dashboard-api.php', {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' }
        });
        
        if (!response.ok) {
            console.warn('Dashboard API not available, using mock data');
            loadMockData();
            return;
        }

        const data = await response.json();
        populateDashboard(data);
    } catch (error) {
        console.warn('Using mock data due to:', error.message);
        loadMockData();
    }
}

function loadMockData() {
    // Mock data for demonstration
    const mockData = {
        monthlyTotal: 0,
        monthlyAvg: 0,
        recentActivities: []
    };
    populateDashboard(mockData);
}

function populateDashboard(data) {
    const goal = getGoal();
    const monthlyTotal = data.monthlyTotal || 0;
    const monthlyAvg = data.monthlyAvg || 0;
    const latestCO2 = data.latestCO2 || 0;

    // TOP STATS
    document.getElementById('monthly-co2').textContent = monthlyTotal.toFixed(1);
    document.getElementById('avg-co2').textContent = monthlyAvg.toFixed(1);

    // ENVIRONMENTAL IMPACT - from latest activity
    if (latestCO2 > 0) {
        const treesEquivalent = Math.round(latestCO2 / 21); // 1 tree = 21kg CO₂/year avg
        const waterSaved = Math.round(latestCO2 * 1000); // Rough estimation
        const cleanEnergyEquiv = Math.round(latestCO2 / 0.4); // Rough kWh equivalent
        
        document.getElementById('trees-equiv').textContent = treesEquivalent;
        document.getElementById('water-saved').textContent = waterSaved;
        document.getElementById('clean-energy').textContent = cleanEnergyEquiv;

        // Format time
        const time = new Date(data.latestTime);
        const now = new Date();
        const diffMs = now - time;
        const diffMins = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMs / 3600000);
        const diffDays = Math.floor(diffMs / 86400000);

        let timeStr = 'Recently';
        if (diffMins < 1) timeStr = 'Just now';
        else if (diffMins < 60) timeStr = `${diffMins}m ago`;
        else if (diffHours < 24) timeStr = `${diffHours}h ago`;
        else if (diffDays < 7) timeStr = `${diffDays}d ago`;
        else timeStr = time.toLocaleDateString();

        const activityDisplay = data.latestActivityType ? ` (${data.latestActivityType})` : '';
        document.getElementById('impact-note').textContent = `From your latest log: ${latestCO2} kg CO₂ ${timeStr}${activityDisplay}`;
    } else {
        document.getElementById('trees-equiv').textContent = '--';
        document.getElementById('water-saved').textContent = '--';
        document.getElementById('clean-energy').textContent = '--';
        document.getElementById('impact-note').textContent = 'No recent data yet. Start logging activities!';
    }

    // GOAL SECTION
    document.getElementById('goal-target-display').textContent = `Target: ${goal} kg CO₂`;
    
    const goalPercent = goal > 0 ? Math.min(Math.round((monthlyTotal / goal) * 100), 100) : 0;
    document.getElementById('goal-pct-text').textContent = goalPercent + '%';
    document.getElementById('goal-bar').style.width = goalPercent + '%';
    
    let goalStatus = 'Set your goal!';
    const goalCard = document.querySelector('.goal-card');
    
    // Clear previous classes
    goalCard.classList.remove('goal-exceeded', 'goal-warning');
    
    if (monthlyTotal === 0) {
        goalStatus = 'Start logging activities!';
    } else if (goalPercent >= 100) {
        goalStatus = '🎉 Goal Exceeded!';
        goalCard.classList.add('goal-exceeded');
    } else if (goalPercent >= 80) {
        goalStatus = '⚠️ Getting close!';
        goalCard.classList.add('goal-warning');
    } else {
        goalStatus = '✓ On Track!';
    }
    document.getElementById('goal-status').textContent = goalStatus;

    // DAILY TIP
    const randomTip = TIPS[Math.floor(Math.random() * TIPS.length)];
    document.getElementById('daily-tip').textContent = randomTip;
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', loadDashboardData);
