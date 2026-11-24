
<?php
session_start();
require_once "koneksi.php";

// Memastikan hanya mahasiswa yang bisa membuka halaman
if (!isset($_SESSION["id"]) || $_SESSION["role"] !== "mahasiswa") {
    header("Location: login.php");
    exit;
}

$user_id = (int) $_SESSION["id"];

// Mengambil riwayat pengajuan mahasiswa
$stmt = mysqli_prepare(
    $conn,
    "SELECT p.*, j.nama_surat
     FROM pengajuan p
     JOIN jenis_surat j ON p.jenis_surat_id = j.id
     WHERE p.user_id = ?
     ORDER BY p.created_at DESC"
);

if (!$stmt) {
    die("Gagal menyiapkan data: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$hasil = mysqli_stmt_get_result($stmt);

// Keterangan status
$keterangan_status = [
    "menunggu" => "Pengajuan Anda sedang menunggu pemeriksaan admin.",
    "diproses" => "Pengajuan Anda sedang diproses oleh admin.",
    "disetujui" => "Selamat! Pengajuan surat Anda telah disetujui.",
    "ditolak" => "Mohon maaf, pengajuan surat Anda ditolak. Silakan baca catatan admin."
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pengajuan - SIPESMA</title>
    <link rel="stylesheet" href="css/style.css">

    <style>
        .riwayat {
            max-width: 1000px;
            margin: 50px auto;
            padding: 25px;
        }

        .riwayat h2 {
            color: #173f78;
            margin-bottom: 25px;
        }

        .item {
            background: white;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.05);
        }

        .item h3 {
            color: #245da5;
            margin-bottom: 15px;
        }

        .status {
            display: inline-block;
            padding: 7px 14px;
            border-radius: 20px;
            background: #fff3cd;
            color: #856404;
            font-size: 13px;
            font-weight: bold;
            margin-top: 12px;
        }

        .status.disetujui {
            background: #d1fae5;
            color: #065f46;
        }

        .status.ditolak {
            background: #fee2e2;
            color: #991b1b;
        }

        .status.diproses {
            background: #dbeafe;
            color: #1e40af;
        }

        .keterangan {
            margin-top: 12px;
            padding: 12px;
            border-radius: 8px;
            background: #f1f5f9;
            color: #334155;
        }

        .catatan {
            margin-top: 15px;
            padding: 15px;
            border-left: 4px solid #245da5;
            background: #f0f5fc;
            border-radius: 5px;
        }

        .kosong {
            background: white;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
        }

        .tanggal {
            color: #64748b;
            font-size: 14px;
        }

        .tombol-cetak {
            display: inline-block;
            margin-top: 18px;
            margin-right: 8px;
            padding: 10px 18px;
            background: #173f78;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: bold;
        }

        .tombol-cetak:hover {
            background: #245da5;
        }

        .tombol-surat {
            background: #15803d;
        }

        .tombol-surat:hover {
            background: #166534;
        }
    </style>
</head>

<body>

<nav class="navbar">
    <div class="logo">SIPESMA</div>
    <a href="dashboard.php" class="nav-link">Dashboard</a>
</nav>

<div class="riwayat">

    <h2>Riwayat Pengajuan Surat</h2>

    <?php if (isset($_GET["pengajuan"])): ?>
        <p style="color:green; margin-bottom:20px;">
            Pengajuan berhasil dikirim!
        </p>
    <?php endif; ?>

    <?php if (mysqli_num_rows($hasil) > 0): ?>

        <?php while ($data = mysqli_fetch_assoc($hasil)): ?>

            <?php
            $status = $data["status"];
            $keterangan = $keterangan_status[$status] ?? "Status belum diketahui.";
            ?>

            <div class="item">

                <h3>
                    <?= htmlspecialchars($data["nama_surat"]); ?>
                </h3>

                <p>
                    <strong>Keperluan:</strong><br>
                    <?= nl2br(htmlspecialchars($data["keperluan"])); ?>
                </p>

                <p class="tanggal">
                    <strong>Tanggal Pengajuan:</strong>
                    <?= htmlspecialchars($data["created_at"]); ?>
                </p>

                <span class="status <?= htmlspecialchars($status); ?>">
                    <?= htmlspecialchars(ucfirst($status)); ?>
                </span>

                <div class="keterangan">
                    <?= htmlspecialchars($keterangan); ?>
                </div>

                <div class="catatan">
                    <strong>Catatan Admin:</strong><br>

                    <?php if (!empty($data["catatan_admin"])): ?>
                        <?= nl2br(htmlspecialchars($data["catatan_admin"])); ?>
                    <?php else: ?>
                        <span style="color:#64748b;">
                            Belum ada catatan dari admin.
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Tombol Cetak Bukti Pengajuan -->
                <a href="cetak_bukti.php?id=<?= (int) $data["id"]; ?>"
                   class="tombol-cetak"
                   target="_blank">
                    Cetak Bukti Pengajuan
                </a>

                <!-- Tombol Cetak Surat hanya jika disetujui -->
                <?php if ($status === "disetujui"): ?>

                    <a href="cetak_surat.php?id=<?= (int) $data["id"]; ?>"
                       class="tombol-cetak tombol-surat"
                       target="_blank">
                        Cetak Surat
                    </a>

                <?php endif; ?>

            </div>

        <?php endwhile; ?>

    <?php else: ?>

        <div class="kosong">
            <h3>Belum Ada Pengajuan</h3>
            <p>Silakan ajukan surat melalui menu pengajuan.</p>
        </div>

    <?php endif; ?>

    <a href="dashboard.php" class="btn">
        Kembali ke Dashboard
    </a>

</div>

</body>
</html>

<?php
mysqli_stmt_close($stmt);
?>
