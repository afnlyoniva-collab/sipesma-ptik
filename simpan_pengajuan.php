
<?php
session_start();
require_once "koneksi.php";

// Memastikan mahasiswa sudah login
if (!isset($_SESSION["id"]) || ($_SESSION["role"] ?? "") !== "mahasiswa") {
    header("Location: login.php");
    exit;
}

// Memastikan formulir dikirim menggunakan POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: pengajuan.php");
    exit;
}

// Memeriksa token keamanan
if (
    empty($_SESSION["csrf_token"]) ||
    empty($_POST["csrf_token"]) ||
    !hash_equals($_SESSION["csrf_token"], $_POST["csrf_token"])
) {
    die("Token keamanan tidak valid. Silakan buka ulang formulir pengajuan.");
}

// Mengambil data formulir
$user_id = (int) $_SESSION["id"];
$jenis_surat_id = (int) ($_POST["jenis_surat_id"] ?? 0);
$keperluan = trim($_POST["keperluan"] ?? "");

// Memeriksa apakah jenis surat dipilih
if ($jenis_surat_id <= 0) {
    die("Jenis surat belum terbaca. Silakan kembali dan pilih jenis surat.");
}

// Memeriksa keperluan
$panjang_keperluan = function_exists("mb_strlen")
    ? mb_strlen($keperluan, "UTF-8")
    : strlen($keperluan);

if ($panjang_keperluan < 10) {
    die("Keperluan terlalu pendek. Minimal 10 karakter.");
}

if ($panjang_keperluan > 2000) {
    die("Keperluan terlalu panjang. Maksimal 2000 karakter.");
}

// Memastikan jenis surat tersedia
$cek = mysqli_prepare(
    $conn,
    "SELECT id FROM jenis_surat WHERE id = ?"
);

if (!$cek) {
    die("Gagal memeriksa jenis surat: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($cek, "i", $jenis_surat_id);
mysqli_stmt_execute($cek);
mysqli_stmt_store_result($cek);

if (mysqli_stmt_num_rows($cek) === 0) {
    mysqli_stmt_close($cek);
    die("Jenis surat tidak ditemukan di database.");
}

mysqli_stmt_close($cek);

// Menyimpan pengajuan
$stmt = mysqli_prepare(
    $conn,
    "INSERT INTO pengajuan
    (user_id, jenis_surat_id, keperluan, status)
    VALUES (?, ?, ?, 'menunggu')"
);

if (!$stmt) {
    die("Gagal menyiapkan penyimpanan: " . mysqli_error($conn));
}

mysqli_stmt_bind_param(
    $stmt,
    "iis",
    $user_id,
    $jenis_surat_id,
    $keperluan
);

if (mysqli_stmt_execute($stmt)) {

    unset($_SESSION["csrf_token"]);

    mysqli_stmt_close($stmt);

    header("Location: riwayat.php?pengajuan=berhasil");
    exit;

} else {
    die("Gagal menyimpan pengajuan: " . mysqli_stmt_error($stmt));
}
?>
