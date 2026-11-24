
<?php
session_start();
require_once "koneksi.php";

// Memastikan mahasiswa sudah login
if (!isset($_SESSION["id"]) || $_SESSION["role"] !== "mahasiswa") {
    header("Location: login.php");
    exit;
}

$user_id = (int) $_SESSION["id"];

// Mengambil data pengajuan berdasarkan ID dan pemiliknya
if (!isset($_GET["id"]) || !ctype_digit($_GET["id"])) {
    die("ID pengajuan tidak valid.");
}

$pengajuan_id = (int) $_GET["id"];

$stmt = mysqli_prepare(
    $conn,
    "SELECT p.*, j.nama_surat, u.nama, u.email
     FROM pengajuan p
     JOIN jenis_surat j ON p.jenis_surat_id = j.id
     JOIN users u ON p.user_id = u.id
     WHERE p.id = ? AND p.user_id = ?"
);

if (!$stmt) {
    die("Gagal mengambil data pengajuan.");
}

mysqli_stmt_bind_param($stmt, "ii", $pengajuan_id, $user_id);
mysqli_stmt_execute($stmt);

$hasil = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($hasil);

if (!$data) {
    die("Data pengajuan tidak ditemukan.");
}

mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Pengajuan - SIPESMA</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            margin: 0;
            padding: 30px;
            color: #1e293b;
        }

        .bukti {
            max-width: 700px;
            margin: 30px auto;
            padding: 40px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 10px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #173f78;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }

        .header h1 {
            color: #173f78;
            margin-bottom: 5px;
        }

        .header p {
            margin: 5px;
            color: #64748b;
        }

        .judul {
            text-align: center;
            margin-bottom: 25px;
        }

        .detail {
            line-height: 2;
        }

        .detail p {
            margin: 8px 0;
        }

        .status {
            display: inline-block;
            padding: 6px 12px;
            background: #dbeafe;
            border-radius: 5px;
            font-weight: bold;
        }

        .catatan {
            margin-top: 20px;
            padding: 15px;
            background: #f1f5f9;
            border-left: 4px solid #245da5;
        }

        .peringatan {
            margin-top: 25px;
            padding: 12px;
            background: #fff7ed;
            border: 1px solid #fed7aa;
            color: #9a3412;
            font-size: 13px;
            text-align: center;
        }

        .footer {
            margin-top: 35px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
        }

        .tombol {
            display: block;
            width: fit-content;
            margin: 20px auto;
            padding: 12px 22px;
            background: #173f78;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            border: none;
            cursor: pointer;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .bukti {
                margin: 0 auto;
                border: none;
                border-radius: 0;
            }

            .tombol {
                display: none;
            }
        }
    </style>
</head>

<body>

<div class="bukti">

    <div class="header">
        <h1>SIPESMA</h1>
        <p>Sistem Informasi Pengajuan Surat Mahasiswa</p>
    </div>

    <div class="judul">
        <h2>BUKTI PENGAJUAN SURAT</h2>
        <p>Dokumen bukti pengajuan mahasiswa</p>
    </div>

    <div class="detail">

        <p>
            <strong>ID Pengajuan:</strong>
            <?= (int) $data["id"]; ?>
        </p>

        <p>
            <strong>Nama Mahasiswa:</strong>
            <?= htmlspecialchars($data["nama"]); ?>
        </p>

        <p>
            <strong>Email:</strong>
            <?= htmlspecialchars($data["email"]); ?>
        </p>

        <p>
            <strong>Jenis Surat:</strong>
            <?= htmlspecialchars($data["nama_surat"]); ?>
        </p>

        <p>
            <strong>Keperluan:</strong><br>
            <?= nl2br(htmlspecialchars($data["keperluan"])); ?>
        </p>

        <p>
            <strong>Tanggal Pengajuan:</strong>
            <?= htmlspecialchars($data["created_at"]); ?>
        </p>

        <p>
            <strong>Status Pengajuan:</strong>
            <span class="status">
                <?= htmlspecialchars(ucfirst($data["status"])); ?>
            </span>
        </p>

    </div>

    <div class="catatan">
        <strong>Catatan Admin:</strong><br>

        <?php if (!empty($data["catatan_admin"])): ?>
            <?= nl2br(htmlspecialchars($data["catatan_admin"])); ?>
        <?php else: ?>
            Belum ada catatan dari admin.
        <?php endif; ?>
    </div>

    <div class="peringatan">
        Dokumen ini merupakan bukti bahwa pengajuan surat telah
        tercatat di sistem SIPESMA dan bukan merupakan surat resmi
        yang diterbitkan oleh pihak kampus.
    </div>

    <div class="footer">
        <p>Dicetak melalui Sistem Informasi Pengajuan Surat Mahasiswa (SIPESMA)</p>
    </div>

</div>

<button class="tombol" onclick="window.print()">
    Cetak / Simpan PDF
</button>

</body>
</html>
