
<?php
session_start();
require_once "koneksi.php";

$pesan = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nama = trim($_POST["nama"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $konfirmasi = $_POST["konfirmasi"];

    if ($nama == "" || $email == "" || $password == "") {
        $pesan = "Semua kolom wajib diisi!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $pesan = "Format email tidak valid!";
    } elseif (strlen($password) < 8) {
        $pesan = "Password minimal 8 karakter!";
    } elseif ($password !== $konfirmasi) {
        $pesan = "Konfirmasi password tidak cocok!";
    } else {

        // Memeriksa apakah email sudah terdaftar
        $cek = mysqli_prepare(
            $conn,
            "SELECT id FROM users WHERE email = ?"
        );

        mysqli_stmt_bind_param($cek, "s", $email);
        mysqli_stmt_execute($cek);
        mysqli_stmt_store_result($cek);

        if (mysqli_stmt_num_rows($cek) > 0) {
            $pesan = "Email sudah terdaftar!";
        } else {

            // Mengamankan password
            $password_hash = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            // Akun yang didaftarkan otomatis menjadi mahasiswa
            $role = "mahasiswa";

            $simpan = mysqli_prepare(
                $conn,
                "INSERT INTO users (nama, email, password, role)
                 VALUES (?, ?, ?, ?)"
            );

            mysqli_stmt_bind_param(
                $simpan,
                "ssss",
                $nama,
                $email,
                $password_hash,
                $role
            );

            if (mysqli_stmt_execute($simpan)) {
                header("Location: login.php?daftar=berhasil");
                exit;
            } else {
                $pesan = "Registrasi gagal. Silakan coba lagi.";
            }

            mysqli_stmt_close($simpan);
        }

        mysqli_stmt_close($cek);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - SIPESMA</title>
    <link rel="stylesheet" href="css/auth.css">
</head>

<body>

<div class="auth-container">

    <div class="auth-box">

        <h1>SIPESMA</h1>
        <h2>Buat Akun Mahasiswa</h2>

        <p class="subtitle">
            Daftarkan akun untuk menggunakan layanan SIPESMA.
        </p>

        <?php if ($pesan != ""): ?>
            <div class="pesan">
                <?= htmlspecialchars($pesan); ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <label>Nama Lengkap</label>
            <input type="text" name="nama"
                   placeholder="Masukkan nama lengkap"
                   value="<?= htmlspecialchars($_POST['nama'] ?? ''); ?>"
                   required>

            <label>Email</label>
            <input type="email" name="email"
                   placeholder="Masukkan email"
                   value="<?= htmlspecialchars($_POST['email'] ?? ''); ?>"
                   required>

            <label>Password</label>
            <input type="password" name="password"
                   placeholder="Minimal 8 karakter"
                   required>

            <label>Konfirmasi Password</label>
            <input type="password" name="konfirmasi"
                   placeholder="Ulangi password"
                   required>

            <button type="submit">Daftar Sekarang</button>

        </form>

        <p class="auth-footer">
            Sudah punya akun?
            <a href="login.php">Login di sini</a>
        </p>

        <a href="index.php" class="back-link">
            Kembali ke Beranda
        </a>

    </div>

</div>

</body>
</html>
