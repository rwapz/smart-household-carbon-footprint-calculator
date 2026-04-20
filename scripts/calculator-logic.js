/**
 * ECOTRACKER PRO — CORE ENGINE
 */

const FACTORS = {
    elec: 0.233, gas: 0.183, water_l: 0.000298,
    petrol: 0.300, diesel: 0.290, ev: 0.052,
    public: 0.089, waste: 20.0
};

const LIFESTYLE = {
    diet:     { vegan: 3.5, veggie: 7.0, average: 14.0, meatheavy: 24.5 },
    shopping: { minimal: 5.0, average: 15.0, heavy: 30.0 },
    flights:  { none: 0, occasional: 8.0, frequent: 20.0 }
};

const UK_AVG = 170;

/* DAILY LEADERBOARDS — Real daily performance grades */
const DAILY_LEADERBOARDS = {
    'Sheffield': [
        {name:'You', score:0, grade:'--', verified:false},
        {name:'EcoSam', score:52, grade:'Excellent', verified:true},
        {name:'GreenAlex', score:67, grade:'Good', verified:true},
        {name:'CleanJo', score:89, grade:'Average', verified:true},
        {name:'NatureKai', score:124, grade:'Bad', verified:true}
    ],
    'Chesterfield': [
        {name:'You', score:0, grade:'--', verified:false},
        {name:'SustainableSarah', score:48, grade:'Excellent', verified:true},
        {name:'EcoMike', score:71, grade:'Good', verified:true},
        {name:'GreenVal', score:95, grade:'Average', verified:true},
        {name:'CarbonKevin', score:118, grade:'Bad', verified:true}
    ],
    'Rotherham': [
        {name:'You', score:0, grade:'--', verified:false},
        {name:'EcoEmma', score:54, grade:'Excellent', verified:true},
        {name:'GreenGary', score:79, grade:'Good', verified:true},
        {name:'CleanChris', score:103, grade:'Average', verified:true},
        {name:'VerdantVicky', score:131, grade:'Bad', verified:true}
    ],
    'Barnsley': [
        {name:'You', score:0, grade:'--', verified:false},
        {name:'EcoEli', score:59, grade:'Excellent', verified:true},
        {name:'GreenGrace', score:74, grade:'Good', verified:true},
        {name:'CleanCarol', score:88, grade:'Average', verified:true},
        {name:'EarthEd', score:112, grade:'Bad', verified:true}
    ],
    'Doncaster': [
        {name:'You', score:0, grade:'--', verified:false},
        {name:'SustainableSteve', score:51, grade:'Excellent', verified:true},
        {name:'GreenGlen', score:68, grade:'Good', verified:true},
        {name:'CleanDallas', score:102, grade:'Average', verified:true},
        {name:'NatureNed', score:134, grade:'Bad', verified:true}
    ]
};

/* MONTHLY LEADERBOARDS — Monthly aggregated scores with realistic grades */
const MONTHLY_LEADERBOARDS = {
    'Sheffield': [
        {name:'You', score:0, grade:'--', verified:false},
        {name:'GreenAlex', score:234, grade:'Excellent', verified:true},
        {name:'MonthlyMark', score:287, grade:'Good', verified:false},
        {name:'CleanJo', score:315, grade:'Average', verified:true},
        {name:'ProSusan', score:398, grade:'Awful', verified:true}
    ],
    'Chesterfield': [
        {name:'You', score:0, grade:'--', verified:false},
        {name:'SustainableSarah', score:198, grade:'Excellent', verified:true},
        {name:'MonthlyMary', score:256, grade:'Good', verified:false},
        {name:'GreenVal', score:342, grade:'Average', verified:true},
        {name:'EcoEngineer', score:456, grade:'Awful', verified:false}
    ],
    'Rotherham': [
        {name:'You', score:0, grade:'--', verified:false},
        {name:'GreenGary', score:267, grade:'Excellent', verified:true},
        {name:'MonthlyMike', score:323, grade:'Good', verified:false},
        {name:'CleanChris', score:368, grade:'Average', verified:true},
        {name:'SustainableStu', score:487, grade:'Awful', verified:false}
    ],
    'Barnsley': [
        {name:'You', score:0, grade:'--', verified:false},
        {name:'GreenGrace', score:278, grade:'Excellent', verified:true},
        {name:'MonthlyMave', score:301, grade:'Good', verified:false},
        {name:'CleanCarol', score:412, grade:'Average', verified:true},
        {name:'LeadingLisa', score:524, grade:'Awful', verified:true}
    ],
    'Doncaster': [
        {name:'You', score:0, grade:'--', verified:false},
        {name:'GreenGlen', score:241, grade:'Excellent', verified:true},
        {name:'MonthlyMatt', score:298, grade:'Good', verified:false},
        {name:'CleanDallas', score:389, grade:'Average', verified:true},
        {name:'TopTina', score:512, grade:'Awful', verified:true}
    ]
};

let currentPeriod = 'weekly';
let currentChartType = 'bar';
let pieChart = null;
let lastVals = { e: 0, g: 0, w: 0, t: 0, total: 0 };

/* ══ DARK MODE ══ */
function toggleDarkMode() {
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    applyTheme(!isDark);
}

function applyTheme(dark) {
    document.documentElement.setAttribute('data-theme', dark ? 'dark' : 'light');
    const btn = document.getElementById('dark-btn');
    if (btn) btn.textContent = dark ? '☀️ Light' : '🌙 Dark';
    try { localStorage.setItem('eco-theme', dark ? 'dark' : 'light'); } catch(e) {}
}

