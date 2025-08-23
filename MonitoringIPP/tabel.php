<?php
// Ambil semua data terlebih dahulu
$statusCounts = [];
$rows = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $status = $row['status_pin'];
    if (!isset($statusCounts[$status])) {
        $statusCounts[$status] = 0;
    }
    $statusCounts[$status]++;
    $rows[] = $row; // Simpan semua data
}
?>

<div class="mb-3">
    <h5>Jumlah per Status :</h5>
    <ul>
        <?php foreach ($statusCounts as $status => $count) : ?>
        <li><strong><?= htmlspecialchars($status) ?> :</strong> <?= $count ?> data</li>
        <?php endforeach; ?>
    </ul>
</div>

<div class="table-responsive">
    <table id="gridViewTable" class="table table-bordered table-striped table-hover">
        <thead>
            <tr class="info">
                <th class="text-center"><strong>NO</strong></th>
                <th class="text-center"><strong>STATUS</strong></th>
                <th class="text-center"><strong>DELIMAN</strong></th>
                <th class="text-center"><strong>NO PB</strong></th>
                <th class="text-center"><strong>TGL PB</strong></th>
                <th class="text-center"><strong>KODE MEMBER</strong></th>
                <th class="text-center"><strong>NAMA MEMBER</strong></th>
                <th class="text-center"><strong>REAL ORDER</strong></th>
                <th class="text-center"><strong>TGL DSP</strong></th>
                <th class="text-center"><strong>TIPE BAYAR</strong></th>
                <th class="text-center"><strong>PIN</strong></th>
                <th class="text-center"><strong>STATUS PIN</strong></th>
                <th class="text-center"><strong>TGL INPUT PIN</strong></th>
                <th class="text-center"><strong>NO AWB</strong></th>
                <th class="text-center"><strong>NO SERTIM</strong></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $noUrut = 0;
            foreach ($rows as $row) {
                $noUrut++;
                echo '<tr>';
                    echo '<td class="text-center">' . $noUrut . '</td>';
                    echo '<td class="text-center" style="white-space: nowrap;">' . htmlspecialchars($row['status']) . '</td>';
                    echo '<td class="text-center" style="white-space: nowrap;">' . htmlspecialchars($row['sti_drivername']) . '</td>';
                    echo '<td class="text-center" style="white-space: nowrap;">' . htmlspecialchars($row['no_pb']) . '</td>';
                    echo '<td class="text-center" style="white-space: nowrap;">' . htmlspecialchars($row['tgl_pb']) . '</td>';
                    echo '<td class="text-center">' . htmlspecialchars($row['kode_member']) . '</td>';
                    echo '<td class="text-left" style="white-space: nowrap;">' . htmlspecialchars($row['nama_member']) . '</td>';
                    echo '<td class="text-end">' . number_format($row['realorder'], 0, '.', ',') . '</td>';
                    echo '<td class="text-center" style="white-space: nowrap;">' . htmlspecialchars($row['tgl_dsp']) . '</td>';
                    echo '<td class="text-center">' . htmlspecialchars($row['tipe_bayar']) . '</td>';
                    echo '<td class="text-center">' . htmlspecialchars($row['pin']) . '</td>';
                    echo '<td class="text-center" style="white-space: nowrap;">' . htmlspecialchars($row['status_pin']) . '</td>';
                    echo '<td class="text-center" style="white-space: nowrap;">' . htmlspecialchars($row['tgl_input_pin']) . '</td>';
                    echo '<td class="text-center" style="white-space: nowrap;">' . htmlspecialchars($row['awi_noawb']) . '</td>';
                    echo '<td class="text-center" style="white-space: nowrap;">' . htmlspecialchars($row['sti_noserahterima']) . '</td>';
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>
</div>
