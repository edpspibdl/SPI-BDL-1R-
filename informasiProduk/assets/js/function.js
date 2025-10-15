
    function loadModalInfoTag() {
        const kodePLU = document.getElementById('kodePLU').textContent.trim();

        if (!kodePLU) {
            alert('Kode PLU tidak boleh kosong!');
            return;
        }

        fetch('tag_modal.php?kodePLU=' + encodeURIComponent(kodePLU))
            .then(response => response.text())
            .then(html => {
                document.getElementById('modalInfoTagContainer').innerHTML = html;
                $('#modalInfoTag').modal('show');
            })
            .catch(error => console.error('Gagal memuat modal:', error));
    }


    function loadModalInfoFull() {
        const kodePLU = document.getElementById('kodePLU').textContent.trim();

        if (!kodePLU) {
            alert('Kode PLU tidak boleh kosong!');
            return;
        }

        fetch('full_modal.php?kodePLU=' + encodeURIComponent(kodePLU))
            .then(response => response.text())
            .then(html => {
                document.getElementById('modalInfoFullContainer').innerHTML = html;
                $('#modalInfoFull').modal('show');
            })
            .catch(error => console.error('Gagal memuat modal:', error));
    }

    function loadModalInfoLokasi() {
        const kodePLU = document.getElementById('kodePLU').textContent.trim();

        if (!kodePLU) {
            alert('Kode PLU tidak boleh kosong!');
            return;
        }

        fetch('lokasi_modal.php?kodePLU=' + encodeURIComponent(kodePLU))
            .then(response => response.text())
            .then(html => {
                document.getElementById('modalInfoLokasiContainer').innerHTML = html;
                $('#modalInfoLokasi').modal('show');
            })
            .catch(error => console.error('Gagal memuat modal:', error));
    }

    function loadModalInfoPenerimaan() {
        const kodePLU = document.getElementById('kodePLU').textContent.trim();

        if (!kodePLU) {
            alert('Kode PLU tidak boleh kosong!');
            return;
        }

        fetch('penerimaan_modal.php?kodePLU=' + encodeURIComponent(kodePLU))
            .then(response => response.text())
            .then(html => {
                document.getElementById('modalInfoPenerimaanContainer').innerHTML = html;
                $('#modalInfoPenerimaan').modal('show');
            })
            .catch(error => console.error('Gagal memuat modal:', error));
    }

    function loadModalInfoPenjualan() {
        const kodePLU = document.getElementById('kodePLU').textContent.trim();

        if (!kodePLU) {
            alert('Kode PLU tidak boleh kosong!');
            return;
        }

        fetch('penjualan_modal.php?kodePLU=' + encodeURIComponent(kodePLU))
            .then(response => response.text())
            .then(html => {
                document.getElementById('modalInfoPenjualanContainer').innerHTML = html;
                $('#modalInfoPenjualan').modal('show');
            })
            .catch(error => console.error('Gagal memuat modal:', error));
    }

    function loadModalInfoPb() {
        const kodePLU = document.getElementById('kodePLU').textContent.trim();

        if (!kodePLU) {
            alert('Kode PLU tidak boleh kosong!');
            return;
        }

        fetch('pb_modal.php?kodePLU=' + encodeURIComponent(kodePLU))
            .then(response => response.text())
            .then(html => {
                document.getElementById('modalInfoPbContainer').innerHTML = html;
                $('#modalInfoPb').modal('show');
            })
            .catch(error => console.error('Gagal memuat modal:', error));
    }

    function loadModalInfoSo() {
        const kodePLU = document.getElementById('kodePLU').textContent.trim();

        if (!kodePLU) {
            alert('Kode PLU tidak boleh kosong!');
            return;
        }

        fetch('so_modal.php?kodePLU=' + encodeURIComponent(kodePLU))
            .then(response => response.text())
            .then(html => {
                document.getElementById('modalInfoSoContainer').innerHTML = html;
                $('#modalInfoSo').modal('show');
            })
            .catch(error => console.error('Gagal memuat modal:', error));
    }

    function toggleSection(containerId, btn) {
        const container = document.getElementById(containerId);
        const icon = btn.querySelector('i');

        if (container.style.display === 'none') {
            container.style.display = 'block';
            if (icon) {
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        } else {
            container.style.display = 'none';
            if (icon) {
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    }
