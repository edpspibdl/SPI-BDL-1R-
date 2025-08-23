<?php
include '../helper/connection.php';

$tgl_mulai = '20250701';     // Contoh nilai; bisa diganti dari input
$tgl_selesai = '20250708';   // Contoh nilai; bisa diganti dari input

$query = "SELECT
                obi_nopb,
                obi_tglpb,
                obi_notrans,
                obi_kdmember,
                cus_namamember,
                (date_trunc('day', obi_tglstruk)::date || obi_kdstation || obi_cashierid || obi_nostruk) AS pk_obi
            FROM
                tbtr_obi_h
                LEFT JOIN tbmaster_customer ON cus_kodemember = obi_kdmember
            WHERE obi_recid = '6'
                AND obi_tipe = 'S'
                AND TO_CHAR(date_trunc('day', obi_tglpb)::date, 'yyyymmdd') BETWEEN :bv_tanggalmulai AND :bv_tanggalselesai
            ORDER BY
                date_trunc('day', obi_tglpb)::date || obi_notrans ASC";

$stmt = $conn->prepare($query);
$stmt->bindParam(':bv_tanggalmulai', $tgl_mulai);
$stmt->bindParam(':bv_tanggalselesai', $tgl_selesai);
$stmt->execute();
?>

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.6.2/css/bootstrap.min.css">
<!-- Optional z-index fix -->
<style>
    .modal-backdrop {
        z-index: 1040 !important;
    }

    .modal {
        z-index: 1060 !important;
    }
</style>


<<section class="section">
    <div class="section-header d-flex justify-content-between">
        <h3 class="text-center">Master PB SPI</h3>
        <a href="index.php" class="btn btn-primary">BACK</a>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="table-1">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>No PB</th>
                                    <th>Tanggal PB</th>
                                    <th>No Transaksi</th>
                                    <th>Kode Member</th>
                                    <th>Nama Member</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 0;
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $no++;
                                ?>
                                    <tr>
                                        <td><?= htmlspecialchars($no) ?></td>
                                        <td class="text-nowrap"><?= htmlspecialchars($row["obi_nopb"]) ?></td>
                                        <td class="text-nowrap"><?= htmlspecialchars($row["obi_tglpb"]) ?></td>
                                        <td class="text-nowrap"><?= htmlspecialchars($row["obi_notrans"]) ?></td>
                                        <td class="text-nowrap"><?= htmlspecialchars($row["obi_kdmember"]) ?></td>
                                        <td><?= htmlspecialchars($row["cus_namamember"]) ?></td>
                                        <td class="text-center">
                                            <button type="button"
                                                class="btn btn-xs btn-lihat btn-primary"
                                                data-id="<?= htmlspecialchars($row['pk_obi']) ?>"
                                                onclick="openModal('<?= htmlspecialchars($row['pk_obi']) ?>')">
                                                <span class="fas fa-search"></span>
                                            </button>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </section>

    <!-- Modal harus berada di luar section agar tidak tertutup elemen lain -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-top" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalTitle">Detail Hadiah</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modalContent">
                    <!-- Konten dari get-hdh.php akan dimuat di sini -->
                </div>
            </div>
        </div>
    </div>

    <!-- JS jQuery & Bootstrap Bundle -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script untuk membuka modal -->
    <script>
        function openModal(hdh_id) {
            console.log("Memanggil modal untuk ID:", hdh_id);
            $.ajax({
                type: "POST",
                url: "get-hdh.php",
                data: {
                    id: hdh_id
                },
                success: function(response) {
                    console.log("Response:", response);
                    $('#modalContent').html(response);
                    $('#myModal').appendTo("body").modal('show');
                },
                error: function(xhr) {
                    console.error("Gagal mengambil data:", xhr.responseText);
                    alert("Gagal mengambil data hadiah:\n" + xhr.responseText);
                }
            });
        }
    </script>