/**
 * Smart Household Calculator Logic
 * Includes area protection and dynamic rendering
 */

const FACTORS = { elec: 0.198, gas: 0.183, petrol: 0.275 };
let currentView = "calc";

function calculateTotal() {
    const elec = parseFloat(document.getElementById('input-elec').value) || 0;
    const gas = parseFloat(document.getElementById('input-gas').value) || 0;
    const petrol = parseFloat(document.getElementById('input-petrol').value) || 0;

    const total = (elec * FACTORS.elec) + (gas * FACTORS.gas) + (petrol * FACTORS.petrol);
    
    // Update Display
    document.getElementById('total-output').innerText = total.toFixed(2);
    
    // Update Progress Bar
    const sum = (elec + gas + petrol) || 1;
    document.getElementById('bar-elec').style.width = `${(elec/sum) * 100}%`;
    document.getElementById('bar-gas').style.width = `${(gas/sum) * 100}%`;
    document.getElementById('bar-petrol').style.width = `${(petrol/sum) * 100}%`;

    updateGrade(total);
}

function updateGrade(val) {
    const badge = document.getElementById('grade-badge');
    if (val < 50) { badge.innerText = "Grade: A"; badge.style.color = "#10b981"; }
    else if (val < 150) { badge.innerText = "Grade: C"; badge.style.color = "#f59e0b"; }
    else { badge.innerText = "Grade: F"; badge.style.color = "#ef4444"; }
}

function toggleView() {
    const calc = document.getElementById('calculator-view');
    const rank = document.getElementById('leaderboard-view');
    const btn = document.getElementById('toggle-view-btn');

    if (currentView === "calc") {
        calc.classList.add('hidden');
        rank.classList.remove('hidden');
        btn.innerText = "📊 Calculator";
        currentView = "rank";
    } else {
        rank.classList.add('hidden');
        calc.classList.remove('hidden');
        btn.innerText = "🏆 Leaderboard";
        currentView = "calc";
    }
}

function updateAreaDisplay() {
    const area = document.getElementById('user-area').value;
    document.getElementById('current-area-display').innerText = area || "Not Set";
}

function sendToDatabaseTable() {
    const area = document.getElementById('user-area').value;
    if (!area) return alert("Select your area first!");
    alert("Data saved for " + area + "!");
}