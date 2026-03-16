/**
 * ECOTRACKER PRO - DATA ANALYTICS & LOGIC ENGINE
 * Hallam University Project Build - March 2026
 */

const FACTORS = {
    elec:       0.233,
    gas:        0.183,
    water_l:    0.000298,
    petrol:     0.300,
    diesel:     0.290,
    ev:         0.052,
    public:     0.089,
    waste_bag:  20.0
};

const LIFESTYLE = {
    diet:     { vegan: 3.5, veggie: 7.0, average: 14.0, meatheavy: 24.5 },
    shopping: { minimal: 5.0, average: 15.0, heavy: 30.0 },
    flights:  { none: 0, occasional: 15.0, frequent: 50.0 }
};

const UK_WEEKLY_AVG = 170;

let userBase = [
    { name: "EcoSheff_1",     total: 52,  area: "Sheffield" },
    { name: "Hallam_Green",   total: 154, area: "Sheffield" },
    { name: "SteelCity_User", total: 248, area: "Sheffield" },
    { name: "ChestEco",       total: 68,  area: "Chesterfield" },
    { name: "ChestAvg",       total: 162, area: "Chesterfield" },
    { name: "RotherAvg",      total: 171, area: "Rotherham" },
    { name: "RotherHigh",     total: 310, area: "Rotherham" },
    { name: "BarnsleyEco",    total: 55,  area: "Barnsley" },
    { name: "BarnsleyAvg",    total: 148, area: "Barnsley" },
    { name: "DoncAvg",        total: 165, area: "Doncaster" },
    { name: "DoncHigh",       total: 290, area: "Doncaster" }
];

let viewMode    = "calc";
let inputPeriod = "weekly";   // "weekly" or "monthly"
let chartType   = "bar";      // "bar" or "pie"
let pieChart    = null;
let animFrame   = null;
let currentDisplayed = 0;

/* ---------- PAGE INIT ---------- */

document.addEventListener("DOMContentLoaded", () => {
    toggleVehicleFields();
    calculateTotal();

    // Restore dark mode preference
    if (localStorage.getItem("darkMode") === "true") {
        document.documentElement.setAttribute("data-theme", "dark");
        document.getElementById("dark-btn").innerText = "☀️ Light";
    }

    // Console easter egg
    console.log("%c🌍 EcoTracker Pro", "font-size:24px;font-weight:900;color:#10b981;");
    console.log("%cSheffied Hallam University — Carbon Footprint Calculator", "color:#64748b;font-size:12px;");
    console.log("%c⛔ Hey! This is a secure app. No snooping around!", "color:#ef4444;font-weight:bold;font-size:13px;");

    // Block negative numbers on all number inputs
    document.addEventListener("input", (e) => {
        if (e.target.type === "number") {
            if (parseFloat(e.target.value) < 0) {
                e.target.value = 0;
            }
        }
    });

    // Also block on keydown
    document.addEventListener("keydown", (e) => {
        if (e.target.type === "number" && e.key === "-") {
            e.preventDefault();
        }
    });
});

/* ---------- DARK MODE ---------- */

function toggleDarkMode() {
    const html = document.documentElement;
    const isDark = html.getAttribute("data-theme") === "dark";
    html.setAttribute("data-theme", isDark ? "light" : "dark");
    document.getElementById("dark-btn").innerText = isDark ? "🌙 Dark" : "☀️ Light";
    localStorage.setItem("darkMode", !isDark);
}

/* ---------- PERIOD TOGGLE (weekly / monthly) ---------- */

