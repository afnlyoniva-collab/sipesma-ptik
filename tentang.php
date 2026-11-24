
<?php
session_start();

if (!isset($_SESSION["id"])) {
    header("Location: login.php");
    exit;
}

$nama = htmlspecialchars($_SESSION["nama"] ?? "Pengguna");
$role = $_SESSION["role"] ?? "mahasiswa";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang SIPESMA</title>

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

        .navbar {
            background: #142d55;
            color: white;
            padding: 17px 7%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
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
            gap: 22px;
            align-items: center;
        }

        .nav-menu a {
            color: #e5efff;
            text-decoration: none;
            font-size: 14px;
        }

        .nav-menu a:hover {
            color: #70b7ff;
        }

        .nav-menu .logout {
            background: white;
            color: #173866;
            padding: 9px 17px;
            border-radius: 8px;
            font-weight: 600;
        }

        .container {
            max-width: 1100px;
            margin: 35px auto;
            padding: 0 22px;
        }

        .hero {
            background: linear-gradient(120deg, #173866, #3476bd);
            color: white;
            padding: 45px 35px;
            border-radius: 18px;
            text-align: center;
        }

        .hero .label {
            color: #d8eaff;
            font-size: 13px;
            letter-spacing: 2px;
            margin-bottom: 12px;
        }

        .hero h1 {
            font-size: 32px;
            margin-bottom: 15px;
        }

        .hero p {
            max-width: 750px;
            margin: auto;
            color: #e5efff;
            line-height: 1.8;
            font-size: 15px;
        }

        .section {
            margin-top: 35px;
        }

        .section-title {
            color: #173866;
            font-size: 23px;
            margin-bottom: 15px;
        }

        .description {
            background: white;
            padding: 27px;
            border-radius: 15px;
            border: 1px solid #e8edf5;
            line-height: 1.9;
            color: #526176;
            font-size: 14px;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }

        .feature-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            border: 1px solid #e8edf5;
        }

        .feature-icon {
            width: 48px;
            height: 48px;
            background: #e8f1ff;
            color: #245b9f;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 12px;
            font-size: 22px;
            margin-bottom: 15px;
        }

        .feature-card h3 {
            color: #173866;
            margin-bottom: 10px;
            font-size: 17px;
        }

        .feature-card p {
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
            padding: 27px 18px;
            text-align: center;
            box-shadow: 0 5px 18px rgba(0,0,0,0.03);
        }

        .team-icon {
            width: 65px;
            height: 65px;
            margin: 0 auto 15px;
            background: #e8f1ff;
            color: #245b9f;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50%;
            font-size: 25px;
            font-weight: 700;
        }

        .team-card h3 {
            color: #173866;
            font-size: 16px;
            line-height: 1.5;
            margin-bottom: 10px;
        }

        .nim {
            display: inline-block;
            background: #f0f5fc;
            color: #245b9f;
            padding: 7px 12px;
            border-radius: 7px;
            font-size: 13px;
            font-weight: 600;
        }

        .campus {
            margin-top: 25px;
            padding: 25px;
            background: #e8f1ff;
            border-left: 5px solid #3476bd;
            border-radius: 12px;
            text-align: center;
            line-height: 1.9;
        }

        .campus strong {
            color: #173866;
        }

        .campus p {
            color: #526176;
            font-size: 14px;
        }

        footer {
            background: #142d55;
            color: #dce8f7;
            text-align: center;
            padding: 28px 15px;
            margin-top: 55px;
            font-size: 13px;
            line-height: 1.9;
        }

        @media(max-width: 600px) {
            .navbar {
                padding: 17px 5%;
            }

            .nav-menu {
                gap: 13px;
                flex-wrap: wrap;
            }

            .hero {
                padding: 35px 22px;
            }

            .hero h1 {
                font-size: 26px;
            }

            .container {
                padding: 0 16px;
            }
        }
    </style>
</head>

<body>

<nav class="navbar">
    <div class="logo">SIPES<span>MA</span></div>

    <div class="nav-menu">
        <a href="dashboard.php">Dashboard</a>
        <a href="tentang.php">Tentang Aplikasi</a>
        <a href="logout.php" class="logout">Keluar</a>
    </div>
</nav>

<main class="container">

    <section class="hero">
        <p class="label">SISTEM INFORMASI</p>

        <h1>Tentang SIPESMA</h1>

        <p>
            Sistem Informasi Pengajuan Surat Mahasiswa merupakan
            aplikasi berbasis web yang dirancang untuk membantu
            proses pengajuan dan pengelolaan surat mahasiswa
            secara digital, praktis, dan terstruktur.
        </p>
    </section>

    <section class="section">
        <h2 class="section-title">Latar Belakang Aplikasi</h2>

        <div class="description">
            SIPESMA dikembangkan sebagai salah satu upaya
            pemanfaatan teknologi informasi dalam mendukung
            pelayanan administrasi akademik mahasiswa.

            <br><br>

            Melalui aplikasi ini, mahasiswa dapat mengajukan
            permohonan surat secara online dan memantau status
            pengajuan. Sementara itu, administrator dapat
            memeriksa, memproses, serta mengelola pengajuan
            surat yang masuk melalui sistem.
        </div>
    </section>

    <section class="section">
        <h2 class="section-title">Fitur Utama SIPESMA</h2>

        <div class="feature-grid">

            <div class="feature-card">
                <div class="feature-icon">📄</div>
                <h3>Pengajuan Surat</h3>
                <p>
                    Mahasiswa dapat mengajukan permohonan
                    surat melalui formulir digital.
                </p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">📋</div>
                <h3>Riwayat Pengajuan</h3>
                <p>
                    Mahasiswa dapat melihat riwayat dan
                    perkembangan status permohonan surat.
                </p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">⚙️</div>
                <h3>Pengelolaan Admin</h3>
                <p>
                    Administrator dapat memeriksa dan
                    memproses permohonan surat mahasiswa.
                </p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">🖨️</div>
                <h3>Cetak Dokumen</h3>
                <p>
                    Pengguna dapat mencetak bukti pengajuan
                    dan dokumen surat sesuai status pengajuan.
                </p>
            </div>

        </div>
    </section>

    <section class="section">
        <h2 class="section-title">Tim Pengembang</h2>

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
                <span class="nim">NIM: 525243351025</span>
            </div>

        </div>

        <div class="campus">
            <p><strong>Program Studi</strong></p>
            <p>Pendidikan Teknologi Informatika dan Komputer (PTIK)</p>

            <p><strong>Fakultas</strong></p>
            <p>Fakultas Teknik</p>

            <p><strong>Universitas</strong></p>
            <p>Universitas Negeri Medan</p>

            <p><strong>Tahun Pengembangan</strong></p>
            <p>2026</p>
        </div>
    </section>

</main>

<footer>
    <strong>SIPESMA</strong> — Sistem Informasi Pengajuan Surat Mahasiswa
    <br>
    Mahasiswa PTIK, Fakultas Teknik
    <br>
    Universitas Negeri Medan © 2026
</footer>

</body>
</html>