/* ══ PERIOD ══ */
function setPeriod(p) {
    currentPeriod = p;
    const isW = p === 'weekly';
    
    /* Update calculator buttons */
    document.getElementById('btn-weekly').classList.toggle('active', isW);
    document.getElementById('btn-monthly').classList.toggle('active', !isW);
    
    /* Update leaderboard buttons */
    const lbWeekly = document.getElementById('lb-btn-weekly');
    const lbMonthly = document.getElementById('lb-btn-monthly');
    if (lbWeekly) lbWeekly.classList.toggle('active', isW);
    if (lbMonthly) lbMonthly.classList.toggle('active', !isW);
    
    /* Update labels */
    document.getElementById('period-hint').textContent   = isW ? 'Enter your weekly usage figures below' : 'Enter your monthly usage figures below';
    document.getElementById('label-elec').textContent    = isW ? 'Electricity (kWh/week)'  : 'Electricity (kWh/month)';
    document.getElementById('label-gas').textContent     = isW ? 'Gas (kWh/week)'           : 'Gas (kWh/month)';
    document.getElementById('label-water').textContent   = isW ? 'Weekly Consumption (Litres)' : 'Monthly Consumption (Litres)';
    document.getElementById('period-label').textContent  = isW ? 'WEEKLY CO2 ESTIMATE' : 'MONTHLY CO2 ESTIMATE';
    
    /* Re-render leaderboard if visible */
    const rankingsDisplay = document.getElementById('rankings-display');
    if (rankingsDisplay && !rankingsDisplay.classList.contains('hidden')) {
        renderLeaderboard();
    }
    
    calculateTotal();
}

/* ══ VEHICLE FIELDS ══ */
function toggleVehicleFields() {
    const v = document.getElementById('vehicle-setup').value;
    document.getElementById('private-vehicle-fields').classList.toggle('hidden', v !== 'private');
    document.getElementById('public-fields').classList.toggle('hidden', v !== 'public');
    calculateTotal();
}

function addCarField() {
    const c = document.getElementById('additional-cars');
    const d = document.createElement('div');
    d.className = 'car-entry'; d.style.marginTop = '10px';
    d.innerHTML = `
        <div><label>Type</label>
        <select class="car-type" onchange="calculateTotal()">
            <option value="petrol">Petrol</option><option value="diesel">Diesel</option><option value="ev">Electric</option>
        </select></div>
        <div><label>Miles/week</label>
        <input type="number" min="0" class="car-miles" placeholder="e.g. 80" oninput="calculateTotal()"></div>
        <button type="button" class="mini-btn danger" onclick="this.closest('.car-entry').remove();calculateTotal();"
            style="grid-column:span 2;margin-top:4px;">✕ Remove</button>`;
    c.appendChild(d);
}

/* ══ MAIN CALC ══ */
function calculateTotal() {
    const pf = currentPeriod === 'monthly' ? 1 / 4.33 : 1;

    const e = (parseFloat(document.getElementById('input-elec').value)  || 0) * pf * FACTORS.elec;
    const g = (parseFloat(document.getElementById('input-gas').value)   || 0) * pf * FACTORS.gas;
    const w = (parseFloat(document.getElementById('input-water').value) || 0) * pf * FACTORS.water_l;

    const diet  = (LIFESTYLE.diet[document.getElementById('input-diet').value]         || 0) * pf;
    const shop  = (LIFESTYLE.shopping[document.getElementById('input-shopping').value] || 0) * pf;
    const fly   = (LIFESTYLE.flights[document.getElementById('input-flights').value]   || 0) * pf;
    const waste = (parseFloat(document.getElementById('input-waste').value) || 0) * FACTORS.waste * pf;

    let transport = 0;
    const setup = document.getElementById('vehicle-setup').value;
    if (setup === 'private') {
        document.querySelectorAll('.car-type').forEach((t, i) => {
            const m = document.querySelectorAll('.car-miles')[i];
            transport += (parseFloat(m?.value) || 0) * (FACTORS[t.value] || 0) * pf;
        });
    } else if (setup === 'public') {
        transport = (parseFloat(document.getElementById('input-public-miles').value) || 0) * FACTORS.public * pf;
    }

    const other = transport + diet + shop + fly + waste;
    const total = e + g + w + other;
    lastVals = { e, g, w, t: other, total };

    document.getElementById('total-output').textContent = total.toFixed(1);

    updateEmoji(total);
    updateRing(total);
    updateBar(e, g, w, other, total);
    updateComparison(total);
    updateAnnual(total);
    updateTrees(total);
    updateTips(e, g, w, transport, diet + shop + fly, waste, total);
    updateBudget();
    const hr = document.getElementById('history-row');
    if (hr) hr.classList.toggle('hidden', total <= 0);
    if (currentChartType === 'pie' && total > 0) drawPie(e, g, w, other);
    
    /* Save current entry to localStorage */
    try {
        const entry = {
            elec: document.getElementById('input-elec').value || '',
            gas: document.getElementById('input-gas').value || '',
            water: document.getElementById('input-water').value || '',
            waste: document.getElementById('input-waste').value || '0',
            diet: document.getElementById('input-diet').value || '0',
            shopping: document.getElementById('input-shopping').value || '0',
            flights: document.getElementById('input-flights').value || 'none',
            area: document.getElementById('user-area').value || '',
            period: currentPeriod,
            timestamp: new Date().toISOString()
        };
        localStorage.setItem('eco-last-entry', JSON.stringify(entry));
    } catch(e) {}
    checkFormCompletion();
}

