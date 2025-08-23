<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filter Data OBI</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2>Filter Data OBI</h2>
        <form action="proses.php" method="POST">
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="tanggalAwal1" class="form-label">Tanggal Awal 1</label>
                    <input type="date" name="tanggalAwal1" id="tanggalAwal1" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label for="tanggalAkhir1" class="form-label">Tanggal Akhir 1</label>
                    <input type="date" name="tanggalAkhir1" id="tanggalAkhir1" class="form-control" required>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="tanggalAwal2" class="form-label">Tanggal Awal 2</label>
                    <input type="date" name="tanggalAwal2" id="tanggalAwal2" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label for="tanggalAkhir2" class="form-label">Tanggal Akhir 2</label>
                    <input type="date" name="tanggalAkhir2" id="tanggalAkhir2" class="form-control" required>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="tanggalAwal3" class="form-label">Tanggal Awal 3</label>
                    <input type="date" name="tanggalAwal3" id="tanggalAwal3" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label for="tanggalAkhir3" class="form-label">Tanggal Akhir 3</label>
                    <input type="date" name="tanggalAkhir3" id="tanggalAkhir3" class="form-control" required>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">Cari</button>
        </form>
    </div>
</body>
</html>
