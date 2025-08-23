<?php
require_once '../helper/connection.php';

if (empty($kodePromo)) {
    echo '<div class="alert alert-warning text-center">Silakan masukkan Kode Promosi untuk mencari data.</div>';
    exit;
}

$query = "SELECT 
    TGL_TRANS,
    KD_STATION,
    TRANS_NO,
    CREATE_BY,
    KD_MEMBER,
    KD_PROMOSI,
    JENIS_HADIAH,
    COALESCE(KET_HADIAH, 'Point Reward') AS KET_HADIAH,
    JMLH_HADIAH 
FROM m_gift_h
WHERE KD_PROMOSI LIKE :kodePromo OR KD_MEMBER LIKE :kodePromo
ORDER BY TGL_TRANS DESC";

$stmt = $conn->prepare($query);
$stmt->bindParam(':kodePromo', $kodePromo, PDO::PARAM_STR);
$stmt->execute();

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
$noUrut = 0;
?>

<div class="container-fluid">
    <div class="table-responsive table-sm">
        <?php if (count($data) === 0): ?>
            <div class="text-center">Tidak ada data yang ditemukan untuk kode promosi tersebut.</div>
        <?php else: ?>
            <?php $noUrut = 0; ?>
            <table id="GridView" class="table table-bordered table-striped table-hover webgrid-table-hidden">
                <thead>
                    <tr class="text-center">
                        <th>
                            <font size="2">No</font>
                        </th>
                        <th>
                            <font size="2">Tanggal</font>
                        </th>
                        <th>
                            <font size="2">Kassa</font>
                        </th>
                        <th>
                            <font size="2">No Struk</font>
                        </th>
                        <th>
                            <font size="2">Kasir</font>
                        </th>
                        <th>
                            <font size="2">Kode Member</font>
                        </th>
                        <th>
                            <font size="2">Kode Promosi</font>
                        </th>
                        <th>
                            <font size="2">Jenis Hadiah</font>
                        </th>
                        <th>
                            <font size="2">Keterangan Hadiah</font>
                        </th>
                        <th>
                            <font size="2">Jumlah</font>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $row): ?>
                        <tr>
                            <td align="center">
                                <font size="2"><?= ++$noUrut ?></font>
                            </td>
                            <td align="center">
                                <font size="2"><?= htmlspecialchars($row['tgl_trans']) ?></font>
                            </td>
                            <td align="left">
                                <font size="2"><?= htmlspecialchars($row['kd_station']) ?></font>
                            </td>
                            <td align="left">
                                <font size="2"><?= htmlspecialchars($row['trans_no']) ?></font>
                            </td>
                            <td align="left">
                                <font size="2"><?= htmlspecialchars($row['create_by']) ?></font>
                            </td>
                            <td align="center">
                                <font size="2"><?= htmlspecialchars($row['kd_member']) ?></font>
                            </td>
                            <td align="center">
                                <font size="2"><?= htmlspecialchars($row['kd_promosi']) ?></font>
                            </td>
                            <td align="left">
                                <font size="2"><?= htmlspecialchars($row['jenis_hadiah']) ?></font>
                            </td>
                            <td align="left">
                                <font size="2"><?= htmlspecialchars($row['ket_hadiah']) ?></font>
                            </td>
                            <td align="left">
                                <font size="2"><?= htmlspecialchars($row['jmlh_hadiah']) ?></font>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>