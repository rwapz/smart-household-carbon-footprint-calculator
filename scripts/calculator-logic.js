<<<<<<< HEAD
/**
 * ECOTRACKER PRO — CORE ENGINE
 */
=======

>>>>>>> 8a01b8fae81aab8bea1de5c2d6f70c4d18e869c4

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
    document.getElementById('btn-weekly').classList.toggle('active', isW);
    document.getElementById('btn-monthly').classList.toggle('active', !isW);
    document.getElementById('period-hint').textContent   = isW ? 'Enter your weekly usage figures below' : 'Enter your monthly usage figures below';
    document.getElementById('label-elec').textContent    = isW ? 'Electricity (kWh/week)'  : 'Electricity (kWh/month)';
    document.getElementById('label-gas').textContent     = isW ? 'Gas (kWh/week)'           : 'Gas (kWh/month)';
    document.getElementById('label-water').textContent   = isW ? 'Weekly Consumption (Litres)' : 'Monthly Consumption (Litres)';
    document.getElementById('period-label').textContent  = isW ? 'WEEKLY CO2 ESTIMATE' : 'MONTHLY CO2 ESTIMATE';
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
    ring.style.stroke  = color;
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
    const tips = [];
    if (e > 10)         tips.push('💡 Switch to LED bulbs and turn off standby devices.');
    if (g > 15)         tips.push('🔥 Lowering your thermostat 1°C can cut gas use by ~10%.');
    if (transport > 10) tips.push('🚲 Try cycling or walking short journeys.');
    if (lifestyle > 20) tips.push('🥗 Reducing meat a few days a week makes a big difference.');
    if (waste > 5)      tips.push('♻️ Composting and recycling can cut waste emissions.');
    if (tips.length === 0) tips.push('🌟 Great work — your footprint is already low!');
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
    document.getElementById('area-title').textContent = `🏆 ${area} Rankings`;
    const entries = [
        {name:'You', score}, {name:'EcoSam', score:82}, {name:'GreenAlex', score:115},
        {name:'CleanJo', score:143}, {name:'NatureKai', score:197}
    ].sort((a,b) => a.score - b.score);
    const medals = ['🥇','🥈','🥉','4️⃣','5️⃣'];
    document.getElementById('leaderboard-list').innerHTML = entries.map((p,i) => `
        <div class="lb-card ${p.name==='You'?'is-you':''}">
            <span>${medals[i]||i+1+'.'}  ${p.name}</span>
            <strong>${p.score.toFixed(1)} kg CO2e</strong>
        </div>`).join('');
}

/* ══ ACTIONS ══ */
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
    const total = document.getElementById('total-output').textContent;
    const csv = [
        'EcoTracker Pro Report',
        `Date,${new Date().toLocaleDateString('en-GB')}`,
        `Period,${currentPeriod}`, `Total (kg CO2e),${total}`,
        `Annual (tonnes),${(parseFloat(total)*52/1000).toFixed(2)}`,
        `Grade,${document.getElementById('grade-badge').textContent}`,
        `Electricity,${lastVals.e.toFixed(2)}`, `Gas,${lastVals.g.toFixed(2)}`,
        `Water,${lastVals.w.toFixed(2)}`, `Other,${lastVals.t.toFixed(2)}`
    ].join('\n');
    const a = document.createElement('a');
    a.href = URL.createObjectURL(new Blob([csv], {type:'text/csv'}));
    a.download = `ecotracker-${new Date().toLocaleDateString('en-GB').replace(/\//g,'-')}.csv`;
    a.click();
}

function sendToDatabaseTable() {
    alert(`Sync ready: ${document.getElementById('total-output').textContent} kg CO2e\n(PHP handles DB sync server-side)`);
}

<<<<<<< HEAD
/* ══ INIT ══ */
window.addEventListener('load', () => {
    try {
        const saved = localStorage.getItem('eco-theme') || 'light';
        applyTheme(saved === 'dark');
    } catch(e) {
        applyTheme(false);
    }
    calculateTotal();
});
=======
function resetAll() { window.location.reload(); }
>>>>>>> 8a01b8fae81aab8bea1de5c2d6f70c4d18e869c4
