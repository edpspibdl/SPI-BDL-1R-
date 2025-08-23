<?php
require_once '_obi.class.php';

$obi = new OBI;

// Validasi ID dari POST
$id = isset($_POST['id']) ? $_POST['id'] : null;
if ($id === null) {
    echo '<div class="alert alert-danger">ID tidak tersedia.</div>';
    exit;
}

$ntf = $obi->notifdspb($id);

if (is_array($ntf) && count($ntf) > 0) {
    foreach ($ntf as $row) { ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong><?= htmlspecialchars($row['belum']) ?> PB Belum Struk!</strong>
            Masih ada PB OBI tanggal <?= htmlspecialchars($row['obi_tgl']) ?> yang belum DSPB.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php }
} else {
    echo '<div class="alert alert-success">Tidak ada PB belum DSPB.</div>';
}
?>