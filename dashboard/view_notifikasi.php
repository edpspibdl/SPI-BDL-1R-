<?php
require_once '_obi.class.php';

$obi = new OBI;

$id = isset($_POST['id']) ? $_POST['id'] : null;

if ($id === null) {
    echo '<div class="alert alert-danger">ID tidak tersedia.</div>';
    exit;
}

$ntf = $obi->notifdspb($id);

if (is_array($ntf) && count($ntf) > 0) {
    foreach ($ntf as $row) { ?>
        <div class="alert alert-success alert-dismissible show" role="alert">
            <strong><?= htmlspecialchars($row['belum']) ?> PB Belum Struk! Atau DSPB</strong>
            Pada Tanggal tanggal <?= htmlspecialchars($row['obi_tgl']) ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
<?php   }
} else {
    echo '<div class="alert alert-success">Tidak ada PB belum DSPB.</div>';
}
?>
