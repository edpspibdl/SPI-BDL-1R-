// assets/js/dashboard.js
let monthlySalesChartInstance = null; // Variabel global untuk chart

// Fungsi helper untuk fetching dan updating elemen
async function fetchDataAndUpdate(url, elementId, formatter, defaultValue = 'Rp0', errorMessage = 'Gagal memuat data') {
    const element = document.getElementById(elementId);
    if (!element) {
        console.error(`ERROR: Elemen #${elementId} tidak ditemukan!`);
        return;
    }

    element.textContent = 'Memuat...';
    element.classList.add('loading-data');

    try {
        const response = await fetch(url);
        if (!response.ok) {
            const text = await response.text();
            throw new Error(`HTTP error! status: ${response.status}. Response text: ${text}`);
        }
        const data = await response.json();

        if (data.success) {
            element.textContent = formatter(data);
        } else {
            element.textContent = defaultValue;
            console.error(`${errorMessage}:`, data.message || 'Error tidak diketahui');
        }
    } catch (error) {
        console.error(`ERROR: Saat mengambil data untuk ${elementId}:`, error);
        element.textContent = 'Error loading data';
    } finally {
        element.classList.remove('loading-data');
    }
}

// Fungsi untuk memperbarui jam
function updateClock() {
    const now = new Date();
    const h = String(now.getHours()).padStart(2, '0');
    const m = String(now.getMinutes()).padStart(2, '0');
    const s = String(now.getSeconds()).padStart(2, '0');
    const clockElement = document.getElementById('clock');
    if (clockElement) {
        clockElement.textContent = `${h}:${m}:${s}`;
    }
}

// Fungsi untuk memperbarui semua kartu statistik
function updateAllStats() {
    // Sales Nett
    fetchDataAndUpdate(
        'sales_nett_berjalan.php', // Perbarui path
        'earnedThisMonthValue',
        (data) => data.earned_this_month.toLocaleString('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 }),
        'Rp0',
        'Gagal mengambil pendapatan bulan ini'
    );

    // Sales Gross
    fetchDataAndUpdate(
        'sales_gross_berjalan.php', // Perbarui path
        'grossThisMonthValue',
        (data) => data.gross_this_month.toLocaleString('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 }),
        'Rp0',
        'Gagal mengambil sales gross bulan ini'
    );

    // Keuntungan/Margin
    fetchDataAndUpdate(
        'margin_berjalan.php', // Perbarui path
        'profitMarginMonthValue',
        (data) => data.margin.toLocaleString('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 }),
        'Rp0',
        'Gagal mengambil keuntungan/margin bulan ini'
    );

    // Member Belanja
    fetchDataAndUpdate(
        'qry_member_belanja.php', // Perbarui path
        'memberKhususBulanIni',
        (data) => new Intl.NumberFormat('id-ID').format(data.total_member_khusus) + " member",
        '0 Member',
        'Gagal mengambil data member'
    );
}

// Fungsi untuk memperbarui grafik bulanan
function updateMonthlySalesChart() {
    const monthlySalesChartElement = document.getElementById('monthlySalesChart');
    if (!monthlySalesChartElement) {
        console.error('ERROR: Elemen canvas dengan ID "monthlySalesChart" tidak ditemukan!');
        const chartContainer = document.querySelector('.chart-container');
        if (chartContainer) {
            chartContainer.innerHTML = '<p class="text-center text-danger">Elemen grafik tidak ditemukan. Pastikan ID canvas sudah benar.</p>';
        }
        return;
    }

    const ctx = monthlySalesChartElement.getContext('2d');

    fetch('sales_bulanan.php') // Perbarui path
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => { throw new Error(`HTTP error! status: ${response.status}. Response text: ${text}`); });
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.sales) {
                const labels = data.sales.map(item => item.month);
                const netSalesData = data.sales.map(item => item.total_sales_numeric);
                const grossSalesData = data.sales.map(item => item.total_gross_numeric);
                const marginData = data.sales.map(item => item.total_margin_numeric);

                if (monthlySalesChartInstance) {
                    monthlySalesChartInstance.destroy();
                }

                monthlySalesChartInstance = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            { label: 'Sales Nett', data: netSalesData, borderColor: 'rgba(75, 192, 192, 1)', backgroundColor: 'rgba(75, 192, 192, 0.2)', borderWidth: 2, fill: false, tension: 0.4 },
                            { label: 'Sales Gross', data: grossSalesData, borderColor: 'rgba(255, 99, 132, 1)', backgroundColor: 'rgba(255, 99, 132, 0.2)', borderWidth: 2, fill: false, tension: 0.4 },
                            { label: 'Margin', data: marginData, borderColor: 'rgba(153, 102, 255, 1)', backgroundColor: 'rgba(153, 102, 255, 0.2)', borderWidth: 2, fill: false, tension: 0.4 }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: { display: true, text: 'Jumlah (IDR)' },
                                ticks: {
                                    callback: function(value) {
                                        return 'Rp. ' + value.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
                                    }
                                }
                            },
                            x: { title: { display: true, text: 'Bulan' } }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) { label += ': '; }
                                        if (context.parsed.y !== null) {
                                            label += 'Rp. ' + context.parsed.y.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
                                        }
                                        return label;
                                    }
                                }
                            },
                            // Pastikan plugin datalabels sudah di-import jika Anda menggunakannya
                            // datalabels: { ... konfigurasi datalabels Anda ... }
                        }
                    }
                });
            } else {
                console.error('Gagal mengambil data sales bulanan:', data.message || 'Data tidak ditemukan atau error tidak diketahui');
                const chartParent = monthlySalesChartElement.parentElement;
                if (chartParent) { chartParent.innerHTML = '<p class="text-center text-danger">Gagal memuat grafik sales & margin bulanan.</p>'; }
            }
        })
        .catch(error => {
            console.error('ERROR: Saat mengambil data sales bulanan:', error);
            const chartParent = monthlySalesChartElement.parentElement;
            if (chartParent) { chartParent.innerHTML = '<p class="text-center text-danger">Error koneksi saat memuat grafik.</p>'; }
        });
}

// Inisialisasi saat halaman selesai dimuat
document.addEventListener('DOMContentLoaded', () => {
    updateClock();
    setInterval(updateClock, 1000);

    updateAllStats(); // Panggil fungsi untuk memperbarui semua kartu
    setInterval(updateAllStats, 30000); // Perbarui setiap 30 detik

    updateMonthlySalesChart();
    setInterval(updateMonthlySalesChart, 60000); // Perbarui setiap 1 menit
});