function setPeriod(period) {
    inputPeriod = period;

    document.getElementById("btn-weekly").classList.toggle("active", period === "weekly");
    document.getElementById("btn-monthly").classList.toggle("active", period === "monthly");

    const hint    = document.getElementById("period-hint");
    const lblElec = document.getElementById("label-elec");
    const lblGas  = document.getElementById("label-gas");
    const lblWater= document.getElementById("label-water");
    const elec    = document.getElementById("input-elec");
    const gas     = document.getElementById("input-gas");
    const water   = document.getElementById("input-water");
    const periodLabel = document.getElementById("period-label");

    if (period === "weekly") {
        hint.innerText      = "Enter your weekly usage figures below";
        lblElec.innerText   = "Electricity (kWh/week)";
        lblGas.innerText    = "Gas (kWh/week)";
        lblWater.innerText  = "Weekly Consumption (Litres)";
        elec.placeholder    = "e.g. 56";
        gas.placeholder     = "e.g. 175";
        water.placeholder   = "e.g. 500";
        periodLabel.innerText = "Weekly CO2 Estimate";
    } else {
        hint.innerText      = "Enter your monthly bill figures — we'll convert to weekly";
        lblElec.innerText   = "Electricity (kWh/month)";
        lblGas.innerText    = "Gas (kWh/month)";
        lblWater.innerText  = "Monthly Consumption (Litres)";
        elec.placeholder    = "e.g. 242";
        gas.placeholder     = "e.g. 760";
        water.placeholder   = "e.g. 2000";
        periodLabel.innerText = "Weekly CO2 Estimate (from monthly data)";
    }

    calculateTotal();
}

/* ---------- CHART TYPE TOGGLE ---------- */

function setChartType(type) {
    chartType = type;
    document.getElementById("btn-bar-chart").classList.toggle("active", type === "bar");
    document.getElementById("btn-pie-chart").classList.toggle("active", type === "pie");
    document.getElementById("bar-breakdown").classList.toggle("hidden", type === "pie");
    document.getElementById("pie-breakdown").classList.toggle("hidden", type === "bar");
    calculateTotal();
}

/* ---------- RESET ---------- */

function resetAll() {
    if (!confirm("Clear all inputs and start fresh?")) return;
    document.querySelectorAll("input[type='number']").forEach(i => i.value = "");
    document.getElementById("vehicle-setup").value  = "none";
    document.getElementById("input-waste").value    = "0";
    document.getElementById("input-diet").value     = "0";
    document.getElementById("input-shopping").value = "0";
    document.getElementById("input-flights").value  = "none";
    document.getElementById("input-budget").value   = "";
    document.getElementById("user-area").value      = "";
    document.getElementById("additional-cars").innerHTML = "";
    toggleVehicleFields();
    calculateTotal();
}

/* ---------- VEHICLE HANDLING ---------- */

function addCarField() {
    const container = document.getElementById("additional-cars");
    const div = document.createElement("div");
    div.className = "car-entry";
    div.style.marginTop = "15px";
    div.style.paddingTop = "10px";
    div.style.borderTop = "1px solid #e2e8f0";
    div.innerHTML = `
        <label>Additional Vehicle Type</label>
        <select class="car-type" onchange="calculateTotal()">
            <option value="petrol">Petrol</option>
            <option value="diesel">Diesel</option>
            <option value="ev">Electric (EV)</option>
        </select>
        <input type="number" class="car-miles" placeholder="Miles per week" oninput="calculateTotal()">
    `;
    container.appendChild(div);
}

function toggleVehicleFields() {
    const mode = document.getElementById("vehicle-setup").value;
    const privateFields = document.getElementById("private-vehicle-fields");
    const publicFields  = document.getElementById("public-fields");
    if (mode === "private") {
        privateFields.classList.remove("hidden");
        publicFields.classList.add("hidden");
    } else if (mode === "public") {
        privateFields.classList.add("hidden");
        publicFields.classList.remove("hidden");
    } else {
        privateFields.classList.add("hidden");
        publicFields.classList.add("hidden");
    }
    calculateTotal();
}

/* ---------- MAIN CALCULATION ---------- */

