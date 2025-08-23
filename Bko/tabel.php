<div class="table-responsive mt-4">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Produk</th>
                <th>Nama Produk</th>
                <th>Total Qty</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($results)): ?>
            <tr><td colspan="4" class="text-center">Tidak ada data.</td></tr>
        <?php else: ?>
            <?php foreach ($results as $i => $row): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($row['kode_produk']) ?></td>
                    <td><?= htmlspecialchars($row['nama_produk']) ?></td>
                    <td><?= number_format($row['total_qty']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>