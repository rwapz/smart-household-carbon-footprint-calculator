/**
 * ECOTRACKER PRO - DATA ANALYTICS & LOGIC ENGINE
 * Focused on Carbon Impact Modeling with Water Consumption Tracking
 */

// Official Impact Coefficients (kg CO2e)
const EMISSION_DATA = {
    elec_kwh: 0.198,
    gas_kwh: 0.183,
    water_liter: 0.0003, // Low impact per liter, but adds up!
    petrol_mile: 0.275,
    diesel_mile: 0.310,
    ev_mile: 0.052,
    waste_bag: 0.520
};

// Regional Leaderboard State - 5 Users per Sheffield Hallam Local Area
let userBase = [
    { name: "EcoSheff_1", total: 110, area: "Sheffield" },
    { name: "Hallam_Green", total: 240, area: "Sheffield" },
    { name: "SteelCity_User", total: 320, area: "Sheffield" },
    { name: "Hillsborough_Pioneer", total: 180, area: "Sheffield" },
    { name: "Cooked_Citizen_99", total: 610, area: "Sheffield" },
    
    { name: "Chessie_Low", total: 95, area: "Chesterfield" },
    { name: "Crooked_Eco", total: 205, area: "Chesterfield" },
    { name: "Peak_Explorer", total: 130, area: "Chesterfield" },
    { name: "Spire_User", total: 310, area: "Chesterfield" },
    { name: "NahBro_Chessie", total: 585, area: "Chesterfield" },

    { name: "Rother_Green", total: 120, area: "Rotherham" },
    { name: "Magna_Impact", total: 290, area: "Rotherham" },
    { name: "Parkgate_Eco", total: 340, area: "Rotherham" },
    { name: "Wickersley_Cutter", total: 155, area: "Rotherham" },
    { name: "Rother_Burner", total: 540, area: "Rotherham" },

    { name: "Donny_Hero", total: 115, area: "Doncaster" },
    { name: "Racecourse_Eco", total: 275, area: "Doncaster" },
    { name: "Frenchgate_Cutter", total: 305, area: "Doncaster" },
    { name: "Donny_Logic", total: 195, area: "Doncaster" },
    { name: "Cooked_Donny", total: 620, area: "Doncaster" }
];

let syncFlag = false;
let appMode = "calc";

/**
 * Handles Complex Vehicle Selection Logic
 */
function toggleVehicleFields() {
    const config = document.getElementById('vehicle-setup').value;
    const pBox = document.getElementById('petrol-box');
    const dBox = document.getElementById('diesel-box');
    const eBox = document.getElementById('ev-box');

    // Reset visibility stack
    [pBox, dBox, eBox].forEach(el => el.classList.add('hidden'));

    if (config === 'petrol') pBox.classList.remove('hidden');
    if (config === 'ev') eBox.classList.remove('hidden');
    if (config === 'petrol-ev') { pBox.classList.remove('hidden'); eBox.classList.remove('hidden'); }
    if (config === 'petrol-diesel') { pBox.classList.remove('hidden'); dBox.classList.remove('hidden'); }
    
    calculateTotal();
}

/**
 * Main Calculation Sequence
 */