function updateEmoji(t) {
    const el = document.getElementById('mood-emoji');
    if (!el) return;
    el.textContent = t === 0 ? '🌍' : t < 60 ? '🌿' : t < 120 ? '😊' : t < 170 ? '😐' : t < 280 ? '😟' : '🔥';
}

function updateRing(total) {
    const ring  = document.getElementById('ring-fill');
    const badge = document.getElementById('grade-badge');
    if (!ring || !badge) return;

    const pct  = Math.min((total / 400) * 100, 100);
    ring.style.strokeDashoffset = 314 - (314 * pct / 100);

    let grade, color;
    if      (total < 60)  { grade = 'A+'; color = '#10b981'; }
    else if (total < 120) { grade = 'A';  color = '#10b981'; }
    else if (total < 170) { grade = 'B';  color = '#3b82f6'; }
    else if (total < 260) { grade = 'C';  color = '#f59e0b'; }
    else if (total < 350) { grade = 'D';  color = '#f97316'; }
    else                  { grade = 'F';  color = '#ef4444'; }

    badge.textContent  = grade;
    badge.style.color  = color;
    /* Trigger pop animation on grade change */
    badge.style.animation = 'none';
    badge.offsetHeight;
    badge.style.animation = 'badgePop 0.4s cubic-bezier(0.34, 1.56, 0.64, 1)';
    ring.style.stroke  = color;
    
    /* Check achievements */
    checkAchievements(total);
}

function checkAchievements(total) {
    const badges = [];
    const diet = document.getElementById('input-diet').value;
    const shopping = document.getElementById('input-shopping').value;
    const flights = document.getElementById('input-flights').value;
    
    if (total < 50) badges.push('🏅 Eco Warrior');
    if (total < 80 && total > 0) badges.push('🌟 Green Champion');
    if (total > 0 && total <= UK_AVG) badges.push('✨ Below Average');
    if (diet === 'vegan') badges.push('🌱 Plant-Based');
    if (shopping === 'minimal') badges.push('♻️ Conscious Shopper');
    if (flights === 'none') badges.push('✈️ Local Traveler');
    
    const badgeEl = document.getElementById('achievement-badges');
    if (badgeEl && badges.length > 0) {
        badgeEl.innerHTML = badges.map(b => `<span class="achievement-badge">${b}</span>`).join('');
        badgeEl.classList.remove('hidden');
    } else if (badgeEl) {
        badgeEl.classList.add('hidden');
    }
}


function updateBar(e, g, w, t, total) {
    if (total > 0) {
        document.getElementById('bar-elec').style.width      = (e / total * 100) + '%';
        document.getElementById('bar-gas').style.width       = (g / total * 100) + '%';
        document.getElementById('bar-water').style.width     = (w / total * 100) + '%';
        document.getElementById('bar-transport').style.width = (t / total * 100) + '%';
    } else {
        ['bar-elec','bar-gas','bar-water','bar-transport'].forEach(id =>
            document.getElementById(id).style.width = '0%');
    }
}

function updateComparison(total) {
    const s = document.getElementById('comparison-section');
    if (!s) return;
    if (total <= 0) { s.classList.add('hidden'); return; }
    s.classList.remove('hidden');
    const max = Math.max(total, UK_AVG) * 1.2;
    document.getElementById('comparison-bar-you').style.width = Math.min(total / max * 100, 100) + '%';
    const marker = document.querySelector('.comparison-marker');
    if (marker) marker.style.left = Math.min(UK_AVG / max * 100, 100) + '%';
    const diff = Math.abs(total - UK_AVG).toFixed(1);
    document.getElementById('comparison-text').textContent =
        total < UK_AVG ? `✅ ${diff} kg below UK weekly average` :
        total === UK_AVG ? '↔️ At the UK weekly average' :
        `⚠️ ${diff} kg above UK weekly average`;
}

function updateAnnual(wk) {
    const el = document.getElementById('annual-projection');
    const out = document.getElementById('annual-output');
    if (!el || !out) return;
    if (wk <= 0) { el.classList.add('hidden'); return; }
    el.classList.remove('hidden');
    out.textContent = (wk * 52 / 1000).toFixed(2);
}

function updateTrees(wk) {
    const s = document.getElementById('trees-section');
    const t = document.getElementById('trees-text');
    if (!s || !t) return;
    if (wk <= 0) { s.classList.add('hidden'); return; }
    s.classList.remove('hidden');
    t.textContent = `🌳 ~${Math.ceil(wk * 52 / 21)} trees needed to offset your annual footprint`;
}

function updateTips(e, g, w, transport, lifestyle, waste, total) {
    const panel = document.getElementById('tips-panel');
    const list  = document.getElementById('tips-list');
    if (!panel || !list) return;
    if (total <= 0) { panel.classList.add('hidden'); return; }
    
    /* Find highest emission category */
    const cats = {energy: e + g, water: w, transport, lifestyle, waste};
    const highest = Object.entries(cats).reduce((a, b) => a[1] > b[1] ? a : b)[0];
    
    const tips = [];
    
    /* Personalized tips based on highest category */
    if (highest === 'energy') {
        if (e > 10) tips.push('💡 Your biggest impact: Switch to LED bulbs and turn off standby devices.');
        if (g > 15) tips.push('🔥 Gas heating is significant: Lowering thermostat 1°C cuts use by ~10%.');
    } else if (highest === 'transport') {
        tips.push('🚗 Transport is your biggest impact! Try cycling/walking short journeys.');
        if (transport > 20) tips.push('🚌 Switching to public transport could halve your emissions.');
    } else if (highest === 'lifestyle') {
        tips.push('🍽️ Food is your biggest impact! Reducing meat a few days/week makes big difference.');
        if (lifestyle > 25) tips.push('🌱 Going vegetarian could save ~50-100kg CO2e/week!');
    } else if (highest === 'waste') {
        tips.push('♻️ Waste is your biggest impact! Composting and recycling significantly helps.');
    } else if (highest === 'water') {
        tips.push('💧 Water usage is your biggest impact! Shorter showers save water + heating energy.');
    }
    
    if (tips.length < 2) tips.push('🌟 Great work — your footprint is low! Keep it up!');
    
    list.innerHTML = tips.map(t => `<div class="tip-item">${t}</div>`).join('');
    panel.classList.remove('hidden');
    const dl = document.getElementById('download-report-btn');
    if (dl) dl.classList.remove('hidden');
}

