<?php
session_start(); // <-- harus duluan
require_once 'helper/connection.php';

if (isset($_POST['submit'])) {
    // Ambil input dan pastikan menjadi uppercase untuk USERID
    $userid = strtoupper($_POST['userid'] ?? ''); 
    $userpassword = $_POST['userpassword'] ?? '';
    $_SESSION['db_target'] = $_POST['db_target'] ?? 'prod';

    if (empty($userid) || empty($userpassword)) {
        $error_message = "Username dan password harus diisi.";
    } else {
        // PERHATIAN: Userpassword seharusnya di-hash (misal: menggunakan password_hash)
        // Sebelum disimpan ke database dan diverifikasi dengan password_verify.
        $sql = "SELECT * FROM tbmaster_user WHERE userid = :userid AND userpassword = :userpassword LIMIT 1";

        try {
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':userid', $userid);
            $stmt->bindParam(':userpassword', $userpassword);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $_SESSION['login'] = $row;
                // Redirect langsung ke index.php jika login berhasil
                header("Location: index.php");
                exit();
            } else {
                $error_message = "Username atau password salah.";
            }
        } catch (PDOException $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>Login</title>
    <link rel="icon" href="./assets/img/logo-spi.webp" type="image/png">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="assets/modules/bootstrap-social/bootstrap-social.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/components.css">
    <link rel="stylesheet" href="./assets/css/css_login.css">
</head>
<body>
    <div id="app">
        <section class="section section-split">
            <div class="container-fluid h-100">
                <div class="row h-100">
                    <div class="col-md-9 d-none d-md-flex left-panel">
                        <div>
                            <img src="./assets/img/spi.png" alt="logo" width="400" class="img-fluid">
                            <h2 class="mt-4">Sistem Informasi Penjualan</h2>
                        </div>
                    </div>

                    <div class="col-12 col-md-3 right-panel">
                        <div class="login-container">
                            <div class="login-brand d-md-none text-center mb-4">
                                <img src="./assets/img/spi.png" alt="logo" width="200" class="img-fluid">
                            </div>

                            <div class="card card-primary">
                                <div class="card-header">
                                    <h4>Monggo Login Dulu Pak e</h4>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="" class="needs-validation" novalidate="">
                                        <div class="form-group">
                                            <label for="db_target">Pilih Database</label>
                                            <select id="db_target" name="db_target" class="form-control" required>
                                                <option value="prod" <?php echo ($_SESSION['db_target'] ?? 'prod') === 'prod' ? 'selected' : ''; ?>>PRODUCTION</option>
                                                <option value="sim" <?php echo ($_SESSION['db_target'] ?? 'prod') === 'sim' ? 'selected' : ''; ?>>SIMULASI</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="userid">Username</label>
                                            <input id="userid" type="text" class="form-control" name="userid" required autofocus
                                                   style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase()">
                                            <div class="invalid-feedback">Mohon isi username</div>
                                        </div>

                                        <div class="form-group">
                                            <label for="userpassword">Password</label>
                                            <input id="userpassword" type="password" class="form-control" name="userpassword" required
                                                   style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase()">
                                            <div class="invalid-feedback">Mohon isi kata sandi</div>
                                        </div>

                                        <div class="form-group">
                                            <button name="submit" type="submit" class="btn btn-primary btn-lg btn-block">Login</button>
                                        </div>

                                        <div class="simple-footer">
                                            Copyright &copy; <?php echo date("Y"); ?> EDP SPI BDL 1R
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        <?php if (isset($error_message)): ?>
        Swal.fire({
            icon: 'error',
            title: 'Login Gagal',
            text: '<?php echo $error_message; ?>'
        });
        <?php endif; ?>

        // PERBAIKAN 2: Mengatasi tombol Enter agar pindah dari Username ke Password
        document.addEventListener('DOMContentLoaded', function() {
            const userIdInput = document.getElementById('userid');
            const userPasswordInput = document.getElementById('userpassword');
            
            if (userIdInput && userPasswordInput) {
                userIdInput.addEventListener('keydown', function(event) {
                    // Cek apakah tombol yang ditekan adalah Enter (keyCode 13 atau key 'Enter')
                    if (event.keyCode === 13 || event.key === 'Enter') {
                        event.preventDefault(); // Mencegah form terkirim
                        userPasswordInput.focus(); // Pindahkan fokus ke input password
                    }
                });
            }
        });
    </script>
</body>
</html>