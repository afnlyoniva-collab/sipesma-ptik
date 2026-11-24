
<?php
session_start();
require_once "koneksi.php";

// Memastikan mahasiswa sudah login
if (!isset($_SESSION["id"]) || ($_SESSION["role"] ?? "") !== "mahasiswa") {
    header("Location: login.php");
    exit;
}

// Membuat token keamanan jika belum tersedia
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

// Mengambil daftar jenis surat
$query = mysqli_query($conn, "SELECT * FROM jenis_surat ORDER BY nama_surat ASC");

if (!$query) {
    die("Gagal mengambil jenis surat: " . mysqli_error($conn));
}

$nama = htmlspecialchars($_SESSION["nama"] ?? "Mahasiswa");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan Surat - SIPESMA</title>
    <link rel="stylesheet" href="css/style.css">

    <style>
        .container-pengajuan {
            width: 90%;
            max-width: 650px;
            margin: 40px auto;
            padding: 30px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
        }

        .container-pengajuan h2 {
            text-align: center;
            margin-bottom: 10px;
        }

        .deskripsi {
            text-align: center;
            color: #666;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 7px;
            box-sizing: border-box;
            font-family: inherit;
        }

        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }

        .btn-kirim {
            width: 100%;
            padding: 13px;
            background: #193b64;
            color: white;
            border: none;
            border-radius: 7px;
            cursor: pointer;
            font-size: 16px;
        }

        .btn-kembali {
            display: inline-block;
            margin-bottom: 20px;
            color: #193b64;
            text-decoration: none;
        }

        .btn-kirim:hover {
            background: #102b4b;
        }
    </style>
</head>

<body>

<nav class="navbar">
    <div class="logo">SIPESMA</div>
    <a href="logout.php" class="nav-link">Keluar</a>
</nav>

<div class="container-pengajuan">

    <a href="dashboard.php" class="btn-kembali">
        &larr; Kembali ke Dashboard
    </a>

    <h2>Formulir Pengajuan Surat</h2>

    <p class="deskripsi">
        Halo, <?= $nama; ?>. Silakan isi formulir pengajuan surat di bawah ini.
    </p>

    <form action="simpan_pengajuan.php" method="POST">

        <!-- Token keamanan CSRF -->
        <input
            type="hidden"
            name="csrf_token"
            value="<?= htmlspecialchars($_SESSION["csrf_token"]); ?>"
        >

        <div class="form-group">
            <label for="jenis_surat">Pilih Jenis Surat</label>

            <select name="jenis_surat_id" id="jenis_surat" required>
                <option value="">-- Pilih Jenis Surat --</option>

                <?php while ($surat = mysqli_fetch_assoc($query)): ?>
                    <option value="<?= (int)$surat["id"]; ?>">
                        <?= htmlspecialchars($surat["nama_surat"]); ?>
                    </option>
                <?php endwhile; ?>

            </select>
        </div>

        <div class="form-group">
            <label for="keperluan">Keperluan Pengajuan</label>

            <textarea
                name="keperluan"
                id="keperluan"
                placeholder="Jelaskan keperluan pengajuan surat Anda..."
                required
            ></textarea>
        </div>

        <button type="submit" class="btn-kirim">
            Kirim Pengajuan
        </button>

    </form>

</div>

<footer>&copy; 2026 SIPESMA</footer>

</body>
</html>
