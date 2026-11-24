
<?php
include "koneksi.php";

// Mengambil daftar jenis surat dari database
$query = mysqli_query($conn, "SELECT * FROM jenis_surat");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>SIPESMA - Sistem Informasi Pengajuan Surat Mahasiswa</title>

    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <!-- NAVIGASI -->
    <nav class="navbar">
    <div class="logo">SIPESMA</div>

    <div>
        <a href="index.php" class="nav-link">Beranda</a>
        <a href="#layanan" class="nav-link">Layanan</a>
        <a href="login.php" class="nav-link">Login</a>
        <a href="register.php" class="nav-link">Daftar</a>
    </div>
</nav>

    <!-- HALAMAN UTAMA -->
    <section class="hero">
        <h1>Sistem Informasi Pengajuan Surat Mahasiswa</h1>

        <p>
            Selamat datang di SIPESMA.
            Layanan digital untuk memudahkan mahasiswa
            dalam melakukan pengajuan surat secara
            praktis, cepat, dan terstruktur.
        </p>

        <a href="#layanan" class="btn">
            Lihat Layanan Surat
        </a>
    </section>

    <!-- DAFTAR SURAT -->
    <section class="surat" id="layanan">

        <h2>Layanan Pengajuan Surat</h2>

        <div class="daftar-surat">

            <?php if (mysqli_num_rows($query) > 0): ?>

                <?php while ($data = mysqli_fetch_assoc($query)): ?>

                    <div class="kartu">

                        <h3>
                            <?= htmlspecialchars($data['nama_surat']); ?>
                        </h3>

                        <p>
                            <?= htmlspecialchars($data['deskripsi'] ?? 'Informasi surat belum tersedia.'); ?>
                        </p>

                    </div>

                <?php endwhile; ?>

            <?php else: ?>

                <p>Belum ada jenis surat yang tersedia.</p>

            <?php endif; ?>

        </div>

    </section>

    <!-- FOOTER -->
    <footer>
        <p>
            &copy; 2026 SIPESMA.
            Sistem Informasi Pengajuan Surat Mahasiswa.
        </p>
    </footer>

</body>
</html>
