/**
 * ECOTRACKER PRO - CORE ENGINE
 * Sheffield Hallam Group Project
 */

// 1. --- Configuration & Data ---
const FACTORS = {
    elec: 0.233, gas: 0.183, water_l: 0.000298,
    petrol: 0.300, diesel: 0.290, ev: 0.052,
    public: 0.089, waste_bag: 20.0
};

const LIFESTYLE = {
    diet: { vegan: 3.5, veggie: 7.0, average: 14.0, meatheavy: 24.5 },
    shopping: { minimal: 5.0, average: 15.0, heavy: 30.0 },
    flights: { none: 0, occasional: 15.0, frequent: 50.0 }
};

const UK_AVG = 170;
let myPieChart = null;
let currentPeriod = 'weekly';
let currentChartType = 'bar';
let userBudget = 0;

// 2. --- Period Toggle ---
function setPeriod(p) {
    currentPeriod = p;
    document.getElementById('btn-weekly').classList.toggle('active', p === 'weekly');
    document.getElementById('btn-monthly').classList.toggle('active', p === 'monthly');
    document.getElementById('period-hint').innerText = p === 'weekly'
        ? 'Enter your weekly usage figures below'
        : 'Enter your monthly usage figures below';

    const suffix = p === 'weekly' ? '/week' : '/month';
    document.getElementById('label-elec').innerText = `Electricity (kWh${suffix})`;
    document.getElementById('label-gas').innerText = `Gas (kWh${suffix})`;
    document.getElementById('label-water').innerText = `${p === 'weekly' ? 'Weekly' : 'Monthly'} Consumption (Litres)`;
    document.getElementById('period-label').innerText = p === 'weekly' ? 'Weekly CO2 Estimate' : 'Monthly CO2 Estimate';

    calculateTotal();
}

// 3. --- Vehicle Toggle ---
function toggleVehicleFields() {
    const setup = document.getElementById('vehicle-setup').value;
    document.getElementById('private-vehicle-fields').classList.toggle('hidden', setup !== 'private');
    document.getElementById('public-fields').classList.toggle('hidden', setup !== 'public');
    calculateTotal();
}

function addCarField() {
    const container = document.getElementById('additional-cars');
    const div = document.createElement('div');
    div.className = 'car-entry';
    div.style.marginTop = '10px';
    div.innerHTML = `
        <label>Vehicle Type</label>
        <select class="car-type" onchange="calculateTotal()">
            <option value="petrol">Petrol</option>
            <option value="diesel">Diesel</option>
            <option value="ev">Electric (EV)</option>
        </select>
        <input type="number" min="0" class="car-miles" placeholder="Miles per week" oninput="calculateTotal()">
    `;
    container.appendChild(div);
}

// 4. --- Core Calculation ---
function calculateTotal() {
    const getV = (id) => parseFloat(document.getElementById(id)?.value) || 0;
    const divisor = currentPeriod === 'monthly' ? 4.33 : 1; // convert monthly to weekly

    // Energy
    const elecKg = (getV('input-elec') / divisor) * FACTORS.elec;
    const gasKg  = (getV('input-gas')  / divisor) * FACTORS.gas;

    // Water
    const waterKg = (getV('input-water') / divisor) * FACTORS.water_l;

    // Transport
    let transportKg = 0;
    const setup = document.getElementById('vehicle-setup').value;
    if (setup === 'private') {
        document.querySelectorAll('.car-entry').forEach(entry => {
            const type  = entry.querySelector('.car-type')?.value || 'petrol';
            const miles = parseFloat(entry.querySelector('.car-miles')?.value) || 0;
            transportKg += (miles / divisor) * (FACTORS[type] || 0);
        });
    } else if (setup === 'public') {
        transportKg = (getV('input-public-miles') / divisor) * FACTORS.public;
    }

    // Waste
    const wasteKg = (parseFloat(document.getElementById('input-waste').value) || 0) * FACTORS.waste_bag;

    // Lifestyle (these are already weekly figures)
    const dietVal     = LIFESTYLE.diet[document.getElementById('input-diet').value]     || 0;
    const shopVal     = LIFESTYLE.shopping[document.getElementById('input-shopping').value] || 0;
    const flightVal   = LIFESTYLE.flights[document.getElementById('input-flights').value]   || 0;
    const lifestyleKg = dietVal + shopVal + flightVal;

    const scores = { elec: elecKg, gas: gasKg, water: waterKg, transport: transportKg, waste: wasteKg, lifestyle: lifestyleKg };
    const total = Object.values(scores).reduce((a, b) => a + b, 0);

    updateUI(total, scores);
}

