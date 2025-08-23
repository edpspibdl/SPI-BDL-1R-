<?php
require_once '../helper/connection.php';

if (empty($kodeMember)) {
    echo '<div class="alert alert-warning text-center">Silakan masukkan kode Member / Kode Promosi untuk mencari data.</div>';
    exit;
}

$query = "SELECT TGL_TRANS, KODE_STATION, TRANS_NO, CREATE_BY, KD_MEMBER, KD_PROMOSI, KELIPATAN, CASHBACK
          FROM m_promosi_h
          WHERE KD_PROMOSI = :kodeMember OR KD_MEMBER = :kodeMember
          ORDER BY TGL_TRANS DESC";

$stmt = $conn->prepare($query);
$stmt->bindParam(':kodeMember', $kodeMember, PDO::PARAM_STR);
$stmt->execute();

$noUrut = 0;
?>

<div class="container-fluid">
    <div class="table-responsive table-sm">
        <table id="GridView" class="table table-bordered table-striped table-hover webgrid-table-hidden">
            <thead>
                <tr class="text-center">
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Kassa</th>
                    <th>No Struk</th>
                    <th>Kasir</th>
                    <th>Kode Member</th>
                    <th>Kode Promosi</th>
                    <th>Kelipatan</th>
                    <th>Nominal Cashback</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>
                    <tr>
                        <td><?= ++$noUrut; ?></td>
                        <td><?= htmlspecialchars($row['tgl_trans']); ?></td>
                        <td align="center"><?= htmlspecialchars($row['kode_station']); ?></td>
                        <td align="center"><?= htmlspecialchars($row['trans_no']); ?></td>
                        <td align="center"><?= htmlspecialchars($row['create_by']); ?></td>
                        <td align="center"><?= htmlspecialchars($row['kd_member']); ?></td>
                        <td align="center"><?= htmlspecialchars($row['kd_promosi']); ?></td>
                        <td align="center"><?= htmlspecialchars($row['kelipatan']); ?></td>
                        <td align="right"><?= htmlspecialchars($row['cashback']); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>