/**
 * CYBER SHIELD - Dashboard Visualization Engine (Chart.js)
 * File: assets/js/dashboard.js
 * BCA Final Year Project
 */

document.addEventListener('DOMContentLoaded', () => {
    // User Scan Analysis Chart (Doughnut)
    const userChartCanvas = document.getElementById('userScanDoughnutChart');
    if (userChartCanvas) {
        const safeCount = parseInt(userChartCanvas.dataset.safe || 0, 10);
        const suspCount = parseInt(userChartCanvas.dataset.suspicious || 0, 10);
        const phishCount = parseInt(userChartCanvas.dataset.phishing || 0, 10);
        const total = safeCount + suspCount + phishCount;

        new Chart(userChartCanvas, {
            type: 'doughnut',
            data: {
                labels: ['Safe URLs', 'Suspicious', 'Phishing Threats'],
                datasets: [{
                    data: total === 0 ? [1, 0, 0] : [safeCount, suspCount, phishCount],
                    backgroundColor: [
                        '#10b981',
                        '#f59e0b',
                        '#ef4444'
                    ],
                    borderColor: '#0b1120',
                    borderWidth: 3,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#94a3b8',
                            font: { family: 'Inter', size: 12 },
                            padding: 16
                        }
                    }
                }
            }
        });
    }

    // Admin Dashboard Daily Scan Line Chart
    const adminDailyChartCanvas = document.getElementById('adminDailyScansChart');
    if (adminDailyChartCanvas) {
        const labels = JSON.parse(adminDailyChartCanvas.dataset.labels || '[]');
        const values = JSON.parse(adminDailyChartCanvas.dataset.values || '[]');

        new Chart(adminDailyChartCanvas, {
            type: 'line',
            data: {
                labels: labels.length ? labels : ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Analyzed URLs',
                    data: values.length ? values : [12, 19, 15, 27, 34, 29, 45],
                    borderColor: '#00f3ff',
                    backgroundColor: 'rgba(0, 243, 255, 0.1)',
                    borderWidth: 2,
                    tension: 0.35,
                    fill: true,
                    pointBackgroundColor: '#00f3ff',
                    pointBorderColor: '#070b14',
                    pointBorderWidth: 2,
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(255, 255, 255, 0.05)' },
                        ticks: { color: '#64748b' }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(255, 255, 255, 0.05)' },
                        ticks: { color: '#64748b' }
                    }
                }
            }
        });
    }
});
