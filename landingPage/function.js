// assets/js/dashboard.js
let monthlySalesChartInstance = null;

// Fungsi Helper Global untuk Format Rupiah
const formatRupiah = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(value);
};

// Fungsi helper untuk fetching dan updating elemen
async function fetchDataAndUpdate(url, elementId, formatter, defaultValue = 'Rp0', errorMessage = 'Gagal memuat data') {
    const element = document.getElementById(elementId);
    if (!element) return;

    element.textContent = 'Memuat...';
    element.classList.add('loading-data');

    try {
        const response = await fetch(url);
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        
        const data = await response.json();
        if (data.success) {
            element.textContent = formatter(data);
        } else {
            element.textContent = defaultValue;
        }
    } catch (error) {
        console.error(`ERROR: ${elementId}:`, error);
        element.textContent = 'Error';
    } finally {
        element.classList.remove('loading-data');
    }
}

function updateClock() {
    const clockElement = document.getElementById('clock');
    if (clockElement) {
        clockElement.textContent = new Date().toLocaleTimeString('id-ID', { hour12: false });
    }
}

function updateAllStats() {
    // Sales Nett
    fetchDataAndUpdate('sales_nett_berjalan.php', 'earnedThisMonthValue', 
        (data) => formatRupiah(data.earned_this_month));

    // Sales Gross
    fetchDataAndUpdate('sales_gross_berjalan.php', 'grossThisMonthValue', 
        (data) => formatRupiah(data.gross_this_month));

    // Keuntungan/Margin
    fetchDataAndUpdate('margin_berjalan.php', 'profitMarginMonthValue', 
        (data) => formatRupiah(data.margin));

    // Member Belanja (Format Ribuan Biasa)
    fetchDataAndUpdate('qry_member_belanja.php', 'memberKhususBulanIni', 
        (data) => new Intl.NumberFormat('id-ID').format(data.total_member_khusus) + " member", '0 member');
}

function updateMonthlySalesChart() {
    const monthlySalesChartElement = document.getElementById('monthlySalesChart');
    if (!monthlySalesChartElement) return;

    const ctx = monthlySalesChartElement.getContext('2d');

    fetch('sales_bulanan.php')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.sales) {
                const labels = data.sales.map(item => item.month);
                const netSalesData = data.sales.map(item => Number(item.total_sales_numeric));
                const grossSalesData = data.sales.map(item => Number(item.total_gross_numeric));
                const marginData = data.sales.map(item => Number(item.total_margin_numeric));

                if (window.monthlySalesChartInstance) {
                    window.monthlySalesChartInstance.destroy();
                }

                window.monthlySalesChartInstance = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Sales Nett',
                                data: netSalesData,
                                borderColor: '#36A2EB',
                                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                                borderWidth: 3,
                                fill: true,
                                tension: 0.3
                            },
                            {
                                label: 'Sales Gross',
                                data: grossSalesData,
                                borderColor: '#FF6384',
                                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                                borderWidth: 3,
                                fill: true,
                                tension: 0.3
                            },
                            {
                                label: 'Margin',
                                data: marginData,
                                borderColor: '#9966FF',
                                backgroundColor: 'rgba(153, 102, 255, 0.1)',
                                borderWidth: 3,
                                fill: true,
                                tension: 0.3
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                ticks: { 
                                    autoSkip: false, // Memastikan semua bulan muncul
                                    font: { size: 11, family: 'Poppins' } 
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    font: { size: 11 },
                                    // FORMAT RUPIAH DI SUMBU Y
                                    callback: (value) => formatRupiah(value)
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: { usePointStyle: true, font: { size: 14, weight: '600' } }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0,0,0,0.8)',
                                padding: 12,
                                callbacks: {
                                    // FORMAT RUPIAH DI TOOLTIP
                                    label: (context) => `${context.dataset.label}: ${formatRupiah(context.raw)}`
                                }
                            }
                        }
                    }
                });
            }
        })
        .catch(error => console.error('Error Chart:', error));
}

document.addEventListener('DOMContentLoaded', () => {
    updateClock();
    setInterval(updateClock, 1000);

    updateAllStats();
    setInterval(updateAllStats, 30000);

    updateMonthlySalesChart();
    setInterval(updateMonthlySalesChart, 60000);
});