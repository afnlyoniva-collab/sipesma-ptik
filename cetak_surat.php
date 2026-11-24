
<?php
session_start();
require_once "koneksi.php";

// Hanya mahasiswa yang sudah login
if (!isset($_SESSION["id"]) || $_SESSION["role"] !== "mahasiswa") {
    header("Location: login.php");
    exit;
}

// Memeriksa ID pengajuan surat
if (!isset($_GET["id"]) || !ctype_digit($_GET["id"])) {
    die("ID pengajuan tidak valid.");
}

$user_id = (int) $_SESSION["id"];
$pengajuan_id = (int) $_GET["id"];

// Mengambil data pengajuan surat dan mahasiswa
$stmt = mysqli_prepare(
    $conn,
    "SELECT p.*, j.nama_surat, j.deskripsi, u.nama, u.email
     FROM pengajuan p
     JOIN jenis_surat j ON p.jenis_surat_id = j.id
     JOIN users u ON p.user_id = u.id
     WHERE p.id = ? AND p.user_id = ?"
);

if (!$stmt) {
    die("Gagal mengambil data surat.");
}

mysqli_stmt_bind_param($stmt, "ii", $pengajuan_id, $user_id);
mysqli_stmt_execute($stmt);

$hasil = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($hasil);

mysqli_stmt_close($stmt);

if (!$data) {
    die("Data pengajuan tidak ditemukan.");
}

// Surat hanya dapat dibuat setelah disetujui admin
if ($data["status"] !== "disetujui") {
    die("Maaf, surat hanya dapat dicetak setelah pengajuan disetujui admin.");
}

// Tanggal dalam bahasa Indonesia
$bulan = [
    1 => "Januari", "Februari", "Maret", "April",
    "Mei", "Juni", "Juli", "Agustus",
    "September", "Oktober", "November", "Desember"
];

$tanggal = date("j") . " " . $bulan[(int) date("n")] . " " . date("Y");

// Menyesuaikan isi berdasarkan jenis surat
$nama_surat = $data["nama_surat"];