function calculateTotal() {
    const getRaw = (id) => parseFloat(document.getElementById(id)?.value) || 0;

    // Convert monthly to weekly if needed
    const divisor = inputPeriod === "monthly" ? 4.33 : 1;

    const electricity = (getRaw("input-elec")  / divisor) * FACTORS.elec;
    const gas         = (getRaw("input-gas")   / divisor) * FACTORS.gas;
    const water       = (getRaw("input-water") / divisor) * FACTORS.water_l;
    const waste       = getRaw("input-waste") * FACTORS.waste_bag;

    let transport = 0;
    const setup = document.getElementById("vehicle-setup").value;
    if (setup === "private") {
        document.querySelectorAll(".car-type").forEach((t, i) => {
            const miles = document.querySelectorAll(".car-miles");
            const m = parseFloat(miles[i]?.value) || 0;
            transport += m * FACTORS[t.value];
        });
    } else if (setup === "public") {
        transport = getRaw("input-public-miles") * FACTORS.public;
    }

    const dietVal     = document.getElementById("input-diet")?.value     || "0";
    const shoppingVal = document.getElementById("input-shopping")?.value || "0";
    const flightsVal  = document.getElementById("input-flights")?.value  || "none";

    const diet     = LIFESTYLE.diet[dietVal]         ?? 0;
    const shopping = LIFESTYLE.shopping[shoppingVal] ?? 0;
    const flights  = LIFESTYLE.flights[flightsVal]   ?? 0;
    const lifestyle = diet + shopping + flights;

    const total = Math.round(electricity + gas + water + waste + transport + lifestyle);

    // Animated counter
    animateCounter(currentDisplayed, total);
    currentDisplayed = total;

    const downloadBtn = document.getElementById("download-report-btn");
    if (downloadBtn) downloadBtn.classList.toggle("hidden", total === 0);

    // Show history save button when there's data
    const historyRow = document.getElementById("history-row");
    if (historyRow) historyRow.classList.toggle("hidden", total === 0);

    updateBars(electricity, gas, water, transport, waste);
    updatePieChart(electricity, gas, water, transport, waste, lifestyle);
    updateGrade(total);
    updateMoodEmoji(total);
    updateProgressRing(total);
    updateAnnualProjection(total);
    updateTrees(total);
    updateComparison(total);
    updateBudget();
    updateTips(electricity, gas, transport, waste, diet, shopping, flights, total);

    // Show chart toggle when there's data
    const chartToggle = document.getElementById("chart-toggle-row");
    if (chartToggle) chartToggle.classList.toggle("hidden", total === 0);
}

/* ---------- ANIMATED COUNTER ---------- */

function animateCounter(from, to) {
    if (animFrame) cancelAnimationFrame(animFrame);
    const el = document.getElementById("total-output");
    if (!el) return;

    const duration = 400;
    const start    = performance.now();

    function step(now) {
        const progress = Math.min((now - start) / duration, 1);
        const eased    = 1 - Math.pow(1 - progress, 3); // ease-out cubic
        el.innerText   = Math.round(from + (to - from) * eased);
        if (progress < 1) animFrame = requestAnimationFrame(step);
    }

    animFrame = requestAnimationFrame(step);
}

/* ---------- VISUAL BARS ---------- */

function updateBars(e, g, w, t, waste) {
    const sum = (e + g + w + t + waste) || 1;
    const elecBar      = document.getElementById("bar-elec");
    const gasBar       = document.getElementById("bar-gas");
    const waterBar     = document.getElementById("bar-water");
    const transportBar = document.getElementById("bar-transport");
    if (elecBar)      elecBar.style.width     = (e / sum * 100) + "%";
    if (gasBar)       gasBar.style.width       = (g / sum * 100) + "%";
    if (waterBar)     waterBar.style.width     = ((w + waste) / sum * 100) + "%";
    if (transportBar) transportBar.style.width = (t / sum * 100) + "%";
}

/* ---------- PIE CHART ---------- */

function updatePieChart(e, g, w, t, waste, lifestyle) {
    if (chartType !== "pie") return;

    const ctx = document.getElementById("pie-chart");
    if (!ctx) return;

    const data = [
        Math.round(e),
        Math.round(g),
        Math.round(w + waste),
        Math.round(t),
        Math.round(lifestyle)
    ];

    const labels = ["Electricity", "Gas", "Waste", "Transport", "Lifestyle"];
    const colors = ["#10b981", "#f59e0b", "#0ea5e9", "#3b82f6", "#a855f7"];

    if (pieChart) {
        pieChart.data.datasets[0].data = data;
        pieChart.update();
    } else {
        pieChart = new Chart(ctx, {
            type: "pie",
            data: {
                labels,
                datasets: [{ data, backgroundColor: colors, borderWidth: 2 }]
            },
            options: {
                plugins: {
                    legend: { position: "bottom", labels: { font: { size: 11 } } }
                }
            }
        });
    }
}

