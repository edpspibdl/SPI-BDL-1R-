<?php
require_once 'helper/connection.php';
session_start();

if (isset($_POST['submit'])) {
    // Ambil input username dan password dari form
    $userid = $_POST['userid'] ?? '';
    $userpassword = $_POST['userpassword'] ?? '';

    // Ambil pilihan DB dari form (default 'prod')
    $_SESSION['db_target'] = $_POST['db_target'] ?? 'prod';

    // Pastikan bahwa nilai username dan password tidak kosong
    if (empty($userid) || empty($userpassword)) {
        // Set error message for empty fields
        $error_message = "Username dan password harus diisi.";
    } else {
        // Query untuk memeriksa kecocokan username dan password
        // Penting: Anda harus mengenkripsi userpassword di database dan saat membandingkan
        // Contoh: WHERE userid = :userid AND userpassword = SHA2(:userpassword, 256)
        // Sangat disarankan untuk mengenkripsi password di database dan saat login.
        $sql = "SELECT * FROM tbmaster_user WHERE userid = :userid AND userpassword = :userpassword LIMIT 1";

        try {
            // Gunakan prepared statement dengan PDO
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':userid', $userid);
            $stmt->bindParam(':userpassword', $userpassword); // Ingat untuk HASH password jika di-hash di DB
            $stmt->execute();

            // Cek apakah ada hasil yang ditemukan
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                // Jika data ditemukan, simpan data user di session
                $_SESSION['login'] = $row;

                // Set success message for SweetAlert
                $success_message = "Login Berhasil!";
            } else {
                // Set error message for invalid login
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

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="assets/modules/bootstrap-social/bootstrap-social.css">

    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/components.css">

    <link rel="stylesheet" href="./assets/css/css_login.css">
</head>

<body>
    <script>
        function createFallingCart() {
            let cart = document.createElement("div");
            cart.className = "cart";
            cart.innerHTML = "🛒"; // Emoji keranjang
            cart.style.left = Math.random() * window.innerWidth + "px";
            cart.style.animationDuration = (Math.random() * 3 + 2) + "s";
            cart.style.animationDelay = "0s";
            document.body.appendChild(cart);

            // Hapus elemen setelah animasi selesai
            setTimeout(() => {
                cart.remove();
            }, 5000);
        }

        setInterval(() => {
            createFallingCart();
        }, 500);
    </script>

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
                                            <input id="userid" type="text" class="form-control" name="userid" tabindex="1" required autofocus>
                                            <div class="invalid-feedback">Mohon isi username</div>
                                        </div>

                                        <div class="form-group">
                                            <label for="userpassword" class="control-label">Password</label>
                                            <input id="userpassword" type="password" class="form-control" name="userpassword" tabindex="2" required>
                                            <div class="invalid-feedback">Mohon isi kata sandi</div>
                                        </div>

                                        <div class="form-group">
                                            <button name="submit" type="submit" class="btn btn-primary btn-lg btn-block" tabindex="3">Login</button>
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
        <?php elseif (isset($success_message)): ?>
            Swal.fire({
                icon: 'success',
                title: '<?php echo $success_message; ?>',
                text: 'Redirecting...',
                timer: 2000,
                willClose: () => {
                    window.location.href = 'index.php';
                }
            });
        <?php endif; ?>
    </script>

</body>

</html>