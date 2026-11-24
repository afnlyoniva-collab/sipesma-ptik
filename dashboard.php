<?php
session_start();

// Memastikan pengguna sudah login
if (!isset($_SESSION["id"])) {
    header("Location: login.php");
    exit;
}

$nama = htmlspecialchars($_SESSION["nama"] ?? "Pengguna");
$role = $_SESSION["role"] ?? "mahasiswa";

if (!in_array($role, ["mahasiswa", "admin"], true)) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard - SIPESMA</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Arial, sans-serif;
        }

        body {
            background: #f3f6fc;
            color: #1e293b;
        }

        /* NAVBAR */
        .navbar {
            background: #142d55;
            color: white;
            padding: 17px 7%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 15px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
        }

        .logo {
            font-size: 24px;
            font-weight: 800;
            letter-spacing: 1px;
        }

        .logo span {
            color: #70b7ff;
        }

        .nav-menu {
            display: flex;
            align-items: center;
            gap: 22px;
        }

        .nav-menu a {
            color: #e5efff;
            text-decoration: none;
            font-size: 14px;
            transition: 0.2s;
        }

        .nav-menu a:hover {
            color: #70b7ff;
        }

        .nav-menu .logout {
            background: #ffffff;
            color: #173866;
            padding: 9px 17px;
            border-radius: 8px;
            font-weight: 600;
        }

        .nav-menu .logout:hover {
            background: #e5efff;
        }

        /* CONTAINER */
        .container {
            max-width: 1150px;
            margin: 35px auto;
            padding: 0 22px;
        }

        /* HERO */
        .hero {
            background: linear-gradient(120deg, #173866, #3476bd);
            color: white;
            padding: 38px;
            border-radius: 18px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(23, 56, 102, 0.12);
        }

        .hero::after {
            content: "";
            position: absolute;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(255,255,255,0.07);
            right: -50px;
            top: -90px;
        }

        .hero .welcome {
            font-size: 14px;
            color: #d8eaff;
            margin-bottom: 10px;
        }

        .hero h1 {
            font-size: 30px;
            margin-bottom: 12px;
        }

        .hero p {
            font-size: 15px;
            line-height: 1.8;
            color: #e5efff;
            max-width: 650px;
        }

        .role-label {
            display: inline-block;
            margin-top: 20px;
            padding: 7px 14px;
            background: rgba(255,255,255,0.17);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 30px;
            font-size: 12px;
            font-weight: 600;
        }

        /* SECTION */
        .section-heading {
            margin: 35px 0 20px;
        }

        .section-heading h2 {
            font-size: 23px;
            color: #173866;
            margin-bottom: 7px;
        }

        .section-heading p {
            color: #718096;
            font-size: 14px;
        }

        /* CARDS */
        .daftar-surat {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 22px;
        }

        .kartu {
            background: white;
            padding: 27px;
            border-radius: 15px;
            border: 1px solid #e8edf5;
            box-shadow: 0 5px 18px rgba(0,0,0,0.035);
            transition: 0.25s;
        }

        .kartu:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(0,0,0,0.07);
        }

        .icon-box {
            width: 50px;
            height: 50px;
            background: #e8f1ff;
            color: #245b9f;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 13px;
            font-size: 23px;
            margin-bottom: 19px;
        }

        .kartu h3 {
            font-size: 18px;
            color: #173866;
            margin-bottom: 10px;
        }

        .kartu p {
            font-size: 14px;
            color: #718096;
            line-height: 1.7;
            min-height: 48px;
            margin-bottom: 20px;
        }

        .btn {
            display: inline-block;
            background: #245b9f;
            color: white;
            padding: 11px 19px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: 0.2s;
        }

        .btn:hover {
            background: #173866;
        }

        /* INFORMASI */
        .info-box {
            margin-top: 30px;
            padding: 23px;
            background: #e8f1ff;
            border-radius: 13px;
            border-left: 5px solid #3476bd;
        }

        .info-box h3 {
            color: #173866;
            margin-bottom: 8px;
            font-size: 16px;
        }

        .info-box p {
            color: #526176;
            font-size: 14px;
            line-height: 1.7;
        }

        /* IDENTITAS TIM PENGEMBANG */
        .team-section {
            margin-top: 38px;
        }

        .team-heading {
            text-align: center;
            margin-bottom: 25px;
        }

        .team-heading h2 {
            color: #173866;
            font-size: 23px;
            margin-bottom: 8px;
        }

        .team-heading p {
            color: #718096;
            font-size: 14px;
            line-height: 1.7;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 20px;
        }

        .team-card {
            background: white;
            border: 1px solid #e8edf5;
            border-radius: 15px;
            padding: 25px 20px;
            text-align: center;
            box-shadow: 0 5px 18px rgba(0,0,0,0.035);
            transition: 0.25s;
        }

        .team-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(0,0,0,0.07);
        }

        .team-icon {
            width: 65px;
            height: 65px;
            margin: 0 auto 15px;
            border-radius: 50%;
            background: #e8f1ff;
            color: #245b9f;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 25px;
            font-weight: 700;
        }

        .team-card h3 {
            color: #173866;
            font-size: 16px;
            margin-bottom: 10px;
            line-height: 1.5;
        }

        .team-card .nim {
            display: inline-block;
            background: #f0f5fc;
            color: #245b9f;
            padding: 7px 12px;
            border-radius: 7px;
            font-size: 13px;
            font-weight: 600;
        }

        .team-identity {
            margin-top: 25px;
            background: white;
            border: 1px solid #e8edf5;
            border-radius: 13px;
            padding: 22px;
            text-align: center;
            line-height: 1.9;
        }

        .team-identity strong {
            color: #173866;
        }

        .team-identity p {
            color: #718096;
            font-size: 14px;
        }

        /* FOOTER */
        footer {
            background: #142d55;
            color: #dce8f7;
            text-align: center;
            padding: 28px 15px;
            margin-top: 55px;
            font-size: 13px;
            line-height: 1.9;
        }

        footer strong {
            color: white;
        }

        footer a {
            color: #91c5ff;
            text-decoration: none;
        }

        /* MOBILE */
        @media(max-width: 600px) {
            .navbar {
                padding: 17px 5%;
            }

            .nav-menu {
                gap: 13px;
                flex-wrap: wrap;
            }

            .nav-menu a {
                font-size: 12px;
            }

            .hero {
                padding: 28px 22px;
            }

            .hero h1 {
                font-size: 25px;
            }

            .container {
                padding: 0 16px;
            }
        }
    </style>