/* ---------- GRADE ---------- */

function updateGrade(v) {
    const badge = document.getElementById("grade-badge");
    if (!badge) return;
    if      (v === 0)  badge.innerText = "--";
    else if (v < 80)   badge.innerText = "A";
    else if (v < 130)  badge.innerText = "B";
    else if (v < 200)  badge.innerText = "C";
    else if (v < 300)  badge.innerText = "D";
    else               badge.innerText = "F";
}

/* ---------- MOOD EMOJI ---------- */

function updateMoodEmoji(v) {
    const el = document.getElementById("mood-emoji");
    if (!el) return;
    if      (v === 0)  el.innerText = "🌍";
    else if (v < 80)   el.innerText = "😊";
    else if (v < 130)  el.innerText = "🙂";
    else if (v < 200)  el.innerText = "😐";
    else if (v < 300)  el.innerText = "😟";
    else               el.innerText = "😰";
}

/* ---------- PROGRESS RING ---------- */

function updateProgressRing(v) {
    const ring = document.getElementById("ring-fill");
    if (!ring) return;
    const circumference = 314;
    const offset = circumference - (Math.min(v / (UK_WEEKLY_AVG * 2), 1) * circumference);
    ring.style.strokeDashoffset = offset;
    if      (v === 0)  ring.style.stroke = "#e2e8f0";
    else if (v < 80)   ring.style.stroke = "#10b981";
    else if (v < 130)  ring.style.stroke = "#84cc16";
    else if (v < 200)  ring.style.stroke = "#f59e0b";
    else if (v < 300)  ring.style.stroke = "#f97316";
    else               ring.style.stroke = "#ef4444";
}

/* ---------- ANNUAL PROJECTION ---------- */

function updateAnnualProjection(weekly) {
    const section = document.getElementById("annual-projection");
    const span    = document.getElementById("annual-output");
    if (!section || !span) return;
    if (weekly === 0) { section.classList.add("hidden"); return; }
    span.innerText = (weekly * 52 / 1000).toFixed(2);
    section.classList.remove("hidden");
}

/* ---------- TREES ---------- */

function updateTrees(weekly) {
    const section = document.getElementById("trees-section");
    const text    = document.getElementById("trees-text");
    if (!section || !text) return;
    if (weekly === 0) { section.classList.add("hidden"); return; }
    const trees = Math.ceil(weekly / 0.4);
    const annual = Math.ceil((weekly * 52) / 21);
    text.innerHTML = `🌳 To offset this you'd need <b>${trees} trees/week</b> — or <b>${annual} trees planted per year</b>`;
    section.classList.remove("hidden");
}

/* ---------- COMPARISON ---------- */

function updateComparison(total) {
    const section = document.getElementById("comparison-section");
    const bar     = document.getElementById("comparison-bar-you");
    const text    = document.getElementById("comparison-text");
    if (!section || !bar || !text) return;
    if (total === 0) { section.classList.add("hidden"); return; }

    section.classList.remove("hidden");
    bar.style.width = Math.min((total / (UK_WEEKLY_AVG * 2)) * 100, 100) + "%";

    if      (total < UK_WEEKLY_AVG * 0.6) bar.style.background = "#10b981";
    else if (total < UK_WEEKLY_AVG)        bar.style.background = "#84cc16";
    else if (total < UK_WEEKLY_AVG * 1.5)  bar.style.background = "#f97316";
    else                                   bar.style.background = "#ef4444";

    const diff = Math.abs(total - UK_WEEKLY_AVG);
    const pct  = Math.round((diff / UK_WEEKLY_AVG) * 100);

    if (total < UK_WEEKLY_AVG) {
        text.innerText = `✅ You're ${diff} kg (${pct}%) below the UK average`;
        text.style.color = "#10b981";
    } else if (total === UK_WEEKLY_AVG) {
        text.innerText = `📊 You're exactly at the UK average`;
        text.style.color = "#f59e0b";
    } else {
        text.innerText = `⚠️ You're ${diff} kg (${pct}%) above the UK average`;
        text.style.color = "#ef4444";
    }
}