function updateBudget() {
    const budget = parseFloat(document.getElementById('input-budget')?.value) || 0;
    const total  = parseFloat(document.getElementById('total-output').textContent) || 0;
    const el = document.getElementById('budget-status');
    if (!el) return;
    if (budget <= 0 || total <= 0) { el.classList.add('hidden'); return; }
    el.classList.remove('hidden', 'budget-ok', 'budget-over');
    if (total <= budget) {
        el.classList.add('budget-ok');
        el.textContent = `✅ Within budget! ${(budget - total).toFixed(1)} kg to spare.`;
    } else {
        el.classList.add('budget-over');
        el.textContent = `⚠️ Over budget by ${(total - budget).toFixed(1)} kg CO2e.`;
    }
}

/* ══ CHART ══ */
function setChartType(type) {
    currentChartType = type;
    document.getElementById('btn-bar-chart').classList.toggle('active', type === 'bar');
    document.getElementById('btn-pie-chart').classList.toggle('active', type === 'pie');
    document.getElementById('bar-breakdown').classList.toggle('hidden', type !== 'bar');
    document.getElementById('pie-breakdown').classList.toggle('hidden', type !== 'pie');
    if (type === 'pie') drawPie(lastVals.e, lastVals.g, lastVals.w, lastVals.t);
}

function drawPie(e, g, w, t) {
    const canvas = document.getElementById('pie-chart');
    if (!canvas) return;
    if (pieChart) pieChart.destroy();
    pieChart = new Chart(canvas, {
        type: 'doughnut',
        data: {
            labels: ['Electricity','Gas','Water','Other'],
            datasets: [{ data: [e,g,w,t].map(v => +v.toFixed(2)),
                backgroundColor: ['#10b981','#f59e0b','#06b6d4','#6366f1'], borderWidth: 0 }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { font: { size: 11 } } } }, cutout: '60%' }
    });
}

/* ══ NAVIGATION ══ */
function goToRankings() {
    const area = document.getElementById('user-area').value;
    if (!area) {
        alert('🏠 Please select an area first to view the leaderboard!');
        document.getElementById('user-area').focus();
        return;
    }
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
    const area  = document.getElementById('user-area').value || 'Sheffield';
    const score = parseFloat(document.getElementById('total-output').textContent) || 0;
    const periodLabel = currentPeriod === 'weekly' ? 'Daily' : 'Monthly';
    document.getElementById('area-title').textContent = `🏆 ${area} ${periodLabel} Rankings (Lower = Better)`;
    
    /* Use period-specific leaderboard data */
    const leaderboards = currentPeriod === 'weekly' ? DAILY_LEADERBOARDS : MONTHLY_LEADERBOARDS;
    const leaderboardData = leaderboards[area] || leaderboards['Sheffield'];
    const entries = [...leaderboardData].map(e => ({...e, score: e.name === 'You' ? score : e.score})).sort((a,b) => a.score - b.score);
    
    const medals = ['🥇','🥈','🥉','4️⃣','5️⃣'];
    document.getElementById('leaderboard-list').innerHTML = entries.map((p,i) => {
        const diff = score > 0 && p.name !== 'You' ? parseFloat((score - p.score).toFixed(1)) : null;
        const diffText = diff !== null ? (diff > 0 ? `+${diff} kg` : `${diff} kg`) : '';
        const badge = p.verified && p.name !== 'You' ? '✓' : '';
        const gradeText = p.grade && p.grade !== '--' ? ` (${p.grade})` : '';
        return `
        <div class="lb-card ${p.name==='You'?'is-you':'lb-rank-'+i}">
            <div class="lb-left">
                <span class="lb-medal">${medals[i]||i+1+'.'}</span>
                <span class="lb-name">${p.name}${badge ? '<span class="lb-verified">'+badge+'</span>' : ''}</span>
            </div>
            <div class="lb-right">
                <strong class="lb-score">${p.score.toFixed(1)}</strong>
                <span class="lb-unit">kg CO2e${gradeText}</span>
                ${diffText ? `<span class="lb-diff">${diffText}</span>` : ''}
            </div>
        </div>`;
    }).join('');
}

/* ══ ACTIONS ══ */
function checkFormCompletion() {
    const energy = document.getElementById('input-elec').value && document.getElementById('input-gas').value;
    const water = document.getElementById('input-water').value;
    const waste = document.getElementById('input-waste').value !== '0';
    const diet = document.getElementById('input-diet').value !== '0';
    const shopping = document.getElementById('input-shopping').value !== '0';
    const flights = document.getElementById('input-flights').value !== 'none';
    
    const allBasicFilled = energy && water && waste && diet && shopping && flights;
    const postBtn = document.getElementById('post-leaderboard-btn');
    
    if (postBtn) {
        if (allBasicFilled) {
            postBtn.classList.remove('hidden');
        } else {
            postBtn.classList.add('hidden');
        }
    }
}

