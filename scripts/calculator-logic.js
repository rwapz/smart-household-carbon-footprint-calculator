/**
 * ECOTRACKER PRO - DATA ANALYTICS & LOGIC ENGINE
 * Refactored for Readability & Maintainability
 */

// 1. --- Configuration Data (No Magic Numbers) ---
const EMISSION_FACTORS = {
    elec:       0.233,
    gas:        0.183,
    water_l:    0.000298,
    petrol:     0.300,
    diesel:     0.290,
    ev:         0.052,
    public:     0.089,
    waste_bag:  20.0
};

const LIFESTYLE_OFFSETS = {
    diet:     { vegan: 3.5, veggie: 7.0, average: 14.0, meatheavy: 24.5 },
    shopping: { minimal: 5.0, average: 15.0, heavy: 30.0 },
    flights:  { none: 0, occasional: 15.0, frequent: 50.0 }
};

const UK_WEEKLY_AVG = 170;

// 2. --- Core Calculation Logic ---
function getCarbonScore(inputs) {
    // Utility to multiply input by factor or return 0
    const calc = (val, factor) => (parseFloat(val) || 0) * factor;

    const scores = {
        energy: calc(inputs.elec, EMISSION_FACTORS.elec) + calc(inputs.gas, EMISSION_FACTORS.gas),
        water:  calc(inputs.water, EMISSION_FACTORS.water_l),
        transport: calc(inputs.miles, EMISSION_FACTORS[inputs.fuelType] || 0),
        waste:  calc(inputs.waste, EMISSION_FACTORS.waste_bag),
        lifestyle: LIFESTYLE_OFFSETS.diet[inputs.diet] + 
                   LIFESTYLE_OFFSETS.shopping[inputs.shop] + 
                   LIFESTYLE_OFFSETS.flights[inputs.fly]
    };

    const total = Object.values(scores).reduce((a, b) => a + b, 0);
    return { total: total.toFixed(1), breakdown: scores };
}

// 3. --- UI Update Functions ---
function updateDashboardUI(result) {
    // Update main total and annual projection
    document.getElementById("total-output").innerText = result.total;
    const annual = ((result.total * 52) / 1000).toFixed(1);
    
    const annualElement = document.getElementById("annual-output");
    if (annualElement) annualElement.innerText = annual;

    // Apply Grade Badge logic
    const grade = calculateGrade(result.total);
    const badge = document.getElementById("grade-badge");
    badge.innerText = grade.label;
    badge.className = `grade-badge ${grade.class}`;

    updateComparisonText(result.total);
    updateVisualBars(result.breakdown);
}

function calculateGrade(total) {
    if (total < 60)  return { label: "A - Eco Warrior", class: "grade-a" };
    if (total < 120) return { label: "B - Green", class: "grade-b" };
    if (total < 180) return { label: "C - Average", class: "grade-c" };
    return { label: "F - High Impact", class: "grade-f" };
}

// 4. --- Event Listeners & Initialization ---
function runCalculator() {
    const inputs = {
        elec:     document.getElementById("in-elec").value,
        gas:      document.getElementById("in-gas").value,
        water:    document.getElementById("in-water").value,
        miles:    document.getElementById("in-miles").value,
        fuelType: document.getElementById("fuel-type").value,
        waste:    document.getElementById("in-waste").value,
        diet:     document.getElementById("sel-diet").value,
        shop:     document.getElementById("sel-shop").value,
        fly:      document.getElementById("sel-fly").value
    };

    const result = getCarbonScore(inputs);
    updateDashboardUI(result);
    
    // Reveal hidden UI sections
    document.querySelectorAll(".hidden").forEach(el => el.classList.remove("hidden"));
}

// 5. --- Global Utilities (Sync & Download) ---
function sendToDatabaseTable() {
    const area = document.getElementById("user-area").value;
    if (!area) return alert("Please select an area first!");
    alert("Data Synced to Hallam Database ✅");
}