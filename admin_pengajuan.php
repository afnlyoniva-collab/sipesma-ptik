
<?php
session_start();
require_once "koneksi.php";

if (!isset($_SESSION["id"])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION["role"] != "admin") {
    header("Location: dashboard.php");
    exit;
}

// Membuat token keamanan formulir
if (!isset($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

// Mengambil data pengajuan
$sql = "SELECT
            pengajuan.id,
            users.nama,
            users.email,
            jenis_surat.nama_surat,
            pengajuan.keperluan,
            pengajuan.status,
            pengajuan.catatan_admin,
            pengajuan.created_at
        FROM pengajuan
        LEFT JOIN users
            ON pengajuan.user_id = users.id
        LEFT JOIN jenis_surat
            ON pengajuan.jenis_surat_id = jenis_surat.id
        ORDER BY pengajuan.created_at DESC";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Gagal mengambil data: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengajuan - SIPESMA</title>
    <link rel="stylesheet" href="css/style.css">

    <style>
        .tabel-container {
            width: 90%;
            margin: 30px auto;
            overflow-x: auto;
            background: white;
            padding: 20px;
            border-radius: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1150px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #193b64;
            color: white;
        }

        tr:nth-child(even) {
            background: #f2f5fa;
        }

        .judul-halaman {
            text-align: center;
            margin-top: 40px;
        }

        .kembali {
            display: inline-block;
            margin: 20px 5%;
        }

        .form-status select,
        .form-status textarea {
            width: 100%;
            padding: 9px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
        }

        .form-status textarea {
            min-height: 75px;
            resize: vertical;
        }

        .btn-simpan {
            background: #193b64;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 6px;
            cursor: pointer;
        }

        .pesan-sukses {
            width: 90%;
            margin: 15px auto;
            padding: 12px;
            background: #d1fae5;
            color: #065f46;
            border-radius: 8px;
        }
    </style>
</head>

<body>

<nav class="navbar">
    <div class="logo">SIPESMA</div>
    <a href="logout.php" class="nav-link">Keluar</a>
</nav>

<h1 class="judul-halaman">Daftar Pengajuan Surat Mahasiswa</h1>

<a href="dashboard.php" class="btn kembali">Kembali ke Dashboard</a>

<?php if (isset($_GET["berhasil"])): ?>
    <div class="pesan-sukses">
        Perubahan pengajuan berhasil disimpan!
    </div>
<?php endif; ?>

<div class="tabel-container">

    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Nama Mahasiswa</th>
                <th>Email</th>
                <th>Jenis Surat</th>
                <th>Keperluan</th>
                <th>Status</th>
                <th>Catatan Admin</th>
                <th>Tanggal Pengajuan</th>
                <th>Aksi Admin</th>
            </tr>
        </thead>

        <tbody>

        <?php if (mysqli_num_rows($result) > 0): ?>

            <?php $no = 1; ?>

            <?php while ($row = mysqli_fetch_assoc($result)): ?>

            <tr>
                <td><?= $no++; ?></td>

                <td>
                    <?= htmlspecialchars($row["nama"] ?? "-"); ?>
                </td>

                <td>
                    <?= htmlspecialchars($row["email"] ?? "-"); ?>
                </td>

                <td>
                    <?= htmlspecialchars($row["nama_surat"] ?? "-"); ?>
                </td>

                <td>
                    <?= htmlspecialchars($row["keperluan"] ?? "-"); ?>
                </td>

                <td>
                    <?= htmlspecialchars(ucfirst($row["status"])); ?>
                </td>

                <td>
                    <?= htmlspecialchars($row["catatan_admin"] ?? "-"); ?>
                </td>

                <td>
                    <?= htmlspecialchars($row["created_at"]); ?>
                </td>

                <td>
                    <form class="form-status" action="proses_pengajuan.php" method="POST">

                        <input type="hidden" name="csrf_token"
                            value="<?= htmlspecialchars($_SESSION["csrf_token"]); ?>">

                        <input type="hidden" name="id"
                            value="<?= (int)$row["id"]; ?>">

                        <label>Status Pengajuan</label>

                        <select name="status" required>
                            <option value="menunggu"
                                <?= $row["status"] == "menunggu" ? "selected" : ""; ?>>
                                Menunggu
                            </option>

                            <option value="diproses"
                                <?= $row["status"] == "diproses" ? "selected" : ""; ?>>
                                Diproses
                            </option>

                            <option value="disetujui"
                                <?= $row["status"] == "disetujui" ? "selected" : ""; ?>>
                                Disetujui
                            </option>

                            <option value="ditolak"
                                <?= $row["status"] == "ditolak" ? "selected" : ""; ?>>
                                Ditolak
                            </option>
                        </select>

                        <label>Catatan Admin</label>

                        <textarea name="catatan_admin"
                            placeholder="Tuliskan catatan untuk mahasiswa..."><?= htmlspecialchars($row["catatan_admin"] ?? ""); ?></textarea>

                        <button type="submit" class="btn-simpan">
                            Simpan Perubahan
                        </button>

                    </form>
                </td>
            </tr>

            <?php endwhile; ?>

        <?php else: ?>

            <tr>
                <td colspan="9" style="text-align:center;">
                    Belum ada pengajuan surat dari mahasiswa.
                </td>
            </tr>

        <?php endif; ?>

        </tbody>
    </table>

</div>

<footer>&copy; 2026 SIPESMA</footer>

</body>
</html>