function postToLeaderboard() {
    const total = parseFloat(document.getElementById('total-output').textContent) || 0;
    const area = document.getElementById('user-area').value;
    const username = prompt('📝 Enter your username for the leaderboard:\n(e.g., "EcoChampion2024")');
    
    if (!username || username.trim().length < 3) {
        alert('❌ Please enter a valid username (at least 3 characters)');
        return;
    }
    
    if (!area) {
        alert('🏠 Please select an area first');
        return;
    }
    
    /* Add user to leaderboard — use period-specific data */
    try {
        const leaderboards = currentPeriod === 'weekly' ? DAILY_LEADERBOARDS : MONTHLY_LEADERBOARDS;
        if (leaderboards[area]) {
            leaderboards[area][0] = {name: username, score: total, verified: false};
            leaderboards[area].sort((a,b) => a.score - b.score);
            
            /* Save to localStorage with period key */
            const key = `eco-leaderboard-${currentPeriod}-${area}`;
            localStorage.setItem(key, JSON.stringify(leaderboards[area]));
            
            alert(`🎉 Great job ${username}! Your score (${total.toFixed(1)} kg) has been posted to the ${area} ${currentPeriod} leaderboard!`);
            renderLeaderboard();
        }
    } catch(e) {
        console.error('Error posting to leaderboard:', e);
        alert('⚠️ Error saving to leaderboard. Please try again.');
    }
}

function resetAll() {
    ['input-elec','input-gas','input-water','input-public-miles','input-budget']
        .forEach(id => { const el = document.getElementById(id); if (el) el.value = ''; });
    ['vehicle-setup','input-diet','input-shopping','input-flights','input-waste','user-area']
        .forEach(id => { const el = document.getElementById(id); if (el) el.selectedIndex = 0; });
    document.getElementById('additional-cars').innerHTML = '';
    toggleVehicleFields();
    setPeriod('weekly');
    calculateTotal();
}

function shareResult() {
    const score = document.getElementById('total-output').textContent;
    const text  = `My ${currentPeriod} carbon footprint is ${score} kg CO2e — EcoTracker Pro 🌍`;
    if (navigator.share) navigator.share({ title: 'EcoTracker Pro', text }).catch(() => copyText(text));
    else copyText(text);
}

function copyText(text) {
    navigator.clipboard?.writeText(text)
        .then(() => alert('📋 Copied to clipboard!'))
        .catch(() => alert(text));
}

function saveToHistory() {
    const total = parseFloat(document.getElementById('total-output').textContent) || 0;
    if (!total) return;
    try {
        const h = JSON.parse(localStorage.getItem('eco-history') || '[]');
        h.push({ date: new Date().toLocaleDateString('en-GB'), value: total, period: currentPeriod });
        if (h.length > 30) h.shift();
        localStorage.setItem('eco-history', JSON.stringify(h));
        const msg = document.getElementById('history-saved-msg');
        if (msg) { msg.classList.remove('hidden'); setTimeout(() => msg.classList.add('hidden'), 2500); }
    } catch(e) {}
}

function downloadData() {
    const total = parseFloat(document.getElementById('total-output').textContent) || 0;
    const grade = document.getElementById('grade-badge').textContent;
    const annual = (total * 52 / 1000).toFixed(2);
    const date = new Date().toLocaleDateString('en-GB');
    
    const html = `<!DOCTYPE html><html><head><meta charset="UTF-8"><style>
body{font-family:'Inter',Arial;margin:0;padding:20px;background:#f8fbff;color:#0f172a}
.container{max-width:600px;margin:0 auto;background:white;padding:30px;border-radius:16px;box-shadow:0 4px 16px rgba(0,0,0,0.1)}
h1{color:#10b981;text-align:center;margin:0 0 8px;font-size:1.8rem}
.subtitle{text-align:center;color:#64748b;font-size:0.9rem;margin-bottom:24px}
.score-box{background:linear-gradient(135deg,#10b981 0%,#12b886 100%);color:white;padding:24px;border-radius:12px;text-align:center;margin:20px 0}
.score-value{font-size:3rem;font-weight:900;margin:0}
.score-unit{font-size:1rem;opacity:0.9}
.grade-badge{display:inline-flex;align-items:center;justify-content:center;background:white;color:#10b981;font-size:2rem;font-weight:900;width:60px;height:60px;border-radius:50%;margin-top:12px}
.section{margin:24px 0}
.section h3{color:#10b981;font-size:0.9rem;text-transform:uppercase;letter-spacing:0.07em;margin:0 0 12px}
.metric-row{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #e2e8f0}
.metric-label{color:#64748b;font-weight:600}
.metric-value{color:#0f172a;font-weight:700}
.breakdown{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:12px}
.breakdown-item{background:#f8fbff;padding:12px;border-radius:8px;border-left:3px solid #10b981}
.breakdown-label{font-size:0.75rem;color:#64748b;font-weight:600;text-transform:uppercase}
.breakdown-value{font-size:1.2rem;font-weight:800;color:#0f172a;margin-top:4px}
.footer{text-align:center;color:#64748b;font-size:0.8rem;margin-top:24px;padding-top:12px;border-top:1px solid #e2e8f0}
</style></head><body><div class="container">
<h1>🌍 EcoTracker Pro</h1>
<p class="subtitle">Carbon Footprint Report</p>
<div class="score-box">
<p style="margin:0 0 8px;opacity:0.9">Your ${currentPeriod.toUpperCase()} Footprint</p>
<p class="score-value">${total.toFixed(1)}</p>
<p class="score-unit">kg CO2e</p>
<div class="grade-badge">${grade}</div>
</div>
<div class="section">
<h3>📊 Summary</h3>
<div class="metric-row"><span class="metric-label">Report Date</span><span class="metric-value">${date}</span></div>
<div class="metric-row"><span class="metric-label">Period</span><span class="metric-value">${currentPeriod.charAt(0).toUpperCase() + currentPeriod.slice(1)}</span></div>
<div class="metric-row"><span class="metric-label">Annual Projection</span><span class="metric-value">${annual} tonnes</span></div>
</div>
<div class="section">
<h3>🔍 Breakdown by Category</h3>
<div class="breakdown">
<div class="breakdown-item"><div class="breakdown-label">⚡ Electricity</div><div class="breakdown-value">${lastVals.e.toFixed(2)} kg</div></div>
<div class="breakdown-item"><div class="breakdown-label">🔥 Gas</div><div class="breakdown-value">${lastVals.g.toFixed(2)} kg</div></div>
<div class="breakdown-item"><div class="breakdown-label">💧 Water</div><div class="breakdown-value">${lastVals.w.toFixed(2)} kg</div></div>
<div class="breakdown-item"><div class="breakdown-label">🚗 Other</div><div class="breakdown-value">${lastVals.t.toFixed(2)} kg</div></div>
</div>
</div>
<div class="footer">Generated by EcoTracker Pro • Sheffield Hallam University</div>
</div></body></html>`;
    
    const a = document.createElement('a');
    a.href = 'data:text/html;charset=utf-8,' + encodeURIComponent(html);
    a.download = `ecotracker-report-${new Date().toLocaleDateString('en-GB').replace(/\//g,'-')}.html`;
    a.click();
}

