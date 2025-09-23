
// Fungsi untuk menampilkan SweetAlert
function showAlert(type, title, message) {
    Swal.fire({
        icon: type,   // 'success', 'error', 'warning', 'info', 'question'
        title: title,
        text: message,
        confirmButtonText: 'OK'
    });
}
