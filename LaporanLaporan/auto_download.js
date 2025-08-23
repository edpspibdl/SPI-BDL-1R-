document.addEventListener('DOMContentLoaded', () => {
    // Fungsi untuk mengklik tombol "Download Semua Laporan Pagi" secara otomatis
    const autoClickDownloadButton = () => {
        const downloadAllBtn = document.getElementById('downloadAllReportsBtn');
        if (downloadAllBtn) {
            // Memberikan sedikit jeda sebelum mengklik
            // Ini memastikan semua elemen halaman dan JS lainnya sudah siap
            setTimeout(() => {
                downloadAllBtn.click();
                console.log("Tombol 'Download Semua Laporan Pagi' telah diklik secara otomatis.");
            }, 1500); // Jeda 1.5 detik (bisa disesuaikan)
        } else {
            console.warn("Tombol 'Download Semua Laporan Pagi' tidak ditemukan.");
        }
    };

    // Panggil fungsi untuk mengklik tombol secara otomatis saat halaman dimuat
    // Kita hanya menjalankan ini jika ada parameter URL tertentu (misalnya ?autodownload=true)
    // agar auto-klik tidak terjadi setiap kali pengguna membuka halaman.
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('autodownload') === 'true') {
        autoClickDownloadButton();
    }
});