function sendToDatabaseTable() {
    alert(`Sync ready: ${document.getElementById('total-output').textContent} kg CO2e\n(PHP handles DB sync server-side)`);
}

/* ══ INIT ══ */
window.addEventListener('load', () => {
    try {
        const saved = localStorage.getItem('eco-theme') || 'dark';
        applyTheme(saved === 'dark');
    } catch(e) {
        applyTheme(true);
    }
    
    // Calculator starts fresh - no old data loaded
    
    calculateTotal();
});

function resetAll() { window.location.reload(); }

/* ══ CONSOLE DEBUG COMMANDS ══ */
window.testDaily = function() {
    console.group('📊 DAILY LEADERBOARDS TEST');
    Object.entries(DAILY_LEADERBOARDS).forEach(([area, users]) => {
        console.log(`%c${area}`, 'color:#10b981;font-weight:bold;font-size:1.1em');
        console.table(users);
    });
    console.groupEnd();
    return 'Logged daily leaderboards to console ✓';
};

window.testMonthly = function() {
    console.group('📊 MONTHLY LEADERBOARDS TEST');
    Object.entries(MONTHLY_LEADERBOARDS).forEach(([area, users]) => {
        console.log(`%c${area}`, 'color:#f59e0b;font-weight:bold;font-size:1.1em');
        console.table(users);
    });
    console.groupEnd();
    return 'Logged monthly leaderboards to console ✓';
};

window.compareLeaderboards = function() {
    console.group('🔄 DAILY vs MONTHLY COMPARISON');
    const areas = Object.keys(DAILY_LEADERBOARDS);
    areas.forEach(area => {
        console.log(`%c${area}`, 'color:#8b5cf6;font-weight:bold');
        const daily = DAILY_LEADERBOARDS[area][1]?.score || 0;
        const monthly = MONTHLY_LEADERBOARDS[area][1]?.score || 0;
        console.log(`  Daily Top: ${daily} kg | Monthly Top: ${monthly} kg | Multiplier: ${(monthly/daily).toFixed(2)}x`);
    });
    console.groupEnd();
    return 'Comparison logged ✓';
};

window.getAreaUsers = function(area = 'Sheffield', period = 'daily') {
    const leaderboards = period === 'daily' ? DAILY_LEADERBOARDS : MONTHLY_LEADERBOARDS;
    const data = leaderboards[area] || leaderboards['Sheffield'];
    console.log(`%c${area} (${period.toUpperCase()})`, `color:${period === 'daily' ? '#10b981' : '#f59e0b'};font-weight:bold`);
    console.table(data);
    return data;
};

window.switchToDaily = function() {
    setPeriod('weekly');
    return '✅ Switched to DAILY view';
};

window.switchToMonthly = function() {
    setPeriod('monthly');
    return '✅ Switched to MONTHLY view';
};

window.debugForm = function() {
    console.group('📋 FORM COMPLETION STATUS');
    const elec = document.getElementById('input-elec').value;
    const gas = document.getElementById('input-gas').value;
    const water = document.getElementById('input-water').value;
    const waste = document.getElementById('input-waste').value;
    const diet = document.getElementById('input-diet').value;
    const shopping = document.getElementById('input-shopping').value;
    const flights = document.getElementById('input-flights').value;
    console.log(`⚡ Electricity: ${elec || '(empty)'}`);
    console.log(`🔥 Gas: ${gas || '(empty)'}`);
    console.log(`💧 Water: ${water || '(empty)'}`);
    console.log(`🗑️  Waste: ${waste}`);
    console.log(`🍽️  Diet: ${diet}`);
    console.log(`🛒 Shopping: ${shopping}`);
    console.log(`✈️  Flights: ${flights}`);
    const postBtn = document.getElementById('post-leaderboard-btn');
    const isVisible = !postBtn?.classList.contains('hidden');
    console.log(`%cPost Button: ${isVisible ? 'VISIBLE' : 'HIDDEN'}`, isVisible ? 'color:#10b981' : 'color:#ef4444');
    console.groupEnd();
    return { elec, gas, water, waste, diet, shopping, flights, postButtonVisible: isVisible };
};

