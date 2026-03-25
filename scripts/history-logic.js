

const SETTINGS = {
    itemsPerPage: 5,
    storageKey: "ecoHistory"
};

let state = {
    currentPage: 1,
    historyData: []
};

// --- 1. Data Management ---
const getHistory = () => JSON.parse(localStorage.getItem(SETTINGS.storageKey)) || [];

const calculateStats = (data) => {
    if (!data.length) return { avg: 0, best: 0, worst: 0 };
    
    const totals = data.map(e => e.total);
    return {
        count: data.length,
        avg: (totals.reduce((a, b) => a + b, 0) / data.length).toFixed(1),
        best: Math.min(...totals),
        worst: Math.max(...totals)
    };
};

// --- 2. UI Rendering ---
const renderTable = () => {
    const data = getHistory();
    const start = (state.currentPage - 1) * SETTINGS.itemsPerPage;
    const paginatedData = data.slice(start, start + SETTINGS.itemsPerPage);

    const tableHTML = paginatedData.map(entry => `
        <tr class="${getRowClass(entry.total)}">
            <td>${entry.date}</td>
            <td>${entry.total} kg</td>
            <td><span class="grade-pill">${entry.grade}</span></td>
            <td><button onclick="deleteEntry('${entry.date}')" class="delete-btn">🗑️</button></td>
        </tr>
    `).join('');

    document.getElementById("table-body").innerHTML = tableHTML || "<tr><td colspan='4'>No data yet.</td></tr>";
    updateStatsUI(data);
};

const getRowClass = (total) => {
    if (total < 80) return "row-best";
    if (total > 200) return "row-worst";
    return "";
};

const updateStatsUI = (data) => {
    const stats = calculateStats(data);
    document.getElementById("stat-entries").innerText = stats.count || 0;
    document.getElementById("stat-avg").innerText = stats.avg || '--';
    document.getElementById("stat-best").innerText = stats.best || '--';
    document.getElementById("stat-worst").innerText = stats.worst || '--';
};

// --- 3. Actions ---
const deleteEntry = (date) => {
    const filtered = getHistory().filter(e => e.date !== date);
    localStorage.setItem(SETTINGS.storageKey, JSON.stringify(filtered));
    renderTable();
};

const clearAllHistory = () => {
    if (confirm("Clear all data?")) {
        localStorage.removeItem(SETTINGS.storageKey);
        renderTable();
    }
};

// Initialize
document.addEventListener('DOMContentLoaded', renderTable);
