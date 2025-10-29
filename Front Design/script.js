// Sidebar Toggle
const sidebar = document.getElementById("sidebar");
document.getElementById("menuToggle").onclick = () => sidebar.classList.add("show");
document.getElementById("closeSidebar").onclick = () => sidebar.classList.remove("show");

// Event Data
const events = [
  "Elanoire Maggie — Sick Leave",
  "Kevin Malona — Annual Leave",
  "Jeremy Gemoy — Work From Home"
];

const eventList = document.getElementById("eventList");
events.forEach(e => {
  let li = document.createElement("li");
  li.textContent = e;
  eventList.appendChild(li);
});

// Chart.js
const ctx = document.getElementById("barChart");
new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ["Jan 24", "Jan 25", "Jan 26", "Jan 27", "Jan 28"],
    datasets: [{
      label: "Work Hours",
      data: [8, 7, 9, 6, 8],
      borderWidth: 1
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display:false } },
  }
});