window.showConsoleHelp = function() {
    console.clear();
    console.log(`%c🌍 EcoTracker Pro — Console Debug Commands`, 'font-size:1.5em;color:#10b981;font-weight:bold');
    console.log(`%c════════════════════════════════════════════`, 'color:#64748b');
    console.log('');
    console.log('%c• testDaily()', 'color:#10b981;font-weight:bold', 'View all daily leaderboard data by area');
    console.log('%c• testMonthly()', 'color:#f59e0b;font-weight:bold', 'View all monthly leaderboard data by area');
    console.log('%c• compareLeaderboards()', 'color:#8b5cf6;font-weight:bold', 'Compare daily vs monthly scores & multipliers');
    console.log('%c• getAreaUsers(\"Sheffield\", \"daily\")', 'color:#06b6d4;font-weight:bold', 'Get users for specific area & period');
    console.log('%c• switchToDaily()', 'color:#10b981;font-weight:bold', 'Switch view to DAILY mode');
    console.log('%c• switchToMonthly()', 'color:#f59e0b;font-weight:bold', 'Switch view to MONTHLY mode');
    console.log('%c• debugForm()', 'color:#ec4899;font-weight:bold', 'Check form field values & post button status');
    console.log('%c• showConsoleHelp()', 'color:#6366f1;font-weight:bold', 'Display this help message');
    console.log('');
    console.log(`%c════════════════════════════════════════════`, 'color:#64748b');
    console.log('%cExample: testDaily() then compareLeaderboards() to understand the data', 'font-style:italic;color:#64748b');
};

console.log('%c💡 Tip: Type showConsoleHelp() in console for debug commands!', 'color:#10b981;font-weight:bold');

/* ══ CALCULATOR DEBUG COMMANDS ══ */
window.viewCalculationFactors = function() {
    console.group('📊 CALCULATION FACTORS');
    console.log('%cEnergy Emissions (kg CO2 per kWh)', 'color:#10b981;font-weight:bold');
    console.log('  Electricity: ' + FACTORS.elec + ' kg CO2/kWh');
    console.log('  Gas: ' + FACTORS.gas + ' kg CO2/kWh');
    console.log('')
    console.log('%cTransport Emissions (kg CO2 per mile)', 'color:#f59e0b;font-weight:bold');
    console.log('  Petrol: ' + FACTORS.petrol + ' kg CO2/mile');
    console.log('  Diesel: ' + FACTORS.diesel + ' kg CO2/mile');
    console.log('  EV: ' + FACTORS.ev + ' kg CO2/mile');
    console.log('  Public Transport: ' + FACTORS.public + ' kg CO2/mile');
    console.log('')
    console.log('%cOther Factors', 'color:#8b5cf6;font-weight:bold');
    console.log('  Water: ' + FACTORS.water_l + ' kg CO2 per litre');
    console.log('  Waste: ' + FACTORS.waste + ' kg CO2 per bag');
    console.log('%cUK Average Weekly: ' + UK_AVG + ' kg CO2e', 'color:#ef4444;font-weight:bold');
    console.groupEnd();
    return FACTORS;
};

window.viewLifestyleOptions = function() {
    console.group('🍽️ LIFESTYLE EMISSION VALUES');
    console.log('%cDiet (kg CO2 per week)', 'color:#ec4899;font-weight:bold', LIFESTYLE.diet);
    console.log('%cShopping (kg CO2 per week)', 'color:#06b6d4;font-weight:bold', LIFESTYLE.shopping);
    console.log('%cFlights (kg CO2 per week)', 'color:#f97316;font-weight:bold', LIFESTYLE.flights);
    console.groupEnd();
    return LIFESTYLE;
};

window.getScoreGrade = function(score) {
    if (score < 50) return 'Excellent';
    if (score < 80) return 'Good';
    if (score < 130) return 'Average';
    if (score < 170) return 'Bad';
    return 'Awful';
};

window.calculateQuick = function(elec = 0, gas = 0, water = 0, waste = 0, diet = 'average', shopping = 'average', flights = 'none') {
    console.group('⚡ QUICK CALCULATION TEST');
    const e = parseFloat(elec) * FACTORS.elec;
    const g = parseFloat(gas) * FACTORS.gas;
    const w = parseFloat(water) * FACTORS.water_l;
    const d = LIFESTYLE.diet[diet] || 0;
    const sh = LIFESTYLE.shopping[shopping] || 0;
    const f = LIFESTYLE.flights[flights] || 0;
    const total = e + g + w + (FACTORS.waste * waste) + d + sh + f;
    
    console.log(`⚡ Electricity (${elec} kWh): ${e.toFixed(2)} kg`);
    console.log(`🔥 Gas (${gas} kWh): ${g.toFixed(2)} kg`);
    console.log(`💧 Water (${water} L): ${w.toFixed(2)} kg`);
    console.log(`🗑️ Waste (${waste} bags): ${(FACTORS.waste * waste).toFixed(2)} kg`);
    console.log(`🍽️ Diet (${diet}): ${d.toFixed(2)} kg`);
    console.log(`🛒 Shopping (${shopping}): ${sh.toFixed(2)} kg`);
    console.log(`✈️ Flights (${flights}): ${f.toFixed(2)} kg`);
    console.log(`%c━━━━━━━━━━━━━━━━━━━━━━━━`, 'color:#64748b');
    console.log(`%c📊 TOTAL: ${total.toFixed(2)} kg CO2e`, `color:#10b981;font-weight:bold;font-size:1.1em`);
    console.log(`%c Grade: ${getScoreGrade(total)}`, `color:#10b981;font-weight:bold`);
    console.log(`%c Annual: ${(total * 52 / 1000).toFixed(2)} tonnes`, `color:#64748b;font-size:0.9em`);
    console.groupEnd();
    return {total: parseFloat(total.toFixed(2)), grade: getScoreGrade(total)};
};