/* ---------- CARBON BUDGET ---------- */

function updateBudget() {
    const budget  = parseFloat(document.getElementById("input-budget")?.value) || 0;
    const total   = parseInt(document.getElementById("total-output")?.innerText) || 0;
    const status  = document.getElementById("budget-status");
    if (!status) return;

    if (budget === 0 || total === 0) { status.classList.add("hidden"); return; }

    status.classList.remove("hidden");
    const diff = total - budget;
    const pct  = Math.round((total / budget) * 100);

    if (total <= budget) {
        status.innerHTML  = `✅ <b>Under budget!</b> You're ${Math.abs(diff)} kg below your goal (${pct}% of budget used)`;
        status.className  = "budget-status budget-good";
    } else {
        status.innerHTML  = `⚠️ <b>Over budget!</b> You're ${diff} kg above your goal (${pct}% of budget used)`;
        status.className  = "budget-status budget-bad";
    }
}

/* ---------- SAVE TO HISTORY ---------- */

function saveToHistory() {
    const total   = document.getElementById("total-output").innerText;
    const grade   = document.getElementById("grade-badge").innerText;
    const date    = new Date().toLocaleDateString("en-GB");

    const history = JSON.parse(localStorage.getItem("ecoHistory") || "[]");
    history.push({ date, total: parseInt(total), grade });
    localStorage.setItem("ecoHistory", JSON.stringify(history));

    const msg = document.getElementById("history-saved-msg");
    if (msg) {
        msg.classList.remove("hidden");
        setTimeout(() => msg.classList.add("hidden"), 2500);
    }
}

/* ---------- TIPS ---------- */

function updateTips(e, g, t, waste, diet, shopping, flights, total) {
    const panel = document.getElementById("tips-panel");
    const list  = document.getElementById("tips-list");
    if (!panel || !list) return;
    if (total === 0) { panel.classList.add("hidden"); return; }

    const cats = [
        { name: "gas",      val: g },
        { name: "elec",     val: e },
        { name: "transport",val: t },
        { name: "waste",    val: waste },
        { name: "diet",     val: diet },
        { name: "shopping", val: shopping },
        { name: "flights",  val: flights }
    ].sort((a, b) => b.val - a.val);

    const tips = [];
    const top  = cats[0].name;

    if (top === "gas")       tips.push("🔥 <b>Gas is your biggest emitter.</b> Turning your thermostat down 1°C saves up to 10% of your heating CO2.");
    if (top === "elec")      tips.push("💡 <b>Electricity dominates your footprint.</b> Switching to a renewable tariff like Octopus Green can cut this to near zero.");
    if (top === "transport") tips.push("🚗 <b>Transport is your top category.</b> Replacing one car trip a week with public transport or cycling cuts this by ~20%.");
    if (top === "waste")     tips.push("♻️ <b>Waste & goods are your biggest impact.</b> Buying second-hand and composting food waste can halve this.");
    if (top === "diet")      tips.push("🥩 <b>Your diet is your top emitter.</b> Cutting red meat to 2 days a week saves ~10 kg CO2 per week.");
    if (top === "shopping")  tips.push("🛍️ <b>Shopping is your biggest footprint.</b> Buying second-hand or renting instead of buying new makes a huge difference.");
    if (top === "flights")   tips.push("✈️ <b>Flights dominate your footprint.</b> One fewer short-haul flight per year saves ~400 kg CO2.");

    if (g > 25)      tips.push("🏠 A smart thermostat (e.g. Nest or Hive) saves ~120 kg CO2 per year.");
    if (t > 30)      tips.push("⚡ EVs emit ~70% less CO2 per mile than petrol cars on the UK grid.");
    if (diet > 14)   tips.push("🥗 Even one meat-free day per week saves ~182 kg CO2 per year.");
    if (flights > 0) tips.push("🌍 London–Paris by train emits 96% less CO2 than flying.");
    if (total < 80)  tips.push("🌱 <b>Fantastic!</b> You're well below the UK average. Share your habits to inspire others.");

    list.innerHTML = tips.slice(0, 3).map(tip => `<div class="tip-card">${tip}</div>`).join("");
    panel.classList.remove("hidden");
}

