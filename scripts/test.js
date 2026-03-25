/**
 * ECOTRACKER PRO - HISTORY PAGE LOGIC
 * Reads from localStorage key "ecoHistory"
 */

const PER_PAGE = 5;
let currentPage = 1;
let trendChart  = null;

/* ---------- BADGES ---------- */
const BADGES = [
    { id: "green_week",  emoji: "♻️",  label: "Green Week",     desc: "Scored under 80 kg in a week",              check: (data) => data.some(e => e.total < 80) },
    { id: "improving",   emoji: "📈",  label: "Improving!",     desc: "Your latest entry is lower than your first", check: (data) => data.length >= 2 && data[data.length-1].total < data[0].total },
    { id: "consistent",  emoji: "🎯",  label: "Consistent",     desc: "5 or more entries saved",                   check: (data) => data.length >= 5 },
    { id: "eco_hero",    emoji: "🌱",  label: "Eco Hero",       desc: "Averaged under 100 kg across all entries",   check: (data) => data.length >= 3 && (data.reduce((s,e) => s+e.total,0)/data.length) < 100 },
    { id: "below_avg",   emoji: "🇬🇧",  label: "Below Average",  desc: "Every entry is under the UK average (170kg)",check: (data) => data.length >= 1 && data.every(e => e.total < 170) },
    { id: "streak",      emoji: "🔥",  label: "On a Streak",    desc: "3 entries in a row getting better",         check: (data) => {
        if (data.length < 3) return false;
        const last3 = data.slice(-3);
        return last3[1].total < last3[0].total && last3[2].total < last3[1].total;
    }},
    { id: "first_save",  emoji: "🌍",  label: "First Step",     desc: "Saved your first entry",                    check: (data) => data.length >= 1 },
    { id: "planet",      emoji: "💚",  label: "Planet Saver",   desc: "Scored under 50 kg in a week",              check: (data) => data.some(e => e.total < 50) },
];

function getHistory() {
    return JSON.parse(localStorage.getItem("ecoHistory") || "[]");
}

function saveHistory(data) {
    localStorage.setItem("ecoHistory", JSON.stringify(data));
}

function gradeColor(grade) {
    const map = { A: "#2ecc71", B: "#84cc16", C: "#f59e0b", D: "#f97316", F: "#e74c3c" };
    return map[grade] || "#aaa";
}

/* ---------- STATS ---------- */
function renderStats(data) {
    document.getElementById("stat-entries").innerText = data.length;
    if (data.length === 0) {
        document.getElementById("stat-avg").innerText   = "--";
        document.getElementById("stat-best").innerText  = "--";
        document.getElementById("stat-worst").innerText = "--";
        return;
    }
    const totals = data.map(e => e.total);
    const avg    = Math.round(totals.reduce((s, v) => s + v, 0) / totals.length);
    document.getElementById("stat-avg").innerText   = avg + " kg";
    document.getElementById("stat-best").innerText  = Math.min(...totals) + " kg";
    document.getElementById("stat-worst").innerText = Math.max(...totals) + " kg";
}

/* ---------- BADGES ---------- */
function renderBadges(data) {
    const container = document.getElementById("badges-container");
    const section   = document.getElementById("badges-section");
    if (!container || !section) return;

    const earned = BADGES.filter(b => b.check(data));

    if (earned.length === 0) {
        section.style.display = "none";
        return;
    }

    section.style.display = "block";
    container.innerHTML = earned.map(b => `
        <div class="badge-pill" title="${b.desc}">
            <span class="badge-emoji">${b.emoji}</span>
            <span class="badge-label">${b.label}</span>
        </div>
    `).join("");
}