// 5. --- UI Updates ---
function updateUI(total, scores) {
    const t = total.toFixed(1);

    // Main number
    document.getElementById('total-output').innerText = t;
    document.getElementById('output-side').scrollTop = 0;

    // Grade & ring
    const grade = getGrade(total);
    document.getElementById('grade-badge').innerText = grade.letter;
    const ring = document.getElementById('ring-fill');
    const pct  = Math.min(total / (UK_AVG * 2), 1);
    ring.style.strokeDashoffset = (314 * (1 - pct)).toFixed(1);
    ring.style.stroke = grade.color;

    // Emoji mood
    const emojis = { 'A': '🌿', 'B': '😊', 'C': '😐', 'D': '😟', 'F': '🔥' };
    document.getElementById('mood-emoji').innerText = emojis[grade.letter] || '🌍';

    // Annual
    const annual = ((total * 52) / 1000).toFixed(2);
    document.getElementById('annual-output').innerText = annual;
    document.getElementById('annual-projection').classList.remove('hidden');

    // Trees
    const treesWeek = Math.ceil(total / 0.026);
    const treesYear = Math.ceil((total * 52) / 0.026);
    document.getElementById('trees-text').innerText =
        `🌳 To offset this you'd need ${treesWeek.toLocaleString()} trees/week — or ${treesYear.toLocaleString()} trees planted per year`;
    document.getElementById('trees-section').classList.remove('hidden');

    // Comparison bar
    const barPct = Math.min((total / (UK_AVG * 1.5)) * 100, 100).toFixed(1);
    document.getElementById('comparison-bar-you').style.width = barPct + '%';
    const diff = (((total - UK_AVG) / UK_AVG) * 100).toFixed(0);
    const compText = document.getElementById('comparison-text');
    if (total <= UK_AVG) {
        compText.innerHTML = `✅ You're ${Math.abs(diff)} kg (${Math.abs(diff)}%) below the UK average`;
    } else {
        compText.innerHTML = `⚠️ You're ${Math.abs(total - UK_AVG).toFixed(0)} kg (${diff}%) above the UK average`;
    }
    document.getElementById('comparison-section').classList.remove('hidden');

    // Chart toggle row
    document.getElementById('chart-toggle-row').classList.remove('hidden');

    // Bar breakdown
    const grandTotal = total || 1;
    ['elec','gas','water','transport'].forEach(key => {
        const el = document.getElementById('bar-' + (key === 'elec' ? 'elec' : key === 'gas' ? 'gas' : key === 'water' ? 'water' : 'transport'));
        if (el) el.style.width = ((scores[key] / grandTotal) * 100).toFixed(1) + '%';
    });

    // Pie chart (only if visible)
    if (currentChartType === 'pie') renderPieChart(scores);

    // Tips
    showTips(scores, total);
    document.getElementById('tips-panel').classList.remove('hidden');

    // History & download buttons
    document.getElementById('history-row').classList.remove('hidden');
    document.getElementById('download-report-btn').classList.remove('hidden');

    // Budget check
    if (userBudget > 0) updateBudgetStatus(total);
}

function getGrade(total) {
    if (total < 80)  return { letter: 'A', color: '#10b981' };
    if (total < 130) return { letter: 'B', color: '#34d399' };
    if (total < 170) return { letter: 'C', color: '#f59e0b' };
    if (total < 220) return { letter: 'D', color: '#f97316' };
    return { letter: 'F', color: '#ef4444' };
}

// 6. --- Tips ---
function showTips(scores, total) {
    const tips = [];
    const top = Object.entries(scores).sort((a,b) => b[1] - a[1])[0][0];
    const labels = { elec:'Electricity', gas:'Gas', water:'Water', transport:'Transport', waste:'Waste', lifestyle:'Lifestyle' };

    tips.push(`🚗 <strong>${labels[top]} is your top category.</strong> Replacing one car trip a week with public transport or cycling cuts this by ~20%.`);
    if (scores.elec > 20) tips.push('🏠 A smart thermostat (e.g. Nest or Hive) saves ~120 kg CO2 per year.');
    if (scores.transport > 30) tips.push('⚡ EVs emit ~70% less CO2 per mile than petrol cars.');
    if (scores.gas > 20) tips.push('🔥 Lowering your thermostat by 1°C can cut gas use by ~10%.');
    if (total < UK_AVG) tips.push('🌍 Great work! You\'re below the UK average — keep it up!');

    document.getElementById('tips-list').innerHTML = tips.map(t => `<div class="tip-item" style="padding:10px;margin:6px 0;background:#f0fdf4;border-radius:8px;font-size:0.85rem;">${t}</div>`).join('');
}

// 7. --- Chart Toggle ---
function setChartType(type) {
    currentChartType = type;
    document.getElementById('btn-bar-chart').classList.toggle('active', type === 'bar');
    document.getElementById('btn-pie-chart').classList.toggle('active', type === 'pie');
    document.getElementById('bar-breakdown').classList.toggle('hidden', type !== 'bar');
    document.getElementById('pie-breakdown').classList.toggle('hidden', type !== 'pie');
    if (type === 'pie') calculateTotal(); // re-render pie
}