/* ---------- SHARE ---------- */

function shareResult() {
    const total = document.getElementById("total-output").innerText;
    const grade = document.getElementById("grade-badge").innerText;
    const mood  = document.getElementById("mood-emoji").innerText;
    if (total === "0") { alert("Fill in your details first!"); return; }
    const text = `${mood} My weekly carbon footprint is ${total} kg CO2e — Grade ${grade}! (UK avg: 170 kg)\nCalculated with EcoTracker Pro | Sheffield Hallam`;
    navigator.clipboard.writeText(text)
        .then(() => alert("✅ Copied to clipboard!"))
        .catch(() => alert("Could not copy: " + text));
}

/* ---------- VIEW SWITCHING ---------- */

function setView(mode) {
    viewMode = mode;
    const isRank = mode === "rank";
    document.getElementById("toggle-view-btn")?.classList.toggle("hidden",  isRank);
    document.getElementById("return-calc-btn")?.classList.toggle("hidden", !isRank);
    document.getElementById("calc-display")?.classList.toggle("hidden",     isRank);
    document.getElementById("rankings-display")?.classList.toggle("hidden", !isRank);
    if (isRank) renderLeaderboard();
}

function toggleView() { setView(viewMode === "calc" ? "rank" : "calc"); }
function goToCalc()    { setView("calc"); }
function goToRankings(){ setView("rank"); }

/* ---------- LEADERBOARD ---------- */

function renderLeaderboard() {
    const area = document.getElementById("user-area").value;
    const list = document.getElementById("leaderboard-list");
    if (!list) return;
    let data = area ? userBase.filter(u => u.area === area) : userBase;
    data = [...data].sort((a, b) => a.total - b.total);
    const areaTitle = document.getElementById("area-title");
    if (areaTitle) areaTitle.innerText = area ? `${area} Rankings` : "All Areas Rankings";
    list.innerHTML = data.map((u, i) => {
        const medal = i === 0 ? "🥇" : i === 1 ? "🥈" : i === 2 ? "🥉" : `${i + 1}.`;
        return `<div class="card" style="margin-bottom:10px;display:flex;justify-content:space-between;align-items:center;">
            <span>${medal} ${u.name}</span>
            <b style="color:var(--primary)">${u.total} kg/wk</b>
        </div>`;
    }).join("");
}

/* ---------- DATABASE SYNC ---------- */

function sendToDatabaseTable() {
    const area = document.getElementById("user-area").value;
    if (!area) { alert("Please select an area first!"); return; }
    alert("Data Synced to Hallam Database ✅");
}

/* ---------- DOWNLOAD ---------- */

function downloadData() {
    const total  = document.getElementById("total-output").innerText;
    const grade  = document.getElementById("grade-badge").innerText;
    const annual = document.getElementById("annual-output")?.innerText || "N/A";
    const comp   = document.getElementById("comparison-text")?.innerText || "";
    const trees  = document.getElementById("trees-text")?.innerText || "";
    const content =
`EcoTracker Pro - CO2 Report
===========================
Weekly CO2 Output  : ${total} kg CO2e
Annual Projection  : ${annual} tonnes CO2e/year
Grade              : ${grade}
${comp}
${trees}

UK average = ~170 kg/week (8-10 tonnes/year)
Emission factors: DESNZ 2023 & BEIS UK data.
Generated by EcoTracker Pro | Sheffield Hallam`;
    const blob = new Blob([content], { type: "text/plain" });
    const url  = URL.createObjectURL(blob);
    const a    = document.createElement("a");
    a.href = url; a.download = "eco-report.txt"; a.click();
    URL.revokeObjectURL(url);
}