function calculateTotal() {
    const pullValue = (id) => Math.max(0, parseFloat(document.getElementById(id).value) || 0);

    // Retrieve Inputs
    const elec = pullValue('input-elec');
    const gas = pullValue('input-gas');
    const water = pullValue('input-water');
    const petrol = pullValue('input-petrol');
    const diesel = pullValue('input-diesel');
    const ev = pullValue('input-ev');
    const waste = pullValue('input-waste');

    document.getElementById('waste-display').innerText = waste;

    // Lifestyle Variables
    const houseM = parseFloat(document.getElementById('house-type').value);
    const dietM = parseFloat(document.getElementById('diet-type').value);

    // Core Calculation Logic
    let footprint = (
        (elec * EMISSION_DATA.elec_kwh) +
        (gas * EMISSION_DATA.gas_kwh) +
        (water * EMISSION_DATA.water_liter) +
        (petrol * EMISSION_DATA.petrol_mile) +
        (diesel * EMISSION_DATA.diesel_mile) +
        (ev * EMISSION_DATA.ev_mile) +
        (waste * EMISSION_DATA.waste_bag)
    ) * houseM * dietM;

    const mainDisplay = document.getElementById('total-output');
    const msg = document.getElementById('cooked-msg');

    // 🦈 Shark Easter Egg
    if (elec === 999 && gas === 999) {
        mainDisplay.innerHTML = "SHARK";
        mainDisplay.style.color = "#0ea5e9";
        msg.innerText = "🦈 SHARK MODE ACTIVE";
    } else {
        const rounded = Math.round(footprint);
        mainDisplay.innerHTML = rounded + '<span class="unit-label">Liters</span>';
        mainDisplay.style.color = "#0f172a";

        // Performance Check
        if (rounded > 500) msg.innerText = "💀 NAH BRO YOU'RE COOKED";
        else if (rounded > 350) msg.innerText = "🔥 High Carbon Impact";
        else msg.innerText = "";
    }

    updateVisualAnalytics(elec, gas, water, (petrol + diesel + ev));
    refreshEnvironmentalGrade(footprint);
}

/**
 * Updates the graphical bar system
 */
function updateVisualAnalytics(e, g, w, t) {
    const total = (e + g + w + t) || 1;
    document.getElementById('bar-elec').style.width = (e / total * 100) + "%";
    document.getElementById('bar-gas').style.width = (g / total * 100) + "%";
    document.getElementById('bar-water').style.width = (w / total * 100) + "%";
    document.getElementById('bar-transport').style.width = (t / total * 100) + "%";
}

function refreshEnvironmentalGrade(val) {
    const badge = document.getElementById('grade-badge');
    if (val < 150) { badge.innerText = "Grade: A"; badge.style.background = "#10b981"; }
    else if (val < 400) { badge.innerText = "Grade: C"; badge.style.background = "#f59e0b"; }
    else { badge.innerText = "Grade: F"; badge.style.background = "#ef4444"; }
}

/**
 * View Logic: Transitions between Calculator and Leaderboard
 */
function toggleView() {
    const calc = document.getElementById('calc-display');
    const rank = document.getElementById('rankings-display');
    const btn = document.getElementById('toggle-view-btn');

    if (appMode === "calc") {
        calc.classList.add('hidden');
        rank.classList.remove('hidden');
        btn.innerText = "📊 Go to Calculator";
        appMode = "rank";
        renderLeaderboard();
    } else {
        rank.classList.add('hidden');
        calc.classList.remove('hidden');
        btn.innerText = "🏆 View Rankings";
        appMode = "calc";
    }
}

/**
 * Renders the regional ranking list
 */
function renderLeaderboard() {
    const area = document.getElementById('user-area').value;
    const list = document.getElementById('leaderboard-list');
    document.getElementById('area-title').innerText = area ? `${area} Performance` : "Regional Rankings";

    let data = area ? userBase.filter(u => u.area === area) : userBase;

    list.innerHTML = data.sort((a,b) => a.total - b.total)
        .map(u => `
            <div style="display:flex; justify-content:space-between; align-items:center; padding:15px; background:#f1f5f9; border-radius:15px; margin-bottom:10px; border: 1px solid #e2e8f0;">
                <div style="display:flex; flex-direction:column;">
                    <span style="font-weight:800; font-size:1rem;">${u.name}</span>
                    ${u.total > 500 ? '<span style="font-size:0.65rem; color:#ef4444; font-weight:900;">NAH BRO COOKED 💀</span>' : ''}
                </div>
                <b style="color:#10b981; font-size:1rem;">${u.total} Liters</b>
            </div>
        `).join('');
}

function sendToDatabaseTable() {
    if (syncFlag) return alert("System message: Data already synced.");
    const area = document.getElementById('user-area').value;
    if (!area) return alert("Please select a location.");

    const score = parseInt(document.getElementById('total-output').innerText) || 0;
    userBase.push({ name: "YOU (Live)", total: score, area: area });
    
    syncFlag = true;
    document.querySelector('.sync-btn').innerText = "Synced ✅";
    renderLeaderboard();
}

function handleAreaChange() { if (appMode === "rank") renderLeaderboard(); }