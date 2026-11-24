
<?php
session_start();
require_once "koneksi.php";

// Memastikan pengguna sudah login
if (!isset($_SESSION["id"])) {
    header("Location: login.php");
    exit;
}

// Memastikan hanya admin yang bisa memproses
if ($_SESSION["role"] != "admin") {
    header("Location: dashboard.php");
    exit;
}

// Memastikan formulir dikirim menggunakan POST
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: admin_pengajuan.php");
    exit;
}

// Memeriksa token keamanan
if (
    !isset($_POST["csrf_token"]) ||
    !hash_equals($_SESSION["csrf_token"], $_POST["csrf_token"])
) {
    die("Permintaan tidak valid. Silakan kembali dan coba lagi.");
}

// Mengambil data dari formulir
$id = filter_var($_POST["id"] ?? null, FILTER_VALIDATE_INT);
$status = $_POST["status"] ?? "";
$catatan = trim($_POST["catatan_admin"] ?? "");

// Status yang diperbolehkan
$status_valid = ["menunggu", "diproses", "disetujui", "ditolak"];

// Memeriksa data
if (!$id || !in_array($status, $status_valid, true)) {
    die("Data pengajuan tidak valid.");
}

// Memperbarui status dan catatan admin
$sql = "UPDATE pengajuan
        SET status = ?, catatan_admin = ?
        WHERE id = ?";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die("Gagal menyiapkan perintah: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "ssi", $status, $catatan, $id);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);

    header("Location: admin_pengajuan.php?berhasil=1");
    exit;
} else {
    die("Gagal memperbarui pengajuan: " . mysqli_stmt_error($stmt));
}
?>
