
<?php
session_start();
require_once "koneksi.php";

if (!isset($_SESSION["id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

$error = "";

// Memproses tambah, edit, dan hapus
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $token = $_POST["csrf_token"] ?? "";

    if (!hash_equals($_SESSION["csrf_token"], $token)) {
        die("Permintaan tidak valid.");
    }

    $aksi = $_POST["aksi"] ?? "";

    // TAMBAH JENIS SURAT
    if ($aksi === "tambah") {

        $nama = trim($_POST["nama_surat"] ?? "");
        $deskripsi = trim($_POST["deskripsi"] ?? "");

        if ($nama === "" || $deskripsi === "") {
            $error = "Nama dan deskripsi wajib diisi.";
        } else {
            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO jenis_surat (nama_surat, deskripsi) VALUES (?, ?)"
            );

            mysqli_stmt_bind_param($stmt, "ss", $nama, $deskripsi);

            if (mysqli_stmt_execute($stmt)) {
                header("Location: kelola_surat.php?berhasil=tambah");
                exit;
            }

            $error = "Gagal menambahkan jenis surat.";
            mysqli_stmt_close($stmt);
        }
    }

    // EDIT JENIS SURAT
    elseif ($aksi === "edit") {

        $id = (int)($_POST["id"] ?? 0);
        $nama = trim($_POST["nama_surat"] ?? "");
        $deskripsi = trim($_POST["deskripsi"] ?? "");

        if ($id <= 0 || $nama === "" || $deskripsi === "") {
            $error = "Data edit tidak lengkap.";
        } else {
            $stmt = mysqli_prepare(
                $conn,
                "UPDATE jenis_surat SET nama_surat = ?, deskripsi = ? WHERE id = ?"
            );

            mysqli_stmt_bind_param($stmt, "ssi", $nama, $deskripsi, $id);

            if (mysqli_stmt_execute($stmt)) {
                header("Location: kelola_surat.php?berhasil=edit");
                exit;
            }

            $error = "Gagal mengubah jenis surat.";
            mysqli_stmt_close($stmt);
        }
    }

    // HAPUS JENIS SURAT
    elseif ($aksi === "hapus") {

        $id = (int)($_POST["id"] ?? 0);

        // Memeriksa apakah jenis surat sudah digunakan
        $cek = mysqli_prepare(
            $conn,
            "SELECT COUNT(*) AS jumlah FROM pengajuan WHERE jenis_surat_id = ?"
        );

        mysqli_stmt_bind_param($cek, "i", $id);
        mysqli_stmt_execute($cek);

        $hasil_cek = mysqli_stmt_get_result($cek);
        $data_cek = mysqli_fetch_assoc($hasil_cek);

        mysqli_stmt_close($cek);

        if ($id <= 0) {
            $error = "ID jenis surat tidak valid.";
        } elseif ($data_cek["jumlah"] > 0) {
            $error = "Jenis surat tidak dapat dihapus karena sudah digunakan dalam pengajuan mahasiswa.";
        } else {
            $hapus = mysqli_prepare(
                $conn,
                "DELETE FROM jenis_surat WHERE id = ?"
            );

            mysqli_stmt_bind_param($hapus, "i", $id);

            if (mysqli_stmt_execute($hapus)) {
                header("Location: kelola_surat.php?berhasil=hapus");
                exit;
            }

            $error = "Gagal menghapus jenis surat.";
            mysqli_stmt_close($hapus);
        }
    }
}

// Mengambil data yang akan diedit
$data_edit = null;
$id_edit = (int)($_GET["edit"] ?? 0);

if ($id_edit > 0) {
    $stmt_edit = mysqli_prepare(
        $conn,
        "SELECT * FROM jenis_surat WHERE id = ?"
    );

    mysqli_stmt_bind_param($stmt_edit, "i", $id_edit);
    mysqli_stmt_execute($stmt_edit);

    $hasil_edit = mysqli_stmt_get_result($stmt_edit);
    $data_edit = mysqli_fetch_assoc($hasil_edit);

    mysqli_stmt_close($stmt_edit);
}

// Mengambil seluruh jenis surat
$query = mysqli_query(
    $conn,
    "SELECT * FROM jenis_surat ORDER BY id DESC"
);

if (!$query) {
    die("Gagal mengambil data: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Jenis Surat - SIPESMA</title>
    <link rel="stylesheet" href="css/style.css">

    <style>
        .container {
            width: 90%;
            max-width: 1000px;
            margin: 35px auto;
        }

        .form-box, .tabel-box {
            background: white;
            padding: 25px;
            margin-bottom: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.06);
        }

        .form-box input,
        .form-box textarea {
            width: 100%;
            padding: 12px;
            margin: 8px 0 18px;
            border: 1px solid #ccc;
            border-radius: 7px;
            box-sizing: border-box;
        }

        .form-box textarea {
            min-height: 90px;
        }

        button, .btn-edit, .btn-batal {
            padding: 10px 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
        }

        .btn-simpan {
            background: #193b64;
            color: white;
        }

        .btn-edit {
            background: #2563eb;
            color: white;
        }

        .btn-hapus {
            background: #dc2626;
            color: white;
        }

        .btn-batal {
            background: #e5e7eb;
            color: #333;
        }

        .kembali {
            display: inline-block;
            margin-bottom: 20px;
            color: #193b64;
            text-decoration: none;
        }

        .pesan {
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 7px;
        }

        .sukses {
            background: #d1fae5;
            color: #065f46;
        }

        .gagal {
            background: #fee2e2;
            color: #991b1b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 650px;
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

        .tabel-box {
            overflow-x: auto;
        }

        .aksi {
            display: flex;
            gap: 7px;
            flex-wrap: wrap;
        }

        .aksi form {
            margin: 0;
        }
    </style>
</head>

<body>

<nav class="navbar">
    <div class="logo">SIPESMA</div>
    <a href="logout.php" class="nav-link">Keluar</a>
</nav>

<div class="container">

    <a href="dashboard.php" class="kembali">
        &larr; Kembali ke Dashboard
    </a>

    <h1>Kelola Jenis Surat</h1>
    <p>Admin dapat mengelola daftar jenis surat mahasiswa.</p>

    <?php if (isset($_GET["berhasil"])): ?>
        <div class="pesan sukses">
            Jenis surat berhasil
            <?= htmlspecialchars($_GET["berhasil"]); ?>!
        </div>
    <?php endif; ?>

    <?php if ($error !== ""): ?>
        <div class="pesan gagal">
            <?= htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <div class="form-box">

        <?php if ($data_edit): ?>

            <h2>Edit Jenis Surat</h2>

            <form method="POST" action="kelola_surat.php">

                <input type="hidden" name="csrf_token"
                    value="<?= htmlspecialchars($_SESSION["csrf_token"]); ?>">

                <input type="hidden" name="aksi" value="edit">

                <input type="hidden" name="id"
                    value="<?= (int)$data_edit["id"]; ?>">

                <label>Nama Jenis Surat</label>
                <input type="text" name="nama_surat"
                    value="<?= htmlspecialchars($data_edit["nama_surat"]); ?>"
                    required>

                <label>Deskripsi Surat</label>
                <textarea name="deskripsi" required><?= htmlspecialchars($data_edit["deskripsi"]); ?></textarea>

                <button type="submit" class="btn-simpan">
                    Simpan Perubahan
                </button>

                <a href="kelola_surat.php" class="btn-batal">
                    Batal
                </a>

            </form>

        <?php else: ?>

            <h2>Tambah Jenis Surat</h2>

            <form method="POST" action="kelola_surat.php">

                <input type="hidden" name="csrf_token"
                    value="<?= htmlspecialchars($_SESSION["csrf_token"]); ?>">

                <input type="hidden" name="aksi" value="tambah">

                <label>Nama Jenis Surat</label>
                <input type="text" name="nama_surat"
                    placeholder="Contoh: Surat Keterangan Aktif Kuliah"
                    required>

                <label>Deskripsi Surat</label>
                <textarea name="deskripsi"
                    placeholder="Jelaskan kegunaan surat..."
                    required></textarea>

                <button type="submit" class="btn-simpan">
                    Tambahkan Surat
                </button>

            </form>

        <?php endif; ?>

    </div>

    <div class="tabel-box">

        <h2>Daftar Jenis Surat</h2>

        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama Surat</th>
                    <th>Deskripsi</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>

                <?php if (mysqli_num_rows($query) > 0): ?>

                    <?php $no = 1; ?>

                    <?php while ($row = mysqli_fetch_assoc($query)): ?>

                        <tr>
                            <td><?= $no++; ?></td>

                            <td>
                                <?= htmlspecialchars($row["nama_surat"]); ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($row["deskripsi"]); ?>
                            </td>

                            <td>
                                <div class="aksi">

                                    <a class="btn-edit"
                                        href="kelola_surat.php?edit=<?= (int)$row["id"]; ?>">
                                        Edit
                                    </a>

                                    <form method="POST"
                                        action="kelola_surat.php"
                                        onsubmit="return confirm('Yakin ingin menghapus jenis surat ini?');">

                                        <input type="hidden" name="csrf_token"
                                            value="<?= htmlspecialchars($_SESSION["csrf_token"]); ?>">

                                        <input type="hidden" name="aksi"
                                            value="hapus">

                                        <input type="hidden" name="id"
                                            value="<?= (int)$row["id"]; ?>">

                                        <button type="submit" class="btn-hapus">
                                            Hapus
                                        </button>

                                    </form>

                                </div>
                            </td>
                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="4" style="text-align:center;">
                            Belum ada jenis surat.
                        </td>
                    </tr>

                <?php endif; ?>

            </tbody>
        </table>

    </div>

</div>

<footer>&copy; 2026 SIPESMA</footer>

</body>
</html>
