<!-- Custom CSS -->
<style>
    /* Styling umum */
    body {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    .main-content {
        flex: 1;
        /* Mendorong footer ke bawah */
        padding-bottom: 60px;
        /* Supaya konten tidak tertutup footer */
    }

    /* Footer tetap di bawah dan berada di kanan */
    .main-footer {
        position: fixed;
        bottom: 0;
        right: 0;
        /* Footer menempel di kanan */
        width: calc(100% - 250px);
        /* Sesuaikan agar tidak menutupi sidebar */
        background: rgb(9, 84, 223);
        /* Warna background */
        color: white;
        /* Warna teks */
        padding: 10px 20px;
        text-align: center;
        z-index: 1000;
    }

    /* Jika sidebar memiliki lebar yang berbeda, sesuaikan */
    @media (max-width: 768px) {
        .main-footer {
            width: calc(100% - 200px);
            /* Sesuaikan dengan sidebar lebih kecil */
        }
    }
</style>
</div>
<footer class="main-footer">
    <div class="footer-center">
        Copyright &copy; <?php echo date("Y"); ?> EDP SPI BDL 1R (MAD.solar)

        <div class="footer-right">

        </div>
</footer>
</div>
</div>

<!-- General JS Scripts -->
<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.nicescroll/3.7.6/jquery.nicescroll.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
<script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
<script src="../assets/js/stisla.js"></script>

<!-- JS Libraies -->
<script src="../assets/modules/jquery.sparkline.min.js"></script>
<script src="../assets/modules/Chart.min.js"></script>
<script src="../assets/modules/owlcarousel2/dist/owl.carousel.min.js"></script>
<script src="../assets/modules/summernote/summernote-bs4.js"></script>
<script src="../assets/modules/chocolat/dist/js/jquery.chocolat.min.js"></script>
<script src="../assets/modules/datatables/datatables.min.js"></script>
<script src="../assets/modules/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js"></script>
<script src="../assets/modules/datatables/Select-1.2.4/js/dataTables.select.min.js"></script>
<script src="../assets/modules/jquery-ui/jquery-ui.min.js"></script>
<script src="../assets/modules/izitoast/js/iziToast.min.js"></script>

<!-- Template JS File -->
<script src="../assets/js/scripts.js"></script>
<script src="../assets/js/custom.js"></script>

<!-- Page Specific JS File -->
<script src="../assets/js/page/index.js"></script>
</body>

</html>