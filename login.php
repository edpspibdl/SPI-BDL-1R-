<?php
ini_set('session.gc_maxlifetime', 36000);
session_set_cookie_params(36000);
session_start();

if (isset($_SESSION['login'])) {
    header("Location: index.php");
    exit();
}

if (isset($_POST['submit'])) {

    $userid       = strtoupper(trim($_POST['userid']));
    $userpassword = trim($_POST['userpassword']);

    $_SESSION['db_target']     = $_POST['db_target'] ?? 'prod';
    $_SESSION['branch_target'] = $_POST['branch_target'] ?? 'spi1r';

    require_once 'helper/connection.php';

    if ($userid === '' || $userpassword === '') {
        $error_message = "Username dan password harus diisi.";
    } else {

        $sql = "
    SELECT *
    FROM tbmaster_user
    WHERE UPPER(userid) = UPPER(:userid)
      AND UPPER(encryptpwd) = UPPER(MD5(:userpassword))
    LIMIT 1
";


        try {
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':userid', $userid);
            $stmt->bindParam(':userpassword', $userpassword);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                $_SESSION['login'] = $row;
                header("Location: index.php");
                exit();
            } else {
                $error_message = "Username atau password salah.";
            }

        } catch (PDOException $e) {
            $error_message = $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1" name="viewport">
    <title>Login</title>

    <link rel="icon" href="./assets/img/logo-spi.webp" type="image/png">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
            <img src="./assets/img/spi.png" width="400" class="img-fluid">
            <h2 class="mt-4">Sistem Informasi Penjualan</h2>
        </div>
    </div>

    <div class="col-12 col-md-3 right-panel">
        <div class="login-container">

            <div class="card card-primary">
                <div class="card-header">
                    <h4>Monggo Login Dulu Pak e</h4>
                </div>

                <div class="card-body">
                    <form method="POST" action="" novalidate>

                        <!-- Database -->
                        <div class="form-group">
                            <label>Pilih Database</label>
                            <select name="db_target" class="form-control" required>
                                <option value="prod" <?= ($_SESSION['db_target'] ?? 'prod') === 'prod' ? 'selected' : ''; ?>>PRODUCTION</option>
                                <option value="sim"  <?= ($_SESSION['db_target'] ?? '') === 'sim' ? 'selected' : ''; ?>>SIMULASI</option>
                            </select>
                        </div>

                        <!-- Cabang -->
                        <div class="form-group">
                            <label>Pilih Cabang</label>
                            <select name="branch_target" class="form-control" required>
                                <option value="spi1r" <?= ($_SESSION['branch_target'] ?? '') === 'spi1r' ? 'selected' : ''; ?>>SPI METRO</option>
                                <option value="spi2u" <?= ($_SESSION['branch_target'] ?? '') === 'spi2u' ? 'selected' : ''; ?>>SPI PRINGSEWU</option>
                                <option value="igrbdl" <?= ($_SESSION['branch_target'] ?? '') === 'igrbdl' ? 'selected' : ''; ?>>INDOGROSIR BANDAR LAMPUNG</option>
                            </select>
                        </div>

                        <!-- Username -->
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text"
                                   name="userid"
                                   class="form-control"
                                   required
                                   autofocus
                                   style="text-transform: uppercase;"
                                   oninput="this.value = this.value.toUpperCase()">
                        </div>

                        <!-- Password -->
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password"
                                   name="userpassword"
                                   class="form-control"
                                   required>
                        </div>

                        <div class="form-group">
                            <button name="submit" class="btn btn-primary btn-lg btn-block">
                                Login
                            </button>
                        </div>

                        <div class="simple-footer">
                            Copyright &copy; <?= date("Y"); ?> EDP SPI BDL 1R
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

<?php if (isset($error_message)): ?>
<script>
Swal.fire({
    icon: 'error',
    title: 'Login Gagal',
    text: '<?= $error_message ?>'
});
</script>
<?php endif; ?>

</body>
</html>