</head>

<body>

<nav class="navbar">

    <div class="logo">
        SIPES<span>MA</span>
    </div>

    <div class="nav-menu">
        <a href="dashboard.php">Dashboard</a>
        <a href="tentang.php">Tentang Aplikasi</a>
        <a href="logout.php" class="logout">Keluar</a>
    </div>

</nav>

<main class="container">

    <section class="hero">

        <p class="welcome">SELAMAT DATANG DI SIPESMA</p>

        <h1>Halo, <?= $nama; ?>!</h1>

        <p>
            Selamat datang di Sistem Informasi Pengajuan Surat Mahasiswa.
            Kelola kebutuhan administrasi surat akademik Anda dengan
            lebih mudah, praktis, dan terstruktur.
        </p>

        <div class="role-label">
            <?= htmlspecialchars(strtoupper($role)); ?>
        </div>

    </section>

    <section>

        <div class="section-heading">

            <h2>
                <?= $role === "admin" ? "Panel Administrasi" : "Layanan Mahasiswa"; ?>
            </h2>

            <p>
                <?= $role === "admin"
                    ? "Kelola dan pantau seluruh pengajuan surat mahasiswa."
                    : "Pilih layanan yang ingin Anda gunakan."; ?>
            </p>

        </div>

        <?php if ($role === "mahasiswa"): ?>

            <div class="daftar-surat">

                <div class="kartu">

                    <div class="icon-box">📄</div>

                    <h3>Pengajuan Surat</h3>

                    <p>
                        Ajukan permohonan surat akademik secara digital
                        melalui sistem SIPESMA.
                    </p>

                    <a href="pengajuan.php" class="btn">
                        Ajukan Sekarang →
                    </a>

                </div>

                <div class="kartu">

                    <div class="icon-box">📋</div>

                    <h3>Riwayat Pengajuan</h3>

                    <p>
                        Lihat seluruh riwayat permohonan dan pantau
                        perkembangan status pengajuan surat Anda.
                    </p>

                    <a href="riwayat.php" class="btn">
                        Lihat Riwayat →
                    </a>

                </div>

            </div>

        <?php elseif ($role === "admin"): ?>

            <div class="daftar-surat">

                <div class="kartu">

                    <div class="icon-box">📑</div>

                    <h3>Kelola Pengajuan</h3>

                    <p>
                        Periksa, verifikasi, dan proses seluruh
                        permohonan surat yang diajukan mahasiswa.
                    </p>

                    <a href="admin_pengajuan.php" class="btn">
                        Lihat Semua Pengajuan →
                    </a>

                </div>

                <div class="kartu">

                    <div class="icon-box">⚙️</div>

                    <h3>Kelola Jenis Surat</h3>

                    <p>
                        Tambahkan, ubah, dan kelola jenis surat
                        yang tersedia dalam sistem.
                    </p>

                    <a href="kelola_surat.php" class="btn">
                        Kelola Surat →
                    </a>

                </div>

            </div>

        <?php endif; ?>

    </section>

    <section class="info-box">

        <h3>Informasi SIPESMA</h3>

        <p>
            SIPESMA merupakan aplikasi berbasis web yang dikembangkan
            untuk mendukung digitalisasi proses administrasi pengajuan
            surat mahasiswa di lingkungan perguruan tinggi.
            Sistem ini dikembangkan oleh mahasiswa Program Studi
            Pendidikan Teknologi Informatika dan Komputer,
            Fakultas Teknik, Universitas Negeri Medan.
        </p>

    </section>

    <!-- IDENTITAS TIM PENGEMBANG -->
    <section class="team-section">

        <div class="team-heading">

            <h2>Tim Pengembang SIPESMA</h2>

            <p>
                Aplikasi ini dikembangkan sebagai bagian dari proyek
                perkuliahan oleh mahasiswa Program Studi PTIK.
            </p>

        </div>

        <div class="team-grid">

            <div class="team-card">

                <div class="team-icon">A</div>

                <h3>Afnel Yoniva Marbun</h3>

                <span class="nim">NIM: 5243151046</span>

            </div>

            <div class="team-card">

                <div class="team-icon">P</div>

                <h3>Putri Adelia Lumbantobing</h3>

                <span class="nim">NIM: 5243151009</span>

            </div>

            <div class="team-card">

                <div class="team-icon">F</div>

                <h3>Febri Ashari Harahap</h3>

                <span class="nim">NIM: 5243351025</span>

            </div>

        </div>

        <div class="team-identity">

            <p><strong>Program Studi</strong></p>
            <p>Pendidikan Teknologi Informatika dan Komputer (PTIK)</p>

            <br>

            <p><strong>Fakultas</strong></p>
            <p>Fakultas Teknik</p>

            <br>

            <p><strong>Universitas</strong></p>
            <p>Universitas Negeri Medan</p>

            <br>

            <p><strong>Tahun Pengembangan</strong></p>
            <p>2026</p>

        </div>

    </section>

</main>

<footer>

    <strong>SIPESMA</strong> — Sistem Informasi Pengajuan Surat Mahasiswa

    <br>

    Dikembangkan oleh Mahasiswa PTIK, Fakultas Teknik

    <br>

    Universitas Negeri Medan © 2026

    <br>

    <a href="tentang.php">Tentang Tim Pengembang</a>

</footer>

</body>
</html>