/* ---------- TABLE ---------- */
function renderTable() {
    const all        = getHistory();
    const filterDate = document.getElementById("date-filter").value;

    const filtered = filterDate
        ? all.filter(e => {
            const parts = e.date.split("/");
            const iso   = `${parts[2]}-${parts[1].padStart(2,"0")}-${parts[0].padStart(2,"0")}`;
            return iso === filterDate;
        })
        : all;

    const sorted = [...filtered].reverse();

    renderStats(all);
    renderBadges(all);

    const totalPages = Math.max(1, Math.ceil(sorted.length / PER_PAGE));
    if (currentPage > totalPages) currentPage = totalPages;

    const start = (currentPage - 1) * PER_PAGE;
    const paged = sorted.slice(start, start + PER_PAGE);

    document.getElementById("btn-prev").disabled = currentPage <= 1;
    document.getElementById("btn-next").disabled = currentPage >= totalPages;

    const container = document.getElementById("table-container");

    if (sorted.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <div class="empty-icon">📭</div>
                <p>No history saved yet.<br>Head to the <a href="calculator.php" style="color:#2ecc71;">Carbon Calculator</a> and click <b>Save to History</b>!</p>
            </div>`;
        document.getElementById("showing-text").innerText = "";
        document.getElementById("trend-section").style.display = "none";
        return;
    }

    // Find best and worst total values for row highlighting
    const allTotals = sorted.map(e => e.total);
    const bestVal   = Math.min(...allTotals);
    const worstVal  = Math.max(...allTotals);

    container.innerHTML = `
        <table class="history-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Source</th>
                    <th>Grade</th>
                    <th>Weekly CO2</th>
                    <th>vs UK Avg</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                ${paged.map((e, i) => {
                    const globalIndex = all.length - 1 - (start + i);
                    const diff    = e.total - 170;
                    const diffStr = diff > 0
                        ? `<span style="color:#e74c3c">+${diff} kg</span>`
                        : `<span style="color:#2ecc71">${diff} kg</span>`;

                    let rowClass = "";
                    let rowBadge = "";
                    if (e.total === bestVal)  { rowClass = "row-best";  rowBadge = " 🏆"; }
                    if (e.total === worstVal && bestVal !== worstVal) { rowClass = "row-worst"; rowBadge = " ⚠️"; }

                    return `
                    <tr class="${rowClass}">
                        <td>${e.date}${rowBadge}</td>
                        <td>Calculator</td>
                        <td><span class="grade-pill" style="background:${gradeColor(e.grade)}">${e.grade}</span></td>
                        <td class="${e.total < 170 ? "total-good" : "total-bad"}">${e.total} kg</td>
                        <td>${diffStr}</td>
                        <td>
                            <button class="delete-btn" onclick="deleteEntry(${globalIndex})" title="Delete this entry">🗑️</button>
                        </td>
                    </tr>`;
                }).join("")}
            </tbody>
        </table>`;

    document.getElementById("showing-text").innerText =
        `Showing ${start + 1}–${Math.min(start + PER_PAGE, sorted.length)} of ${sorted.length} ${sorted.length === 1 ? "entry" : "entries"}`;

    renderTrendChart(all);
}

/* ---------- DELETE SINGLE ENTRY ---------- */
function deleteEntry(index) {
    if (!confirm("Delete this entry?")) return;
    const data = getHistory();
    data.splice(index, 1);
    saveHistory(data);
    renderTable();
}

/* ---------- EXPORT PDF ---------- */
function exportCSV() {
    const data = getHistory();
    if (data.length === 0) { alert("No history to export!"); return; }

    // Load jsPDF dynamically then generate
    const script = document.createElement("script");
    script.src   = "https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js";
    script.onload = () => generatePDF(data);
    document.head.appendChild(script);
}

function generatePDF(data) {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    const green  = [46, 204, 113];
    const dark   = [44, 62, 80];
    const grey   = [127, 140, 141];
    const red    = [231, 76, 60];
    const light  = [244, 247, 246];

    const pageW = doc.internal.pageSize.getWidth();

    // Header bar
    doc.setFillColor(...green);
    doc.rect(0, 0, pageW, 28, "F");

    // Logo text
    doc.setTextColor(255, 255, 255);
    doc.setFontSize(20);
    doc.setFont("helvetica", "bold");
    doc.text("ECOTRACKER", 14, 17);
    doc.setFontSize(9);
    doc.setFont("helvetica", "normal");
    doc.text("Smart Household Carbon Footprint Calculator", 14, 24);

    // Title
    doc.setTextColor(...dark);
    doc.setFontSize(16);
    doc.setFont("helvetica", "bold");
    doc.text("Activity History Report", 14, 42);

    doc.setFontSize(9);
    doc.setFont("helvetica", "normal");
    doc.setTextColor(...grey);
    doc.text(`Generated: ${new Date().toLocaleDateString("en-GB")}`, 14, 49);

    // Stats boxes
    const totals  = data.map(e => e.total);
    const avg     = Math.round(totals.reduce((s,v) => s+v, 0) / totals.length);
    const best    = Math.min(...totals);
    const worst   = Math.max(...totals);

    const stats = [
        { label: "Total Entries", val: data.length },
        { label: "Weekly Average", val: avg + " kg" },
        { label: "Best Week",  val: best + " kg" },
        { label: "Worst Week", val: worst + " kg" },
    ];

    let sx = 14;
    stats.forEach(s => {
        doc.setFillColor(...light);
        doc.roundedRect(sx, 55, 42, 20, 3, 3, "F");
        doc.setTextColor(...green);
        doc.setFontSize(13);
        doc.setFont("helvetica", "bold");
        doc.text(String(s.val), sx + 21, 65, { align: "center" });
        doc.setTextColor(...grey);
        doc.setFontSize(7);
        doc.setFont("helvetica", "normal");
        doc.text(s.label, sx + 21, 71, { align: "center" });
        sx += 46;
    });

    // Table header
    let y = 85;
    doc.setFillColor(...green);
    doc.rect(14, y, pageW - 28, 9, "F");
    doc.setTextColor(255, 255, 255);
    doc.setFontSize(8);
    doc.setFont("helvetica", "bold");
    doc.text("Date",         18,  y + 6);
    doc.text("Source",       55,  y + 6);
    doc.text("Grade",        95,  y + 6);
    doc.text("CO2 (kg)",    125,  y + 6);
    doc.text("vs UK Avg",   165,  y + 6);
    y += 9;

    // Sort oldest first
    const sorted = [...data].sort((a, b) => {
        const toDate = d => { const p = d.split("/"); return new Date(p[2], p[1]-1, p[0]); };
        return toDate(a.date) - toDate(b.date);
    });

    sorted.forEach((e, i) => {
        // Alternate row shading
        if (i % 2 === 0) {
            doc.setFillColor(248, 250, 252);
            doc.rect(14, y, pageW - 28, 9, "F");
        }

        const diff    = e.total - 170;
        const diffStr = (diff > 0 ? "+" : "") + diff + " kg";

        doc.setTextColor(...dark);
        doc.setFontSize(8);
        doc.setFont("helvetica", "normal");
        doc.text(e.date,       18,  y + 6);
        doc.text("Calculator", 55,  y + 6);

        // Grade pill colour
        const gradeColors = { A: green, B: [132,204,22], C: [245,158,11], D: [249,115,22], F: red };
        const gc = gradeColors[e.grade] || grey;
        doc.setFillColor(...gc);
        doc.roundedRect(93, y + 1, 14, 7, 2, 2, "F");
        doc.setTextColor(255,255,255);
        doc.setFont("helvetica", "bold");
        doc.text(e.grade, 100, y + 6.5, { align: "center" });

        doc.setFont("helvetica", "normal");
        doc.setTextColor(e.total < 170 ? green[0] : red[0], e.total < 170 ? green[1] : red[1], e.total < 170 ? green[2] : red[2]);
        doc.text(e.total + " kg", 125, y + 6);

        doc.setTextColor(diff > 0 ? red[0] : green[0], diff > 0 ? red[1] : green[1], diff > 0 ? red[2] : green[2]);
        doc.text(diffStr, 165, y + 6);

        y += 9;

        // New page if needed
        if (y > 270) {
            doc.addPage();
            y = 20;
        }
    });

    // Footer
    doc.setDrawColor(...green);
    doc.setLineWidth(0.5);
    doc.line(14, 282, pageW - 14, 282);
    doc.setTextColor(...grey);
    doc.setFontSize(7);
    doc.setFont("helvetica", "normal");
    doc.text("EcoTracker Pro | Smart Household", 14, 288);
    doc.text("UK average = ~170 kg CO2e/week", pageW - 14, 288, { align: "right" });

    doc.save("ecotracker-history.pdf");
}

/* ---------- TREND CHART ---------- */
function renderTrendChart(data) {
    const section = document.getElementById("trend-section");
    if (data.length < 2) { section.style.display = "none"; return; }
    section.style.display = "block";

    const sorted = [...data].sort((a, b) => {
        const toDate = d => { const p = d.split("/"); return new Date(p[2], p[1]-1, p[0]); };
        return toDate(a.date) - toDate(b.date);
    });

    const labels = sorted.map(e => e.date);
    const values = sorted.map(e => e.total);

    // Colour each point based on grade
    const pointColors = sorted.map(e => gradeColor(e.grade));

    const ctx = document.getElementById("trend-chart");
    if (trendChart) trendChart.destroy();

    trendChart = new Chart(ctx, {
        type: "line",
        data: {
            labels,
            datasets: [
                {
                    label: "Your weekly CO2 (kg)",
                    data: values,
                    borderColor: "#2ecc71",
                    backgroundColor: "rgba(46,204,113,0.1)",
                    borderWidth: 3,
                    pointBackgroundColor: pointColors,
                    pointRadius: 6,
                    tension: 0.4,
                    fill: true
                },
                {
                    label: "UK Average (170 kg)",
                    data: Array(labels.length).fill(170),
                    borderColor: "#e74c3c",
                    borderWidth: 2,
                    borderDash: [6, 4],
                    pointRadius: 0,
                    fill: false
                }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: "bottom" } },
            scales: {
                y: { beginAtZero: true, title: { display: true, text: "kg CO2e / week" } }
            }
        }
    });
}

function changePage(dir) {
    currentPage += dir;
    renderTable();
}

function clearHistory() {
    if (!confirm("Delete ALL saved history? This cannot be undone.")) return;
    localStorage.removeItem("ecoHistory");
    currentPage = 1;
    renderTable();
}

/* ---------- SEED TEST DATA (remove before production) ---------- */
function seedTestData() {
    const existing = getHistory();
    if (existing.length > 0) return; // don't overwrite real data

    const testData = [
        { date: "03/01/2026", total: 312, grade: "F" },  // terrible — January
        { date: "10/01/2026", total: 287, grade: "F" },  // still bad
        { date: "17/01/2026", total: 254, grade: "D" },  // getting worse then better
        { date: "24/01/2026", total: 231, grade: "D" },  // slowly improving
        { date: "31/01/2026", total: 198, grade: "D" },  // above avg
        { date: "07/02/2026", total: 176, grade: "C" },  // just above avg
        { date: "14/02/2026", total: 163, grade: "C" },  // around avg
        { date: "21/02/2026", total: 145, grade: "C" },  // below avg now
        { date: "28/02/2026", total: 118, grade: "B" },  // good
        { date: "07/03/2026", total: 94,  grade: "B" },  // well below avg
        { date: "14/03/2026", total: 72,  grade: "A" },  // great
        { date: "16/03/2026", total: 48,  grade: "A" },  // amazing
    ];

    saveHistory(testData);
}

seedTestData();

renderTable();