window.testAllAreas = function() {
    console.group('🗺️ ALL AREAS AND USERS');
    const areas = ['Sheffield', 'Chesterfield', 'Rotherham', 'Barnsley', 'Doncaster'];
    const daily = DAILY_LEADERBOARDS;
    const monthly = MONTHLY_LEADERBOARDS;
    areas.forEach(area => {
        console.log(`%c${area}`, 'color:#10b981;font-weight:bold;font-size:1.1em');
        const d = daily[area];
        const m = monthly[area];
        console.log('  Daily (Weekly View):', d.map(u => `${u.name}(${u.score}kg/${u.grade})`).join(' | '));
        console.log('  Monthly View:', m.map(u => `${u.name}(${u.score}kg/${u.grade})`).join(' | '));
    });
    console.groupEnd();
    return {daily, monthly};
};

window.getCurrentFormValues = function() {
    console.group('📋 CURRENT FORM VALUES');
    const values = {
        electricity: document.getElementById('input-elec').value || '(empty)',
        gas: document.getElementById('input-gas').value || '(empty)',
        water: document.getElementById('input-water').value || '(empty)',
        waste: document.getElementById('input-waste').value,
        diet: document.getElementById('input-diet').value,
        shopping: document.getElementById('input-shopping').value,
        flights: document.getElementById('input-flights').value,
        area: document.getElementById('user-area').value || '(not selected)',
        period: currentPeriod,
        currentTotal: document.getElementById('total-output').textContent || '0'
    };
    console.table(values);
    console.groupEnd();
    return values;
};

window.quickLeaderboardTest = function(area = 'Sheffield') {
    const daily = DAILY_LEADERBOARDS[area];
    const monthly = MONTHLY_LEADERBOARDS[area];
    console.group(`🏆 ${area} LEADERBOARD COMPARISON`);
    console.log('%cDAILY (Weekly Mode)', 'color:#10b981;font-weight:bold;font-size:1.1em');
    console.table(daily);
    console.log('%cMONTHLY (Monthly Mode)', 'color:#f59e0b;font-weight:bold;font-size:1.1em');
    console.table(monthly);
    console.groupEnd();
};

window.showConsoleHelp = function() {
    console.clear();
    console.log(`%c🌍 EcoTracker Pro — All Console Commands`, 'font-size:1.5em;color:#10b981;font-weight:bold');
    console.log(`%c════════════════════════════════════════════`, 'color:#64748b');
    console.log('');
    console.log('%c⚙️  CALCULATOR & FACTORS', 'color:#10b981;font-weight:bold;font-size:1.1em');
    console.log('%c• viewCalculationFactors()', 'color:#10b981;font-weight:bold', '- Show all emission factors');
    console.log('%c• viewLifestyleOptions()', 'color:#10b981;font-weight:bold', '- Show diet/shopping/flight values');
    console.log('%c• calculateQuick(elec, gas, water, waste, diet, shopping, flights)', 'color:#10b981;font-weight:bold', '- Quick calc test');
    console.log('%c• getScoreGrade(score)', 'color:#10b981;font-weight:bold', '- Get grade for score');
    console.log('%c• getCurrentFormValues()', 'color:#10b981;font-weight:bold', '- View all form inputs');
    console.log('');
    console.log('%c📊 LEADERBOARDS', 'color:#f59e0b;font-weight:bold;font-size:1.1em');
    console.log('%c• testDaily()', 'color:#f59e0b;font-weight:bold', '- Show all daily leaderboards');
    console.log('%c• testMonthly()', 'color:#f59e0b;font-weight:bold', '- Show all monthly leaderboards');
    console.log('%c• quickLeaderboardTest(area)', 'color:#f59e0b;font-weight:bold', '- Compare daily vs monthly for area');
    console.log('%c• testAllAreas()', 'color:#f59e0b;font-weight:bold', '- View all areas with users');
    console.log('%c• compareLeaderboards()', 'color:#f59e0b;font-weight:bold', '- Compare multipliers');
    console.log('%c• getAreaUsers(area, period)', 'color:#f59e0b;font-weight:bold', '- Get specific area users');
    console.log('');
    console.log('%c🎮 UI CONTROLS', 'color:#8b5cf6;font-weight:bold;font-size:1.1em');
    console.log('%c• switchToDaily()', 'color:#8b5cf6;font-weight:bold', '- Switch to weekly/daily mode');
    console.log('%c• switchToMonthly()', 'color:#8b5cf6;font-weight:bold', '- Switch to monthly mode');
    console.log('%c• debugForm()', 'color:#8b5cf6;font-weight:bold', '- Check form completion status');
    console.log('');
    console.log(`%c════════════════════════════════════════════`, 'color:#64748b');
    console.log('%cQUICK TESTS:', 'font-weight:bold;color:#64748b');
    console.log('%cviewCalculationFactors(); calculateQuick(50, 100, 200, 2, "vegan", "average", "none")', 'color:#999;font-style:italic');
    console.log('%ctestAllAreas(); quickLeaderboardTest("Sheffield")', 'color:#999;font-style:italic');
};

console.log('%c✨ Type showConsoleHelp() to see all commands!', 'color:#10b981;font-weight:bold;font-size:1.1em');
