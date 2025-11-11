<?php
ini_set('session.gc_maxlifetime', 36000); // 10 jam
session_set_cookie_params(36000);
session_start();

// HAPUS require_once 'helper/connection.php'; DARI SINI
// require_once 'helper/connection.php'; // <--- HAPUS BARIS INI

if (isset($_SESSION['login'])) {
    header("Location: index.php");
    exit();
}

if (isset($_POST['submit'])) {
    $userid = strtoupper($_POST['userid'] ?? '');
    $userpassword = $_POST['userpassword'] ?? '';
    
    // TANGKAP DAN SETEL SESI BARU DARI FORM SECEPATNYA
    $_SESSION['db_target'] = $_POST['db_target'] ?? 'prod';
    $_SESSION['branch_target'] = $_POST['branch_target'] ?? 'spi1r'; // Gunakan nilai default yang valid

    // PENTING: PANGGIL KONEKSI HANYA DI SINI
    // Koneksi sekarang akan menggunakan nilai $_SESSION yang baru disetel di atas
    require_once 'helper/connection.php';

    if (empty($userid) || empty($userpassword)) {
        $error_message = "Username dan password harus diisi.";
    } else {
        // ... (Logika autentikasi tetap sama) ...
        $sql = "SELECT * FROM tbmaster_user WHERE userid = :userid AND userpassword = :userpassword LIMIT 1";

        try {
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':userid', $userid);
            $stmt->bindParam(':userpassword', $userpassword);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $_SESSION['login'] = $row;
                // Nilai db_target dan branch_target sudah disetel di atas, tapi bisa disetel lagi untuk memastikan:
                // $_SESSION['db_target'] = $_POST['db_target']; 
                // $_SESSION['branch_target'] = $_POST['branch_target']; 

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

                                        <!-- Pilih Database -->
                                        <div class="form-group">
                                            <label for="db_target">Pilih Database</label>
                                            <select id="db_target" name="db_target" class="form-control" required>
                                                <option value="prod" <?php echo ($_SESSION['db_target'] ?? 'prod') === 'prod' ? 'selected' : ''; ?>>PRODUCTION</option>
                                                <option value="sim" <?php echo ($_SESSION['db_target'] ?? '') === 'sim' ? 'selected' : ''; ?>>SIMULASI</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                        <label for="branch_target">Pilih Cabang</label>
                                            <select id="branch_target" name="branch_target" class="form-control" required>
                                                
                                                <option value="spi1r" 
                                                    <?php echo ($_SESSION['branch_target'] ?? '') === 'spi1r' ? 'selected' : ''; ?>>
                                                    SPI METRO
                                                </option>
                                                
                                                <option value="spi2u" 
                                                    <?php echo ($_SESSION['branch_target'] ?? '') === 'spi2u' ? 'selected' : ''; ?>
                                                    >
                                                    SPI PRINGSEWU
                                                </option>
                                                
                                                <option value="igrbdl" 
                                                    <?php echo ($_SESSION['branch_target'] ?? '') === 'igrbdl' ? 'selected' : ''; ?>
                                                    disabled>
                                                    INDOGROSIR BANDAR LAMPUNG
                                                </option>
                                                
                                            </select>
                                        </div>
                                        <!-- Username -->
                                        <div class="form-group">
                                            <label for="userid">Username</label>
                                            <input id="userid" type="text" class="form-control" name="userid" required autofocus
                                                   style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase()">
                                            <div class="invalid-feedback">Mohon isi username</div>
                                        </div>

                                        <!-- Password -->
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

        // Enter untuk pindah dari Username ke Password
        document.addEventListener('DOMContentLoaded', function() {
            const userIdInput = document.getElementById('userid');
            const userPasswordInput = document.getElementById('userpassword');
            
            if (userIdInput && userPasswordInput) {
                userIdInput.addEventListener('keydown', function(event) {
                    if (event.keyCode === 13 || event.key === 'Enter') {
                        event.preventDefault();
                        userPasswordInput.focus();
                    }
                });
            }
        });
    </script>
</body>
</html>
