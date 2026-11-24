
<?php
session_start();
require_once "koneksi.php";

$pesan = "";

if (isset($_GET["daftar"]) && $_GET["daftar"] == "berhasil") {
    $pesan = "Pendaftaran berhasil! Silakan login.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = mysqli_prepare(
        $conn,
        "SELECT id, nama, email, password, role
         FROM users WHERE email = ?"
    );

    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);

    $hasil = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($hasil);

    if ($user && password_verify($password, $user["password"])) {

        session_regenerate_id(true);

        $_SESSION["id"] = $user["id"];
        $_SESSION["nama"] = $user["nama"];
        $_SESSION["email"] = $user["email"];
        $_SESSION["role"] = $user["role"];

        header("Location: dashboard.php");
        exit;

    } else {
        $pesan = "Email atau password salah!";
    }

    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIPESMA</title>
    <link rel="stylesheet" href="css/auth.css">
</head>

<body>

<div class="auth-container">

    <div class="auth-box">

        <h1>SIPESMA</h1>
        <h2>Selamat Datang</h2>

        <p class="subtitle">
            Silakan masuk ke akun SIPESMA Anda.
        </p>

        <?php if ($pesan != ""): ?>
            <div class="pesan">
                <?= htmlspecialchars($pesan); ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <label>Email</label>
            <input type="email" name="email"
                   placeholder="Masukkan email"
                   required>

            <label>Password</label>
            <input type="password" name="password"
                   placeholder="Masukkan password"
                   required>

            <button type="submit">Login</button>

        </form>

        <p class="auth-footer">
            Belum punya akun?
            <a href="register.php">Daftar sekarang</a>
        </p>

        <a href="index.php" class="back-link">
            Kembali ke Beranda
        </a>

    </div>

</div>

</body>
</html>