function renderPieChart(scores) {
    const ctx = document.getElementById('pie-chart')?.getContext('2d');
    if (!ctx) return;
    if (myPieChart) myPieChart.destroy();
    myPieChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Electricity', 'Gas', 'Water', 'Transport', 'Waste', 'Lifestyle'],
            datasets: [{
                data: [scores.elec, scores.gas, scores.water, scores.transport, scores.waste, scores.lifestyle],
                backgroundColor: ['#10b981','#3b82f6','#06b6d4','#f59e0b','#ef4444','#8b5cf6']
            }]
        },
        options: { plugins: { legend: { display: false } } }
    });
}

// 8. --- Budget ---
function updateBudget() {
    userBudget = parseFloat(document.getElementById('input-budget').value) || 0;
    calculateTotal();
}

function updateBudgetStatus(total) {
    const el = document.getElementById('budget-status');
    if (!el) return;
    el.classList.remove('hidden');
    if (total <= userBudget) {
        el.style.color = '#10b981';
        el.innerText = `✅ Under budget by ${(userBudget - total).toFixed(1)} kg`;
    } else {
        el.style.color = '#ef4444';
        el.innerText = `⚠️ Over budget by ${(total - userBudget).toFixed(1)} kg`;
    }
}

// 9. --- History & Download ---
function saveToHistory() {
    const total = document.getElementById('total-output').innerText;
    const date  = new Date().toLocaleDateString('en-GB');
    const history = JSON.parse(localStorage.getItem('ecoHistory') || '[]');
    history.push({ date, total, period: currentPeriod });
    localStorage.setItem('ecoHistory', JSON.stringify(history));
    const msg = document.getElementById('history-saved-msg');
    msg.classList.remove('hidden');
    setTimeout(() => msg.classList.add('hidden'), 2500);
}

function downloadData() {
    const total = document.getElementById('total-output').innerText;
    const annual = document.getElementById('annual-output').innerText;
    const date   = new Date().toLocaleDateString('en-GB');
    const text   = `EcoTracker Pro Report\nDate: ${date}\nWeekly CO2: ${total} kg CO2e\nAnnual Projection: ${annual} tonnes CO2e\n`;
    const blob   = new Blob([text], { type: 'text/plain' });
    const a      = document.createElement('a');
    a.href       = URL.createObjectURL(blob);
    a.download   = 'ecotracker-report.txt';
    a.click();
}

// 10. --- Share ---
function shareResult() {
    const total = document.getElementById('total-output').innerText;
    if (navigator.share) {
        navigator.share({ title: 'My EcoTracker Result', text: `My weekly carbon footprint is ${total} kg CO2e! 🌍` });
    } else {
        navigator.clipboard.writeText(`My weekly carbon footprint is ${total} kg CO2e! 🌍`);
        alert('Result copied to clipboard!');
    }
}

// 11. Dark mode handled by accessibility.js

// 12. --- Leaderboard (mock data) ---
function goToRankings() {
    document.getElementById('calc-display').classList.add('hidden');
    document.getElementById('rankings-display').classList.remove('hidden');
    document.getElementById('toggle-view-btn').classList.add('hidden');
    document.getElementById('return-calc-btn').classList.remove('hidden');
    renderLeaderboard();
}

function goToCalc() {
    document.getElementById('calc-display').classList.remove('hidden');
    document.getElementById('rankings-display').classList.add('hidden');
    document.getElementById('toggle-view-btn').classList.remove('hidden');
    document.getElementById('return-calc-btn').classList.add('hidden');
}

function renderLeaderboard() {
    const area = document.getElementById('user-area').value || 'Sheffield';
    document.getElementById('area-title').innerText = `🏆 ${area} Rankings`;
    const mock = [
        { name: 'Alex T.', score: 89 }, { name: 'Jamie R.', score: 112 },
        { name: 'Sam K.',  score: 134 }, { name: 'Morgan L.', score: 155 },
        { name: 'You',     score: parseFloat(document.getElementById('total-output').innerText) || 154 }
    ].sort((a,b) => a.score - b.score);

    document.getElementById('leaderboard-list').innerHTML = mock.map((u, i) =>
        `<div style="display:flex;justify-content:space-between;padding:10px 14px;margin:6px 0;background:${u.name==='You'?'#f0fdf4':'#f8fafc'};border-radius:10px;font-weight:${u.name==='You'?700:400}">
            <span>${i+1}. ${u.name}</span><span>${u.score} kg</span>
        </div>`
    ).join('');
}

// 13. --- Sync ---
function sendToDatabaseTable() {
    const area = document.getElementById('user-area').value;
    if (!area) return alert('Select an area first!');
    alert(`Data for ${area} synced ✅`);
}

function resetAll() { window.location.reload(); }