if (stripos($nama_surat, "PKLI") !== false ||
    stripos($nama_surat, "Praktek Kerja Lapangan") !== false) {

    $isi_surat = "Dalam rangka meningkatkan kemampuan dan keterampilan mahasiswa, dengan hormat kami memohon bantuan Saudara untuk memberikan izin kepada mahasiswa kami untuk melaksanakan Praktek Kerja Lapangan Industri (PKLI). Adapun mahasiswa tersebut adalah sebagaimana tercantum dalam identitas di bawah ini.";

} else {

    $isi_surat = "Dengan hormat, sehubungan dengan permohonan " .
        htmlspecialchars($nama_surat) .
        ", bersama ini kami menyampaikan bahwa mahasiswa dengan identitas berikut telah mengajukan permohonan melalui Sistem Informasi Pengajuan Surat Mahasiswa (SIPESMA). Isi dan ketentuan surat ini perlu disesuaikan serta diverifikasi oleh pihak fakultas sebelum diterbitkan.";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat - SIPESMA</title>

    <style>
        body {
            font-family: "Times New Roman", serif;
            background: #e5e7eb;
            color: #000;
            margin: 0;
            padding: 25px;
        }

        .surat {
            width: 210mm;
            min-height: 297mm;
            box-sizing: border-box;
            padding: 20mm 25mm;
            margin: auto;
            background: white;
        }

        .kop {
            display: flex;
            align-items: center;
            gap: 15px;
            text-align: center;
        }

        .logo {
            width: 85px;
            height: 85px;
            object-fit: contain;
        }

        .kop-text {
            flex: 1;
        }

        .kop-text h3,
        .kop-text h2,
        .kop-text p {
            margin: 3px 0;
        }

        .kop-text h3 {
            font-size: 13px;
            font-weight: normal;
        }

        .kop-text h2 {
            font-size: 19px;
        }

        .kop-text h4 {
            font-size: 16px;
            margin: 3px 0;
        }

        .kop-text p {
            font-size: 11px;
        }

        .garis {
            border-top: 3px solid black;
            border-bottom: 1px solid black;
            height: 3px;
            margin: 12px 0 25px;
        }

        .tanggal {
            text-align: right;
            margin-bottom: 20px;
        }

        .identitas {
            border-collapse: collapse;
            width: 100%;
            margin: 15px 0;
        }

        .identitas td {
            padding: 5px;
            vertical-align: top;
        }

        .isi {
            text-align: justify;
            line-height: 1.5;
            font-size: 12pt;
        }

        .ttd {
            width: 260px;
            margin-left: auto;
            margin-top: 40px;
            text-align: left;
            line-height: 1.5;
        }

        .nama-pejabat {
            margin-top: 65px;
            font-weight: bold;
            text-decoration: underline;
        }

        .peringatan {
            margin-top: 25px;
            border: 1px dashed #777;
            padding: 10px;
            font-family: Arial, sans-serif;
            font-size: 10px;
            text-align: center;
        }

        .tombol {
            display: block;
            margin: 20px auto;
            padding: 12px 22px;
            border: none;
            border-radius: 5px;
            background: #173f78;
            color: white;
            cursor: pointer;
            font-size: 15px;
        }

        @media print {
            @page {
                size: A4;
                margin: 0;
            }

            body {
                background: white;
                padding: 0;
            }

            .surat {
                margin: 0;
                width: 210mm;
                min-height: 297mm;
            }

            .tombol {
                display: none;
            }
        }
    </style>
</head>

<body>

<div class="surat">

    <div class="kop">

        <img src="images/logo-unimed.jpg"
             alt="Logo UNIMED"
             class="logo">

        <div class="kop-text">
            <h3>KEMENTERIAN PENDIDIKAN TINGGI, SAINS, DAN TEKNOLOGI</h3>
            <h2>UNIVERSITAS NEGERI MEDAN</h2>
            <h4>FAKULTAS TEKNIK</h4>
            <p>
                Jalan Willem Iskandar Psr. V - Kotak Pos No. 1589 - Medan 20221
            </p>
            <p>
                Telepon (061) 6625971, Fax. (061) 6614002 - 6613319
            </p>
            <p>Laman: www.ft.unimed.ac.id</p>
        </div>

    </div>

    <div class="garis"></div>

    <div class="tanggal">
        Medan, <?= htmlspecialchars($tanggal); ?>
    </div>

    <div class="isi">

        <table class="identitas">
            <tr>
                <td width="90">Nomor</td>
                <td width="10">:</td>
                <td>........................../UN33.5.1/PG/<?= date("Y"); ?></td>
            </tr>
            <tr>
                <td>Lampiran</td>
                <td>:</td>
                <td>-</td>
            </tr>
            <tr>
                <td>Hal</td>
                <td>:</td>
                <td><?= htmlspecialchars($nama_surat); ?></td>
            </tr>
        </table>

        <br>

        <p>Yth. Pihak yang Berkepentingan</p>
        <p>di Tempat</p>

        <p>
            <?= $isi_surat; ?>
        </p>

        <table class="identitas">
            <tr>
                <td width="130">Nama</td>
                <td width="10">:</td>
                <td><?= htmlspecialchars($data["nama"]); ?></td>
            </tr>
            <tr>
                <td>Email</td>
                <td>:</td>
                <td><?= htmlspecialchars($data["email"]); ?></td>
            </tr>
            <tr>
                <td>Jenis Surat</td>
                <td>:</td>
                <td><?= htmlspecialchars($nama_surat); ?></td>
            </tr>
            <tr>
                <td>Keperluan</td>
                <td>:</td>
                <td><?= nl2br(htmlspecialchars($data["keperluan"])); ?></td>
            </tr>
        </table>

        <p>
            Demikian surat ini dibuat untuk dapat diproses sesuai dengan
            ketentuan yang berlaku. Atas perhatian dan kerja sama yang baik,
            kami mengucapkan terima kasih.
        </p>

        <div class="ttd">
            <p>a.n. Dekan,</p>
            <p>Wakil Dekan Bidang Akademik</p>

            <div class="nama-pejabat">
                [Nama Pejabat Berwenang]
            </div>
            <p>NIP. [NIP Pejabat]</p>
        </div>

    </div>

    <div class="peringatan">
        DRAF SURAT SIPESMA — Dokumen ini belum menjadi surat resmi.
        Nomor surat, tujuan, isi, dan tanda tangan wajib diverifikasi
        serta disahkan oleh pejabat Fakultas Teknik Universitas Negeri Medan.
    </div>

</div>

<button class="tombol" onclick="window.print()">
    Cetak Surat / Simpan PDF
</button>

</body>
</html>
